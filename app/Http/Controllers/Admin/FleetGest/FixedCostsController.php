<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Console\Commands\RunDailyTasks;
use App\Models\FleetGest\Cost;
use App\Models\FleetGest\FixedCost;
use App\Models\FleetGest\Vehicle;
use App\Models\Provider;
use Illuminate\Support\Facades\Redirect;
use Response;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Date, Auth;

class FixedCostsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_fixed_costs';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_fixed_costs']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function index() {
//    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        
        $action = 'Adicionar custo fixo';
  
        $cost = new FixedCost();

        $provider = new Provider();

        $formOptions = array('route' => array('admin.fleet.fixed-costs.store'), 'method' => 'POST', 'files' => true);

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

        return view('admin.fleet.fixed_costs.edit', compact('cost', 'action', 'formOptions', 'vehicles', 'provider'))->render();
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

        $cost = FixedCost::filterSource()
                    ->findOrfail($id);

        $provider = Provider::findOrfail($cost->provider_id);

        $action = 'Editar custo fixo';

        $formOptions = array('route' => array('admin.fleet.fixed-costs.update', $cost->id), 'method' => 'PUT', 'files' => true);

        $vehicles = [$cost->vehicle_id => $cost->vehicle->name];

        return view('admin.fleet.fixed_costs.edit', compact('cost', 'action', 'formOptions', 'vehicles', 'provider'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        FixedCost::flushCache(FixedCost::CACHE_TAG);
        $input = $request->all();
        $cost = FixedCost::filterSource()
            ->findOrNew($id);

        $exists = $cost->exists;
        if ($cost->validate($input)) {
            $cost->fill($input);
            $cost->save();

            //apaga todos os registos de histórico
            Cost::where('source_type', 'FixedCost')
                ->where('vehicle_id', $cost->vehicle_id)
                ->where('source_id', $cost->id)
                ->forceDelete();

            $startDate = new Date($input['start_date']);
            if($startDate->format('Y-m-d') <= date('Y-m-d')) {
                //ve quantos meses anteriores foi criada a taxa
                $months = CarbonPeriod::create($startDate, '1 month', Carbon::today());
                //adiciona 1 novo registo por cada mês
                foreach ($months as $month) {
                    $dt = $month->startOfMonth();
                    $vehicleCost = new Cost();
                    $vehicleCost->type        = 'fixed';
                    $vehicleCost->source_type = 'FixedCost';
                    $vehicleCost->source_id   = $cost->id;
                    $vehicleCost->vehicle_id  = $cost->vehicle_id;
                    $vehicleCost->provider_id = $cost->provider_id;
                    $vehicleCost->type_id     = $cost->type_id;
                    $vehicleCost->description = $cost->description;
                    $vehicleCost->total       = $cost->total;
                    $vehicleCost->obs         = $cost->obs;
                    $vehicleCost->date        = $dt;
                    $vehicleCost->created_by  = Auth::user()->id;
                    $vehicleCost->save();
                }
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $cost->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        FixedCost::flushCache(FixedCost::CACHE_TAG);

        $cost = FixedCost::filterSource()
            ->findOrfail($id);

        //apaga todos os registos de histórico de custo
        Cost::where('source_type', 'FixedCost')
            ->where('vehicle_id', $cost->vehicle_id)
            ->where('source_id', $cost->id)
            ->forceDelete();


        if (!$cost->delete()) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o custo.');
        }

        return Redirect::back()->with('success', 'Custo removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        FixedCost::flushCache(FixedCost::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = FixedCost::filterSource()
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

        $data = FixedCost::filterSource()
                        ->select();

        //filter vehicle
        $value = $request->get('vehicle');
        if($request->has('vehicle')) {
            $data = $data->where('vehicle_id', $value);
        }

        //filter type
        $value = $request->get('type');
        if($request->has('type')) {
            $data = $data->where('type', $value);
        }
        
        return Datatables::of($data)
            ->edit_column('vehicle_id', function($row) {
                return view('admin.fleet.fixed_costs.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('type', function($row) {
                return trans('admin/fleet.fixed-costs.types.'.$row->type);
            })
            ->edit_column('start_date', function($row) {
                return $row->start_date->format('Y-m-d');
            })
            ->edit_column('end_date', function($row) {
                return $row->end_date->format('Y-m-d');
            })
            ->edit_column('total', function($row) {
                return view('admin.fleet.vehicles.datatables.total', compact('row'))->render();
            })
            ->edit_column('description', function($row) {
                return view('admin.fleet.fixed_costs.datatables.description', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.fleet.fixed_costs.datatables.actions', compact('row'))->render();
            })
            ->make(true);

    }

    public function searchProviderSelect2(Request $request)
    {
        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';
        
        $fields = [
            'id',
            'code',
            'name'
        ];

        try {
            $results = [];

            $providers = Provider::
                filterSource()->
                filterAgencies()->
                //isActive()
                //->
                where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search);
                })
                // ->isDepartment(false)
                // ->isProspect(false)
                ->get($fields);

            if ($providers) {
                $results = array();
                foreach ($providers as $provider) {
                    $results[] = [
                        'id'    => $provider->id,
                        'text'  => $provider->code . ' - ' . str_limit($provider->name, 40),
                        
                        'code'      => strtoupper(trim($provider->code)),
                        'name'      => strtoupper(trim($provider->name)),
                    ];
                }
            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum cliente encontrado.'
                ];
            }
        } catch (\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage() . ' Line ' . $e->getLine()
            ];
        }

        return Response::json($results);
    }

}
