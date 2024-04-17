<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Models\Billing;
use App\Models\CacheSetting;
use App\Models\CustomerBilling;
use Auth, Date, Setting, Excel;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\ShippingStatus;
use App\Models\ShippingExpense;
use App\Models\Shipment;
use Mockery\Exception;

class ShipmentsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'shipments';

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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',shipments']);
    }

    /**
     * Export index controller
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {

        try {
            return $this->currentList($request);
        } catch (Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param Request $request 
     * @return mixed
     */
    public function exportAlternative(Request $request) {
        $request->alternative = true;
        return $this->currentList($request);
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function currentList(Request $request)
    {
        $ids = $request->id;

        $data = Shipment::filterAgencies()
            ->with('customer', 'agency', 'provider', 'status', 'operator', 'service', 'expenses', 'department');

        if (!empty($ids)) {
            $data = $data->whereIn('id', $ids);
        }

        if (Auth::user()->is_developer) {
            $data = $data->whereIn('customer_id', [205]);
        }

        //limit search
        $value = $request->limit_search;
        if ($request->has('limit_search') && !empty($value)) {
            $minId = (int) CacheSetting::get('shipments_limit_search');
            if ($minId) {
                $data = $data->where('id', '>=', $minId);
            }
        }

        //filter hide final status
        $value = $request->hide_final_status;
        if ($request->has('hide_final_status') && !empty($value)) {
            $finalStatus = ShippingStatus::where('is_final', 1)->pluck('id')->toArray();
            if (in_array(config('app.source'), ['corridadotempo'])) {
                $finalStatus[] = 9;
            }
            $data = $data->whereNotIn('status_id', $finalStatus);
        }

        //show hidden
        $value = $request->hide_scheduled;
        if ($request->has('hide_scheduled') && !empty($value)) {
            $data = $data->where('date', '<=', date('Y-m-d'));
        }

        //filter period
        $value = $request->period;
        if ($request->has('period')) {
            if ($value == "1") { //MANHA
                $data = $data->where(function ($q) {
                    $q->whereRaw('HOUR(created_at) between "00:00:00" and "13:00:00"');
                    $q->orWhereRaw('HOUR(created_at) between "18:00:00" and "23:59:59"');
                });
            } else {
                $data = $data->where(function ($q) {
                    $q->whereRaw('HOUR(created_at) between "13:00:00" and "18:00:00"');
                });
            }
        }

        //filter customer
        $value = $request->customer;
        if ($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter customer
        $value = $request->dt_customer;
        if ($request->has('dt_customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter status
        $value = $request->get('status');
        if (!empty($value)) {
            $value = explode(',', $value);
            $data = $data->whereIn('status_id', $value);
        }

        //filter service
        $value = $request->get('service');
        if (!empty($value)) {
            $value = explode(',', $value);
            $data = $data->whereIn('service_id', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if (!empty($value)) {
            $value = explode(',', $value);
            $data = $data->whereIn('provider_id', $value);
        }

        //filter route
        $value = $request->route;
        if ($request->has('route')) {
            $data = $data->where('route_id', $value);
        }

        //filter agency
        $value = $request->agency;
        if (!empty($value)) {
            $value = explode(',', $value);
            $data = $data->whereIn('agency_id', $value);
        }

        //filter agency
        $value = $request->sender_agency;
        if (!empty($value)) {
            $value = explode(',', $value);
            $data = $data->whereIn('sender_agency_id', $value);
        }

        //filter recipient agency
        $value = $request->recipient_agency;
        if (!empty($value)) {
            $value = explode(',', $value);
            $data = $data->whereIn('recipient_agency_id', $value);
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {

            $dtMax = $dtMin;

            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            if ($request->has('date_unity') && !empty($request->has('date_unity'))) { //filter by shipment status date
                $dtMin = $dtMin . ' 00:00:00';
                $dtMax = $dtMax . ' 23:59:59';
                $statusId = $request->get('date_unity');

                if (in_array($statusId, ['3', '4', '5', '9', '36'])) {
                    $data = $data->whereHas('history', function ($q) use ($dtMin, $dtMax, $statusId) {
                        $q->where('status_id', $statusId)->whereBetween('created_at', [$dtMin, $dtMax]);
                    });
                } elseif ($statusId == 'delivery') {
                    $data->whereBetween('delivery_date', [$dtMin, $dtMax]);
                } elseif ($statusId == 'billing') {
                    $data->whereBetween('billing_date', [$dtMin, $dtMax]);
                } elseif ($statusId == 'creation') {
                    $data->whereBetween('created_at', [$dtMin, $dtMax]);
                }
            } else { //filter by shipment date
                $data = $data->whereBetween('date', [$dtMin, $dtMax]);
            }
        }

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            $value = explode(',', $value);
            if (in_array('not-assigned', $value)) {
                $data = $data->where(function ($q) use ($value) {
                    $q->whereNull('operator_id')
                        ->orWhereIn('operator_id', $value);
                });
            } else {
                $data = $data->whereIn('operator_id', $value);
            }
        }

        //filter charge
        $value = $request->charge;
        if ($request->has('charge')) {
            if ($value == 0) {
                $data = $data->whereNull('charge_price');
            } elseif ($value == 1) {
                $data = $data->whereNotNull('charge_price');
            }
        }

        //filter payment at recipient
        $value = $request->payment_recipient;
        if ($request->has('payment_recipient')) {
            if ($value == '0') {
                $data = $data->where('payment_at_recipient', 0);
            } elseif ($value == '1') {
                $data = $data->where('payment_at_recipient', 1);
            }
        }

        //show is blocked
        $value = $request->blocked;
        if ($request->has('blocked')) {
            $data = $data->where('is_blocked', $value);
        }

        //show printed
        $value = $request->printed;
        if ($request->has('printed')) {
            $data = $data->where('is_printed', $value);
        }

        //filter invoice
        $value = $request->get('invoice');
        if ($request->has('invoice')) {
            if ($value == '0') {
                $data = $data->whereNull('invoice_doc_id');
            } else {
                $data = $data->whereNotNull('invoice_doc_id');
            }
        }

        //filter expenses
        $value = $request->get('expenses');
        if ($request->has('expenses')) {
            if ($value == '0') {
                $data = $data->where(function ($q) {
                    $q->whereNull('total_expenses');
                    $q->orWhere('total_expenses', 0.00);
                });
            } else {
                $data = $data->where('total_expenses', '>', 0.00);
            }
        }

        //show hidden
        $value = $request->deleted;
        if ($request->has('deleted') && !empty($value)) {
            $data = $data->withTrashed();
        }

        //filter type
        $value = $request->get('shp_type');
        if ($request->has('shp_type')) {
            if ($value == Shipment::TYPE_SHIPMENT) {
                $data = $data->whereNull('type');
            } else if ($value == 'sync-error') {
                $data = $data->whereNotNull('webservice_method')
                    ->whereNull('submited_at');
            } else if ($value == 'sync-no') {
                $data = $data->whereNull('webservice_method');
            } else if ($value == 'sync-yes') {
                $data = $data->whereNotNull('webservice_method')
                    ->whereNotNull('submited_at');
            } else if ($value == 'noprice') {
                $data = $data->where(function ($q) {
                    $q->whereNull('total_price');
                    $q->orWhere('total_price', '0.00');
                });
            } else if ($value == 'pod_signature') {
                $data = $data->whereHas('last_history', function ($q) {
                    $q->where('signature', '<>', '');
                });
            } else if ($value == 'pod_file') {
                $data = $data->whereHas('last_history', function ($q) {
                    $q->where('filepath', '<>', '');
                });
            } else if ($value == 'pudo') {
                $data = $data->whereNotNull('recipient_pudo_id');
            } else {
                $data = $data->where('type', $value);
            }
        }

        //filter vehicle
        $value = $request->vehicle;
        if ($request->has('vehicle')) {
            if ($value == '-1') {
                $data = $data->where(function ($q) {
                    $q->whereNull('vehicle');
                    $q->orWhere('vehicle', '');
                });
            } else {
                $data = $data->where('vehicle', $value);
            }
        }

        //filter route
        /*$value = $request->route;
        if($request->has('route')) {
            if($value == '-1') {
                $data = $data->whereNull('route_id');
            } else {
                $data = $data->where('route_id', $value);
            }
        }*/

        //filter trailer
        $value = $request->trailer;
        if ($request->has('trailer')) {
            if ($value == '-1') {
                $data = $data->where(function ($q) {
                    $q->whereNull('trailer');
                    $q->orWhere('trailer', '');
                });
            } else {
                $data = $data->where('trailer', $value);
            }
        }

        //filter sender country
        $value = $request->get('sender_country');
        if ($request->has('sender_country')) {
            $data = $data->where('sender_country', $value);
        }

        //filter recipient country
        $value = $request->get('recipient_country');
        if ($request->has('recipient_country')) {
            $data = $data->where('recipient_country', $value);
        }

        //filter recipient zip code
        $value = $request->get('recipient_zip_code');
        if (!empty($value)) {

            $values = explode(',', $value);
            $zipCodes = array_map(function ($item) {
                return str_contains($item, '-') ? $item : substr($item, 0, 4) . '%';
            }, $values);

            $data = $data->where(function ($q) use ($zipCodes) {
                foreach ($zipCodes as $zipCode) {
                    $q->orWhere('recipient_zip_code', 'like', $zipCode . '%');
                }
            });
        }

        //filter workgroups
        $value = $request->get('workgroups');
        if ($request->has('workgroups')) {

            $workgroup = UserWorkgroup::remember(config('cache.query_ttl'))
                ->cacheTags(UserWorkgroup::CACHE_TAG)
                ->filterSource()
                ->whereIn('id', $value)
                ->get(['services'])
                ->toArray();

            $serviceIds = [];
            foreach ($workgroup as $group) {
                if (is_array(@$group['services'])) {
                    $serviceIds = array_merge($serviceIds, $group['services']);
                }
            }

            if ($serviceIds) {
                $data = $data->whereIn('service_id', $serviceIds);
            }
        }

        //filter recipient district
        $district = $request->get('recipient_district');
        $county   = $request->get('recipient_county');
        if ($request->has('recipient_district') || $request->has('recipient_county')) {

            $zipCodes = ZipCode::remember(config('cache.query_ttl'))
                ->cacheTags(ShippingStatus::CACHE_TAG)
                ->where('district_code', $district)
                ->where('country', 'pt');

            if ($county) {
                $zipCodes = $zipCodes->where('county_code', $county);
            }

            $zipCodes = $zipCodes->groupBy('zip_code')
                ->pluck('zip_code')
                ->toArray();

            $data = $data->where(function ($q) use ($zipCodes) {
                $q->where('recipient_country', 'pt');
                $q->whereIn(DB::raw('SUBSTRING(`recipient_zip_code`, 1, 4)'), $zipCodes);
            });
        }

        $data = $data->take($this->maxRows + 1)
            ->with('history')
            ->get();


        $ignoreFields = [];
        if ($request->doc_source == 'billing') {
            $ignoreFields[] = 'cost_price';
        } else if (!empty($request->doc_source) && $request->doc_source != 'admin') {
            $ignoreFields[] = 'price';
        } else if (!empty($request->doc_source) && $request->doc_source != 'admin') {
            $ignoreFields[] = 'cost_price';
        }

        $source = $request->doc_source;

        $alternative = $request->alternative ?? false;

        if($request->exportString) {
            return Shipment::exportExcel($data, $request->filename, $request->exportString, $ignoreFields, $source, $alternative);
        }

        Shipment::exportExcel($data, $request->filename, $request->exportString, $ignoreFields, $source, $alternative);
    }

    /**
     * @param Request $request 
     * @return mixed
     */
    public function exportDimensions(Request $request) {
   
        $ids = $request->id;

        $data = Shipment::filterAgencies()
            ->with('customer', 'agency', 'provider', 'status', 'operator', 'service', 'pack_dimensions', 'department')
            ->applyRequestFilters($request);

        if (!empty($ids)) {
            $data = $data->whereIn('id', $ids);
        }

        if (Auth::user()->is_developer) {
            $data = $data->whereIn('customer_id', [205]);
        }

        if($request->has('month') && $request->has('billing_period')) { //faturação mensal clientes
            $period = Billing::getPeriodDates($request->get('year'), $request->get('month'), $request->get('period'));
            $period = [$period['first'], $period['last']];
            $data->whereBetween('billing_date', $period);
        }

        $data = $data->take($this->maxRows + 1)
            ->get();

        $ignoreFields = [];
        if ($request->doc_source == 'billing') {
            $ignoreFields[] = 'cost_price';
        } else if (!empty($request->doc_source) && $request->doc_source != 'admin') {
            $ignoreFields[] = 'price';
        } else if (!empty($request->doc_source) && $request->doc_source != 'admin') {
            $ignoreFields[] = 'cost_price';
        }

        $source = $request->doc_source;

        $alternative = $request->alternative ?? false;

        if($request->exportString) {
            return Shipment::exportExcelDimensions($data, $request->filename, $request->exportString, $ignoreFields, $source, $alternative);
        }

        Shipment::exportExcelDimensions($data, $request->filename, $request->exportString, $ignoreFields, $source, $alternative);
    }
}
