<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Models\Invoice;
use Html, Auth, Date, Setting, Excel;
use Illuminate\Http\Request;

class InvoicesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'invoices';

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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',invoices']);
    }

    /**
     * Export customers refunds
     *
     * @param type $shipmentId
     * @return type
     */
    public function export (Request $request){

        $ids = $request->get('id');

        if(!empty($ids)) {
            $data = Invoice::with('user', 'customer')
                ->filterSource()
                ->whereIn('id', $ids)
                ->orderBy('doc_date', 'desc')
                ->get();
        } else {
            $data = Invoice::with('user', 'customer')
                ->filterSource();

            //filter date min
            $dtMin = $request->get('date_min');
            if($request->has('date_min')) {
                $dtMax = $dtMin;
                if($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                }
                $data = $data->whereBetween('doc_date', [$dtMin, $dtMax]);
            }

            //filter agencies
            if($request->has('agency') || $request->has('route')) {
                $data = $data->whereHas('customer', function ($q) use($request) {
                    if($request->has('agency')) {
                        $q->where('agency_id', $request->get('agency'));
                    }
                    if($request->has('route')) {
                        $q->where('route_id', $request->get('route'));
                    }
                });
            }

            //filter year
            $value = $request->year;
            if($request->has('year')) {
                $data = $data->whereRaw('YEAR(doc_date) = ' . $value);
            }

            //filter month
            $value = $request->month;
            if($request->has('month')) {
                $data = $data->whereRaw('MONTH(doc_date) = ' . $value);
            }

            //filter doc id
            $value = $request->doc_id;
            if($request->has('doc_id')) {
                $data = $data->where('doc_id', $value);
            }

            //filter doc type
            $value = $request->doc_type;
            if($request->has('doc_type')) {
                $value = explode(',', $value);
                $data = $data->whereIn('doc_type', $value);
            }

            //filter customer
            $value = $request->customer;
            if($request->has('customer')) {
                $data = $data->where('customer_id', $value);
            }

            //filter operator
            $value = $request->operator;
            if($request->has('operator')) {
                $value = explode(',', $value);
                $data = $data->whereIn('created_by', $value);
            }

            //filter payment method
            $value = $request->payment_method;
            if($request->has('payment_method')) {
                if(!is_array($value)) {
                    $value = explode(',', $value);
                }
                $data = $data->whereIn('payment_method', $value);
            }

            //filter draft
            $value = $request->draft;
            if($request->has('draft')) {
                $data = $data->where('is_draft', $value);
            }

            //filter settle
            $value = $request->settle;
            if($request->has('settle')) {
                $data = $data->where('is_settle', $value);
            }

            //filter target
            $value = $request->target;
            if($request->has('target')) {
                $value = explode(',', $value);
                $data = $data->whereIn('target', $value);
            }

            //filter deleted
            $value = $request->deleted;
            if($request->has('deleted') && empty($value)) {
                $data = $data->where('is_deleted', $value);
            }

            $data = $data->get();
        }

        $header = [
            'Data Doc',
            'Data Vencimento',
            'Tipo Doc',
            'Série',
            'Nº Doc',
            'Referência',
            'NIF',
            'Cliente',
            'Morada',
            'Cod. Postal',
            'Localidade',
            'País',
            'Subtotal',
            'IVA',
            'Total',
            'Pendente',
            'Estado',
            'Modo Pagamento',
            'Condição Pagamento',
            'Criado em',
            'Criado por'
        ];


        Excel::create('Listagem de Faturas', function($file) use($data, $header){

            $file->sheet('Listagem', function($sheet) use($data, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function($row){
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                foreach($data as $invoice) {

                    $total    = $invoice->doc_total;
                    $subtotal = $invoice->doc_subtotal;
                    $vat      = $invoice->doc_vat;

                    if($invoice->is_settle || $invoice->doc_type == 'receipt') {
                        $pending  = 0;
                    } else {
                        $pending = $total;
                        if(!empty($invoice->doc_total_pending)) {
                            $pending  = $invoice->doc_total;
                        }
                    }

                    if($invoice->doc_type == 'credit-note' && $invoice->doc_total > 0.00) {
                        $total    = $total * -1;
                        $subtotal = $subtotal * -1;
                        $vat      = $vat * -1;
                        $pending  = $pending * -1;
                    }


                    $rowData = [
                        $invoice->doc_date,
                        $invoice->due_date,
                        trans('admin/billing.types_code.' . $invoice->doc_type),
                        $invoice->doc_series,
                        $invoice->is_draft ? 'RASCUNHO' : $invoice->doc_id,
                        $invoice->reference,
                        $invoice->vat,
                        $invoice->billing_name,
                        $invoice->billing_address,
                        $invoice->billing_zip_code,
                        $invoice->billing_city,
                        $invoice->billing_country,
                        forceDecimal($subtotal),
                        forceDecimal($vat),
                        forceDecimal($total),
                        forceDecimal($pending),
                        $invoice->is_deleted ? 'Apagado' : ($invoice->is_settle ? 'Pago' : 'Não Pago'),
                        $invoice->payment_method ? @$invoice->paymentMethod->name : '',
                        $invoice->payment_condition ? @$invoice->paymentCondition->name : '',
                        $invoice->created_at,
                        @$invoice->user->name
                    ];

                    $sheet->appendRow($rowData);
                }
            });

        })->export('xls');
    }
}
