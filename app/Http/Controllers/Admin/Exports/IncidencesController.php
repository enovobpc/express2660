<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Models\Agency;
use App\Models\IncidenceResolutionType;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use Auth, Date, Setting, Excel;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Mockery\Exception;

class IncidencesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'incidences';

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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',incidences']);
    }

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
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function currentList(Request $request)
    {
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
