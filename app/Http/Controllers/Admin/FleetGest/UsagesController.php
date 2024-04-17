<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\FleetGest\UsageLog;
use App\Models\FleetGest\Vehicle;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Http\Request;

class UsagesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_usages';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_usages']);
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

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterSource()
            ->ignoreAdmins()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $types = trans('admin/fleet.usages-logs.types');

        return $this->setContent('admin.fleet.usage_logs.index', compact('vehicles', 'operators', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        
        $action = 'Novo registo utilização';
  
        $usageLog = new UsageLog();

        $formOptions = array('route' => array('admin.fleet.usages.store'), 'method' => 'POST', 'files' => true);

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterSource()
            ->ignoreAdmins()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

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

        if($request->has('operator')) {
            $operators = [$request->operator => @$operators[$request->operator]];
        }

        $hours = listHours(5); 

        $data = compact(
            'usageLog',
            'action',
            'formOptions',
            'vehicles',
            'operators',
            'hours'
        );

 

        return view('admin.fleet.usage_logs.edit', $data)->render();
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

        $usageLog = UsageLog::filterSource()
                        ->findOrfail($id);

        $action = 'Editar registo utilização';

        $formOptions = array('route' => array('admin.fleet.usages.update', $usageLog->id), 'method' => 'PUT', 'files' => true);

        $vehicles = [$usageLog->vehicle_id => $usageLog->vehicle->name];

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $hours = listHours(5);

        $data = compact(
            'usageLog',
            'action',
            'formOptions',
            'vehicles',
            'operators',
            'hours'
        );

        return view('admin.fleet.usage_logs.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        UsageLog::flushCache(UsageLog::CACHE_TAG);

        $input = $request->all();
        $input['start_date'] = $input['start_date'] . ' ' .$input['start_hour'].':00';
        $input['end_date']   = $input['end_date'] . ' ' .$input['end_hour'].':00';

        $usageLog = UsageLog::filterSource()
                        ->findOrNew($id);

        if ($usageLog->validate($input)) {
            $usageLog->fill($input);
            $usageLog->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $usageLog->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        UsageLog::flushCache(UsageLog::CACHE_TAG);

        $usageLog = UsageLog::filterSource()
                    ->findOrfail($id);

        if (!$usageLog->delete()) {
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

        UsageLog::flushCache(UsageLog::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = UsageLog::filterSource()
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

        $data = UsageLog::with('vehicle')
                ->filterSource()
                ->select();

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('start_date', [$dtMin, $dtMax]);
        }

        //filter operator
        $value = $request->get('operator');
        if($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        //filter vehicle
        $value = $request->get('vehicle');
        if($request->has('vehicle')) {
            $data = $data->where('vehicle_id', $value);
        }

        $value = $request->get('type');
        if($request->has('type')){
            $data = $data->where('type', $value);
        }

        return Datatables::of($data)
            ->edit_column('vehicle', function($row) {
                return view('admin.fleet.usage_logs.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('start_date', function($row) {
                $date = $row->start_date;
                return view('admin.fleet.usage_logs.datatables.datetime', compact('date'))->render();
            })
            ->edit_column('end_date', function($row) {
                $date = $row->end_date;
                return view('admin.fleet.usage_logs.datatables.datetime', compact('date'))->render();
            })
            ->edit_column('start_km', function($row) {
                return number($row->start_km);
            })
            ->edit_column('end_km', function($row) {
                return number($row->end_km);
            })
            ->add_column('total_km', function($row) {
                return number($row->end_km - $row->start_km);
            })
            ->add_column('duration', function($row) {
                if($row->end_date) {
                    $diff = $row->start_date->diff($row->end_date)->format('%H:%I:%S');
                    return $diff;
                }
            })
            ->edit_column('services', function($row) {
                return view('admin.fleet.usage_logs.datatables.services', compact('row'))->render();
            })
            ->edit_column('operator', function($row) {
                return @$row->operator->name;
            })
            ->edit_column('type', function($row) {
                return view('admin.fleet.usage_logs.datatables.type', compact('row'))->render(); 
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.fleet.usage_logs.datatables.actions', compact('row'))->render();
            })    
            ->make(true);
    }

}
