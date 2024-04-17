<?php

namespace App\Http\Controllers\Account;

use App\Models\Agency;
use App\Models\Billing;
use App\Models\IncidenceResolutionType;
use App\Models\ShipmentHistory;
use Auth, Date, Setting, Excel;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\ShippingStatus;
use App\Models\ShippingExpense;
use App\Models\Shipment;
use Mockery\Exception;

class ExportController extends \App\Http\Controllers\Controller {

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
    public function __construct() {}

    /**
     * Export index controller
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request) {

        try {
            return $this->currentList($request);
        } catch (Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export shipments
     *
     * @param Request $request
     * @param $customerId
     * @return \Illuminate\Http\Response
     */
    public function shipments(Request $request) {

        $customer = Auth::guard('customer')->user();

        $ids = $request->id;

        $data = Shipment::filterAgencies()
            ->with('customer', 'agency', 'provider', 'status', 'operator', 'service', 'expenses')
            ->filterCustomer();

        if($customer->hide_old_shipments && @$customer->login_created_at) {
            $data = $data->where('date', '>=', $customer->login_created_at->format('Y-m-d'));
        }

        if (!empty($ids)) {
            $data = $data->whereIn('id', $ids);
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter status
        $value = $request->get('status');
        if($request->has('status')) {
            $data = $data->where('status_id', $value);
        }

        //filter service
        $value = $request->get('service');
        if($request->has('service')) {
            $data = $data->where('service_id', $value);
        }

        //filter printed label
        $value = $request->label;
        if($request->has('printed')) {
            $data = $data->where('is_printed', $value);
        }

        //filter sender country
        $value = $request->sender_country;
        if($request->has('sender_country')) {
            $data = $data->where('sender_country', $value);
        }

        //filter recipient country
        $value = $request->recipient_country;
        if($request->has('recipient_country')) {
            $data = $data->where('recipient_country', $value);
        }

        //filter charge
        $value = $request->charge;
        if($request->has('charge')) {
            if($value == 0) {
                $data = $data->whereNull('charge_price');
            } elseif($value == 1) {
                $data = $data->whereNotNull('charge_price');
            }
        }

        $data = $data->take(5001)->get(); //max 5000 rows

        $ignoreFields = ['cost_price', 'provider_id'];

        if(!$customer->show_billing) {
            $ignoreFields[] = 'price';
        }

        try {
            return Shipment::exportExcel($data, $request->filename, $request->exportString, $ignoreFields, 'customer');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export month extract
     *
     * @param Request $request
     * @param $customerId
     * @return \Illuminate\Http\Response
     */
    public function monthExtract(Request $request) {

        $customer = Auth::guard('customer')->user();

        $year   = $request->year  ? $request->year : date('Y');
        $month  = $request->month ? $request->month : date('n');
        $period = $request->period ? $request->period : '30d';
        $ids    = $request->ids ? $request->ids : [];
        $exportString = $request->exportString;

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        $shipments = Shipment::filterAgencies()
            ->where('is_collection', 0)
            ->whereCustomerId($customer->id);

        if($ids) {
            $shipments = $shipments->whereIn('id', $ids);
        } else {
            $shipments = $shipments->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);
        }

        if(empty($ids)) {
            $ids = $shipments->orderBy('billing_date', 'asc')
                ->pluck('id')
                ->toArray();
        }

        $request = new \Illuminate\Http\Request();
        $request->id            = $ids;
        $request->filename      = 'Resumo de Envios - ' . Billing::getPeriodName($year, $month, $period);
        $request->exportString  = $exportString;

        return $this->shipments($request);
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function incidences(Request $request)
    {

        $customer = Auth::guard('customer')->user();
        $customerId = $customer->customer_id ? $customer->customer_id : $customer->id;

        $ids = $request->id;

        try {
            $myAgencies = Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->withTrashed()
                ->pluck('id')
                ->toArray();

            $resolutionsTypes = IncidenceResolutionType::remember(config('cache.query_ttl'))
                ->cacheTags(IncidenceResolutionType::CACHE_TAG)
                ->pluck('name', 'id')
                ->toArray();

            $bindings = [
                'shipments.tracking_code',
                'shipments.agency_id',
                'shipments.sender_agency_id',
                'shipments.recipient_agency_id',
                'shipments.sender_name',
                'shipments.sender_zip_code',
                'shipments.sender_city',
                'shipments.recipient_name',
                'shipments.recipient_zip_code',
                'shipments.recipient_city',
                'shipments.service_id',
                'shipments.provider_id',
                'shipments.status_id',
                'shipments.volumes',
                'shipments.weight',
                'shipments.obs',
                'shipments.date',
                'shipments.charge_price',
                'shipments_history.id',
                'shipments_history.shipment_id',
                'shipments_history.id as history_id',
                'shipments_history.operator_id',
                'shipments_history.incidence_id',
                'shipments_history.obs as incidence_obs',
                'shipments_history.resolved',
                'shipments_history.created_at',
            ];

            $data = ShipmentHistory::with('resolutions', 'incidence')
                ->join('shipments', function ($join) {
                    $join->on('shipments_history.shipment_id', '=', 'shipments.id');
                })
                ->where('customer_id', $customerId)
                ->whereIn('shipments_history.id', $ids)
                ->where('shipments_history.status_id', ShippingStatus::INCIDENCE_ID)
                ->whereIn('shipments.agency_id', $myAgencies)
                ->whereNull('shipments.deleted_at');

            //filter resolved
            $value = $request->resolved;
            if ($request->has('resolved')) {
                if ($request->has('charge')) {
                    if ($value == 0) {
                        $data = $data->whereNull('resolved');
                    } elseif ($value == 1) {
                        $data = $data->whereNotNull('resolved');
                    }
                }
            }

            //filter incidence
            $value = $request->get('incidence');
            if (!empty($value)) {
                $data = $data->whereIn('incidence_id', $value);
            }

            //filter service
            $value = $request->get('service');
            if (!empty($value)) {
                $data = $data->whereIn('service_id', $value);
            }

            //filter provider
            $value = $request->get('provider');
            if (!empty($value)) {
                $data = $data->whereIn('provider_id', $value);
            }

            //filter operator
            $value = $request->operator;
            if ($request->has('operator')) {
                $data = $data->whereIn('shipments_history.operator_id', $value);
            }

            //filter date min
            $dtMin = $request->get('date_min');
            if ($request->has('date_min')) {
                $dtMax = $dtMin;
                if ($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                }
                $data = $data->whereBetween('shipments_history.created_at', [$dtMin . ' 00:00:00', $dtMax . ' 23:59:59']);
            }

            $data = $data->get($bindings);


            $header = [
                'TRK',
                'Remetente',
                'Destinatário',
                'Data Incidência',
                'Motivo Incidência',
                'Observações',
                'Ação tomar',
                'Solução',
                'Data Solução'
            ];


            Excel::create('Listagem de Incidências', function ($file) use ($data, $header, $resolutionsTypes) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header, $resolutionsTypes) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $shipment) {

                        $solution = null;
                        if(@$shipment->resolutions) {
                            $solution = $shipment->resolutions->filter(function ($item) use ($shipment) {
                                return $item->shipment_history_id == $shipment->history_id;
                            })->last();
                        }


                        $rowData = [
                            @$shipment->tracking_code,
                            @$shipment->sender_name,
                            @$shipment->recipient_name,
                            @$shipment->created_at,
                            @$shipment->incidence->name,
                            @$shipment->incidence_obs,
                            @$resolutionsTypes[@$solution->resolution_type_id],
                            @$solution->obs,
                            @$solution->created_at
                        ];
                        $sheet->appendRow($rowData);
                    }
                });

            })->export('xls');

        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        };
    }
}
