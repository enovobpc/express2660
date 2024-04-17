<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\FleetGest\Accessory;
use App\Models\FleetGest\Vehicle;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Http\Request;

class AccessoriesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_accessories';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_accessories']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $vehicles = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        return $this->setContent('admin.fleet.accessories.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        
        $action = 'Adicionar acessório';
  
        $accessory = new Accessory();
        $accessory->code = $accessory->setCode(false);

        $formOptions = array('route' => array('admin.fleet.accessories.store'), 'method' => 'POST', 'files' => true);

        $vehicles = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->isActive()
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        if($request->has('vehicle')) {
            $vehicles = [$request->vehicle => @$vehicles[$request->vehicle]];
        }

        return view('admin.fleet.accessories.edit', compact('accessory', 'action', 'formOptions', 'vehicles'))->render();
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id) {
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $accessory = Accessory::filterSource()
                        ->findOrfail($id);

        $action = 'Editar acessório';

        $formOptions = array('route' => array('admin.fleet.accessories.update', $accessory->id), 'method' => 'PUT', 'files' => true);

        $vehicles = [$accessory->vehicle_id => $accessory->vehicle->name];

        return view('admin.fleet.accessories.edit', compact('accessory', 'action', 'formOptions', 'vehicles'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Accessory::flushCache(Accessory::CACHE_TAG);

        $input = $request->all();

        $accessory = Accessory::filterSource()->findOrNew($id);

        if ($accessory->validate($input)) {
            $accessory->fill($input);
            $accessory->source = config('app.source');
            $accessory->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $accessory->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Accessory::flushCache(Accessory::CACHE_TAG);

        $accessory = Accessory::filterSource()->findOrfail($id);

        if (!$accessory->delete()) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o acessório.');
        }

        return Redirect::back()->with('success', 'Acessório removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Accessory::flushCache(Accessory::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = Accessory::filterSource()
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

        $data = Accessory::with('vehicle')
                ->filterSource()
                ->select();

        //filter date min
        $dtMin = $request->get('buy_date_min');
        if($request->has('buy_date_min')) {
            $dtMax = $dtMin;
            if($request->has('buy_date_max')) {
                $dtMax = $request->get('buy_date_max');
            }

            $data = $data->whereBetween('buy_date', [$dtMin, $dtMax]);
        }

        //filter vehicle
        $value = $request->get('vehicle');
        if($request->has('vehicle')) {
            $data = $data->where('vehicle_id', $value);
        }

        //filter type
        $value = $request->get('type');
        if($request->has('type')) {
            $data = $data->whereIn('type', $value);
        }

        return Datatables::of($data)
            ->add_column('vehicle', function($row) {
                return view('admin.fleet.accessories.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('name', function($row) {
                return view('admin.fleet.accessories.datatables.name', compact('row'))->render();
            })
            ->edit_column('type', function($row) {
                if($row->type) {
                    return trans('admin/fleet.accessories.types.'. $row->type);
                }
            })
            ->edit_column('buy_date', function($row) {
                return $row->buy_date->format('Y-m-d');
            })
            ->edit_column('validity_date', function($row) {
                return $row->validity_date->format('Y-m-d');
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.fleet.accessories.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

}
