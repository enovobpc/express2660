<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Mpdf;
use Mail, Setting, View, Auth;

class AgencyBilling extends BaseModel
{

    use SoftDeletes;


    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_agency_billing';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agencies_billing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'agency_id', 'partner_agency_id',

        'count_month_nacional', 'total_month_nacional',
        'count_month_import', 'total_month_import',
        'count_month_spain', 'total_month_spain',
        'count_month_internacional', 'total_month_internacional',

        'count_month', 'total_month', 'total_month_vat', 'total_month_no_vat',

        'month', 'year', 'period', 'billed', 'api_key'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'total_month'        => 'required',
        'total_month_vat'    => 'required',
        'total_month_no_vat' => 'required',
    );

    /**
     *
     * Relashionships
     *
     */

    public function agency()
    {
        return $this->belongsTo('App\Models\Agency', 'agency_id');
    }

    /**
     * Get customer billing for a given month and year
     *
     * @param $agencyId
     * @param null $month
     * @param null $year
     * @return collection customer
     */
    public static function getBilling($agencyId, $partnerAgencyId, $month = null, $year = null, $period = '30d')
    {
        $month = is_null($month) ? date('m') : $month;
        $year = is_null($year) ? date('Y') : $year;

        $firstMonthDay = $year . '-' . $month . '-01';
        $lastMonthDay = $year . '-' . $month . '-31';

        if($period == '1q') {
            $firstMonthDay = $year . '-' . $month . '-01';
            $lastMonthDay = $year . '-' . $month . '-15';
        } elseif($period == '2q') {
            $firstMonthDay = $year . '-' . $month . '-16';
            $lastMonthDay = $year . '-' . $month . '-31';
        }

        $agency = Agency::filterAgencies()
            ->with(['billing' => function ($q) use ($partnerAgencyId, $year, $month, $period) {
                $q->where('year', $year)
                ->where('month', $month)
                ->where('period', $period)
                ->where('partner_agency_id', $partnerAgencyId);
            }])
            ->firstOrNew(['id' => $agencyId]);

        if (!$agency->exists) {
            $agency->id = $agencyId;
            $agency->code = '';
            $agency->name = 'Faturação sem agencia associado';
        }

        $bindings = [
            'id',
            'volumes',
            'total_price',
            'cost_price',
            'total_price_for_recipient',
            'payment_at_recipient',
            'recipient_country',
            'sender_country',
            'service_id',
            'ignore_billing',
            'charge_price',
            'date'];

        $allShipmentsExpedition = Shipment::filterAgencies()
            ->where(function($q) use($agencyId, $partnerAgencyId) {
                $q->where('agency_id', $agencyId)
                  ->where('recipient_agency_id', $partnerAgencyId);
            })
            ->whereRaw('shipments.agency_id <> shipments.recipient_agency_id')
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->whereRaw('YEAR(date) = '.$year)
            ->whereRaw('MONTH(date) = '.$month)
            ->get($bindings);

        $allShipmentsDelivery = Shipment::filterAgencies()
            ->where(function($q) use($agencyId, $partnerAgencyId) {
                $q->where('agency_id', $partnerAgencyId)
                  ->where(function($q) use($agencyId, $partnerAgencyId) {
                      $q->where('recipient_agency_id', $agencyId)
                        ->orWhere('sender_agency_id', $agencyId);
                  });
            })
            //->whereRaw('shipments.agency_id <> shipments.recipient_agency_id')
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->whereRaw('YEAR(date) = '.$year)
            ->whereRaw('MONTH(date) = '.$month)
            ->get($bindings);

        /**
         * PROCESS EXPEDITIONS
         */
        $agency->expedition_empty_prices = $allShipmentsExpedition->filter(function($item) {
            return (empty($item->cost_price) || $item->cost_price == 0.00) && !$item->payment_at_recipient;
        })->count();


        //get nacional
        $shipmentsExpeditionNacional = $allShipmentsExpedition->filter(function ($item) {
            return in_array($item->sender_country, [Setting::get('app_country')])  && in_array($item->recipient_country, [Setting::get('app_country')]);
        });

        //get import
        $shipmentsExpeditionImport = $allShipmentsExpedition->filter(function ($item) {
            return !in_array($item->sender_country, [Setting::get('app_country')])  && in_array($item->recipient_country, [Setting::get('app_country')]);
        });

        //get export
        $shipmentsExpeditionExport = $allShipmentsExpedition->filter(function ($item) {
            return in_array($item->sender_country, [Setting::get('app_country'), 'es'])  && !in_array($item->recipient_country, [Setting::get('app_country')])
                || !in_array($item->sender_country, [Setting::get('app_country')])  && !in_array($item->recipient_country, [Setting::get('app_country')]);
        });

        //get export spain
        $shipmentsExpeditionExportSpain = $allShipmentsExpedition->filter(function ($item) {
            return in_array($item->sender_country, [Setting::get('app_country'), 'es'])  && $item->recipient_country == 'es';
        });


        //total expedition shipments
        $agency->expedition_shipments_nacional      = $shipmentsExpeditionNacional->sum('cost_price');
        $agency->expedition_shipments_import        = $shipmentsExpeditionImport->sum('cost_price');
        $agency->expedition_shipments_export        = $shipmentsExpeditionExport->sum('cost_price');
        $agency->expedition_shipments_export_spain  = $shipmentsExpeditionExportSpain->sum('cost_price');
        $agency->expedition_shipments_export_internacional = $agency->expedition_shipments_export - $agency->expedition_shipments_export_internacional;

        $agency->expedition_total_shipments         = $allShipmentsExpedition->sum('cost_price');
        $agency->expedition_total_shipments_vat     = $agency->expedition_shipments_nacional + $agency->expedition_shipments_import;
        $agency->expedition_total_shipments_no_vat  = $agency->expedition_shipments_export;
        $agency->expedition_count_shipments_volumes = $allShipmentsExpedition->sum('volumes');
        $agency->expedition_count_shipments         = $allShipmentsExpedition->count('cost_price');

        //all export
        $agency->expedition_total_export = $shipmentsExpeditionExport->sum('cost_price');
        $agency->expedition_count_export = $shipmentsExpeditionExport->count('cost_price');
        $agency->expedition_count_export_volumes = $shipmentsExpeditionExport->sum('volumes');

        //export spain
        $agency->expedition_total_shipments_spain = $shipmentsExpeditionExportSpain->sum('cost_price');
        $agency->expedition_count_shipments_spain = $shipmentsExpeditionExportSpain->count('total_price');
        $agency->expedition_count_shipments_volumes_spain = $shipmentsExpeditionExportSpain->sum('volumes');

        //export internacional
        $agency->expedition_total_shipments_internacional = $shipmentsExpeditionExport->sum('cost_price') - $agency->expedition_total_shipments_spain;
        $agency->expedition_count_shipments_internacional = $shipmentsExpeditionExport->count('cost_price') - $agency->expedition_count_shipments_spain;
        $agency->expedition_count_shipments_volumes_internacional = $shipmentsExpeditionExport->sum('volumes') - $agency->expedition_count_shipments_volumes_internacional;

        //import
        $agency->expedition_total_shipments_import = $shipmentsExpeditionImport->sum('cost_price');
        $agency->expedition_count_shipments_import = $shipmentsExpeditionImport->count('cost_price');
        $agency->expedition_count_shipments_import_volumes = $shipmentsExpeditionImport->sum('volumes');

        //nacional
        $agency->expedition_total_shipments_nacional = $shipmentsExpeditionNacional->sum('cost_price');
        $agency->expedition_count_shipments_nacional = $shipmentsExpeditionNacional->count('cost_price');
        $agency->expedition_count_shipments_nacional_volumes = $shipmentsExpeditionNacional->sum('volumes');
        
        

        /**
         * PROCESS DELIVERIES
         */
        $agency->delivery_empty_prices = $allShipmentsDelivery->filter(function($item) {
            return (empty($item->delivery_price) || $item->delivery_price == 0.00) && !$item->payment_at_recipient;
        })->count();

        //get nacional
        $shipmentsDeliveryNacional = $allShipmentsDelivery->filter(function ($item) {
            return in_array($item->sender_country, [Setting::get('app_country')])  && in_array($item->recipient_country, [Setting::get('app_country')]);
        });

        //get import
        $shipmentsDeliveryImport = $allShipmentsDelivery->filter(function ($item) {
            return !in_array($item->sender_country, [Setting::get('app_country')])  && in_array($item->recipient_country, [Setting::get('app_country')]);
        });

        //get export
        $shipmentsDeliveryExport = $allShipmentsDelivery->filter(function ($item) {
            return in_array($item->sender_country, [Setting::get('app_country'), 'es'])  && !in_array($item->recipient_country, [Setting::get('app_country')])
                || !in_array($item->sender_country, [Setting::get('app_country')])  && !in_array($item->recipient_country, [Setting::get('app_country')]);
        });

        //get export spain
        $shipmentsDeliveryExportSpain = $allShipmentsDelivery->filter(function ($item) {
            return in_array($item->sender_country, [Setting::get('app_country'), 'es']) && $item->recipient_country == 'es';
        });



        //total delivery shipments
        $agency->delivery_shipments_nacional      = $shipmentsDeliveryNacional->sum('delivery_price');
        $agency->delivery_shipments_import        = $shipmentsDeliveryImport->sum('delivery_price');
        $agency->delivery_shipments_export        = $shipmentsDeliveryExport->sum('delivery_price');
        $agency->delivery_shipments_export_spain  = $shipmentsDeliveryExportSpain->sum('delivery_price');
        $agency->delivery_shipments_export_internacional = $agency->delivery_shipments_export - $agency->delivery_shipments_export_internacional;

        $agency->delivery_total_shipments         = $allShipmentsDelivery->sum('delivery_price');
        $agency->delivery_total_shipments_vat     = $agency->delivery_shipments_nacional + $agency->delivery_shipments_import;
        $agency->delivery_total_shipments_no_vat  = $agency->delivery_shipments_export;
        $agency->delivery_count_shipments_volumes = $allShipmentsDelivery->sum('volumes');
        $agency->delivery_count_shipments         = $allShipmentsDelivery->count('cost_price');

        //all export
        $agency->delivery_total_export = $shipmentsDeliveryExport->sum('delivery_price');
        $agency->delivery_count_export = $shipmentsDeliveryExport->count('delivery_price');
        $agency->delivery_count_export_volumes = $shipmentsDeliveryExport->sum('volumes');

        //export spain
        $agency->delivery_total_shipments_spain = $shipmentsDeliveryExportSpain->sum('delivery_price');
        $agency->delivery_count_shipments_spain = $shipmentsDeliveryExportSpain->count('delivery_price');
        $agency->delivery_count_shipments_volumes_spain = $shipmentsDeliveryExportSpain->sum('volumes');

        //export internacional
        $agency->delivery_total_shipments_internacional = $shipmentsDeliveryExport->sum('delivery_price') - $agency->delivery_total_shipments_spain;
        $agency->delivery_count_shipments_internacional = $shipmentsDeliveryExport->count('delivery_price') - $agency->delivery_count_shipments_spain;
        $agency->delivery_count_shipments_volumes_internacional = $shipmentsDeliveryExport->sum('volumes') - $agency->delivery_count_shipments_volumes_internacional;

        //import
        $agency->delivery_total_shipments_import = $shipmentsDeliveryImport->sum('delivery_price');
        $agency->delivery_count_shipments_import = $shipmentsDeliveryImport->count('delivery_price');
        $agency->delivery_count_shipments_import_volumes = $shipmentsDeliveryImport->sum('volumes');

        //nacional
        $agency->delivery_total_shipments_nacional = $shipmentsDeliveryNacional->sum('delivery_price');
        $agency->delivery_count_shipments_nacional = $shipmentsDeliveryNacional->count('delivery_price');
        $agency->delivery_count_shipments_nacional_volumes = $shipmentsDeliveryNacional->sum('volumes');

        /**
         * Update values if agency is not PT
         */
         if($agency->country && $agency->country != Setting::get('app_country')) {
            $agency->expedition_total_shipments_vat = 0;
            $agency->delivery_total_shipments_vat   = 0;
        }


        /**
         * MONTH TOTALS EXPEDITIONS
         */
        $agency->total_month         = abs($agency->expedition_total_shipments - $agency->delivery_total_shipments);
        $agency->total_month_vat     = abs($agency->expedition_total_shipments_vat - $agency->delivery_total_shipments_vat);
        $agency->total_month_no_vat  = abs($agency->expedition_total_shipments_no_vat - $agency->delivery_total_shipments_no_vat);

        $agency->count_month                = abs($agency->expedition_count_shipments - $agency->delivery_count_shipments);
        $agency->count_month_nacional       = abs($agency->expedition_count_shipments_nacional - $agency->delivery_count_shipments_nacional);
        $agency->count_month_internacional  = abs($agency->expedition_count_shipments_internacional - $agency->delivery_count_shipments_internacional);
        $agency->count_month_import         = abs($agency->expedition_count_shipments_import - $agency->delivery_count_shipments_import);
        $agency->count_month_spain          = abs($agency->expedition_count_shipments_spain - $agency->delivery_count_shipments_spain);

        $agency->total_month_nacional       = abs($agency->expedition_total_shipments_nacional - $agency->delivery_total_shipments_nacional);
        $agency->total_month_internacional  = abs($agency->expedition_total_shipments_internacional - $agency->delivery_total_shipments_internacional);
        $agency->total_month_import         = abs($agency->expedition_total_shipments_import - $agency->delivery_total_shipments_import);
        $agency->total_month_spain          = abs($agency->expedition_total_shipments_spain - $agency->delivery_total_shipments_spain);

        $agency->billing_month = $agency->delivery_total_shipments > $agency->expedition_total_shipments  ? true : false;

        return $agency;
    }

    /**
     * Send email with billing info
     *
     * @param Request $request [customer_id, month, year, invoice, summary]
     * @param $id
     */
    public static function sendEmail($data) {

        $data['month']   = $data['month'] ? $data['month'] : date('n');
        $data['year']    = $data['year'] ? $data['year'] : date('Y');
        $data['period']  = $data['period'] ? $data['period'] : '30d';
        $data['summary'] = @$data['summary'];
        $data['invoice'] = @$data['invoice'];

        $monthName = trans('datetime.month.'.$data['month']);
        $subject  = 'Fatura - '. $monthName . ' ' . $data['year'];
        $filename = 'Resumo de Liquidação - '. $monthName . ' ' . $data['year'];

        if($data['period'] == '1q') {
            $subject  = 'Faturação - 1ª Quinzena ('. $monthName . ' ' . $data['year'].')';
            $filename = 'Resumo de Liquidação - 1ª Quinzena ('. $monthName . ' ' . $data['year'].')';
        } elseif($data['period'] == '2q') {
            $subject  = 'Faturação - 2ª Quinzena ('. $monthName . ' ' . $data['year'].')';
            $filename = 'Resumo de Liquidação - 2ª Quinzena ('. $monthName . ' ' . $data['year'].')';
        }

        if(empty($data['invoice']) && empty($data['summary'])) {
            return false;
        }

        $summaryFile = $invoiceFile = null;
        if($data['summary']){
            $summaryFile = AgencyBilling::printShipments($data['agency_id'], $data['partner_agency_id'], $data['month'], $data['year'], 'S', null, $data['period']);
        }

        if($data['invoice']) {

            $billing = AgencyBilling::where('agency_id', $data['agency_id'])
                ->where('partner_agency_id', $data['partner_agency_id'])
                ->where('month', $data['month'])
                ->where('year', $data['year'])
                ->where('period', $data['period'])
                ->first(['invoice_id', 'invoice_type', 'api_key']);

            if(@$billing->invoice_id) {
                $data['invoiceId'] = @$billing->invoice_id;
                $invoice = new Invoice(@$billing->api_key);
                $content = $invoice->getDocumentPdf(@$billing->invoice_id, @$billing->invoice_type);
                $invoiceFile = base64_decode($content);
            } else {
                $data['invoice'] = false;
            }
        }

        Mail::send('emails.billing_summary_agencies', compact('data', 'monthName'), function($message) use($data, $monthName, $invoiceFile, $summaryFile, $subject, $filename) {

            $message->to($data['email']);

            if(!empty(Setting::get('billing_email_cc'))) {
                $emails = validateNotificationEmails(Setting::get('billing_email_cc'));
                $message = $message->cc($emails['valid']);
            }

            $message = $message->from(config('mail.from.address'), config('mail.from.name'))
                ->subject($subject);

            //attach summary file
            if($data['summary']) {
                $filename = $filename . '.pdf';
                $message->attachData($summaryFile, $filename, ['mime' => 'application/pdf']);
            }

            //attach invoice file
            if($data['invoice'] && $invoiceFile) {
                $filename = 'Fatura '.$data['invoiceId'] . '/'.$data['year'].' - '.$monthName.' '.$data['year'].'.pdf';
                $message->attachData($invoiceFile, $filename, [ 'mime' => 'application/pdf']);
            }
        });

        if(count(Mail::failures()) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Create PDF file with shipments of a given month and year
     *
     * @param $customerId
     * @param null $month
     * @param null $year
     * @param string $outputFormat [I = Output to screen, F = Save on server, S = send by email]
     * @return mixed
     */
    public static function printShipments($sourceAgency, $partnerAgency, $month = null, $year = null, $outputFormat = 'I', $shipmentsIds = null, $period = '30d') {

        ini_set("pcre.backtrack_limit", "5000000");

        $year   = $year ? $year : date('Y');
        $month  = $month ? $month : date('m');
        $period = $period ? $period : '30d';

        $allShipmentsExpedition = Shipment::filterAgencies()
            ->where(function($q) use($sourceAgency, $partnerAgency) {
                $q->where('agency_id', $sourceAgency)
                    ->where('recipient_agency_id', $partnerAgency);
            })
            ->whereRaw('shipments.agency_id <> shipments.recipient_agency_id')
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->whereRaw('YEAR(date) = '.$year)
            ->whereRaw('MONTH(date) = '.$month)
            ->get();

        $allShipmentsDelivery = Shipment::filterAgencies()
            ->where(function($q) use($sourceAgency, $partnerAgency) {
                $q->where('agency_id', $partnerAgency)
                    ->where(function($q) use($sourceAgency, $partnerAgency) {
                        $q->where('recipient_agency_id', $sourceAgency)
                            ->orWhere('sender_agency_id', $sourceAgency);
                    });
            })
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->whereRaw('YEAR(date) = '.$year)
            ->whereRaw('MONTH(date) = '.$month)
            ->get();

        $sourceAgency = AgencyBilling::getBilling($sourceAgency, $partnerAgency, $month, $year, $period);

        $partnerAgency   = Agency::findOrFail($partnerAgency);
        $myAgencies      = Auth::user()->agencies;
        $myAgencies      = $myAgencies ? $myAgencies : [];

        ini_set("memory_limit", "-1");
        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 30,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'shipmentsExpedition' => $allShipmentsExpedition,
            'shipmentsDelivery'   => $allShipmentsDelivery,
            'sourceAgency'     => $sourceAgency,
            'partnerAgency'    => $partnerAgency,
            'myAgencies'       => $myAgencies,
            'documentTitle'    => 'Faturação entre Agências',
            'documentSubtitle' => trans('datetime.month.'.$month) . ' de '. $year,
            'view'             => 'admin.billing_agencies.pdf.summary'
        ];

        $mpdf->WriteHTML(View::make('admin.layouts.pdf', $data)->render()); //write

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Faturação entre Agências.pdf', $outputFormat); //output to screen

        exit;
    }

    /*
     |--------------------------------------------------------------------------
     | Accessors & Mutators
     |--------------------------------------------------------------------------
     |
     | Eloquent provides a convenient way to transform your model attributes when
     | getting or setting them. Simply define a 'getFooAttribute' method on your model
     | to declare an accessor. Keep in mind that the methods should follow camel-casing,
     | even though your database columns are snake-case.
     |
    */
}
