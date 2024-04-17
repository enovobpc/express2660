<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\FleetGest\Cost;
use App\Models\FleetGest\Vehicle;
use App\Models\Provider;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Html, Croppa, Date, DB, Setting;

class StatsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_stats';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_stats']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $metrics   = $request->get('metrics', 'daily');
        $period    = $request->get('period', '30');
        $vehicle   = $request->get('vehicle');

        $periodList = [];

        if ($metrics == 'daily') {
            $periodList = trans('admin/fleet.stats.daily');

            $startDate = null;
            if (!empty($period)) {
                $period = str_replace('d', '', $period);
                $startDate   = Date::today()->subDays($period);
            }

            $startDate = $request->get('date_min') ? $request->date_min : $startDate->format('Y-m-d');
            $endDate   = $request->get('date_max') ? $request->date_max : date('Y-m-d');
        } elseif ($metrics == 'yearly') {
            $years = yearsArr(2018, null, true);

            $request->year  = $request->year ? $request->year : date('Y');

            $startDate = $request->get('date_min', $request->year . '-01-01');
            $endDate   = $request->get('date_max', $request->year . '-12-31');
            
            $startDate = empty($startDate) ? date('Y') . '-01-01' : $startDate;
            $endDate   = empty($endDate) ? date('Y') . '-12-31' : $endDate;
        } elseif ($metrics == 'monthly') {

            $request->year  = $request->year ? $request->year : date('Y');
            $request->month = $request->month ? $request->month : date('n');

            $startDate = $request->get('date_min', $request->year . '-' . $request->month . '-01');
            $endDate   = $request->get('date_max', $request->year . '-' . $request->month . '-31');

            $startDate = empty($startDate) ? date('Y-m') . '-01' : $startDate;
            $endDate   = empty($endDate) ? date('Y-m') . '-31' : $endDate;

            $years  = yearsArr(2018, null, true);
            $months = trans('datetime.month');
        }


        //VEHICLES
        $allVeichles = Vehicle::filterSource()
            ->filterAgencies()
            ->select();

        if ($request->has('vehicle')) {
            $allVeichles = $allVeichles->where('id', $vehicle);
        } else {
            $allVeichles = $allVeichles->isActive(); //se não tem filtro de viatura força a ser só as viaturas ativas
        }

        $allVeichles = $allVeichles->get();


        if ($request->has('vehicle') && !empty($request->get('vehicle'))) {
            $allVeichles = $allVeichles->filter(function ($item) use ($vehicle) {
                return $item->id == $vehicle;
            });
        }

        $veichlesIds    = $allVeichles->pluck('id')->toArray();
        $licensesPlates = $allVeichles->pluck('license_plate')->toArray();




        //LIST VEHICLES (ACTIVE + INACTIVE)
        $veichlesList = Vehicle::filterSource()
            ->filterAgencies()
            ->orderBy('type')
            ->get(['id', 'status', 'license_plate']);

        $veichlesList = $veichlesList->groupBy('status');

        $arr = [];
        foreach ($veichlesList as $status => $vehicleList) {
            $arr[trans('admin/fleet.vehicles.status.' . $status)] = $vehicleList->pluck('license_plate', 'id')->toArray();
        }

        $veichlesList = $arr;


        //CALC GAINS
        $allGains = \App\Models\Shipment::whereBetween('date', [$startDate, $endDate])
            ->whereIn('vehicle', $licensesPlates)
            ->get([
                'vehicle',
                'date',
                'total_price',
                'total_expenses',
                DB::raw('DATE_FORMAT(date, \'%Y-%m\') as month_year'),
            ]);

        $gains = $allGains->groupBy('vehicle');


        //CALC COSTS
        $allCosts = Cost::with('provider', 'vehicle')
            ->whereIn('vehicle_id', $veichlesIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->get([
                'id',
                'vehicle_id',
                'provider_id',
                'type',
                'total',
                DB::raw('DATE(date) as dt'),
                DB::raw('YEAR(date) as year'),
                DB::raw('MONTH(date) as month'),
                DB::raw('DAY(date) as day'),
                DB::raw('DATE_FORMAT(date, \'%Y-%m\') as month_year')
            ]);


        $costsByProvider = $allCosts->groupBy('provider_id');
        $costs           = $allCosts->groupBy('vehicle_id');

        $veichlesTotals = [];
        $balanceChart = [];

        $globalStats['gains']    = $globalStats['services'] = $globalStats['costs'] =
            $globalStats['balance']  = $globalStats['fuel'] = $globalStats['maintenances'] =
            $globalStats['expenses'] = $globalStats['fixed_costs'] = $globalStats['tolls'] = 0;

        foreach ($allVeichles as $veichle) {

            $arr = @$gains[$veichle->license_plate];
            $countServices = $gainsTotal = 0;
            if ($arr) {
                $countServices  = $arr->count();
                $gainsTotal     = $arr->sum('total_price') + $arr->sum('total_expenses');
            }

            $vehicleCosts = @$costs[$veichle->id];
            $totalCosts = 0;
            $costByType = [];
            if ($vehicleCosts) {
                $vehicleCosts = $vehicleCosts->groupBy('type');
                foreach ($vehicleCosts as $type => $vehicleCost) {
                    $total = $vehicleCost->sum('total');
                    $costByType[$type] = $total;
                    $totalCosts += $total;
                }
            }

            $veichlesTotals[$veichle->license_plate] = [
                'services'      => $countServices,
                'gains'         => (float) $gainsTotal,
                'costs'         => (float) $totalCosts,
                'gains_percent' => percent($gainsTotal + $totalCosts, $gainsTotal),
                'costs_percent' => percent($gainsTotal + $totalCosts, $totalCosts),
                'balance'       => (float) $gainsTotal - (float) $totalCosts,
                'fuel'          => (float) @$costByType['gas_station'],
                'maintenances'  => (float) @$costByType['maintenance'],
                'expenses'      => (float) @$costByType['expense'],
                'fixed_costs'   => (float) @$costByType['fixed'],
                'tolls'         => (float) @$costByType['toll'],
            ];

            $balanceChart['labels'][]   = "'" . $veichle->license_plate . "'";
            $balanceChart['gains'][]    = (float) $gainsTotal;
            $balanceChart['costs'][]    = (float) $totalCosts;
            $balanceChart['services'][] = (int) $countServices;

            $globalStats['services'] += $veichlesTotals[$veichle->license_plate]['services'];
            $globalStats['gains'] += $veichlesTotals[$veichle->license_plate]['gains'];
            $globalStats['costs'] += $veichlesTotals[$veichle->license_plate]['costs'];
            $globalStats['balance'] += $veichlesTotals[$veichle->license_plate]['balance'];
            $globalStats['fuel'] += $veichlesTotals[$veichle->license_plate]['fuel'];
            $globalStats['maintenances'] += $veichlesTotals[$veichle->license_plate]['maintenances'];
            $globalStats['expenses'] += $veichlesTotals[$veichle->license_plate]['expenses'];
            $globalStats['fixed_costs'] += $veichlesTotals[$veichle->license_plate]['fixed_costs'];
            $globalStats['tolls'] += $veichlesTotals[$veichle->license_plate]['tolls'];
        }

        $globalStats['gains_percent'] = percent($globalStats['gains'] + $globalStats['costs'], $globalStats['gains']);
        $globalStats['costs_percent'] = percent($globalStats['gains'] + $globalStats['costs'], $globalStats['costs']);

        if ($globalStats['balance'] > 0) {
            $globalStats['balance_percent'] = percent($globalStats['gains'] + $globalStats['costs'], $globalStats['balance']);
        } else {
            $globalStats['balance_percent'] = percent($globalStats['gains'] + $globalStats['costs'], -1 * $globalStats['balance']);
        }


        /**
         * Stats by veichle
         */
        $veichlesGraphData = [];

        if (count($veichlesTotals) == 1) {
            $veichlesGraphData['labels'] = ["'Viatura " . $veichle->license_plate . "'"];
            $veichlesGraphData['tolls'][]        = $veichlesTotals[$veichle->license_plate]['tolls'];
            $veichlesGraphData['fuel'][]         = $veichlesTotals[$veichle->license_plate]['fuel'];
            $veichlesGraphData['expenses'][]     = $veichlesTotals[$veichle->license_plate]['expenses'];
            $veichlesGraphData['fixed_costs'][]  = $veichlesTotals[$veichle->license_plate]['fixed_costs'];
            $veichlesGraphData['maintenances'][] = $veichlesTotals[$veichle->license_plate]['maintenances'];
            $veichlesGraphData['stacked'] = false;
        } else {
            foreach ($veichlesTotals as $key => $data) {
                $veichlesGraphData['labels'][]       = "'" . $key . "'";
                $veichlesGraphData['tolls'][]        = $data['tolls'];
                $veichlesGraphData['fuel'][]         = $data['fuel'];
                $veichlesGraphData['expenses'][]     = $data['expenses'];
                $veichlesGraphData['fixed_costs'][]  = $data['fixed_costs'];
                $veichlesGraphData['maintenances'][] = $data['maintenances'];
            }
            $veichlesGraphData['stacked'] = true;
        }

        $veichlesGraphData['labels']   = @implode($veichlesGraphData['labels'], ',');
        $veichlesGraphData['tolls']    = @implode($veichlesGraphData['tolls'], ',');
        $veichlesGraphData['fuel']     = @implode($veichlesGraphData['fuel'], ',');
        $veichlesGraphData['expenses'] = @implode($veichlesGraphData['expenses'], ',');
        $veichlesGraphData['fixed_costs']  = @implode($veichlesGraphData['fixed_costs'], ',');
        $veichlesGraphData['maintenances'] = @implode($veichlesGraphData['maintenances'], ',');

        $balanceChart['labels']   = @implode($balanceChart['labels'], ',');
        $balanceChart['gains']    = @implode($balanceChart['gains'], ',');
        $balanceChart['costs']    = @implode($balanceChart['costs'], ',');
        $balanceChart['services'] = @implode($balanceChart['services'], ',');

        //SUMMARY CHART
        $summaryChart = [];
        $summaryChart['labels'] = ["'Ganhos'", "'Combustível'", "'Manutenções'", "'Despesas Gerais'", "'Custos Fixas'", "'Portagens'"];
        $summaryChart['colors'] = ["'#41b200'", "'#d10c00'", "'#ff851b'", "'#ffc107'", "'#f54735'", "'#e88d6d'"];
        $summaryChart['values'] = [
            $globalStats['gains'],
            $globalStats['fuel'],
            $globalStats['maintenances'],
            $globalStats['expenses'],
            $globalStats['fixed_costs'],
            $globalStats['tolls'],
        ];
        $summaryChart['labels']   = @implode($summaryChart['labels'], ',');
        $summaryChart['colors']   = @implode($summaryChart['colors'], ',');
        $summaryChart['values']   = @implode($summaryChart['values'], ',');


        //HISTORY CHART
        if ($metrics == 'yearly') {
            $periods = CarbonPeriod::create($startDate, $endDate, 'P1M');
            $costs = $allCosts->groupBy('month_year');
            $gains = $allGains->groupBy('month_year');
        } elseif ($metrics == 'monthly') {
            $periods = CarbonPeriod::create($startDate, $endDate);
            $costs = $allCosts->groupBy('dt');
            $gains = $allGains->groupBy('date');
        } elseif ($metrics == 'daily') {
            $periods = CarbonPeriod::create($startDate, $endDate);
            $costs = $allCosts->groupBy('dt');
            $gains = $allGains->groupBy('date');
        }

        $historyChart = [];
        foreach ($periods as $period) {

            if ($metrics == 'yearly') {
                $label = trans('datetime.month-tiny.' . $period->month) . ' ' . $period->year;
                $period = $period->format('Y-m');
            } elseif ($metrics == 'monthly') {
                $period = $period->format('Y-m-d');
                $label = $period;
            } elseif ($metrics == 'daily') {
                $period = $period->format('Y-m-d');
                $label = $period;
            }

            $gainsPeriod = @$gains[$period];
            if (!empty($gainsPeriod)) {
                $gainsPeriod = $gainsPeriod->sum('total_price') + $gainsPeriod->sum('total_expenses');
            }


            if (isset($costs[$period])) {
                $values = $costs[$period]->groupBy('type');
                $costsPeriod = [];
                foreach ($values as $key => $value) {
                    $costsPeriod[$key] = $value->sum('total');
                }
            }

            $historyChart['labels'][] = "'" . $label . "'";
            $historyChart['gains'][] = (float) @$gainsPeriod;
            $historyChart['maintenances'][] = (float)@$costsPeriod['maintenances'];
            $historyChart['expenses'][] = (float)@$costsPeriod['expenses'];
            $historyChart['fixed_costs'][] = (float)@$costsPeriod['fixed_costs'];
            $historyChart['tolls'][] = (float)@$costsPeriod['tolls'];
            $historyChart['fuel'][] = (float)@$costsPeriod['gas_station'];
        }

        $historyChart['labels']   = @implode($historyChart['labels'], ',');
        $historyChart['gains']    = @implode($historyChart['gains'], ',');
        $historyChart['tolls']    = @implode($historyChart['tolls'], ',');
        $historyChart['fuel']     = @implode($historyChart['fuel'], ',');
        $historyChart['expenses'] = @implode($historyChart['expenses'], ',');
        $historyChart['fixed_costs']  = @implode($historyChart['fixed_costs'], ',');
        $historyChart['maintenances'] = @implode($historyChart['maintenances'], ',');

        /**
         * Providers
         */
        $providers = Provider::filterSource()
            //->filterFleetProviders()
            ->pluck('name', 'id');

        $providerStats = [];
        foreach ($costsByProvider as $providerId => $providerCosts) {

            $total = $providerCosts->sum('total');
            $count = $providerCosts->count();
            $type  = $providerCosts->first()->type;

            if ($type == 'gas_station') {
                $typeIcon = '<i class="fas fa-gas-pump text-red"></i>';
            } elseif ($type == 'maintenance') {
                $typeIcon = '<i class="fas fa-wrench text-orange"></i>';
            } elseif ($type == 'tolls') {
                $typeIcon = '<i class="fas fa-road text-purple"></i>';
            } else {
                $typeIcon = '<i class="fas fa-euro-sign text-yellow"></i>';
            }

            $providerStats[] = [
                'type'      => $type,
                'typeIcon'  => $typeIcon,
                'name'      => $providerId ? (@$providers[$providerId] ? @$providers[$providerId] : 'Sem fornecedor') : 'Sem fornecedor',
                'count'     => $count,
                'total'     => number_format($total, 2, '.', '') . Setting::get('app_currency')
            ];
        }

        $data = compact(
            'veichlesList',
            'metrics',
            'veichlesGraphData',
            'balanceChart',
            'summaryChart',
            'historyChart',
            'globalStats',
            'veichlesGraphData',
            'providerStats',
            'chartStacked',
            'periodList',
            'months',
            'years'
        );

        return $this->setContent('admin.fleet.stats.index', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getResumeChart(Request $request, $vehicleId)
    {

        $today      = new Date();
        $endDate    = $today->format('Y-m-d');
        $startDate  = $today->subMonth(6)->format('Y-m-d');
        $graphsData = Vehicle::getStatistics($startDate, $endDate, $vehicleId);

        $graphData = [];
        foreach ($graphsData as $key => $data) {

            $date = explode('-', $key);

            $graphData['labels'][]        = trans('datetime.month-tiny.' . $date[1]) . '/' . $date[0];
            $graphData['gains'][]         = @$data['gains'];
            $graphData['maintenance'][]   = @$data['maintenance'];
            $graphData['fuel'][]          = @$data['fuel'];
            $graphData['others'][]        = @$data['gains'];
        }

        return json_encode($graphData);
    }
}
