<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\FleetGest\Accessory;
use App\Models\FleetGest\Cost;
use App\Models\FleetGest\Expense;
use App\Models\FleetGest\FuelLog;
use App\Models\FleetGest\Maintenance;
use App\Models\FleetGest\UsageLog;
use App\Models\FleetGest\Vehicle;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Auth, Excel, DB, Hash;

class ExportsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_vehicles';

    /**
     * Store last row of each iteration
     *
     * @var type
     */
    protected $lastRow = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_vehicles']);
    }

    /**
     * Export file
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $exportMethod)
    {
        return $this->{$exportMethod}($request);
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function vehicles(Request $request)
    {
        $ids = $request->id;

        $header = [
            'Código',
            'Referência',
            'Matrícula',
            'Designação',
            'Tipo',
            'Marca',
            'Modelo',
            'Agência',
            'Motorista',
            'Reboque',
            'Combustível',
            'Peso Bruto',
            'Data Matrícula',
            'Data IPO',
            'Data IUC',
            'Data Seguro',
            'Data compra',
            'Km compra',
            'Consumo Atual',
            'KM atuais',
            'Observações'
        ];

        try {
            $data = Vehicle::with('operator', 'agency', 'brand', 'model', 'trailer')
                ->filterSource()
                ->filterAgencies();

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            } else {
                //filter type
                $value = $request->type;
                if ($request->has('type')) {
                    $data = $data->where('type', $value);
                }

                //filter brand
                $value = $request->brand_id;
                if ($request->has('brand_id')) {
                    $data = $data->where('brand_id', $value);
                }

                //filter operator
                $value = $request->operator_id;
                if ($request->has('operator_id')) {
                    $data = $data->where('operator_id', $value);
                }
            }

            $data = $data->get();

            Excel::create('Listagem de Viaturas', function ($file) use ($data, $header) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $vehicle) {

                        $rowData = [
                            $vehicle->code,
                            $vehicle->reference,
                            $vehicle->license_plate,
                            $vehicle->name,
                            trans('admin/fleet.vehicles.types.' . $vehicle->type),
                            @$vehicle->brand->name,
                            @$vehicle->model->name,
                            @$vehicle->agency->name,
                            @$vehicle->operator->name,
                            @$vehicle->trailer->license_plate,
                            trans('admin/fleet.fuel.' . $vehicle->fuel),
                            $vehicle->gross_weight,
                            $vehicle->registration_date ? $vehicle->registration_date->format('Y-m-d') : '',
                            $vehicle->ipo_date,
                            $vehicle->iuc_date,
                            $vehicle->insurance_date ? $vehicle->insurance_date->format('Y-m-d') : '',
                            $vehicle->buy_date,
                            $vehicle->initial_km,
                            $vehicle->counter_consumption,
                            $vehicle->counter_km,
                            $vehicle->obs
                        ];
                        $sheet->appendRow($rowData);
                    }
                });
            })->export('xls');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }

    /**
     * Export fuel log
     * @param Request $request
     * @param $customerId
     */
    public function fuel(Request $request)
    {
        try {
            $ids = $request->id;

            $data = FuelLog::with('operator', 'provider', 'vehicle')
                ->select();

            //filter date min
            $dtMin = $request->get('date_min');
            if ($request->has('date_min')) {
                $dtMax = $dtMin;
                if ($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                }

                $data = $data->whereBetween('date', [$dtMin, $dtMax]);
            }

            //filter provider
            $value = $request->get('fuel_provider');
            if ($request->has('fuel_provider')) {
                $data = $data->where('provider_id', $value);
            }

            //filter provider
            $value = $request->get('provider');
            if ($request->has('provider')) {
                $data = $data->where('provider_id', $value);
            }

            //filter operator
            $value = $request->get('fuel_operator');
            if ($request->has('fuel_operator')) {
                $data = $data->where('operator_id', $value);
            }

            //filter vehicle
            $value = $request->get('vehicle');
            if ($request->has('vehicle')) {
                $data = $data->where('vehicle_id', $value);
            }

            //filter operator
            $value = $request->get('operator');
            if ($request->has('operator')) {
                $data = $data->where('operator_id', $value);
            }

            //filter product
            $value = $request->get('product');
            if ($request->has('product')) {
                $data = $data->where('product', $value);
            }

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            }

            $data = $data->get();



            $header = [
                'Data',
                'Viatura',
                'Fornecedor',
                'Combustível',
                'Km',
                'Litros',
                'Preço/L',
                'Total',
                'Viagem',
                'Consumo',
                'Motorista',
                'Registado por',
                'Observações'
            ];

            $products = ['fuel' => 'Combustível', 'adblue' => 'AdBlue'];

            Excel::create('Registo Abastecimentos', function ($file) use ($data, $header, $products) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header, $products) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $item) {
                        $rowData = [
                            $item->date->format('Y-m-d'),
                            @$item->vehicle->license_plate,
                            @$item->provider->name,
                            isset($products[$item->product]) ? $products[$item->product] : 'Valor não encontrado',
                            $item->km,
                            $item->liters,
                            $item->price_per_liter,
                            $item->total,
                            $item->balance_km,
                            $item->balance_liter_km,
                            @$item->operator->name,
                            @$item->creator->name,
                            $item->obs,
                        ];
                        $sheet->appendRow($rowData);
                    }
                });
            })->export('xls');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }

    /**
     * Export maintenances
     * @param Request $request
     * @param $customerId
     */
    public function maintenances(Request $request)
    {

        try {
            $ids = $request->id;

            $dtMin = $request->get('date_min');
            $dtMax = $request->get('date_max');
            if ($request->has('maintenance_date_min')) {
                $dtMin = $request->get('maintenance_date_min');
            }

            if ($request->has('maintenance_date_max')) {
                $dtMax = $request->get('maintenance_date_max');
            }

            $data = Maintenance::filterSource()
                ->with('vehicle', 'provider', 'operator', 'creator')
                ->with(['parts' => function($q) {
                    $q->with('product');
                }]);

            //filter parts
            $value = $request->get('parts');
            if ($request->has('parts')) {
                $value = explode(',', $value);
                $data = $data->whereHas('parts', function($q) use($value) {
                    $q->whereIn('billing_product_id', $value);
                });
            }

            //filter date min
            if (!empty($dtMin)) {
                if (empty($dtMax)) {
                    $dtMax = $dtMin;
                }
                $data = $data->whereBetween('date', [$dtMin, $dtMax]);
            }

            //filter operator
            $value = $request->get('operator');
            if ($request->has('operator')) {
                $data = $data->where('operator_id', $value);
            }

            //filter provider
            $value = $request->get('provider');
            if ($request->has('provider')) {
                $data = $data->where('provider_id', $value);
            }

            //filter service
            $value = $request->get('service');
            if ($request->has('service')) {
                $data = $data->where('service_id', $value);
            }

            //filter vehicle
            $value = $request->get('vehicle');
            if ($request->has('vehicle')) {
                $data = $data->where('vehicle_id', $value);
            }

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            }

            $data = $data->get();

            $header = [
                'Data',
                'Viatura',
                'Fornecedor',
                'Serviço',
                'Km',
                'Total',
                'Peças',
                'Motorista',
                'Registado por',
                'Observações'
            ];


            Excel::create('Registo Manutenções', function ($file) use ($data, $header) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $item) {

                        $parts = $item->parts->pluck('product.name')->toArray();
                        $parts = implode(',', $parts);

                        $rowData = [
                            $item->date->format('Y-m-d'),
                            @$item->vehicle->license_plate,
                            @$item->provider->name,
                            $item->title,
                            $item->km,
                            $item->total,
                            $parts,
                            @$item->operator->name,
                            @$item->creator->name,
                            $item->obs,
                        ];
                        $sheet->appendRow($rowData);
                    }
                });
            })->export('xls');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }

    /**
     * Export costs
     * @param Request $request
     * @param $customerId
     */
    public function costsBalance(Request $request)
    {

        try {
            $ids = $request->id;

            $startDate = $request->get('start_date');
            $endDate   = $request->get('end_date');

            $data = Cost::filterSource()
                ->with('vehicle', 'provider')
                ->whereBetween('date', [$startDate, $endDate]);

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            }

            $data = $data->get();
            $vehicles = $data->groupBy('vehicle.license_plate');

            $vehiclesLicensesPlates = $data->pluck('vehicle.license_plate')->toArray();
            $vehiclesLicensesPlates = array_unique($vehiclesLicensesPlates);

            $shipmentsTotals = Shipment::whereIn('vehicle', $vehiclesLicensesPlates)
                ->whereBetween('date', [$startDate, $endDate])
                ->groupBy('vehicle')
                ->get([
                    'vehicle',
                    DB::raw('(sum(total_price) + sum(total_expenses)) as total')
                ]);


            $shipmentsTotals = $shipmentsTotals->groupBy('vehicle')->toArray();

            $header = [
                'Viatura',
                'Data Início',
                'Data Fim',
                'Combustível',
                'Manutenções',
                'Portagens',
                'Despesas Gerais',
                'Despesas Fixas',
                'Total Despesas',
                'Total Serviços',
                'Saldo',
            ];

            Excel::create('Balanço de Custos', function ($file) use ($vehicles, $startDate, $endDate, $header, $shipmentsTotals) {

                $file->sheet('Listagem', function ($sheet) use ($vehicles, $startDate, $endDate, $header, $shipmentsTotals) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    $totalFuel = $totalMaintenances =
                        $totalTolls = $totalExpenses = $totalFixed =
                        $totalExcelCosts = $totalExcelGains = 0;

                    foreach ($vehicles as $licensePlate => $vehicle) {

                        /* if(Auth::user()->id == 1 && $licensePlate == '38-OI-98') {
                            dd($vehicle->toArray());
                        }*/

                        $fuel = $vehicle->filter(function ($item) {
                            return $item->source_type == 'FuelLog';
                        });

                        $maintenances = $vehicle->filter(function ($item) {
                            return $item->source_type == 'Maintenance';
                        });

                        $tolls = $vehicle->filter(function ($item) {
                            return $item->source_type == 'TollLog';
                        });

                        $expenses = $vehicle->filter(function ($item) {
                            return $item->source_type == 'Expense';
                        });

                        $fixed = $vehicle->filter(function ($item) {
                            return $item->source_type == 'FixedCost';
                        });

                        $totalFuel += @$fuel->sum('total');
                        $totalMaintenances += @$maintenances->sum('total') +
                            $totalTolls += @$tolls->sum('total');
                        $totalExpenses += @$expenses->sum('total');
                        $totalFixed += @$fixed->sum('total');

                        $totalCosts = @$fuel->sum('total') +
                            @$maintenances->sum('total') +
                            @$tolls->sum('total') +
                            @$expenses->sum('total') +
                            @$fixed->sum('total');

                        $totalGains = @$shipmentsTotals[$licensePlate][0]['total'];

                        $totalExcelCosts += $totalCosts;
                        $totalExcelGains += $totalGains;

                        $balance = $totalGains - $totalCosts;

                        $rowData = [
                            @$licensePlate,
                            $startDate,
                            $endDate,
                            @$fuel->sum('total'),
                            @$maintenances->sum('total'),
                            @$tolls->sum('total'),
                            @$expenses->sum('total'),
                            @$fixed->sum('total'),
                            $totalCosts,
                            $totalGains,
                            $balance
                        ];
                        $sheet->appendRow($rowData);
                    }

                    if (!$vehicles->isEmpty()) {
                        $rowData = [
                            '',
                            '',
                            '',
                            $totalFuel,
                            $totalMaintenances,
                            $totalTolls,
                            $totalExpenses,
                            $totalFixed,
                            $totalExcelCosts,
                            $totalExcelGains,
                            $totalExcelGains - $totalExcelCosts
                        ];

                        $sheet->appendRow($rowData);
                    }
                });
            })->export('xls');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }

    /**
     * Export accessories
     * @param Request $request
     * @param $customerId
     */
    public function accessories(Request $request)
    {

        try {
            $ids = $request->id;

            $data = Accessory::with('vehicle')
                ->select();

            //filter date min
            $dtMin = $request->get('buy_date_min');
            if ($request->has('buy_date_min')) {
                $dtMax = $dtMin;
                if ($request->has('buy_date_max')) {
                    $dtMax = $request->get('buy_date_max');
                }

                $data = $data->whereBetween('buy_date', [$dtMin, $dtMax]);
            }

            //filter type
            $value = $request->get('type');
            if ($request->has('type')) {
                $value = explode(',', $value);
                $data = $data->whereIn('type', $value);
            }

            //filter vehicle
            $value = $request->get('vehicle');
            if ($request->has('vehicle')) {
                $data = $data->where('vehicle_id', $value);
            }

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            }

            $data = $data->get();


            $header = [
                'Referência',
                'Acessório',
                'Tipo',
                'Viatura',
                'Marca',
                'Modelo',
                'Data Compra',
                'Data Validade',
                'Observações'
            ];


            Excel::create('Listagem de Acessórios por Viatura', function ($file) use ($data, $header) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $item) {

                        $rowData = [
                            $item->code,
                            $item->name,
                            trans('admin/fleet.accessories.types.' . $item->type),
                            @$item->vehicle->license_plate,
                            $item->brand,
                            @$item->model,
                            $item->buy_date->format('Y-m-d'),
                            $item->validity_date->format('Y-m-d'),
                            $item->obs,
                        ];
                        $sheet->appendRow($rowData);
                    }
                });
            })->export('xls');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }

    /**
     * Export usages
     * @param Request $request
     * @param $customerId
     */
    public function usages(Request $request)
    {

        try {
            $ids = $request->id;

            $data = UsageLog::with('vehicle', 'operator');

            if($request->has('operator')){
                $data = $data->where('operator_id', $request->get('operator'));
            }

            $data = $data->select();

            //filter date
            $dtMin = $request->has('usage_operator') ? $request->get('usage_date_min') : $request->get('date_min');
            if ($request->has('date_min') || $request->has('usage_date_min')) {
                $dtMax = $dtMin;
                if ($request->has('date_max') || $request->get('usage_date_max')) {
                    $dtMax = $request->has('usage_operator') ? $request->get('usage_date_max') : $request->get('date_max');
                }

                $data = $data->whereBetween('start_date', [$dtMin, $dtMax]);
            }

            //filter vehicle
            $value = $request->get('vehicle');
            if ($request->has('vehicle')) {
                $data = $data->where('vehicle_id', $value);
            }

            //filter operator
            $value = $request->has('usage_operator') ? $request->get('usage_operator') : $request->get('operator');
            if ($request->has('operator') || $request->has('usage_operator')) {
                $data = $data->where('operator_id', $value);
            }

            //filter type
            $value = $request->get('type');
            if ($request->has('type')) {
                $data = $data->where('type', $value);
            }

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            }

            $data = $data->get();


            $header = [
                'Viatura',
                'Motorista',
                'Tipo',
                'Início Condução',
                'Km Iniciais',
                'Fim Condução',
                'Km Finais',
                'Duração',
                'Km totais',
                'Serviços'
            ];


            Excel::create('Histórico de Utilização Viaturas', function ($file) use ($data, $header) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $item) {

                        $diff = $item->start_date->diff($item->end_date)->format('%H:%I:%S');

                        $services = $item->services ? $item->services : [];

                        $rowData = [
                            @$item->vehicle->license_plate,
                            @$item->operator->name,
                            trans('admin/fleet.usages-logs.types.' . $item->type),
                            $item->start_date->format('Y-m-d'),
                            number($item->start_km),
                            $item->end_date->format('Y-m-d'),
                            number($item->end_km),
                            $diff,
                            number($item->end_km - $item->start_km),
                            implode(',', $services),
                        ];

                        $sheet->appendRow($rowData);
                    }
                });
            })->export('xls');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }

    /**
     * Export expenses
     * @param Request $request
     * @param $customerId
     */
    public function expenses(Request $request)
    {

        try {
            $ids = $request->id;

            $requestData = $request->all();

            $input = [];
            foreach ($requestData as $key => $value) {
                $key = str_replace('expenses_', '', $key);
                $input[$key] = $value;
            }

            $data = Expense::with('vehicle', 'operator', 'provider', 'operator')
                ->filterSource()
                ->select();

            //filter date min
            if (isset($input['date_min'])) {
                $dtMin = $input['date_min'];
                $dtMax = $dtMin;
                if (isset($input['date_max'])) {
                    $dtMax = $input['date_max'];
                }
                $data = $data->whereBetween('date', [$dtMin, $dtMax]);
            }

            //filter vehicle
            if (isset($input['vehicle'])) {
                $data = $data->where('vehicle_id', $input['vehicle']);
            }

            //filter provider
            if (isset($input['provider'])) {
                $data = $data->where('provider_id', $input['provider']);
            }

            //filter operator
            if (isset($input['operator'])) {
                $data = $data->where('operator_id', $input['operator']);
            }

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            }

            $data = $data->get();

            $header = [
                'Data',
                'Viatura',
                'Despesa',
                'Fornecedor',
                'Km',
                'Total',
                'Motorista',
                'Registado por',
                'Observações'
            ];

            Excel::create('Registo Despesas Gerais', function ($file) use ($data, $header) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $item) {

                        $rowData = [
                            $item->date->format('Y-m-d'),
                            @$item->vehicle->license_plate,
                            $item->title,
                            @$item->provider->name,
                            $item->km,
                            $item->total,
                            @$item->operator->name,
                            @$item->creator->name,
                            $item->obs,
                        ];
                        $sheet->appendRow($rowData);
                    }
                });
            })->export('xls');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }
}
