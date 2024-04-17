<?php

namespace App\Http\Controllers\Admin\Printer;

use App\Models\CustomerBalance;
use App\Models\Invoice;
use App\Models\Provider;
use App\Models\PurchaseInvoice;
use Mpdf\Mpdf;
use Setting, File, Response, Auth, Mail;
use Illuminate\Http\Request;

class BalanceController extends \App\Http\Controllers\Admin\Controller {

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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customers']);
    }

    /**
     * Print list of current account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function balanceCustomer(Request $request, $customerId = null) {

        if($request->get('source') == 'providers') {
            $this->summaryProvider($request);
        } else {
            if (is_null($customerId)) {
                Invoice::printCustomersBalanceSummary($request); //resumo geral de conta corrente de todos clientes
            } else {
                Invoice::printCustomerBalance($customerId, $request); //conta corrente de cliente
            }
        }
    }

    /**
     * Print provider balance account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function balanceProvider(Request $request, $providerId) {

        $ids      = $request->get('id');
        $provider = Provider::filterSource()->find($providerId);

        if(!empty($ids)) {
            $invoices = PurchaseInvoice::with('user', 'type')
                ->filterSource()
                ->whereNull('is_scheduled')
                ->whereIn('id', $ids)
                ->orderBy('doc_date', 'asc')
                ->get();
        } else {
            $data = PurchaseInvoice::with('user', 'type')
                ->where('provider_id', $providerId)
                ->filterSource()
                ->orderBy('doc_date', 'asc')
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

            //filter expired
            $value = $request->expired;
            if($request->has('expired')) {
                if($value) {
                    $data = $data->where('due_date', '<', date('Y-m-d'));
                } else {
                    $data = $data->where('due_date', '>=', date('Y-m-d'));
                }
            }

            //filter sense
            $value = $request->sense;
            if($request->has('sense')) {
                $data = $data->where('sense', $value);
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
            $value = $request->type;
            if($request->has('type')) {
                $value = explode(',', $value);
                $data = $data->whereIn('type_id', $value);
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

            $invoices = $data->get();
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

        $data = [
            'invoices'        => $invoices,
            'provider'        => $provider,
            'documentTitle'   => 'Conta Corrente Fornecedor',
            'documentSubtitle'=> '',//$provider->code .' - '. $provider->company,
            'view'            => 'admin.printer.invoices.purchase.balance'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Conta Corrente Fornecedor.pdf', 'I'); //output to screen

        exit;
    }
}
