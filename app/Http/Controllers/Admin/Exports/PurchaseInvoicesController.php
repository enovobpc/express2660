<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Models\Bank;
use App\Models\PaymentMethod;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceType;
use App\Models\PurchasePaymentNote;
use Html, Auth, Date, Setting, Excel;
use Illuminate\Http\Request;

class PurchaseInvoicesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'purchase-invoices';

    /**
     * Store last row of each iteration
     *
     * @var type
     */
    protected $lastRow = null;

    /**
     * Store last row of each iteration
     *
     * @var type
     */
    protected $maxRows = 5000;

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
     * Export purchase invoices
     *
     * @param type $shipmentId
     * @return type
     */
    public function export (Request $request){

        $ids = $request->get('id');

        if(!empty($ids)) {
            $data = PurchaseInvoice::with('user', 'type')
                ->filterSource()
                ->whereIn('id', $ids)
                ->whereNull('is_scheduled')
                ->orderBy('doc_date', 'desc')
                ->get();
        } else {
            $data = PurchaseInvoice::with('user', 'type')
                ->filterSource()
                ->whereNull('is_scheduled')
                ->where('is_deleted', 0);

            //filter date min
            $dtMin = $request->get('date_min');
            if($request->has('date_min')) {
                $dtMax = $dtMin;
                if($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                }

                $dateUnity = 'doc_date';
                if($request->has('date_unity')) {
                    if($request->date_unity == 'due') {
                        $dateUnity = 'due_date';
                    } elseif($request->date_unity == 'pay') {
                        $dateUnity = 'payment_date';
                    }
                }

                $data = $data->whereBetween($dateUnity, [$dtMin, $dtMax]);
            }

            //filter paid
            $value = $request->paid;
            if($request->has('paid')) {
                if($value) {
                    $data = $data->where('is_settle', 1);
                } else {
                    $data = $data->where('is_settle', 0);
                }
            }

            //filter ignore invoice
            $value = $request->ignore_stats;
            if($request->has('ignore_stats')) {
                $data = $data->where('ignore_stats', $value);
            }

            //filter target
            $value = $request->target;
            if($request->has('target')) {
                $data = $data->where('target', $value);
            }

            //filter target id
            $value = $request->target_id;
            if($request->has('target_id')) {
                $data = $data->where('target_id', $value);
            }

            //filter type
            $value = explode(',', $request->type);
            if($request->has('type')) {
                $data = $data->whereIn('type_id', $value);
            }

            //filter doc id
            $value = $request->doc_id;
            if($request->has('doc_id')) {
                $data = $data->where('doc_id', $value);
            }

            //filter doc type
            if($request->has('doc_type')) {
                $value = explode(',', $request->doc_type);
                $data = $data->whereIn('doc_type', $value);
            }

            //filter provider
            $value = $request->provider;
            if($request->has('provider')) {
                $data = $data->where('provider_id', $value);
            }

            //filter payment method
            $value = $request->payment_method;
            if($request->has('payment_method')) {
                $data = $data->whereIn('payment_method', $value);
            }

            //filter deleted
            $value = $request->deleted;

            if($request->has('deleted') && empty($value)) {
                $data = $data->where('is_deleted', $value);
            }

            $data = $data->get();
        }

        $header = [
            'Tipo Despesa',
            'Data Doc',
            'Data Vencimento',
            'Tipo Doc',
            'Referência',
            'NIF',
            'Fornecedor',
            'Morada',
            'Cod. Postal',
            'Localidade',
            'País',
            'Subtotal',
            'IVA',
            'Total',
            'Por Pagar',
            'Estado',
            'Condição Pagamento',
            'Criado em',
            'Criado por',
            'Observações'
        ];


        Excel::create('Listagem de Despesas', function($file) use($data, $header){

            $file->sheet('Listagem', function($sheet) use($data, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function($row){
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                foreach($data as $invoice) {

                    $rowData = [
                        @$invoice->type->name,
                        $invoice->doc_date,
                        $invoice->due_date,
                        trans('admin/billing.types_code.' . $invoice->doc_type),
                        $invoice->reference,
                        $invoice->vat,
                        $invoice->billing_name,
                        $invoice->billing_address,
                        $invoice->billing_zip_code,
                        $invoice->billing_city,
                        $invoice->billing_country,
                        $invoice->subtotal,
                        $invoice->vat_total,
                        $invoice->total,
                        $invoice->total_unpaid,
                        $invoice->is_deleted ? 'Apagado' : ($invoice->is_settle ? 'Pago' : 'Não Pago'),
                        $invoice->payment_condition ? @$invoice->paymentCondition->name : '',
                        $invoice->created_at,
                        $invoice->user->name,
                        $invoice->obs
                    ];

                    $sheet->appendRow($rowData);
                }
            });

        })->export('xls');
    }
    
    /**
     * Export purchase payment notes
     *
     * @param type $shipmentId
     * @return type
     */
    public function exportPaymentNotes (Request $request){

        $ids = $request->get('id');

        if(!empty($ids)) {
            $data = PurchasePaymentNote::with('user')
                ->filterSource()
                ->whereIn('id', $ids)
                ->orderBy('doc_date', 'desc')
                ->get();
        } else {
            $data = PurchasePaymentNote::with('user', 'payment_methods')
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

                //filter provider
                $value = $request->provider;
                if($request->has('provider')) {
                    $data = $data->where('provider_id', $value);
                }
                $data = $data->get();
            }

        $banks          = Bank::pluck('name', 'bank_code')->toArray();
        $paymentMethods = PaymentMethod::pluck('name', 'code')->toArray();

        $header = [
            'Data Doc',
            'Referência',
            'NIF',
            'Fornecedor',
            'Morada',
            'Cod. Postal',
            'Localidade',
            'País',
            'Total',
            'Data Pagamento',
            'Meio Pagamento',
            'Banco',
            'Montante pago',
            'Criado em',
            'Criado por',
            'Observações'
        ];

        Excel::create('Listagem de Notas de Pagamento', function($file) use($data, $header, $paymentMethods, $banks){

            $file->sheet('Listagem', function($sheet) use($data, $header, $paymentMethods, $banks) {

                $sheet->row(1, $header);
                $sheet->row(1, function($row){
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                foreach($data as $invoice) {

                    $payments = $invoice->payment_methods; //em alternativa podia ser feito um ciclo foreach que inseria 1 linha por cada pagamento
                    
                    if($payments->isEmpty()) {
                        $rowData = [
                            $invoice->doc_date,
                            $invoice->reference,
                            $invoice->vat,
                            $invoice->billing_name,
                            $invoice->billing_address,
                            $invoice->billing_zip_code,
                            $invoice->billing_city,
                            $invoice->billing_country,
                            $invoice->total,
                            '', 
                            '',
                            '',
                            $invoice->total,
                            $invoice->created_at,
                            $invoice->user != null ? $invoice->user->name : 'Sistema',
                            $invoice->obs
                        ];

                        $sheet->appendRow($rowData);

                    } else {

                        $i = 0;
                        foreach($payments as $payment) {
                            $rowData = [
                                $invoice->doc_date,
                                $invoice->reference,
                                $invoice->vat,
                                $invoice->billing_name,
                                $invoice->billing_address,
                                $invoice->billing_zip_code,
                                $invoice->billing_city,
                                $invoice->billing_country,
                                $i > 0 ? '' : $invoice->total, //caso tenha +1 pagamento, não coloca o total na 2ª linha para que ao somar o excel não de valores errados
                                @$payment->date, 
                                @$paymentMethods[@$payment->method],
                                @$payment->bank ? @$banks[@$payment->bank] : '',
                                @$payment->total, 
                                $invoice->created_at,
                                $invoice->user != null ? $invoice->user->name : 'Sistema',
                                $invoice->obs
                            ];
        
                            $i++;
                            $sheet->appendRow($rowData);
                        }
                    }

                    
                }
            });

        })->export('xls');
    }

    /**
     * Export anual purchase invoices totals by type
     * 
     * @param Request $request
     * @return void
     */
    public function exportGroupedByType(Request $request) {
        $purchaseInvoiceTypes = PurchaseInvoiceType::filterSource()
            ->get();

        $header = [
            'Tipo Despesa',
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro',
        ];

        $year = $request->get('year', date('Y'));
        Excel::create('Listagem de Despesas Agrupada Pelo Tipo ' . $year, function($file) use($year, $purchaseInvoiceTypes, $header){

            $file->sheet('Listagem', function($sheet) use($year, $purchaseInvoiceTypes, $header) {

                function mapMonthValues($data) {
                    $arr = [];
                    for ($i = 1; $i <= 12; $i++) {
                        $arr[$i] = isset($data[$i]) ? $data[$i] : '0.00';
                    }
            
                    return $arr;
                }

                $sheet->row(1, $header);
                $sheet->row(1, function($row){
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                foreach($purchaseInvoiceTypes as $type) {
                    $data = PurchaseInvoice::filterSource()
                        ->whereNull('is_scheduled')
                        ->where('is_deleted', 0)
                        ->where('type_id', $type->id)
                        ->whereRaw("YEAR(doc_date) = '$year'")
                        ->selectRaw('SUM(total) AS "total", MONTH(doc_date) AS "month"')
                        ->groupBy('month')
                        ->get()
                        ->pluck('total', 'month')
                        ->toArray();

                    $rowData = [$type->name];
                    $rowData = array_merge($rowData, mapMonthValues($data));

                    $sheet->appendRow($rowData);
                }
            });

        })->export('xls');
    }
}
