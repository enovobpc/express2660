<?php

namespace App\Http\Controllers\Account;

use App\Models\Billing;
use App\Models\CustomerBalance;
use App\Models\CustomerBilling;
use App\Models\ProductSale;
use App\Models\ShippingStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Models\Shipment;
use DB, View, Setting;


class BillingController extends \App\Http\Controllers\Controller
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
    public function index(Request $request) {
        
        $customer = Auth::guard('customer')->user();

        //extracts
        $years = yearsArr(2016, date('Y'), true);

        if($customer->hide_old_shipments) {
            $years = yearsArr(@$customer->login_created_at->year, date('Y'), true);
        }
        
        if($customer->id == 91) {
            $years = yearsArr(2017, date('Y'), true);
        }

        $customer = Auth::guard('customer')->user();

        $totalUnpaid     = $customer->balance_total;
        $totalExpired    = $customer->balance_total_expired;
        $lastBalanceDate = new \Carbon\Carbon($customer->balance_last_update);
        $balanceDiff     = \Carbon\Carbon::now()->diffInHours($lastBalanceDate);

        $data = compact(
            'customer',
            'years',
            'totalUnpaid',
            'totalExpired',
            'balanceDiff',
            'lastBalanceDate'
        );

        return $this->setContent('account.billing.index', $data);
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {
        $customer = Auth::guard('customer')->user();

        $year   = $request->get('year');
        $month  = $request->get('month');
        $period = $request->get('period', '30d');

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        $productsBought = ProductSale::where(function ($q) use($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->groupBy(DB::raw('DATE_FORMAT(date, \'%Y-%c\')'))
            ->get([
                DB::raw('DATE_FORMAT(date, \'%Y-%c\') as yearmonth'),
                DB::raw('sum(subtotal) as subtotal')
            ]);

        $data = Shipment::leftJoin('customers_covenants', function($q) use($periodFirstDay, $periodLastDay) {
                $q->on('customers_covenants.customer_id', '=', 'shipments.customer_id');
                $q->whereRaw('customers_covenants.start_date <= shipments.billing_date');
                $q->whereRaw('customers_covenants.end_date >= shipments.billing_date');
                $q->whereNull('customers_covenants.deleted_at');
            })
           ->where('shipments.customer_id', $customer->id);
        
        if($year) {
            $data = $data->whereRaw('YEAR(billing_date) = '.$year);
        }
        
        if($month) {
            $data = $data->whereRaw('MONTH(billing_date) = '.$month);
        }
        
        if($customer->hide_old_shipments && @$customer->login_created_at->year) {
            $data = $data->whereRaw('YEAR(date) >= '. $customer->login_created_at->year);
        }

        $data = $data->where(function($q) {
                    $q->where('is_collection', 0);
                    $q->orWhere(function($q) {
                        $q->where('is_collection', 1);
                        $q->where('status_id', 18);
                    });
                })
                ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
                ->groupBy(DB::raw('DATE_FORMAT(billing_date, \'%Y-%c\')'))
                ->select([
                    'shipments.id',
                    DB::raw('DATE_FORMAT(billing_date, \'%Y-%c\') as yearmonth'),
                    DB::raw('count(total_price) as count_shipments'),
                    DB::raw('sum(total_price) as total'),
                    DB::raw('sum(total_expenses) as expenses'),
                    DB::raw('sum(amount) as covenants'),
                    DB::raw('count(amount) as count_covenants'),
                    DB::raw('avg(total_price) as price_avg'),
                    DB::raw('avg(weight) as weight_avg'),
                    DB::raw('MONTH(billing_date) as month'),
                    DB::raw('YEAR(billing_date) as year')
                ]);

        
        return Datatables::of($data)
            ->edit_column('yearmonth', function($row) {
                $date = explode('-', $row->yearmonth);

                if(!isset($date[0]) || !isset($date[1])) {
                    return 'N/A';
                } else {
                    return @$date[0] . '&nbsp;&nbsp;&nbsp;&nbsp;' .trans('datetime.month.'.@$date[1]);
                }
            })
            ->add_column('shipments', function($row) {
                $value = $row->total + $row->expenses;
                return view('account.billing.datatables.extracts.price', compact('value'))->render();
            })
            ->add_column('covenants', function($row) {
                //ao agrupar, soma o valor total de todos os "left"
                $value = $row->covenants;
                $count = $row->count_covenants;

                if($count) {
                    $value = $value/$count;
                }

                return view('account.billing.datatables.extracts.price', compact('value'))->render();
            })
            ->add_column('others', function($row) use($productsBought) {
                $value = @$productsBought->filter(function($item) use($row) {
                    return $item->yearmonth == $row->yearmonth;
                })->first()->subtotal;
                return view('account.billing.datatables.extracts.price', compact('value'))->render();
            })
            ->edit_column('price_avg', function($row) {
                return money($row->price_avg, Setting::get('app_currency'));
            })
            ->edit_column('weight_avg', function($row) {
                return money($row->weight_avg, 'kg');
            })
            ->edit_column('total', function($row) use($productsBought) {
                $productsBought = @$productsBought->filter(function($item) use($row) {
                    return $item->yearmonth == $row->yearmonth;
                })->first()->subtotal;

                $covenants = $row->covenants;
                $count = $row->count_covenants;

                if($count) {
                    $covenants = $covenants/$count;
                }

                $value = $row->total + $row->expenses + $covenants + $productsBought;
                return view('account.billing.datatables.extracts.price', compact('value'))->render();
            })
            ->add_column('download', function($row) {
                return view('account.billing.datatables.extracts.actions', compact('row'))->render();
            })
            ->make(true);
    }
    
    /**
     * Print list of shipments
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printBilling(Request $request) {
        $year     = $request->has('year')  ? $request->year : date('Y');
        $month    = $request->has('month') ? $request->month : date('m');
        $period   = $request->has('period') ? $request->period : '30d';
        $customer = Auth::guard('customer')->user();

        CustomerBilling::printShipments($customer->id, $month, $year, 'I', null, $period);
    }
    
    /**
     * Customer billing grouped by recipients
     * 
     * @return type
     */
    public function byRecipients(Request $request) {

        $year  = $request->has('year')  ? $request->year : date('Y');
        $month = $request->has('month') ? $request->month : date('m');


        $customer = Auth::guard('customer')->user();
        
        $bindings = [
            'id',
            'recipient_id',
            'recipient_name as recipient',
            DB::raw('count(total_price) as shipments'),
            DB::raw('sum(is_collection) as collections'),
            DB::raw('sum(total_price) as total'),
            DB::raw('sum(total_expenses) as expenses'),
            DB::raw('MONTH(date) as month'),
            DB::raw('YEAR(date) as year')
        ];
        
        $billing = Shipment::with('service', 'status')
                        ->filterAgencies()
                        ->whereRaw('MONTH(date) = '.$month)
                        ->whereRaw('YEAR(date) = '.$year)
                        ->where('customer_id', $customer->id)
                        ->groupBy('recipient_name')
                        ->orderBy('total', 'desc')
                        ->get($bindings);
        
        return view('account.billing.customers', compact('billing', 'month', 'year'))->render();
    }


}