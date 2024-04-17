<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\Provider;
use App\Models\User;
use App\Models\FleetGest\Vehicle;
use App\Models\FleetGest\FuelLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, File, Storage, Setting;

class FuelLogsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_fuel_logs';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_fuel_logs']);
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

        $data = compact(
            'vehicles',
            'providers',
            'operators'
        );

        return $this->setContent('admin.fleet.fuel_logs.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $action = 'Registar abastecimento';
        
        $fuel = new FuelLog;
                
        $formOptions = array('route' => array('admin.fleet.fuel.store'), 'method' => 'POST', 'files' => true);

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

        $data = compact(
            'fuel',
            'vehicles',
            'providers',
            'operators',
            'action',
            'formOptions'
        );

        return view('admin.fleet.fuel_logs.edit', $data)->render();
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

        $fuel = FuelLog::with('vehicle')
                ->filterSource()
                ->findOrfail($id);

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

        $vehicles = [$fuel->vehicle_id => @$fuel->vehicle->name];

        $action = 'Editar abastecimento';

        $formOptions = array('route' => array('admin.fleet.fuel.update', $fuel->id), 'method' => 'PUT', 'files' => true);

        $compact = compact(
            'fuel',
            'operators',
            'providers',
            'vehicles',
            'action',
            'formOptions'
        );

        return view('admin.fleet.fuel_logs.edit', $compact)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        FuelLog::flushCache(FuelLog::CACHE_TAG);

        $input = $request->all();

        $fuelLog = FuelLog::whereHas('vehicle', function($q){
                $q->filterSource();
                $q->filterAgencies();
            })->findOrNew($id);

        if ($fuelLog->validate($input)) {
            $fuelLog->fill($input);
            $fuelLog->save();

            //delete file
            if ($request->delete_file && !empty($fuelLog->filepath)) {
                File::delete(public_path().'/'.$fuelLog->filepath);
                $fuelLog->filepath = null;
                $fuelLog->filename = null;
            }

            //upload file
            if($request->hasFile('file')) {

                if ($fuelLog->exists && !empty($fuelLog->filepath)) {
                    File::delete(storage_path().'/'.$fuelLog->filepath);
                }

                if (!$fuelLog->upload($request->file('file'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o ficheiro.');
                }

            } else {
                $fuelLog->save();
            }

            FuelLog::updateVehicleCounters($fuelLog->vehicle_id);

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $fuelLog->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        FuelLog::flushCache(FuelLog::CACHE_TAG);

        $result = FuelLog::filterSource()
            ->find($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo de abastecimento.');
        }

        return Redirect::back()->with('success', 'Registo de abastecimento removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {
        
        $ids = explode(',', $request->ids);
        
        $rows = FuelLog::whereHas('vehicle', function($q){
                $q->filterSource();
                $q->filterAgencies();
            })->whereIn('id', $ids)
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

        $data = FuelLog::filterSource()
            ->with('vehicle', 'provider', 'invoice')
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

        //filter product
        $value = $request->get('product');
        if($request->has('product')) {
            $data = $data->where('product', $value);
        }

        return Datatables::of($data)
            ->edit_column('date', function($row) {
                return $row->date->format('Y-m-d');
            })
            ->edit_column('vehicle', function($row) {
                return view('admin.fleet.fuel_logs.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('km', function($row) {
                return money($row->km, '', 0) ;
            })
            ->edit_column('price_per_liter', function($row) {
                return money($row->price_per_liter, Setting::get('app_currency'));
            })
            ->edit_column('product', function($row) {
                return view('admin.fleet.fuel_logs.datatables.product', compact('row'))->render();
            })
            ->edit_column('total', function($row) {
                return '<b data-total="'.$row->total.'">' . money($row->total, Setting::get('app_currency')) . '</b>';
            })
            ->edit_column('balance_liter_km', function($row) {
                return view('admin.fleet.fuel_logs.datatables.consumption', compact('row'))->render();
            })
            ->edit_column('balance_km', function($row) {
                return view('admin.fleet.fuel_logs.datatables.balance', compact('row'))->render();
            })
            ->edit_column('operator', function($row) {
                return view('admin.fleet.fuel_logs.datatables.operator', compact('row'))->render();
            })
            ->edit_column('provider', function($row) {
                return view('admin.fleet.fuel_logs.datatables.provider', compact('row'))->render();
            })
            ->edit_column('obs', function($row) {
                return view('admin.fleet.fuel_logs.datatables.obs', compact('row'))->render();
            })
            ->edit_column('assigned_invoice_id', function($row) {
                return @$row->invoice->reference;
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.fleet.fuel_logs.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

}
