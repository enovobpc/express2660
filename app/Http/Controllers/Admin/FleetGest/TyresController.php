<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\FleetGest\Tyre;
use App\Models\FleetGest\TyrePosition;
use App\Models\FleetGest\Vehicle;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Http\Request;

class TyresController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_tyres';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_tyres']);
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

        return $this->setContent('admin.fleet.tyres.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        
        $action = 'Adicionar pneu';
  
        $tyre = new Tyre();

        $formOptions = array('route' => array('admin.fleet.tyres.store'), 'method' => 'POST', 'files' => true);

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

        $positions = TyrePosition::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterSource()
            ->categoryMechanic()
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterSource()
            ->ignoreAdmins()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'tyre', 
            'action', 
            'formOptions', 
            'vehicles', 
            'positions',
            'providers',
            'operators'
        );

        return view('admin.fleet.tyres.edit', $data)->render();
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

        $tyre = Tyre::filterSource()
                        ->findOrfail($id);

        $action = 'Editar pneu';

        $formOptions = array('route' => array('admin.fleet.tyres.update', $tyre->id), 'method' => 'PUT', 'files' => true);

        $vehicles = [$tyre->vehicle_id => $tyre->vehicle->name];

        $positions = TyrePosition::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterSource()
            ->categoryMechanic()
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterSource()
            ->ignoreAdmins()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'tyre', 
            'action', 
            'formOptions', 
            'vehicles', 
            'positions',
            'providers',
            'operators'
        );

        return view('admin.fleet.tyres.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Tyre::flushCache(Tyre::CACHE_TAG);

        $input = $request->all();

        $tyre = Tyre::filterSource()->findOrNew($id);

        if ($tyre->validate($input)) {
            $tyre->fill($input);
            $tyre->source = config('app.source');
            $tyre->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $tyre->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Tyre::flushCache(Tyre::CACHE_TAG);

        $tyre = Tyre::filterSource()->findOrfail($id);

        if (!$tyre->delete()) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::back()->with('success', 'Registo removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Tyre::flushCache(Tyre::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = Tyre::filterSource()
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

        $data = Tyre::with('vehicle')
                ->filterSource()
                ->select();

        //filter date min
        $dtMin = $request->get('tyre_date_min');
        if($request->has('tyre_date_min')) {
            $dtMax = $dtMin;
            if($request->has('tyre_date_max')) {
                $dtMax = $request->get('date');
            }

            $data = $data->whereBetween('buy_date', [$dtMin, $dtMax]);
        }

        //filter vehicle
        $value = $request->get('vehicle');
        if($request->has('vehicle')) {
            $data = $data->where('vehicle_id', $value);
        }

        //filter type
        $value = $request->get('position');
        if($request->has('position')) {
            $data = $data->whereIn('position_id', $value);
        }

        return Datatables::of($data)
            ->add_column('vehicle', function($row) {
                return view('admin.fleet.tyres.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('position_id', function($row) {
               return @$row->position->name;
            })
            ->edit_column('date', function($row) {
                return @$row->date ? @$row->date->format('Y-m-d') : '';
            })
            ->edit_column('end_date', function($row) {
                return !empty(@$row->end_date) ? @$row->end_date->format('Y-m-d') : '';
            })
            ->edit_column('kms', function($row) {
                return money($row->kms, '', 0);
            })
            ->edit_column('depth', function($row) {
                return view('admin.fleet.tyres.datatables.depth', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.fleet.tyres.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

}
