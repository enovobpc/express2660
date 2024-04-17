<?php

namespace App\Http\Controllers\Admin;

use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Vehicle;
use App\Models\Agency;
use App\Models\User;


class VehiclesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'vehicles';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',vehicles']);
        validateModule('vehicles');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        return $this->setContent('admin.vehicles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $vehicle = new Vehicle();

        $agencies = Agency::remember(config('cache.query_ttl'))
                        ->cacheTags(Agency::CACHE_TAG)
                        ->filterSource()
                        ->get(['id', 'code', 'print_name', 'color']);

        $operators = User::remember(config('cache.query_ttl'))
                        ->cacheTags(User::CACHE_TAG)
                        ->filterSource()
                        ->ignoreAdmins()
                        ->orderBy('code', 'asc')
                        ->pluck('name', 'id')
                        ->toArray();

        $action = 'Adicionar Viatura';

        $formOptions = array('route' => array('admin.vehicles.store'), 'method' => 'POST', 'class' => 'form-vehicles');

        $data = compact(
            'vehicle',
            'action',
            'formOptions',
            'agencies',
            'operators'
        );

        return view('admin.vehicles.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return $this->update($request, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $vehicle = Vehicle::findOrfail($id);

        $agencies = Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterSource()
                ->get(['id', 'code', 'print_name', 'color']);

        $operators = User::remember(config('cache.query_ttl'))
                ->cacheTags(User::CACHE_TAG)
                ->filterSource()
                ->ignoreAdmins()
                ->orderBy('code', 'asc')
                ->pluck('name', 'id')
                ->toArray();

        $action = 'Editar Viatura';

        $formOptions = array('route' => array('admin.vehicles.update', $vehicle->id), 'method' => 'PUT', 'class' => 'form-vehicles');

        $data = compact(
            'vehicle',
            'action',
            'formOptions',
            'agencies',
            'operators'
        );

        return view('admin.vehicles.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Vehicle::flushCache(Vehicle::CACHE_TAG);
        User::flushCache(User::CACHE_TAG);

        $input = $request->all();
        $input['is_default'] = $request->get('is_default', false);

        if($input['is_default']) {
            Vehicle::filterSource()
                    ->where('is_default', 1)
                    ->update(['is_default' => 0]);
        }

        $vehicle = Vehicle::filterSource()->findOrNew($id);

        if ($vehicle->validate($input)) {
            $vehicle->fill($input);
            $vehicle->source = config('app.source');
            $vehicle->save();

            if($vehicle->operator_id) {
                User::whereVehicle($vehicle->license_plate)->update(['vehicle' => null]);
                User::whereId($vehicle->operator_id)->update(['vehicle' => $vehicle->license_plate]);
            } else {
                User::whereVehicle($vehicle->license_plate)->update(['vehicle' => null]);
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $vehicle->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Vehicle::flushCache(Vehicle::CACHE_TAG);

        $result = Vehicle::filterSource()
                            ->whereId($id)
                            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a viatura');
        }

        return Redirect::route('admin.vehicles.index')->with('success', 'Viatura removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Vehicle::flushCache(Vehicle::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = Vehicle::filterSource()
                            ->whereIn('id', $ids)
                            ->delete();
        
        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = Vehicle::filterSource()
                    ->filterAgencies()
                    ->select();

        $agencies = Agency::get(['id','code', 'name', 'color']);
        $agencies = $agencies->groupBy('id')->toArray();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.vehicles.datatables.name', compact('row'))->render();
                })
                ->edit_column('is_default', function($row) {
                    return view('admin.vehicles.datatables.default', compact('row'))->render();
                })
                ->edit_column('type', function($row) {
                    return trans('admin/fleet.vehicles.types.' . $row->type);
                })
                ->edit_column('gross_weight', function($row) {
                    return money($row->gross_weight, 'kg');
                })
                ->edit_column('usefull_weight', function($row) {
                    return money($row->usefull_weight, 'kg');
                })
                ->edit_column('agencies', function($row) use ($agencies) {
                    return view('admin.partials.datatables.agencies', compact('row', 'agencies'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.vehicles.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }


    /**
     * Remove the specified resource from storage.
     * GET /admin/services/sort
     *
     * @return Response
     */
    public function sortEdit() {

        $items = Vehicle::remember(config('cache.query_ttl'))
                    ->cacheTags(User::CACHE_TAG)
                    ->filterSource()
                    ->ordered()
                    ->get(['id', 'name']);

        $route = route('admin.vehicles.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        Vehicle::flushCache(Vehicle::CACHE_TAG);

        try {
            Vehicle::setNewOrder($request->ids);
            $response = [
                'result'  => true,
                'message' => 'Ordenação gravada com sucesso.',
            ];
        } catch (\Exception $e) {
            $response = [
                'result'  => false,
                'message' => 'Erro ao gravar ordenação. ' . $e->getMessage(),
            ];
        }

        return Response::json($response);
    }

}
