<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Models\Agency;
use App\Models\Service;
use App\Models\ShipmentHistory;
use Html, Auth, Date, Setting, Excel;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\ShippingStatus;
use App\Models\ShippingExpense;
use App\Models\Shipment;

class RefundsController extends \App\Http\Controllers\Admin\Controller
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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',refunds_customers']);
    }

    /**
     * Export customers refunds
     *
     * @param type $shipmentId
     * @return type
     */
    public function customersExport(Request $request)
    {

        $input = $request->all();
        $ids = @$input['id'];

        if ($request->has('customer')) {
            $ids = $this->getCustomerAvailableRefunds($request->get('customer'));
        } elseif (!$request->has('id')) {
            $sourceAgencies = Agency::where('source', config('app.source'))
                ->pluck('id')
                ->toArray();

            $ids = $data = Shipment::where('is_collection', 0)
                ->whereNotNull('charge_price')
                ->whereIn('shipments.agency_id', $sourceAgencies)
                ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
                ->applyRefundsRequestFilters($request)
                ->pluck('id')
                ->toArray();
        }


        $data = Shipment::with(['status' => function ($q) {
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(ShippingStatus::CACHE_TAG);
        }])
            ->with(['service' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Service::CACHE_TAG);
            }])
            ->with('last_history')
            ->whereNotNull('charge_price')
            ->whereIn('id', $ids)
            ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
            ->get();

        $header = [
            'Data',
            'TRK Envio',
            'Referência',
            'Serviço',
            'Cliente',
            'Nome Remetente',
            'Morada Remetente',
            'CP Remetente',
            'Localidade Remetente',
            'Pais Remetente',
            'Contacto Remetente',
            'Nome Destinatário',
            'Morada Destinatário',
            'CP Destinatário',
            'Localidade Destinatário',
            'Pais Destinatário',
            'Contacto Destinatário',
            'Pessoa Contacto',
            'Volumes',
            'Peso',
            'Último Estado',
            'Último Estado',
            'Observações do Envio',
            'Valor de Reembolso',
        ];

        if (!Setting::get('refunds_control_customers_hide_received_column')) {
            $header[] = 'Forma Recebimento';
            $header[] = 'Data Recebimento';
        }

        if (!Setting::get('refunds_control_customers_hide_paid_column')) {
            $header[] = 'Forma Reembolso';
            $header[] = 'Data Reembolso';
        }

        $header[] = 'Observações Reembolso';

        if (Auth::check()) {
            $header[] = 'TRK Fornecedor';
        }

        $header[] = 'IBAN Cliente';

        Excel::create('Listagem de Reembolsos', function ($file) use ($data, $header) {

            $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                $sheet->setColumnFormat(array(
                    'B' => '@',
                    'C' => '@', //referencia
                    'AA' => '@',
                    'AB' => '@',
                ));

                foreach ($data as $shipment) {

                    $rowData = [
                        $shipment->date,
                        $shipment->tracking_code,
                        $shipment->reference,
                        @$shipment->service->display_code,
                        $shipment->customer ? $shipment->customer->code : '',
                        $shipment->sender_name,
                        $shipment->sender_address,
                        $shipment->sender_zip_code,
                        $shipment->sender_city,
                        $shipment->sender_country,
                        str_replace(' ', '', $shipment->sender_phone),
                        $shipment->recipient_name,
                        $shipment->recipient_address,
                        $shipment->recipient_zip_code,
                        $shipment->recipient_city,
                        $shipment->recipient_country,
                        str_replace(' ', '', $shipment->recipient_phone),
                        $shipment->recipient_attn,
                        $shipment->volumes,
                        $shipment->weight,
                        $shipment->status->name,
                        @$shipment->last_history->created_at,
                        $shipment->obs,
                        $shipment->charge_price
                    ];

                    if (!Setting::get('refunds_control_customers_hide_received_column')) {
                        $rowData[] = @$shipment->refund_control->received_method ? trans('admin/refunds.payment-methods.' . @$shipment->refund_control->received_method) : '';
                        $rowData[] = @$shipment->refund_control->received_date;
                    }

                    if (!Setting::get('refunds_control_customers_hide_paid_column')) {
                        $rowData[] = @$shipment->refund_control->payment_method ? trans('admin/refunds.payment-methods.' . @$shipment->refund_control->payment_method) : '';
                        $rowData[] = @$shipment->refund_control->payment_date;
                    }

                    $rowData[] = @$shipment->refund_control->obs;

                    if (Auth::check()) {
                        $rowData[] = @$shipment->provider_tracking_code . ' ';
                    }

                    $rowData[] = @$shipment->customer->iban_refunds;

                    $sheet->appendRow($rowData);
                }
            });
        })->export('xls');
    }

    /**
     * Export agencies refunds
     *
     * @param type $shipmentId
     * @return type
     */
    public function agenciesExport(Request $request)
    {

        $input = $request->all();
        $ids = $input['id'];

        $data = Shipment::with('provider', 'refund_agencies')
            ->whereNotNull('charge_price')
            ->whereIn('id', $ids)
            ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
            ->get();

        $header = [
            'Data',
            'TRK',
            'Referência',
            'Agência Origem',
            'Agência Destino',
            'Serviço',
            'Remetente',
            'Morada Remetente',
            'Cod Post Remetente',
            'Localidade Remetente',
            'Pais Remetente',
            'Contacto Remetente',
            'Destinatário',
            'P. Contacto',
            'Morada Destinatário',
            'Cod Post Destinatário',
            'Localidade Destinatário',
            'Pais Destinatário',
            'Contacto Destinatário',
            'Volumes',
            'Peso',
            'Estado',
            'Observações Expedição',
            'Cobrança',
            'Forma Recebimento',
            'Data Recebimento',
            'Forma Reembolso',
            'Data Reembolso',
            'Observações Reembolso',

        ];

        Excel::create('Listagem de Reembolsos entre Agências', function ($file) use ($data, $header) {

            $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                foreach ($data as $shipment) {

                    $rowData = [
                        $shipment->date,
                        $shipment->tracking_code,
                        $shipment->reference,
                        $shipment->agency->name,
                        $shipment->recipientAgency->name,
                        @$shipment->service->display_code,
                        $shipment->sender_name,
                        $shipment->sender_address,
                        $shipment->sender_zip_code,
                        $shipment->sender_city,
                        $shipment->sender_country,
                        str_replace(' ', '', $shipment->sender_phone),
                        $shipment->recipient_name,
                        $shipment->recipient_attn,
                        $shipment->recipient_address,
                        $shipment->recipient_zip_code,
                        $shipment->recipient_city,
                        $shipment->recipient_country,
                        str_replace(' ', '', $shipment->recipient_phone),
                        $shipment->volumes,
                        $shipment->weight,
                        $shipment->status->name,
                        $shipment->obs,

                        $shipment->charge_price ? money($shipment->charge_price) : '',
                        @$shipment->refund_agencies->received_method ? trans('admin/refunds.payment-methods.' . @$shipment->refund_agencies->received_method) : '',
                        @$shipment->refund_agencies->received_date,
                        @$shipment->refund_agencies->payment_method ? trans('admin/refunds.payment-methods.' . @$shipment->refund_agencies->payment_method) : '',
                        @$shipment->refund_agencies->payment_date,
                        @$shipment->refund_agencies->obs,
                    ];
                    $sheet->appendRow($rowData);
                }
            });
        })->export('xls');
    }

    /**
     * Export cash on delivery
     *
     * @param type $shipmentId
     * @return type
     */
    public function codExport(Request $request)
    {

        $input = $request->all();
        $ids = $input['id'];

        $data = Shipment::with('provider', 'refund_agencies')
            ->whereNotNull('charge_price')
            ->whereIn('id', $ids)
            ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
            ->get();

        $header = [
            'Data',
            'TRK',
            'Referência',
            'Agência Origem',
            'Agência Destino',
            'Serviço',
            'Remetente',
            'Morada Remetente',
            'Cod Post Remetente',
            'Localidade Remetente',
            'Pais Remetente',
            'Contacto Remetente',
            'Destinatário',
            'P. Contacto',
            'Morada Destinatário',
            'Cod Post Destinatário',
            'Localidade Destinatário',
            'Pais Destinatário',
            'Contacto Destinatário',
            'Volumes',
            'Peso',
            'Estado',
            'Observações Expedição',
            'Portes',
            'Forma Pagamento',
            'Data Pagamento',
            'Observações Pagamento',
        ];

        Excel::create('Listagem de Portes no Destino', function ($file) use ($data, $header) {

            $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                foreach ($data as $shipment) {

                    $rowData = [
                        $shipment->date,
                        $shipment->tracking_code,
                        $shipment->reference,
                        $shipment->agency->name,
                        $shipment->recipientAgency->name,
                        @$shipment->service->display_code,
                        $shipment->sender_name,
                        $shipment->sender_address,
                        $shipment->sender_zip_code,
                        $shipment->sender_city,
                        $shipment->sender_country,
                        str_replace(' ', '', $shipment->sender_phone),
                        $shipment->recipient_name,
                        $shipment->recipient_attn,
                        $shipment->recipient_address,
                        $shipment->recipient_zip_code,
                        $shipment->recipient_city,
                        $shipment->recipient_country,
                        str_replace(' ', '', $shipment->recipient_phone),
                        $shipment->volumes,
                        $shipment->weight,
                        $shipment->status->name,
                        $shipment->obs,

                        $shipment->payment_at_recipient ? money($shipment->total_price_for_recipient) : '',
                        @$shipment->cod_control->payment_method ? trans('admin/refunds.payment-methods.' . @$shipment->cod_control->payment_method) : '',
                        @$shipment->cod_control->payment_date,
                        @$shipment->cod_control->obs,
                    ];
                    $sheet->appendRow($rowData);
                }
            });
        })->export('xls');
    }

    /**
     * Get customer available refunds
     *
     * @param $customerId
     * @return mixed
     */
    public function getCustomerAvailableRefunds($customerId)
    {

        $shipments = Shipment::where(function ($q) use ($customerId) {
            $q->where('customer_id', $customerId);
            $q->orWhere('requested_by', $customerId);
        })
            ->with(['status' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(ShippingStatus::CACHE_TAG);
                $q->select(['id', 'name', 'color', 'is_final']);
            }])
            ->with('last_history')
            ->with('refund_control')
            ->where('is_collection', 0)
            ->whereNotNull('charge_price')
            ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
            ->whereHas('refund_control', function ($q) {
                $q->whereNull('payment_method');
                $q->whereNull('payment_date');
                $q->whereNotNull('received_method');
                $q->whereNotNull('received_date');
                $q->where('received_method', '<>', 'claimed');
                $q->where('canceled', 0);
            })
            ->pluck('id')
            ->toArray();

        return $shipments;
    }
}
