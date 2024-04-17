<?php

namespace App\Http\Controllers\Account;

use App\Models\Customer;
use App\Models\CustomerBalance;
use App\Models\CustomerMessage;
use App\Models\CustomerRanking;
use App\Models\Invoice;
use App\Models\RefundControl;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ShipmentAnalytics;
use App\Models\ShippingStatus;
use Carbon\Carbon;
use Jenssegers\Date\Date;
use Setting;

class HomeController extends \App\Http\Controllers\Controller
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
    protected $sidebarActiveOption = 'dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * 
     * Account home controller
     * 
     */
    public function index(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        if ($customer->customer_id && $customer->view_parent_shipments) {
            $customer = Customer::find($customer->customer_id);
        }

        if (!(hasModule('invoices') && Setting::get('show_customers_ballance') && $customer->show_billing)) {
            return redirect()->route('account.shipments.index');
        }

        $customerId = $customer->id;

        $year  = !empty($request->get('year'))  ? $request->get('year')  : date('Y');
        $month = !empty($request->get('month')) ? $request->get('month') : date('m');
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);

        if ($customer && ($customer->hide_old_shipments && $year < @$customer->login_created_at->year)) {
            $year = date('Y');
        }

        $metrics = 'day';
        if (empty($request->get('month')) && empty($request->get('year'))) {
            $curMonth  = new Date($year . '-' . $month . '-01');
            $lastMonth = $curMonth->copy()->subMonth(1);

            $startDate = $curMonth->format('Y-m-d');
            $endDate   = $curMonth->endOfMonth()->format('Y-m-d');

            $startDateLastMonth =  $lastMonth->startOfMonth()->format('Y-m-d');
            $endDateLastMonth =  $lastMonth->endOfMonth()->format('Y-m-d');
        } elseif (!empty($request->get('month'))) {
            $curMonth  = new Date($year . '-' . $month . '-01');
            $lastMonth = $curMonth->copy()->subMonth(1);

            $startDate = $curMonth->format('Y-m-d');
            $endDate   = $curMonth->endOfMonth()->format('Y-m-d');

            $startDateLastMonth =  $lastMonth->startOfMonth()->format('Y-m-d');
            $endDateLastMonth =  $lastMonth->endOfMonth()->format('Y-m-d');
        } else {
            $curYear  = new Date($year . '-01-01');
            $lastYear = $curYear->copy()->subYear(1);

            $startDate = $curYear->format('Y-m-d');
            $endDate   = $curYear->endOfYear()->format('Y-m-d');

            $startDateLastMonth =  $lastYear->startOfMonth()->format('Y-m-d');
            $endDateLastMonth =  $lastYear->endOfYear()->format('Y-m-d');
            $metrics = 'month';
        }


        $billingGraphData = ShipmentAnalytics::getForPeriod($startDate, $endDate, $metrics, $customerId);
        $totalMonthDays = date('t', mktime(0, 0, 0, $month, 1, $year));
        $billingTotals = [];

        if ($metrics == 'day') {
            for ($i = 1; $i <= $totalMonthDays; $i++) {

                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $data = @$billingGraphData[$year . '-' . $month . '-' . $day];

                $billingGraphData['labels'][]       = "'" . $day . ' ' . trans('datetime.month-tiny.' . intval($month)) . "'";
                $billingGraphData['billed'][]       = @$data['billed'];
                $billingGraphData['shipments'][]    = @$data['shipments'];
                $billingGraphData['volumes'][]      = @$data['volumes'];

                $billingTotals['shipments']         = @$billingTotals['shipments'] + $data['shipments'];
                $billingTotals['shipments_avg']     = @$billingTotals['shipments_avg'] + $data['shipments_avg'];
                $billingTotals['billed']            = @$billingTotals['billed'] + $data['billed'];
                $billingTotals['collections']       = @$billingTotals['collections'] + $data['collections'];
            }
        } else {
            foreach ($billingGraphData as $key => $data) {
                $billingGraphData['labels'][]       = "'" . trans('datetime.month-tiny.' . $key) . "'";
                $billingGraphData['billed'][]       = $data['billed'];
                $billingGraphData['shipments'][]    = $data['shipments'];
                $billingGraphData['volumes'][]    = $data['volumes'];

                $billingTotals['shipments']         = @$billingTotals['shipments'] + $data['shipments'];
                $billingTotals['shipments_avg']     = @$billingTotals['shipments_avg'] + $data['shipments_avg'];
                $billingTotals['billed']            = @$billingTotals['billed'] + $data['billed'];
                $billingTotals['collections']       = @$billingTotals['collections'] + $data['collections'];
            }
        }

        $billingGraphData['labels'] = @implode($billingGraphData['labels'], ',');
        $billingGraphData['billed'] = @implode($billingGraphData['billed'], ',');
        $billingGraphData['shipments'] = @implode($billingGraphData['shipments'], ',');
        $billingGraphData['volumes'] = @implode($billingGraphData['volumes'], ',');



        $bindings = [
            'id',
            'status_id',
            'volumes',
            'weight',
            'total_price',
            'total_expenses',
            'service_id',
            'provider_id',
            'recipient_zip_code',
            'recipient_country',
            'sender_country',
        ];

        $allShipments = Shipment::remember(5)
            ->with(['history' => function ($q) {
                $q->remember(5);
                $q->whereIn('status_id', [ShippingStatus::INCIDENCE_ID]);
            }])
            ->where('customer_id', $customerId)
            ->where('is_collection', 0)
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->where('payment_at_recipient', 0)
            ->whereBetween('date', [$startDate, $endDate])
            ->get($bindings);

        $allShipmentsLastMonth = Shipment::remember(5)
            ->with(['history' => function ($q) {
                $q->remember(5);
                $q->whereIn('status_id', [ShippingStatus::INCIDENCE_ID]);
            }])
            ->where('customer_id', $customerId)
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->where('payment_at_recipient', 0)
            ->where('is_collection', 0)
            ->whereBetween('date', [$startDateLastMonth, $endDateLastMonth])
            ->get($bindings);


        $groupedServices = $allShipments->groupBy('service_id');
        $statusStatistics = [];
        foreach ($groupedServices as $serviceId => $shipments) {

            $shipments = $shipments->groupBy('status_id');

            $status = [
                'pending'   => count(@$shipments[ShippingStatus::PENDING_ID]),
                'accepted'  => count(@$shipments[ShippingStatus::ACCEPTED_ID]),
                'pickup'    => count(@$shipments[ShippingStatus::IN_PICKUP_ID]),
                'transit'   => count(@$shipments[ShippingStatus::IN_TRANSPORTATION_ID]) + count(@$shipments[ShippingStatus::IN_DISTRIBUTION_ID]),
                'delivered' => count(@$shipments[ShippingStatus::DELIVERED_ID]),
                'devolved'  => count(@$shipments[ShippingStatus::DEVOLVED_ID]),
                'incidence' => count(@$shipments[ShippingStatus::INCIDENCE_ID]),
            ];

            $statusStatistics[$serviceId] = $status;
        }

        //STATUS CHART
        $totals = [];
        $totals['last_month'] = [
            'total_billing'   => @$allShipmentsLastMonth->sum('total_price') + @$allShipmentsLastMonth->sum('total_expenses'),
            'count_shipments' => @$allShipmentsLastMonth->count(),
            'sum_volumes'     => @$allShipmentsLastMonth->sum('volumes'),
            'shipments_day'   => @$allShipmentsLastMonth->count() / 22,
            'avg_weight'      => $allShipmentsLastMonth->avg('weight'),
            'incidences'      => $allShipmentsLastMonth->filter(function ($item) {
                return !$item->history->isEmpty();
            })->count(),
            'deliveries'      => $allShipmentsLastMonth->filter(function ($item) {
                return $item->status_id == ShippingStatus::DELIVERED_ID && $item->history->isEmpty();
            })->count()
        ];

        $totals['cur_month'] = [
            'total_billing'   => @$allShipments->sum('total_price') + @$allShipments->sum('total_expenses'),
            'count_shipments' => @$allShipments->count(),
            'sum_volumes'     => @$allShipments->sum('volumes'),
            'shipments_day'   => @$allShipments->count() / 22,
            'avg_weight'      => $allShipments->avg('weight'),
            'incidences'      => $allShipments->filter(function ($item) {
                return !$item->history->isEmpty();
            })->count(),
            'deliveries'      => $allShipments->filter(function ($item) {
                return $item->status_id == ShippingStatus::DELIVERED_ID && $item->history->isEmpty();
            })->count()
        ];

        $totals['balance'] = [
            'total_billing'   => $this->calcBalance(@$totals['last_month']['total_billing'], @$totals['cur_month']['total_billing'], true),
            'count_shipments' => $this->calcBalance(@$totals['last_month']['count_shipments'], @$totals['cur_month']['count_shipments']),
            'sum_volumes'     => $this->calcBalance(@$totals['last_month']['sum_volumes'], @$totals['cur_month']['sum_volumes']),
            'shipments_day'   => $this->calcBalance(@$totals['last_month']['shipments_day'], @$totals['cur_month']['shipments_day']),
            'avg_weight'      => $this->calcBalance(@$totals['last_month']['avg_weight'], @$totals['cur_month']['avg_weight']),
            'incidences'      => $this->calcBalance(@$totals['last_month']['incidences'], @$totals['cur_month']['incidences']),
            'deliveries'      => $this->calcBalance(@$totals['last_month']['deliveries'], @$totals['cur_month']['deliveries']),
        ];

        $statusTotals = [
            'pending'  => $allShipments->filter(function ($item) {
                return $item->status_id == ShippingStatus::PENDING_ID ||
                    $item->status_id == ShippingStatus::PICKUP_REQUESTED_ID;
            })->count(),
            'accepted'  => $allShipments->filter(function ($item) {
                return $item->status_id == ShippingStatus::ACCEPTED_ID ||
                    $item->status_id == ShippingStatus::PICKUP_ACCEPTED_ID ||
                    $item->status_id == ShippingStatus::WAINTING_SYNC_ID ||
                    $item->status_id == ShippingStatus::READ_BY_COURIER_OPERATOR;
            })->count(),
            'pickup'  => $allShipments->filter(function ($item) {
                return $item->status_id == ShippingStatus::PICKUP_ACCEPTED_ID ||
                    $item->status_id == ShippingStatus::IN_PICKUP_ID;
            })->count(),
            'incidence' => $allShipments->filter(function ($item) {
                return $item->status_id == ShippingStatus::INCIDENCE_ID;
            })->count(),
            'transport' => $allShipments->filter(function ($item) {
                return $item->status_id == ShippingStatus::IN_DISTRIBUTION_ID || $item->status_id == ShippingStatus::IN_TRANSPORTATION_ID;
            })->count(),
            'devolved' => $allShipments->filter(function ($item) {
                return $item->status_id == ShippingStatus::DEVOLVED_ID;
            })->count(),
            'delivery' => $allShipments->filter(function ($item) {
                return $item->status_id == ShippingStatus::DELIVERED_ID;
            })->count(),
        ];

        $years = yearsArr(@$customer->login_created_at->year ? @$customer->login_created_at->year : @$customer->created_at->year, date('Y'), true);

        //REFUNDS
        $unconfirmedRefunds = RefundControl::whereHas('shipment', function ($q) use ($customer) {
            $q->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                if ($customer->customer_id) {
                    $q->orWhere('customer_id', $customer->customer_id);
                }
            });
        })
            ->where(function ($q) {
                $q->whereNotNull('payment_method');
                $q->orWhereNotNull('payment_date');
            })
            ->where('confirmed', 0)
            ->count();

        $today = Carbon::today();

        $invoices = Invoice::where('customer_id', $customer->id)
            ->filterBalanceDocs()
            ->whereNotIn('doc_type', [Invoice::DOC_TYPE_RC])
            ->orderBy('doc_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        $isShippingBlocked = $customer->is_shipping_blocked;

        //GLOBAL CHART
        $graphsData = CustomerRanking::whereCustomerId($customer->id)
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $graphData = [];
        foreach ($graphsData as $data) {
            $graphData['labels'][]      = trans('datetime.month-tiny.' . $data->month);
            $graphData['shipments'][]   = $data->shipments;
            $graphData['volumes'][]     = $data->volumes;
            $graphData['billing'][]     = $data->billing;
            $graphData['price_avg'][]   = $data->price_avg;
            $graphData['weight_avg'][]  = $data->weight_avg;
            $graphData['volumes_avg'][] = $data->volumes_avg;
        }

        if (!empty($graphData)) {
            $graphData['labels']        = '"' . implode('","', $graphData['labels']) . '"';
            $graphData['shipments']     = implode(',', $graphData['shipments']);
            $graphData['volumes']       = implode(',', $graphData['volumes']);
            $graphData['billing']       = implode(',', $graphData['billing']);
            $graphData['price_avg']     = implode(',', $graphData['price_avg']);
            $graphData['weight_avg']    = implode(',', $graphData['weight_avg']);
            $graphData['volumes_avg']   = implode(',', $graphData['volumes_avg']);
        }

        $messagesPopup = CustomerMessage::getUnread($customerId);

        $compact = compact(
            'billingGraphData',
            'billingTotals',
            'statusTotals',
            'years',
            'customer',
            'unconfirmedRefunds',
            'totalUnpaid',
            'totalExpired',
            'invoices',
            'today',
            'isShippingBlocked',
            'messagesPopup',
            'graphData',
            'totals',
            'statusChart'
        );

        return $this->setContent('account.dashboard.index', $compact);
    }


    /**
     * Calc value
     * @param $lastValue
     * @param $curValue
     */
    public function calcBalance($lastValue, $curValue, $percent = false)
    {

        if (!$curValue) {
            return 0;
        }

        if ($percent) {
            $percent = $lastValue > 0.00 ? (($curValue * 100) / $lastValue) : 0;

            if ($lastValue > $curValue) {
                $balance = -1 * (100 - $percent); //ex: -83% comparado mês passado
            } else {
                $balance = 100 - $percent; //ex: +30% comparado mês passado
            }
        } else {
            $balance = $curValue - $lastValue;
        }

        return $balance;
    }
}
