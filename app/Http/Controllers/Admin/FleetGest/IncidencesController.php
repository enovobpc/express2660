<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\FleetGest\Maintenance;
use App\Models\Provider;
use App\Models\FleetGest\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, File, Setting;
use App\Models\FleetGest\Incidence;

class IncidencesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_incidences';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_incidences']);
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
            ->isActive()
            ->orderBy('name', 'desc')
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterSource()
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
            'vehicles',
            'providers',
            'operators'
        );

        return $this->setContent('admin.fleet.incidences.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $action = 'Registar Ocorrência ou Sinistro';
        
        $incidence = new Incidence;
                
        $formOptions = array('route' => array('admin.fleet.incidences.store'), 'method' => 'POST', 'files' => true);

        $vehicles = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        if($request->has('vehicle')) {
            $vehicles = [$request->vehicle => @$vehicles[$request->vehicle]];
        }

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterSource()
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
            'incidence',
            'vehicles',
            'providers',
            'operators',
            'action',
            'formOptions'
        );

        return view('admin.fleet.incidences.edit', $data)->render();
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

        $incidence = Incidence::with('vehicle')
            ->filterSource()
            ->findOrfail($id);

        $vehicles = [$incidence->vehicle_id => @$incidence->vehicle->name];

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterSource()
            ->categoryGasStation()
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

        $action = 'Editar Ocorrência ou Sinistro';

        $formOptions = array('route' => array('admin.fleet.incidences.update', $incidence->id), 'method' => 'PUT', 'files' => true);


        $data = compact(
            'incidence',
            'vehicles',
            'providers',
            'operators',
            'action',
            'formOptions'
        );

        return view('admin.fleet.incidences.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Incidence::flushCache(Incidence::CACHE_TAG);

        $input = $request->all();

        $incidence = Incidence::filterSource()
                        ->findOrNew($id);

        if ($incidence->validate($input)) {
            $incidence->fill($input);
            $incidence->save();

            //delete file
            if ($request->delete_file && !empty($incidence->filepath)) {
                File::delete(public_path().'/'.$incidence->filepath);
                $incidence->filepath = null;
                $incidence->filename = null;
            }

            //upload file
            if($request->hasFile('file')) {

                if ($incidence->exists && !empty($incidence->filepath)) {
                    File::delete(storage_path().'/'.$incidence->filepath);
                }

                if (!$incidence->upload($request->file('file'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o ficheiro.');
                }

            } else {
                $incidence->save();
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $incidence->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Incidence::flushCache(Incidence::CACHE_TAG);

        $result = Incidence::filterSource()
                    ->find($id)
                    ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a ocorrência ou sinistro.');
        }

        return Redirect::back()->with('success', 'Ocorrência ou sinistro removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Incidence::flushCache(Incidence::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $rows = Incidence::filterSource()
                    ->whereIn('id', $ids)
                    ->get();

        foreach($rows as $row) {
            $result = $row->delete();
        }
        
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

        $data = Incidence::filterSource()
                        ->with('vehicle', 'operator')
                        ->select();

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter provider
        $value = $request->get('provider');
        if($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter vehicle
        $value = $request->get('vehicle');
        if($request->has('vehicle')) {
            $data = $data->where('vehicle_id', $value);
        }

        //filter operator
        $value = $request->get('operator');
        if($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('date', function($row) {
                return $row->date->format('Y-m-d');
            })
            ->edit_column('vehicle_id', function($row) {
                return view('admin.fleet.incidences.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('km', function($row) {
                if($row->km) {
                    return money($row->km, '', 0);
                }
            })
            ->edit_column('total', function($row) {
                return '<b>' . money($row->total, Setting::get('app_currency')) . '</b>';
            })
            ->edit_column('operator_id', function($row) {
                return view('admin.fleet.incidences.datatables.operator', compact('row'))->render();
            })
            ->edit_column('is_fixed', function($row) {
                return view('admin.fleet.incidences.datatables.fixed', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.fleet.incidences.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fixEdit($id) {

        $action = 'Resolução de Ocorrência ou Sinistro';

        $incidence = Incidence::with('vehicle')
            ->filterSource()
            ->findOrfail($id);

        $formOptions = array('route' => array('admin.fleet.incidences.fix.update', $incidence->id), 'method' => 'POST');

        $maintenances = Maintenance::where('vehicle_id', $incidence->vehicle_id)
                                ->orderBy('date', 'desc')
                                ->take(50)
                                ->pluck('title', 'id')
                                ->toArray();

        return view('admin.fleet.incidences.fix', compact('incidence', 'action', 'formOptions', 'maintenances'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fixUpdate(Request $request, $id) {

        $input = $request->all();

        $maintenance = Maintenance::findOrFail($input['maintenance_id']);

        $input['total'] = $maintenance->total;
        $input['provider_id'] = $maintenance->provider_id;
        $input['is_fixed'] = 1;

        $incidence = Incidence::filterSource()
            ->findOrNew($id);

        if ($incidence->validate($input)) {
            $incidence->fill($input);
            $incidence->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $incidence->errors()->first());
    }

}
