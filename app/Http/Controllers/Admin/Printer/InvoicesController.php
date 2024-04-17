<?php

namespace App\Http\Controllers\Admin\Printer;

use App\Models\Invoice;
use App\Models\PurchaseInvoice;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Mpdf\Mpdf;
use Setting, Response, Auth, DB;

class InvoicesController extends \App\Http\Controllers\Admin\Controller
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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',invoices']);
    }

    /**
     * Print list of current account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function summary(Request $request, $customerId = null)
    {

        //muda de mapa
        if ($request->has('group_by') && $request->has('settle')) {
            if ($request->group_by == 'customer' && !$request->settle) {
                return $this->printBalanceMap($request, 'unpaid');
            }
        }

        $ids = $request->get('id');

        if (!empty($ids)) {
            $invoices = Invoice::with('user')
                ->filterSource()
                ->whereIn('id', $ids)
                ->where(function ($q) {
                    $q->where('is_hidden', 0);
                    $q->orWhereNull('is_hidden');
                })
                ->orderBy('doc_date', 'asc')
                ->orderByRaw('CAST(`doc_id` AS SIGNED) asc')
                ->get();
        } else {
            $data = Invoice::filterSource()
                ->with('customer', 'user')
                ->whereNull('deleted_at')
                ->where('is_scheduled', 0)
                ->where(function ($q) {
                    $q->where('is_hidden', 0);
                    $q->orWhereNull('is_hidden');
                });

            //filter date min
            $dtMin = $request->get('date_min');
            if ($request->has('date_min')) {
                $dtMax = $dtMin;
                if ($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                }
                $data = $data->whereBetween('doc_date', [$dtMin, $dtMax]);
            }

            $expired = $request->get('expired');
            if ($request->has('expired')) {
                if ($expired) {
                    $data = $data->where('due_date', '<', date('Y-m-d'));
                } else {
                    $data = $data->where('due_date', '>=', date('Y-m-d'));
                }
            }

            //filter agencies
            if ($request->has('agency') || $request->has('route')) {
                $data = $data->whereHas('customer', function ($q) use ($request) {
                    if ($request->has('agency')) {
                        $q->where('agency_id', $request->get('agency'));
                    }
                    if ($request->has('route')) {
                        $q->where('route_id', $request->get('route'));
                    }
                });
            }

            //filter serie
            $value = $request->serie;
            if ($request->has('serie')) {
                $value = explode(',', $value);
                $data = $data->whereIn('doc_series_id', $value);
            }

            //filter year
            $value = $request->year;
            if ($request->has('year')) {
                $data = $data->whereRaw('YEAR(doc_date) = ' . $value);
            }

            //filter month
            $value = $request->month;
            if ($request->has('month')) {
                $data = $data->whereRaw('MONTH(doc_date) = ' . $value);
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

            //incluir valores sem doc.
            $value = $request->get('nodoc');
            if ($request->get('tab') != 'nodoc' && (!$request->has('nodoc') || ($request->has('nodoc') && $value == 0))) {
                $data = $data->where('doc_type', '<>', 'nodoc');
            }

            //filter customer
            $value = $request->customer;
            if ($request->has('customer')) {
                if ($value == '1') { //CFINAL
                    $data = $data->where(function ($q) {
                        $q->where('customer_id', 1);
                        $q->orWhereNull('vat');
                        $q->orWhere('vat', '');
                    });
                } else {
                    $data = $data->where('customer_id', $value);
                }
            }

            //filter operator
            $value = $request->operator;
            if ($request->has('operator')) {
                $value = explode(',', $value);
                $data = $data->whereIn('created_by', $value);
            }

            //filter payment method
            $value = $request->payment_method;
            if ($request->has('payment_method')) {
                $value = explode(',', $value);
                $data = $data->whereIn('payment_method', $value);
            }

            //filter draft
            $value = $request->draft;
            if ($request->has('draft')) {
                $data = $data->where('is_draft', $value);
            }

            //filter settle
            $value = $request->settle;
            if ($request->has('settle')) {
                $data = $data->where('is_settle', $value);
            }

            //filter target
            $value = $request->target;
            if ($request->has('target')) {
                $value = explode(',', $value);
                $data = $data->whereIn('target', $value);
            }

            //filter deleted
            $value = $request->deleted;
            if ($request->has('deleted') && empty($value)) {
                $data = $data->where('is_deleted', $value);
            }

            $invoices = $data->orderBy('doc_date', 'asc')
                ->orderByRaw('CAST(`doc_id` AS SIGNED) asc')
                ->get();
        }

        ini_set("pcre.backtrack_limit", "50000000");
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
        $mpdf->packTableData = true;
        $mpdf->simpleTables = true;


        $docTitle = 'Resumo de Documentos';
        $docTypes = $invoices->pluck('doc_type')->toArray();
        if(count($docTypes) == 1) {
            $docTitle = 'Resumo de ' . trans('admin/billing.types-plural.'.$docTypes[0]);
        }

        $data = [
            'invoices'        => $invoices,
            'documentTitle'   => $docTitle,
            'documentSubtitle' => '',
            'view'            => 'admin.printer.invoices.summary'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf_h', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Documentos.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Print list of current account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function operatorAccountability(Request $request)
    {

        $input = $request->all();

        $startDate = $input['start_date'];
        $endDate   = $input['end_date'];

        $paymentMethods = PaymentMethod::filterSource()
            ->pluck('name', 'code')
            ->toArray();

        $invoices = Invoice::with('user')
            ->filterSource()
            ->whereNull('deleted_at')
            ->where('is_deleted', 0)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($request->has('operator')) {
            $invoices->where('created_by', @$input['operator']);
        }

        $invoices = $invoices->get([
            '*',
            DB::raw('DATE(created_at) as creation_date')
        ]);

        $invoices = $invoices->groupBy('user.name');

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
            'startDate'       => $startDate,
            'endDate'         => $endDate,
            'paymentMethods'  => $paymentMethods,
            'documentTitle'   => 'Prestação de contas por colaborador',
            'documentSubtitle' => $startDate == $endDate ? $startDate : 'Período de ' . $startDate . ' a ' . $endDate,
            'view'            => 'admin.printer.invoices.accountability'
        ];

        $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Prestação Contas [' . $startDate . ' - ' . $endDate . '].pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Print list of customers ballance
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function customersBalance(Request $request)
    {

        $mode   = $request->get('mode');
        $nodocs = $request->get('nodoc', false);

        if ($mode == 'monthly') {
            return $this->monthlyBalance($request);
        }

        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        if (empty($startDate)) {
            $endDate   = Date::today()->format('Y-m-d');
            $startDate = Date::today()->subDays(30)->format('Y-m-d');
        }

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

        $data = [];
        $data['gains'] = Invoice::getInvoicesSummaryByCustomer($startDate, $endDate, false, $nodocs, $request);

        $data = [
            'documentTitle'   => 'Resumo de Faturação e Recebimentos',
            'documentSubtitle' => 'Relatório por cliente entre ' . $startDate . ' e ' . $endDate,
            'view'            => 'admin.printer.invoices.map_customer_sales',
            'data'            => $data,
            'nodoc'           => $nodocs
        ];

        $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Faturação e Recebimentos.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Print list of montlhly balance
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printBalanceMap(Request $request, $mapType)
    {
        if ($mapType == 'yearly') {
            return $this->printMapYearly($request);
        } elseif ($mapType == 'vat') {
            return $this->printMapVatBalance($request);
        } elseif ($mapType == 'unpaid') {
            return $this->printSummaryUnpaidByCustomer($request);
        }

        return false;
    }

    /**
     * Print list of montlhly balance
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printMapYearly(Request $request)
    {

        $year  = $request->get('year', date('Y'));
        $group = $request->get('group');
        $nodoc = $request->get('nodoc');

        ini_set("pcre.backtrack_limit", "50000000");
        ini_set("memory_limit", "-1");

        $configs = [
            'format'        => 'A4',
            'margin_top'    => 30,
            'margin_bottom' => 10,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ];

        if ($group == 'customer') {
            $configs = [
                'format'        => 'A4-L',
                'margin_top'    => 20,
                'margin_bottom' => 10,
                'margin_left'   => 10,
                'margin_right'  => 10,
            ];
        }

        $mpdf = new Mpdf($configs);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;
        $mpdf->packTableData = true;
        $mpdf->simpleTables = true;

        $invoices = Invoice::getInvoicesSummaryByMonth($year, $group, $nodoc);

        if ($group == 'customer') {
            $data = [
                'documentTitle'   => 'Mapa vendas anual por cliente',
                'documentSubtitle' => 'Ano ' . $year,
                'view'            => 'admin.printer.invoices.map_yearly_customer',
                'data'            => $invoices,
                'noDocs'          => $nodoc
            ];
        } else {
            $data = [
                'documentTitle'   => 'Mapa vendas anual',
                'documentSubtitle' => 'Ano ' . $year,
                'view'            => 'admin.printer.invoices.map_yearly',
                'data'            => $invoices,
                'noDocs'          => $nodoc
            ];
        }

        $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Faturação e Recebimentos.pdf', 'I'); //output to screen

        exit;
    }


    /**
     * Print list of current account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printSummaryUnpaidByCustomer(Request $request)
    {

        $customer  = $request->get('customer');

        //filter date min
        $period = null;
        $dtMin  = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $period = [$dtMin, $dtMax];
        }

        $invoices = Invoice::with('customer')
            ->filterSource()
            ->whereNotIn('doc_type', ['regularization', 'receipt', 'internal-doc', 'proforma-invoice'])
            ->where('is_draft', 0)
            ->where('is_settle', 0);

        if (!empty($customer)) {
            $invoices = $invoices->where('customer_id', $customer);
        }

        if (!empty($period)) {
            $invoices = $invoices->whereBetween('doc_date', $period);
        }

        $expired = $request->get('expired');
        if ($request->has('expired')) {
            if ($expired) {
                $invoices = $invoices->where('due_date', '<', date('Y-m-d'));
            } else {
                $invoices = $invoices->where('due_date', '>=', date('Y-m-d'));
            }
        }

        $value = $request->get('nodoc');  //ignora sem doc.
        if (!$request->has('nodoc') || $request->has('nodoc') && $value == 0) {
            $invoices = $invoices->where('doc_type', '<>', 'nodoc');
        }

        $invoices = $invoices->orderBy('billing_name', 'asc')->get();

        $invoices = $invoices->groupBy('customer_id');


        ini_set("pcre.backtrack_limit", "50000000");
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
            'period'          => $period,
            'documentTitle'   => 'Mapa Pendentes por Cliente',
            'documentSubtitle' => 'Mapa de saldos em ' . date('Y-m-d'),
            'view'            => 'admin.printer.invoices.map_unpaid_by_customer',
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Despesas.pdf', 'I'); //output to screen

        exit;
    }


    /**
     * Print list of montlhly balance
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printMapVatBalance(Request $request)
    {

        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');


        ini_set("pcre.backtrack_limit", "50000000");
        ini_set("memory_limit", "-1");

        $configs = [
            'format'        => 'A4',
            'margin_top'    => 30,
            'margin_bottom' => 10,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ];

        $mpdf = new Mpdf($configs);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;
        $mpdf->packTableData = true;
        $mpdf->simpleTables = true;

        $vatBalance = Invoice::getVatBalance($startDate, $endDate);
        $vatPurchaseBalance = PurchaseInvoice::getVatBalance($startDate, $endDate);

        $data = [
            'documentTitle'   => 'Mapa resumo taxas IVA',
            'documentSubtitle' => 'Período de ' . $startDate . ' a ' . $endDate,
            'view'            => 'admin.printer.invoices.map_vat_balance',
            'data'            => $vatBalance,
            'purchases'       => $vatPurchaseBalance
        ];


        $mpdf->WriteHTML(view('admin.printer.shipments.layouts.charging_instructions', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Faturação e Recebimentos.pdf', 'I'); //output to screen

        exit;
    }
}
