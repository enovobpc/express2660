<?php

namespace App\Models;

use Mail, Setting, DB;

use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Mpdf;


class ProviderBilling extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_providers_billing';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'providers_billing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'billing_type', 'month', 'year', 'period','billed', 'api_key',
        'total_month', 'total_month_vat', 'total_month_no_vat', 'total_month_cost',
        'total_discount', 'fuel_tax', 'irs_tax', 'invoice_type', 'shipments',
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
        'total_month_cost'   => 'required',
    );

    /**
     *
     * Relashionships
     *
     */
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    /**
     * Return customer billing billed shipments
     * @param $providerId
     * @param $year
     * @param $month
     * @param $period
     * @return array
     */
    public static function getBilledShipments($providerId, $year, $month, $period, $billingCollection = null) {

        if(is_null($billingCollection)) {
            $billingCollection = ProviderBilling::where('customer_id', $providerId)
                ->where('month',$month)
                ->where('year',$year)
                ->where('period',$period)
                ->get(['invoice_type', 'invoice_id', 'shipments', 'covenants', 'products', 'billing_type', 'total_month']);
        }

        $billedIds    = [];
        $invoicesIds  = [];
        $billingTypes = [];
        $noDocIds     = [];
        $totalBilled = 0;
        foreach ($billingCollection as $row) {

            $totalBilled+= $row->total_month;
            if($row->invoice_type == 'nodoc') {
                $invoicesIds[$row->invoice_id] = trans('admin/billing.types-list.' . $row->invoice_type);
                $noDocIds[] = $row->invoice_id;
            } else {
                $invoicesIds[$row->invoice_id] = trans('admin/billing.types_code.' . $row->invoice_type) . ' ' . $row->invoice_id;
            }
            $billingTypes[] = $row->billing_type;

            if($row->shipments) {
                foreach ($row->shipments as $id) {
                    $billedIds['shipments'][] = $id;
                }
            }
        }

        return [
            'billing_types' => $billingTypes,
            'invoices'      => $invoicesIds,
            'nodoc_ids'     => $noDocIds,
            'total'         => $totalBilled,
            'ids'           => $billedIds,

        ];
    }

    /**
     * Create PDF file with shipments of a given month and year
     *
     * @param $providerId
     * @param null $month
     * @param null $year
     * @param string $outputFormat [I = Output to screen, F = Save on server, S = send by email]
     * @return mixed
     */
    public static function printShipments($providerId, $month = null, $year = null, $returnMode = 'pdf', $dataIds = null, $period = '30d') {

        ini_set("pcre.backtrack_limit", "5000000");
        ini_set("memory_limit", "-1");

        $provider = Provider::filterSource()
            ->filterAgencies()
            ->isCarrier()
            ->find($providerId);

        $ids = $dataIds;
        if(!is_null($dataIds)) {
            if(isset($dataIds['shipments'])) {
                $ids = $dataIds['shipments'];
            }
        }

        $year   = empty($year)  ? date('Y') : $year;
        $month  = empty($month) ? date('n') : $month;
        $period = empty($period) ? '30d' : $period;
        $month  = str_pad($month, 2, "0", STR_PAD_LEFT);

        $filename       = Billing::getPeriodName($year, $month, $period);
        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        $documentTitle  = 'Faturação Fornecedores - '. $filename;

        $shipments = Shipment::with(['service' => function($q){
                        $q->remember(config('cache.query_ttl'));
                        $q->cacheTags(Service::CACHE_TAG);
                    }])
                    ->with(['status' => function($q){
                        $q->remember(config('cache.query_ttl'));
                        $q->cacheTags(ShippingStatus::CACHE_TAG);
                    }])
                    ->with(['expenses' => function($q){
                        $q->remember(config('cache.query_ttl'));
                        $q->cacheTags(ShipmentExpense::CACHE_TAG);
                    }])
                    ->filterAgencies()
                    ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
                    ->where('is_collection', 0)
                    ->where('provider_id', $providerId);

        if(!empty($ids)) {
            $shipments = $shipments->whereIn('id', $ids);
        } else {
            $shipments = $shipments->whereBetween('date', [$periodFirstDay, $periodLastDay]);
        }

        /*if(Setting::get('billing_ignored_services')) {
            $shipments = $shipments->whereNotIn('service_id', Setting::get('billing_ignored_services'));
        }*/

        $shipments = $shipments->orderBy('date', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();

        $billingData = null; //ProviderBilling::getBilling($providerId, $month, $year, $period, $dataIds);

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 11,
            'margin_right'  => 5,
            'margin_top'    => 28,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;
        $mpdf->debug = true;

        $data = [
            'documentTitle'    => $documentTitle,
            'documentSubtitle' => $provider->code ? $provider->code .' - '.$provider->name : $provider->name,
            'shipments'        => $shipments,
            'provider'         => $provider,
            'month'            => $month,
            'year'             => $year,
            'period'           => $period,
            'billingData'      => $billingData,
            'view'             => 'admin.printer.billing.providers.summary'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if($returnMode == 'string') {
            return $mpdf->Output($documentTitle . '.pdf', 'S'); //output base64 string
        }

        if($returnMode == 'array') {
            return [
                'mime'      => 'application/pdf',
                'title'     => $documentTitle,
                'filename'  => $documentTitle.'.pdf',
                'content'   => $mpdf->Output($documentTitle . '.pdf', 'S'),
                'shipments' => $shipments
            ];
        }

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        return $mpdf->Output($documentTitle . '.pdf', 'I'); //output to screen
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
    public function setFuelTaxAttribute($value) {
        $this->attributes['fuel_tax'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setIrsTaxAttribute($value) {
        $this->attributes['irs_tax'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setTotalMonthCostAttribute($value) {
        $this->attributes['total_month_cost'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setTotalDiscountAttribute($value) {
        $this->attributes['total_discount'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setShipmentsAttribute($value) {
        $value = is_array($value) ? array_filter($value) : $value;
        $this->attributes['shipments'] = empty($value) ? null : json_encode($value);
    }

    public function getFuelTaxTotalAttribute() {
        $tax = $this->fuel_tax / 100;
        return number($this->total_month * $tax);
    }

    public function getFuelTaxTotalVatAttribute() {
        $tax = $this->fuel_tax / 100;
        return number($this->total_month_vat * $tax);
    }

    public function getFuelTaxTotalNoVatAttribute() {
        $tax = $this->fuel_tax / 100;
        return number(($this->total_month - $this->total_month_vat) * $tax);
    }

    public function getShipmentsAttribute($value) {
        return json_decode($value, true);
    }
}
