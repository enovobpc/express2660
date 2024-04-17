<?php

namespace App\Http\Controllers\Admin\Printer;

use App\Models\FleetGest\Cost;
use App\Models\FleetGest\Vehicle;
use App\Models\Invoice;
use App\Models\Provider;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceType;
use App\Models\PurchasePaymentNote;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use App\Models\User;
use App\Models\UserExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Date\Date;
use Mpdf\Mpdf;
use Setting, Response, Auth, DB;

class PurchaseInvoicesController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',purchase_invoices']);
    }

    /**
     * Print list of current account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request, $grouped = null)
    {

        $ids = $request->get('id');
        if (!$grouped) {
            $grouped = $request->get('grouped', false);
        }

        $types = null;
        if ($grouped) {
            if ($grouped == 2) { //grouped by vehicle
                //$this->listingGroupedByVehicle($request);
            } else {
                $types = PurchaseInvoiceType::ordered()->pluck('name', 'id')->toArray();
            }
        }

        $sortField = 'doc_date';
        if(config('app.source') == 'corridexcelente') {
            $sortField = 'payment_date';
        }

        if (!empty($ids)) {
            $invoices = PurchaseInvoice::with('user', 'type')
                ->filterSource()
                ->whereNotIn('doc_type', ['payment-note'])
                ->whereNull('is_scheduled')
                ->whereIn('id', $ids)
                ->orderBy('doc_date', 'desc')
                ->get();
        } else {
            $data = PurchaseInvoice::with('user', 'type')
                ->filterSource()
                ->whereNotIn('doc_type', ['payment-note'])
                ->whereNull('is_scheduled')
                ->where('is_deleted', 0);

            //filter date min
            $dtMin = $request->get('date_min');
            if ($request->has('date_min')) {
                $dtMax = $dtMin;
                if ($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                }

                $dateUnity = 'doc_date';
                if ($request->has('date_unity')) {
                    if ($request->date_unity == 'due') {
                        $dateUnity = 'due_date';
                    } elseif ($request->date_unity == 'pay') {
                        $dateUnity = 'payment_date';
                    }
                }

                $data = $data->whereBetween($dateUnity, [$dtMin, $dtMax]);
            }

            //filter paid
            $value = $request->paid;
            if ($request->has('paid')) {
                if ($value) {
                    $data = $data->where('is_settle', 1);
                } else {
                    $data = $data->where('is_settle', 0);
                }
            }

            //filter expired
            $value = $request->expired;
            if ($request->has('expired')) {
                if ($value) {
                    $data = $data->where('due_date', '<', date('Y-m-d'));
                } else {
                    $data = $data->where('due_date', '>=', date('Y-m-d'));
                }
            }

            //filter sense
            $value = $request->sense;
            if ($request->has('sense')) {
                $data = $data->where('sense', $value);
            }

            //filter ignore invoice
            $value = $request->ignore_stats;
            if ($request->has('ignore_stats')) {
                $data = $data->where('ignore_stats', $value);
            }

            //filter target
            $value = $request->target;
            if ($request->has('target')) {
                $data = $data->where('target', $value);
            }

            //filter target id
            $value = $request->target_id;
            if ($request->has('target_id')) {
                $data = $data->where('target_id', $value);
            }

            //filter type
            $value = $request->type;
            if ($request->has('type')) {
                $value = explode(',', $value);
                $data = $data->whereIn('type_id', $value);
            }

            //filter doc id
            $value = $request->doc_id;
            if ($request->has('doc_id')) {
                $data = $data->where('doc_id', $value);
            }

            //filter doc type
            $value = $request->doc_type;
            if ($request->has('doc_type')) {
                $value = explode(',', $value);
                $data = $data->whereIn('doc_type', $value);
            }

            //filter provider
            $value = $request->provider;
            if ($request->has('provider')) {
                $data = $data->where('provider_id', $value);
            }

            //filter payment method
            $value = $request->payment_method;
            if ($request->has('payment_method')) {
                $data = $data->whereIn('payment_method', $value);
            }

            //filter deleted
            $value = $request->deleted;

            if ($request->has('deleted') && empty($value)) {
                $data = $data->where('is_deleted', $value);
            }

            $invoices = $data->orderBy($sortField)
                            ->get();

        }

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4-L',
            'margin_top'    => 30,
            'margin_bottom' => 20,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'invoices'        => $invoices,
            'grouped'         => $grouped,
            'types'           => $types,
            'documentTitle'   => 'Listagem de Despesas',
            'documentSubtitle' => '',
            'view'            => 'admin.printer.invoices.purchase.listing'
        ];

        $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Despesas.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Print list of payment notes
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function listingPaymentNotes(Request $request, $grouped = null)
    {
        $ids = $request->get('id');

        $sortField = 'doc_date';
        if(config('app.source') == 'corridexcelente') {
            $sortField = 'payment_date';
        }

        if (!empty($ids)) {
            $invoices = PurchasePaymentNote::with('user')
                ->filterSource()
                ->whereIn('id', $ids)
                ->orderBy('doc_date', 'desc')
                ->get();
        } else {
            $data = PurchasePaymentNote::with('user')
                ->filterSource();

            //filter date min
            $dtMin = $request->get('date_min');
            if ($request->has('date_min')) {
                $dtMax = $dtMin;
                if ($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                }
                $data = $data->whereBetween('doc_date', [$dtMin, $dtMax]);
            }

            //filter doc id
            $value = $request->doc_id;
            if ($request->has('doc_id')) {
                $data = $data->where('doc_id', $value);
            }

            //filter provider
            $value = $request->provider;
            if ($request->has('provider')) {
                $data = $data->where('provider_id', $value);
            }

            //filter deleted
            $value = $request->deleted;

            if ($request->has('deleted') && empty($value)) {
                $data = $data->where('is_deleted', $value);
            }

            $invoices = $data->orderBy($sortField)
                            ->get();

        }
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4-L',
            'margin_top'    => 30,
            'margin_bottom' => 20,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'invoices'        => $invoices,
            'documentTitle'   => 'Listagem de Notas de Pagamento',
            'documentSubtitle' => '',
            'view'            => 'admin.printer.invoices.purchase.listing-payment-notes'
        ];

        $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Notas de Pagamento.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Print map
     *
     * @param Request $request
     * @param $mapType
     * @return bool
     */
    public function printMap(Request $request, $mapType)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        if (empty($startDate)) {
            $endDate   = Date::today()->format('Y-m-d');
            $startDate = Date::today()->subDays(30)->format('Y-m-d');
        }

        if ($mapType == 'type') {
            return $this->printSummaryByType($request);
        } elseif ($mapType == 'vehicle') {
            return $this->printSummaryByVehicle($request);
        } elseif ($mapType == 'unpaid') {
            return $this->printSummaryUnpaidByProvider($request, $startDate, $endDate);
        }

        return Redirect::back()->with('error', 'Mapa não disponível.');
    }


    /**
     * Print list of current account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printSummaryUnpaidByProvider(Request $request, $startDate, $endDate)
    {

        $provider  = $request->get('provider');
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $period    = [$startDate, $endDate];


        $invoices = PurchaseInvoice::with('provider')
            ->filterSource()
            ->where('is_deleted', 0)
            ->where('is_settle', 0)
            ->whereBetween('doc_date', $period);

        if (!empty($provider)) {
            $invoices = $invoices->where('provider_id', $provider);
        }

        if (!empty($startDate) || !empty($endDate)) {
            $invoices = $invoices->whereBetween('doc_date', $period);
        }

        $invoices = $invoices->orderBy('billing_name', 'asc')->get();
        $invoices = $invoices->groupBy('provider_id');

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_top'    => 30,
            'margin_bottom' => 20,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;
        
        $data = [
            'invoices'        => $invoices,
            'documentTitle'   => 'Mapa Pendentes por Fornecedor',
            'documentSubtitle' => 'Mapa pendentes por fornecedor entre ' . $startDate . ' e ' . $endDate,
            'view'            => 'admin.printer.invoices.purchase.map_unpaid_by_provider',
        ];

        $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Despesas.pdf', 'I'); //output to screen

        exit;
    }


    /**
     * Print list of current account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printSummaryByVehicle(Request $request)
    {

        $details = $request->get('details');
        $grouped = $request->get('grouped', true);

        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        if (empty($startDate)) {
            $endDate   = Date::today()->format('Y-m-d');
            $startDate = Date::today()->subDays(30)->format('Y-m-d');
        }

        $period = [$startDate, $endDate];

        $types = null;
        $expensesTypes = PurchaseInvoiceType::filterSource()->get();
        $allVehicles   = \App\Models\Vehicle::listVehicles(false, 'name', 'license_plate');
        $allUsers      = User::filterSource()
            ->whereHas('roles', function ($q) {
                $q->where('role_id', '<>', 1);
            })
            ->pluck('name', 'id')
            ->toArray();

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_top'    => 30,
            'margin_bottom' => 20,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $global = $this->getGlobalCosts($request, $period, true, true);
        $fleet  = $this->getFleetCosts($request, $period, $grouped, $details);
        $users  = $this->getUsersCosts($request, $period, $grouped, $details);
        $gainsVehicle  = $this->getShipmentsBilled($request, $period, true);
        $gainsOperator = $this->getShipmentsBilled($request, $period, false, true);

        $data = [];
        $data['geral'] = $global;
        $data['users'] = $users;

        $totals['shipments'] = $this->getShipmentsTotals($request, $period);
        $totals['sales']     = $this->getInvoicesSalesTotals($request, $period);
        $totals['purchases'] = $this->getInvoicesPurchasesTotals($request, $period);
        $totals['balance']   = $totals['sales']['subtotal'] - $totals['purchases']['subtotal'];

        $data['types'] = [];
        foreach ($expensesTypes as $expense) {

            $subtotal = @$global[$expense->id]['subtotal'];
            $total    = @$global[$expense->id]['total'];
            $vat      = @$global[$expense->id]['vat'];
            $count    = @$global[$expense->id]['count'];

            $data['types'][$expense->id] = [
                'name'      => $expense->name,
                'subtotal'  => $subtotal,
                'total'     => $total,
                'vat'       => $vat,
                'count'     => $count
            ];
        }
        aasort($data['types'], 'subtotal', SORT_DESC);

        $data['fleet'] = [];
        foreach ($allVehicles as $licensePlate => $vehicleName) {

            $costs = @$fleet[$licensePlate];
            if ($costs && $details) {
                aasort($costs, 'subtotal', SORT_DESC);
            }

            $data['fleet'][$licensePlate] = [
                'name'   => $vehicleName,
                'costs'  => $details ? $costs : $costs['subtotal'],
                'gains'  => @$gainsVehicle[$licensePlate][0]['subtotal']
            ];
        }


        $data['users'] = [];
        foreach ($allUsers as $userId => $username) {

            $costs = @$users[$userId];
            if ($costs && $details) {
                aasort($costs, 'subtotal', SORT_DESC);
            }

            $data['users'][$userId] = [
                'name'   => $username,
                'costs'  => $details ? $costs : $costs['subtotal'],
                'gains'  => @$gainsOperator[$userId][0]['subtotal']
            ];
        }

        $data = [
            'types'           => $types,
            'documentTitle'   => 'Mapa Resumo de Despesas',
            'documentSubtitle' => 'Mapa resumo de valores efetivamente faturados',
            'view'            => 'admin.printer.invoices.purchase.map_summary_vehicle',
            'data'            => $data,
            'details'         => $details,
            'startDate'       => $startDate,
            'endDate'         => $endDate,
            'totals'          => $totals
        ];

        $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Despesas.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * @param $request
     * @param $period
     * @param $grouped
     * @return array
     */
    public function getGains($request, $period, $docDetails = true)
    {

        $invoices = Invoice::with(['customer' => function ($q) {
            $q->select(['id', 'name', 'code']);
        }])
            ->filterSource()
            ->whereBetween('doc_date', $period)
            ->take(20)
            ->get([
                'customer_id',
                'doc_type',
                'doc_subtotal',
                'doc_vat'
            ]);

        $customersInvoices = $invoices->groupBy('customer_id');

        $groupedData = [];
        foreach ($customersInvoices as $customerId => $invoices) {

            $customerTotal = 0;
            $docs = $totalInvoices = $totalReceipts = $totalNoDoc = [];
            foreach ($invoices as $invoice) {

                $customerCode = @$invoice->customer->code;
                $customerName = @$invoice->customer->name;

                if ($invoice->doc_type == 'receipt') {
                    $totalReceipts['total'] = @$totalReceipts['total'] + $invoice->doc_subtotal;
                    $totalReceipts['vat']   = @$totalReceipts['vat'] + $invoice->doc_vat;
                    $totalReceipts['count'] = @$totalReceipts['count'] + 1;
                } elseif ($invoice->doc_type == 'invoice-receipt' || $invoice->doc_type == 'internal-doc') {
                    $totalInvoices['total'] = @$totalInvoices['total'] + $invoice->doc_subtotal;
                    $totalInvoices['vat']   = @$totalInvoices['vat'] + $invoice->doc_vat;
                    $totalInvoices['count'] = @$totalInvoices['count'] + 1;
                    $totalReceipts['total'] = @$totalReceipts['total'] + $invoice->doc_subtotal;
                    $totalReceipts['vat']   = @$totalReceipts['vat'] + $invoice->doc_vat;
                    $totalReceipts['count'] = @$totalReceipts['count'] + 1;
                    $customerTotal += $invoice->doc_subtotal;
                } elseif ($invoice->doc_type == 'nodoc') {
                    $totalNoDoc['total'] = @$totalNoDoc['total'] + $invoice->doc_subtotal;
                    $totalNoDoc['vat']   = @$totalNoDoc['vat'] + $invoice->doc_vat;
                    $totalNoDoc['count'] = @$totalNoDoc['count'] + 1;
                    $customerTotal += $invoice->doc_subtotal;
                } else {
                    $totalInvoices['total'] = @$totalInvoices['total'] + $invoice->doc_subtotal;
                    $totalInvoices['vat']   = @$totalInvoices['vat'] + $invoice->doc_vat;
                    $totalInvoices['count'] = @$totalInvoices['count'] + 1;
                    $customerTotal += $invoice->doc_subtotal;
                }

                if ($docDetails) {
                    $docs[$invoice->doc_type] = [
                        'total' => @$docs[$invoice->type_id]['total'] + $invoice->doc_total,
                        'vat' => @$docs[$invoice->type_id]['vat'] + $invoice->doc_vat,
                        'count' => @$docs[$invoice->type_id]['count'] + 1
                    ];
                }
            }

            $groupedData[$customerId] = [
                'code'      => $customerCode,
                'name'      => $customerName,
                'total'     => $customerTotal,
                'docs'      => $docs,
                'invoices'  => $totalInvoices,
                'receipts'  => $totalReceipts,
                'nodoc'     => $totalNoDoc
            ];
        }


        aasort($groupedData, 'total', SORT_DESC);

        return $groupedData;
    }

    /**
     * Get shipments billed
     *
     * @param $request
     * @param $period
     * @param $grouped
     * @return array
     */
    public function getShipmentsBilled($request, $period, $groupByVehicle = false, $groupByOperator = false)
    {

        $shipments = Shipment::filterAgencies()
            ->whereBetween('billing_date', $period)
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->where('is_collection', 0);

        if ($groupByVehicle) {
            $shipments = $shipments->where(function ($q) {
                $q->whereNotNull('vehicle')
                    ->where('vehicle', '<>', '');
            })
                ->groupBy('vehicle');
        }

        if ($groupByOperator) {
            $shipments = $shipments->where(function ($q) {
                $q->whereNotNull('operator_id');
            })
                ->groupBy('operator_id');
        }

        $shipments = $shipments->get([
            'vehicle',
            'operator_id',
            DB::raw('count(id) as count'),
            DB::raw('(sum(total_price) + sum(total_expenses) + sum(total_price_for_recipient)) as subtotal'),
        ]);

        if ($groupByVehicle) {
            $shipments = $shipments->groupBy('vehicle')->toArray();
        }

        if ($groupByOperator) {
            $shipments = $shipments->groupBy('operator_id')->toArray();
        }

        return $shipments;
    }

    /**
     * @param Request $request
     * @param $period
     * @param bool $groupByVehicle
     * @param bool $details Se false, mostra só o total de despesas da viatura. se true, descrimina as despesas por tipo
     * @return array
     */
    public function getFleetCosts(Request $request, $period, $groupByVehicle = true, $details = true)
    {


        $costs = Cost::with(['vehicle' => function ($q) {
            $q->select(['id', 'name', 'license_plate']);
        }])
            ->with(['type' => function ($q) {
                $q->select(['id', 'name']);
            }])
            ->filterSource()
            ->where('source_type', 'Expense') //não considera os registos do tipo manutenção, fuel que são inseridos diretamente ao registar combusivoel/manutencoes
            ->whereNull('assigned_invoice_id') //ignora todos os registos que estejam associados a faturas de compra
            ->whereBetween('date', $period)
            //->whereNotNull('type_id')
            ->get([
                'vehicle_id',
                'type_id',
                'total'
            ]);

        if ($groupByVehicle) {

            $vehicleCosts = $costs->groupBy('vehicle_id');

            $groupedCosts = [];
            foreach ($vehicleCosts as $vehicle => $costs) {

                if ($details) {
                    $arr = [];

                    foreach ($costs as $cost) {

                        $arr[$cost->type_id] = [
                            'vehicle_id' => @$cost->vehicle_id,
                            'vehicle' => @$cost->vehicle->license_plate,
                            'name'    => @$cost->vehicle->name,
                            'type'    => @$cost->type->name,
                            'subtotal' => @$arr[$cost->type_id]['subtotal'] + $cost->total,
                            'count'   => @$arr[$cost->type_id]['count'] + 1
                        ];
                    }

                    aasort($arr, 'subtotal', SORT_DESC);
                    $groupedCosts[@$cost->vehicle->license_plate] = $arr;
                } else {
                    $groupedCosts[@$costs->first()->vehicle->license_plate] = [
                        'vehicle' => @$costs->first()->vehicle->license_plate,
                        'name'    => @$costs->first()->vehicle->name,
                        'type'    => @$costs->first()->type->name,
                        'subtotal' => $costs->sum('total'),
                        'count'   => @$costs->count()
                    ];
                }
            }

            return $groupedCosts;
        } else {
            $vehicleCosts = $costs->groupBy('type_id');

            $groupedCosts = [];
            foreach ($vehicleCosts as $typeId => $costs) {

                $groupedCosts[$typeId] = [
                    'name'      => @$costs->first()->type->name,
                    'type'      => @$costs->first()->type->name,
                    'subtotal'  => $costs->sum('total'),
                    'count'     => @$costs->count()
                ];
            }

            return $groupedCosts;
        }
    }

    /**
     * Return users expenses
     * @param Request $request
     * @param $period
     * @param bool $groupByUser
     * @param bool $details
     * @return array
     */
    public function getUsersCosts(Request $request, $period, $groupByUser = true, $details = true)
    {

        $costs = UserExpense::with(['user' => function ($q) {
            $q->select(['id', 'fullname', 'name']);
        }])
            ->with(['type' => function ($q) {
                $q->select(['id', 'name']);
            }])
            //->whereNull('assigned_invoice_id') //ignora todos os registos que estejam associados a faturas de compra
            ->where('is_fixed', 0)
            ->filterSource()
            ->whereBetween('date', $period)
            ->whereNotNull('type_id')
            ->get([
                'user_id',
                'type_id',
                'total'
            ]);

        $userCosts = $costs->groupBy('user_id');

        if ($groupByUser) {
            $groupedCosts = [];
            foreach ($userCosts as $userId => $costs) {
                if ($details) {
                    $subtotal = $count = 0;
                    foreach ($costs as $cost) {
                        $subtotal += $cost->total;
                        $count++;
                    }

                    $groupedCosts[$userId][$cost->type_id] = [
                        'name'     => $costs[0]->first()->user->fullname ?: ($costs[0]->first()->user->name ?? ''),
                        'type'     => $costs[0]->type->name,
                        'subtotal' => $subtotal,
                        'count'    => $count
                    ];
                } else {
                    $name =  @$costs->first()->user->fullname ? @$costs->first()->user->fullname : @$costs->first()->user->name;
                    $groupedCosts[@$costs->first()->user->id] = [
                        'name'     => $name,
                        'type'     => @$costs->first()->type->name,
                        'subtotal' => @$costs->sum('total'),
                        'count'    => @$costs->count()
                    ];
                }
            }

            return $groupedCosts;
        } else {

            $userCosts = $userCosts->groupBy('type_id');
            $groupedCosts = [];
            foreach ($userCosts as $typeId => $costs) {
                $groupedCosts[$typeId] = [
                    'name'      => @$costs->first()->type->name,
                    'type'      => @$costs->first()->type->name,
                    'subtotal'  => @$costs->sum('total'),
                    'count'     => @$costs->count()
                ];
            }

            return $groupedCosts;
        }
    }

    /**
     *
     *
     */
    public function getGlobalCosts(Request $request, $period, $groupByType = true, $ignoreAsigned = false)
    {

        $costs = PurchaseInvoice::with('type')
            ->filterSource()
            ->where(function ($q) {
                $q->where('is_scheduled', 0);
                $q->orWhereNull('is_scheduled');
            })
            ->whereBetween('doc_date', $period)
            ->where('is_deleted', 0)
            ->where('is_draft', 0)
            ->where('ignore_stats', 0)
            ->where('doc_type', '<>', ['credit-note']);

        if ($ignoreAsigned) { //custos globais só os que não estão imputados
            $costs = $costs->whereNull('assigned_targets');
        }

        $costs = $costs->get([
            'id',
            'assigned_targets',
            'type_id',
            'subtotal',
            'vat_total',
            'total'
        ]);

        if ($groupByType) {

            $costs = $costs->groupBy('type_id');

            $groupedCosts = [];
            foreach ($costs as $typeId => $costs) {

                $groupedCosts[$typeId] = [
                    'name'     => @$costs->first()->type->name,
                    'type'     => @$costs->first()->type->name,
                    'type_id'  => @$costs->first()->type->id,
                    'subtotal' => @$costs->sum('subtotal'),
                    'vat'      => @$costs->sum('vat_total'),
                    'total'    => @$costs->sum('total'),
                    'count'    => @$costs->count(),
                ];
            }

            //aasort($groupedCosts, 'total', SORT_DESC);

            return $groupedCosts;
        }

        return $costs->toArray();
    }

    /**
     * Return invoices total to a given period of time
     *
     * @param $request
     * @param $period
     * @return array
     */
    public function getInvoicesSalesTotals($request, $period)
    {

        $invoices = Invoice::whereBetween('doc_date', $period)
            ->where('is_deleted', 0)
            ->where('is_draft', 0)
            ->whereIn('doc_type', ['invoice', 'invoice-receipt', 'simplified-invoice', 'nodoc'])
            ->get([
                DB::raw('count(id) as count'),
                DB::raw('sum(doc_subtotal) as subtotal'),
                DB::raw('sum(doc_vat) as vat'),
                DB::raw('sum(doc_total) as total')
            ]);

        if ($invoices) {
            $data = $invoices->toArray();
            return $data[0];
        }

        return [
            "count"    => 0,
            "subtotal" => 0.00,
            "vat"      => 0.00,
            "total"    => 0.00,
        ];
    }

    /**
     * Return shipments total to a given period of time
     *
     * @param $request
     * @param $period
     * @return array
     */
    public function getShipmentsTotals($request, $period)
    {

        $shipments = Shipment::filterAgencies()
            ->whereBetween('billing_date', $period)
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->where('is_collection', 0)
            ->get([
                DB::raw('count(id) as count'),
                DB::raw('(sum(total_price) + sum(total_expenses) + sum(total_price_for_recipient)) as subtotal'),
            ]);

        if ($shipments) {
            $data = $shipments->toArray();
            return $data[0];
        }

        return [
            "count"    => 0,
            "subtotal" => 0.00,
        ];
    }

    /**
     * Return invoices total to a given period of time
     *
     * @param $request
     * @param $period
     * @return array
     */
    public function getInvoicesPurchasesTotals($request, $period)
    {

        $invoices = PurchaseInvoice::filterSource()
            ->where(function ($q) {
                $q->where('is_scheduled', 0);
                $q->orWhereNull('is_scheduled');
            })
            ->whereBetween('doc_date', $period)
            ->where('is_deleted', 0)
            ->where('is_draft', 0)
            ->where('ignore_stats', 0)
            ->where('doc_type', '<>', ['credit-note'])
            ->get([
                DB::raw('count(id) as count'),
                DB::raw('sum(subtotal) as subtotal'),
                DB::raw('sum(vat_total) as vat'),
                DB::raw('sum(total) as total')
            ]);

        if ($invoices) {
            $data = $invoices->toArray();
            return $data[0];
        }

        return [
            "count"    => 0,
            "subtotal" => 0.00,
            "vat"      => 0.00,
            "total"    => 0.00,
        ];
    }
}
