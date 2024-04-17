<?php

namespace App\Http\Controllers\DefaultSite;

use App\Models\Customer;
use App\Models\CustomerBalance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use DB, View, Setting, Carbon\Carbon, Jenssegers\Date\Date;


class BalanceController extends \App\Http\Controllers\Controller
{
    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.default';

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'billing';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Customer billing index controller
     *
     * @return type
     */
    public function index(Request $request, $hash) {

        $customer = $this->getCustomer($hash);

        $totalUnpaid  = $customer->balance_total;
        $totalExpired = $customer->balance_expired_count;

        $lastBalanceDate = new \Carbon\Carbon($customer->balance_last_update);
        $balanceDiff     = \Carbon\Carbon::now()->diffInHours($lastBalanceDate);

        $data = compact(
            'customer',
            'totalUnpaid',
            'totalExpired',
            'customer',
            'balanceDiff',
            'lastBalanceDate',
            'hash'
        );

        return $this->setContent('default.balance', $data);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request, $hash) {

        $customer = $this->getCustomer($hash);

        $data = CustomerBalance::whereHas('customer', function($q){
                $q->filterAgencies();
            })
            ->where('customer_id', $customer->id)
            ->select();

        //filter payment date
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter sense
        $value = $request->sense;
        if($request->has('sense')) {
            if($value == 'hidden') {
                $data = $data->where('is_hidden', 1);
            } else {
                $data = $data->where('sense', $value)
                    ->where('is_hidden', 0);
            }
        }

        //filter is paid
        $value = $request->paid;
        if($request->has('paid')) {
            if($value == '2') {
                $data = $data->withTrashed()
                    ->whereNotNull('deleted_at');
            } else {
                $data = $data->where('is_paid', $value);
            }
        }

        $today = Carbon::today();

        return Datatables::of($data)
            ->edit_column('date', function($row) {
                $date = new Date($row->date);
                return $date->format('Y-m-d');
            })
            ->add_column('debit', function($row) {
                return view('admin.billing.balance.datatables.documents.debit', compact('row'))->render();
            })
            ->add_column('credit', function($row) {
                return view('admin.billing.balance.datatables.documents.credit', compact('row'))->render();
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
            ->edit_column('is_paid', function($row) {
                return view('admin.billing.balance.datatables.documents.paid', compact('row'))->render();
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
     * Sync customer balance and payment status
     * @param $customerId
     */
    public function syncBalanceAll($hash){
        $customer = $this->getCustomer($hash);
        return CustomerBalance::syncBalanceAll($customer->id);
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

        $customerBalance = CustomerBalance::where('customer_id', $customer->id)
            ->findOrNew($id);

        $document = new App\Models\Invoice();
        $document = $document->getDocumentPdf($customerBalance->doc_id, $customerBalance->doc_type, $customerBalance->doc_serie_id, false);

        $data = base64_decode($document);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="Fatura '.$customerBalance->doc_id.'"');
        echo $data;
    }

    /**
     * Return customer
     */
    public function getCustomer($hash) {

        $hash       = base64_decode($hash);
        $year       = substr($hash, 8, 4);
        $month      = substr($hash, 12, 2);
        $day        = substr($hash, 14, 2);
        $hours      = substr($hash, 16, 2);
        $minutes    = substr($hash, 18, 2);
        $secounds   = substr($hash, 20, 2);

        $createdAt  = $year.'-'.$month.'-'.$day. ' ' . $hours.':'.$minutes.':'.$secounds;
        $customerId = (int) substr($hash, 0, 8);

        $customer = Customer::filterSource()
            ->whereId($customerId)
            ->where('created_at', $createdAt)
            ->first();

        return $customer;
    }
}