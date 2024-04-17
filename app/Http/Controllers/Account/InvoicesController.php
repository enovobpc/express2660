<?php

namespace App\Http\Controllers\Account;

use App\Models\CustomerBalance;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB, View, Excel, Response, Date, App, Setting, Auth;
use Yajra\Datatables\Datatables;

class InvoicesController extends \App\Http\Controllers\Controller
{
    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.account';

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'balance';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        if(!hasModule('invoices') || !Setting::get('show_customers_ballance')) {
            return App::abort(404);
        }
    }

    /**
     * Customer billing index controller
     *
     * @return type
     */
    public function index(Request $request) {

        $customer = Auth::guard('customer')->user();

        $totalUnpaid  = $customer->balance_total;
        $totalExpired = $customer->balance_expired_count;

        $lastBalanceDate = new \Carbon\Carbon($customer->balance_last_update);
        $balanceDiff     = \Carbon\Carbon::now()->diffInHours($lastBalanceDate);
        
        return $this->setContent('account.balance.index', compact('totalUnpaid', 'totalExpired', 'customer', 'balanceDiff', 'lastBalanceDate'));
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request) {

        $customer = Auth::guard('customer')->user();

        $data = Invoice::where('customer_id', $customer->id)
            ->filterBalanceDocs()
            ->orderBy('doc_date', 'desc')
            ->orderBy('id', 'desc');
            
        //filter payment date
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('doc_date', [$dtMin, $dtMax]);
        }

        //filter sense
        $value = $request->sense;
        if($request->has('sense')) {
            if($value == 'credit') {
                $data = $data->where(function ($q) {
                    $q->where('doc_total_credit', '<=', '0.00');
                    $q->whereNull('doc_total_debit');
                });
            } else {
                $data = $data->where('doc_total_debit', '>=', '0.00');
            }
        }

        //filter is paid
        $value = $request->paid;
        if($request->has('paid')) {
            if($value == '2') {
                $data = $data->withTrashed()
                    ->whereNotNull('deleted_at');
            } else {
                $data = $data->where('is_settle', $value);
            }
        }
        

        $today = Carbon::today();

        return Datatables::of($data)
            ->edit_column('sort', function($row) {
                $date = new Date($row->doc_date);
                return $date->format('Y-m-d');
            })
            ->edit_column('doc_total', function($row) {
                return view('account.billing.datatables.invoices.doc_total', compact('row'))->render();
            })
            ->edit_column('doc_serie', function($row) {
                return view('account.billing.datatables.invoices.serie', compact('row'))->render();
            })
            ->edit_column('doc_type', function($row) {
                return view('admin.billing.balance.datatables.documents.type', compact('row'))->render();
            })
            ->edit_column('reference', function($row) {
                return view('account.billing.datatables.invoices.reference', compact('row'))->render();
            })
            ->edit_column('is_settle', function($row) {
                return view('account.billing.datatables.invoices.is_settle', compact('row'))->render();
            })
            ->edit_column('due_date', function($row) use($today) {
                return view('account.billing.datatables.invoices.due_date', compact('row', 'today'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('account.billing.datatables.invoices.actions', compact('row'))->render();
            })
            /*->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })*/
            ->make(true);
    }

    /**
     * Import invoices from invoice gateway
     * @param $customerId
     */
    public function syncBalanceAll(){
        $customer = Auth::guard('customer')->user();
        return Invoice::importFromGateway($customer->id);
    }

    /**
     * Get invoice
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getInvoice($id) {

        $customer = Auth::guard('customer')->user();

        $invoice = Invoice::where('customer_id', $customer->id)->find($id);

        if(!$invoice) {
            return redirect()->back()->with('error', 'Fatura não encontrada.');
        }

        return Invoice::downloadPdf(['id' => $id], 'pdf');
    }

    /**
     * Get invoice
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printSummary(Request $request) {
        $customer = Auth::guard('customer')->user();
        Invoice::printCustomerBalance($customer->id, $request);
    }
}