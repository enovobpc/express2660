<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Shipment;
use Illuminate\Console\Command;
use File, Mail, Date;

class SyncPlatforms extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:syncPlatforms {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Shipments Between Platforms';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->info("Sync Shipments Between Platforms");

        $date = date('Y-m-d');
        if (!empty($this->argument('date'))) {
            $date = $this->argument('date');
        }

        if (config('app.source') == 'sentidosuposto') {
            $this->pontualhd($date);
            $this->rochaerocha($date);
        } elseif (config('app.source') == 'nomadyellow') {
            $this->thyman($date);
        } elseif (config('app.source') == 'rapex') {
            $this->tortuga($date);
        } elseif (config('app.source') == 'argo') {
            $this->xkl($date);
        } elseif (config('app.source') == 'utiltrans') {
            $this->okestafetas($date);
        } elseif (config('app.source') == 'tarefacolossal') {
            $this->perfilinteligente($date);
        } elseif (config('app.source') == 'lousaestradas') {
            $this->transcapital($date);
        }


        $this->info("Sync completed");
        return;
    }

    /**
     * Pontual HD
     */
    public function pontualhd($date)
    {

        $url = 'https://pontualhd.pt/core/sync/platform?date=' . $date;
        $contents = json_decode(file_get_contents($url));

        foreach ($contents as $content) {

            $shipment = Shipment::firstOrNew([
                'provider_tracking_code' => $content->provider_tracking_code
            ]);


            $data = (array) $content;

            $agencyId   = 97;
            $customerId = 16640;
            $providerId = 341;
            $services = [
                '199' => '639', //10h
                '200' => '640', //14h
                '201' => '641', //24h
                '202' => '642', //72h
                '203' => '645', //aereo
                '204' => '644', //terrestre
            ];

            $serviceId = @$services[@$data['service_id']];

            if (!$shipment->exists) {
                echo 'Nao existe = ' . $shipment->tracking_code . '<br/>';
                $shipment->fill($data);
                $shipment->reference            = 'TRK' . $shipment->tracking_code;
                $shipment->agency_id            = $agencyId;
                $shipment->sender_agency_id     = $agencyId;
                $shipment->recipient_agency_id  = $agencyId;
                $shipment->customer_id          = $customerId;
                $shipment->total_price          = 0;
                $shipment->cost_price           = 0;
                $shipment->total_expenses       = $shipment->charge_price ? '1' : null;
                $shipment->recipient_id         = null;
                $shipment->provider_id          = $providerId;
                $shipment->volumes              = $shipment->volumes ? $shipment->volumes : 0;
                $shipment->service_id           = $serviceId;
                $shipment->department_id        = null;
                $shipment->operator_id          = null;
                $shipment->pickup_operator_id   = null;
                $shipment->obs_internal         = '';
                $shipment->status_id            = 15;
                $shipment->is_blocked           = 0;
                $shipment->ignore_billing       = 0;
                $shipment->price_fixed          = 0;
                $shipment->vehicle              = null;
                $shipment->is_printed           = 0;
                $shipment->conferred            = 0;
                $shipment->invoice_id           = null;
                $shipment->invoice_type         = null;
                $shipment->invoice_draft        = 0;
                $shipment->invoice_key          = null;
                $shipment->parent_tracking_code = $data['collection_tracking_code'] ? $data['collection_tracking_code'] : null;
                $shipment->type                 = $data['collection_tracking_code'] ? 'P' : null;
                $shipment->tracking_code        = null;
                $shipment->setTrackingCode();
            } else {
                echo 'Existe = ' . $shipment->tracking_code . '<br/>';
            }
        }
    }

    /**
     * Rocha & Rocha
     */
    public function rochaerocha($date)
    {

        $url = 'https://portal.rochaerocha.pt/core/sync/platform?date=' . $date;
        $contents = json_decode(file_get_contents($url));

        $services = [
            '3'  => '639', //10h
            '4'  => '640', //14h
            '5'  => '641', //24h
            '6'  => '642', //72h
            '8'  => '645', //aereo
            '9'  => '644', //terrestre
            '10' => '646', //AI
            '11' => '647', //MI
        ];

        foreach ($contents as $content) {

            $shipment = Shipment::firstOrNew([
                'provider_tracking_code' => $content->provider_tracking_code
            ]);

            $data = (array) $content;

            $agencyId   = 97;
            $customerId = 17849;
            $providerId = 341;
            $serviceId  = @$services[@$data['service_id']];

            if (!$shipment->exists) {
                echo 'Nao existe = ' . $shipment->tracking_code . '<br/>';
                $shipment->fill($data);
                $shipment->reference            = 'TRK' . $shipment->tracking_code;
                $shipment->agency_id            = $agencyId;
                $shipment->sender_agency_id     = $agencyId;
                $shipment->recipient_agency_id  = $agencyId;
                $shipment->customer_id          = $customerId;
                $shipment->total_price          = 0;
                $shipment->cost_price           = 0;
                $shipment->total_expenses       = $shipment->charge_price ? '1' : null;
                $shipment->recipient_id         = null;
                $shipment->provider_id          = $providerId;
                $shipment->volumes              = $shipment->volumes ? $shipment->volumes : 0;
                $shipment->service_id           = $serviceId;
                $shipment->department_id        = null;
                $shipment->operator_id          = null;
                $shipment->pickup_operator_id   = null;
                $shipment->obs_internal         = '';
                $shipment->status_id            = 15;
                $shipment->is_blocked           = 0;
                $shipment->ignore_billing       = 0;
                $shipment->price_fixed          = 0;
                $shipment->vehicle              = null;
                $shipment->is_printed           = 0;
                $shipment->conferred            = 0;
                $shipment->invoice_id           = null;
                $shipment->invoice_type         = null;
                $shipment->invoice_draft        = 0;
                $shipment->invoice_key          = null;
                $shipment->parent_tracking_code = $data['collection_tracking_code'] ? $data['collection_tracking_code'] : null;
                $shipment->type                 = $data['collection_tracking_code'] ? 'P' : null;
                $shipment->tracking_code        = null;
                $shipment->setTrackingCode();
            } else {
                echo 'Existe = ' . $shipment->tracking_code . '<br/>';
            }
        }
    }

    /**
     * Aveirofast
     * @param $date
     */
    public function aveirofast($date)
    {

        $agencyId   = 97;
        $customerId = 16640;
        $providerId = 341;
        $services = [
            '87' => '98', //10h
            '88' => '102', //14h
            '89' => '106', //24h
            '90' => '70', //72h
            '131' => '195', //aereo ilhas
            '132' => '193', //maritimo ilhas
            '95'  => '63', //int terrestre
            '96'  => '63', //int terrestre small
            '120' => '58', //int aereo
        ];

        $url = 'https://aveirofast.com/webservice/sage?date=' . $date;

        $contents = json_decode(file_get_contents($url));

        foreach ($contents as $content) {

            $shipment = Shipment::firstOrNew([
                'provider_tracking_code' => $content->provider_tracking_code
            ]);


            $data = (array) $content;

            $serviceId = @$services[@$data['service_id']];

            if (!$shipment->exists) {
                //dd($content);
                echo 'Nao existe = ' . $shipment->tracking_code . '<br/>';
                $shipment->fill($data);
                $shipment->reference = 'TRK' . $shipment->tracking_code;
                $shipment->agency_id = $agencyId;
                $shipment->sender_agency_id = $agencyId;
                $shipment->recipient_agency_id = $agencyId;
                $shipment->customer_id = $customerId;
                $shipment->requested_by = $customerId;
                $shipment->total_price = 0;
                $shipment->cost_price = 0;
                $shipment->total_expenses = $shipment->charge_price ? '1' : null;
                $shipment->recipient_id = null;
                $shipment->provider_id = $providerId;
                $shipment->volumes = $shipment->volumes ? $shipment->volumes : 0;
                $shipment->service_id = $serviceId;
                $shipment->department_id = null;
                $shipment->operator_id = null;
                $shipment->pickup_operator_id = null;
                $shipment->obs_internal = '';
                $shipment->status_id = 15;
                $shipment->is_blocked = 0;
                $shipment->ignore_billing = 0;
                $shipment->price_fixed = 0;
                $shipment->vehicle = null;
                $shipment->is_printed = 0;
                $shipment->conferred = 0;
                $shipment->invoice_id = null;
                $shipment->invoice_type = null;
                $shipment->invoice_draft = 0;
                $shipment->invoice_key = null;
                $shipment->parent_tracking_code = $data['collection_tracking_code'] ? $data['collection_tracking_code'] : null;
                $shipment->type = $data['collection_tracking_code'] ? 'P' : null;
                $shipment->tracking_code = null;
                $shipment->setTrackingCode();
            } else {
                echo 'Existe = ' . $shipment->tracking_code . '<br/>';
            }
        }
    }

    /*
     * THYMAN
     */
    public function thyman($date)
    {

        $agencyId   = 54;
        $customerId = 15806;
        $providerId = 235;
        $services = [
            '3' => '464', //10H
            '4' => '465', //14H
            '5' => '466', //24H
            '6' => '467', //72H
            '8' => '470', //INT-T
            '9' => '472', //INT-A
            '12' => '478', //MI
            '11' => '477', //AI
        ];

        $customer = Customer::find($customerId);

        $url = 'https://shipping.thyman.com/core/sync/platform?date=' . $date;

        $contents = json_decode(file_get_contents($url));

        foreach ($contents as $content) {

            $shipment = Shipment::firstOrNew([
                'provider_tracking_code' => $content->provider_tracking_code
            ]);


            $data = (array) $content;

            $serviceId = @$services[@$data['service_id']];

            if (!$shipment->exists) {
                // dd($content);
                echo 'Nao existe = ' . $shipment->tracking_code . '<br/>';
                $shipment->fill($data);
                $shipment->reference = 'TRK' . $shipment->tracking_code;
                $shipment->zone = 'pt';
                $shipment->agency_id = $agencyId;
                $shipment->sender_id = null;
                $shipment->recipient_id = null;
                $shipment->sender_agency_id = $agencyId;
                $shipment->recipient_agency_id = $agencyId;
                $shipment->customer_id = $customerId;
                $shipment->requested_by = $customerId;
                $shipment->sender_name = $customer->name;
                $shipment->sender_address = $customer->address;
                $shipment->sender_zip_code = $customer->zip_code;
                $shipment->sender_city = $customer->city;
                $shipment->sender_country = $customer->country;
                $shipment->sender_phone = $customer->phone;
                $shipment->total_price = 0;
                $shipment->cost_price = 0;
                $shipment->total_expenses = $shipment->charge_price ? '1' : null;
                $shipment->recipient_id = null;
                $shipment->provider_id = $providerId;
                $shipment->volumes = $shipment->volumes ? $shipment->volumes : 0;
                $shipment->service_id = $serviceId;
                $shipment->department_id = null;
                $shipment->operator_id = null;
                $shipment->pickup_operator_id = null;
                $shipment->obs_internal = '';
                $shipment->status_id = 15;
                $shipment->is_blocked = 0;
                $shipment->ignore_billing = 0;
                $shipment->price_fixed = 0;
                $shipment->vehicle = null;
                $shipment->is_printed = 0;
                $shipment->conferred = 0;
                $shipment->invoice_id = null;
                $shipment->invoice_type = null;
                $shipment->invoice_draft = 0;
                $shipment->invoice_key = null;
                $shipment->parent_tracking_code = $data['collection_tracking_code'] ? $data['collection_tracking_code'] : null;
                $shipment->type = $data['collection_tracking_code'] ? 'P' : null;
                $shipment->tracking_code = null;
                $shipment->setTrackingCode();
            } else {
                echo 'Existe = ' . $shipment->tracking_code . '<br/>';
            }
        }
    }

    /*
     * TORTUGA
     */
    public function tortuga($date)
    {

        $agencyId   = 90;
        $customerId = 11220;
        $providerId = 479;
        $services = [
            '519' => '595', //10H
            '520' => '596', //14H
            '521' => '597', //24H
            '522' => '598', //72H
            '526' => '600', //INT-T
            '525' => '599', //INT-A
            '602' => '655', //MI
            '603' => '654', //AI
        ];

        $customer = Customer::find($customerId);

        $url = 'https://tortugaveloz.pt/core/sync/platform?date=' . $date;

        $contents = json_decode(file_get_contents($url));

        foreach ($contents as $content) {

            $shipment = Shipment::firstOrNew([
                'provider_tracking_code' => $content->provider_tracking_code
            ]);


            $data = (array) $content;

            $serviceId = @$services[@$data['service_id']];

            if (!$shipment->exists) {
                //dd($content);
                echo 'Nao existe = ' . $shipment->tracking_code . '<br/>';
                $shipment->fill($data);
                $shipment->reference = 'TRK' . $shipment->tracking_code;
                $shipment->zone = 'pt';
                $shipment->agency_id = $agencyId;
                $shipment->sender_id = null;
                $shipment->recipient_id = null;
                $shipment->sender_agency_id = $agencyId;
                $shipment->recipient_agency_id = $agencyId;
                $shipment->customer_id = $customerId;
                $shipment->requested_by = $customerId;
                $shipment->sender_name = $customer->name;
                $shipment->sender_address = $customer->address;
                $shipment->sender_zip_code = $customer->zip_code;
                $shipment->sender_city = $customer->city;
                $shipment->sender_country = $customer->country;
                $shipment->sender_phone = $customer->phone;
                $shipment->total_price = 0;
                $shipment->cost_price = 0;
                $shipment->total_expenses = $shipment->charge_price ? '1' : null;
                $shipment->recipient_id = null;
                $shipment->provider_id = $providerId;
                $shipment->volumes = $shipment->volumes ? $shipment->volumes : 0;
                $shipment->service_id = $serviceId;
                $shipment->department_id = null;
                $shipment->operator_id = null;
                $shipment->pickup_operator_id = null;
                $shipment->obs_internal = '';
                $shipment->status_id = 15;
                $shipment->is_blocked = 0;
                $shipment->ignore_billing = 0;
                $shipment->price_fixed = 0;
                $shipment->vehicle = null;
                $shipment->is_printed = 0;
                $shipment->conferred = 0;
                $shipment->invoice_id = null;
                $shipment->invoice_type = null;
                $shipment->invoice_draft = 0;
                $shipment->invoice_key = null;
                $shipment->parent_tracking_code = $data['collection_tracking_code'] ? $data['collection_tracking_code'] : null;
                $shipment->type = $data['collection_tracking_code'] ? 'P' : null;
                $shipment->tracking_code = null;
                $shipment->setTrackingCode();
            } else {
                echo 'Existe = ' . $shipment->tracking_code . '<br/>';
            }
        }
    }

    /**
     * Pontual HD
     */
    public function xkl($date)
    {

        $url = 'https://transportes-xkl.pt/core/sync/platform?date=' . $date;
        $contents = json_decode(file_get_contents($url));

        foreach ($contents as $content) {

            $shipment = Shipment::firstOrNew([
                'provider_tracking_code' => $content->provider_tracking_code
            ]);


            $data = (array) $content;

            $agencyId   = 130;
            $customerId = 143;
            $providerId = 2;
            $services = [
                /*'' => '2', //10h
                '' => '3', //14h*/
                '4' => '4', //24h
                '5' => '5', //72h
                //'' => '6', //aereo
                '3' => '7', //terrestre
            ];

            $serviceId = @$services[@$data['service_id']];

            if (!$shipment->exists) {
                echo 'Nao existe = ' . $shipment->tracking_code . '<br/>';
                $shipment->fill($data);
                $shipment->reference            = 'TRK' . $shipment->tracking_code;
                $shipment->agency_id            = $agencyId;
                $shipment->sender_agency_id     = $agencyId;
                $shipment->recipient_agency_id  = $agencyId;
                $shipment->customer_id          = $customerId;
                $shipment->requested_by         = $customerId;
                $shipment->total_price          = 0;
                $shipment->cost_price           = 0;
                $shipment->total_expenses       = $shipment->charge_price ? '1' : null;
                $shipment->recipient_id         = null;
                $shipment->provider_id          = $providerId;
                $shipment->volumes              = $shipment->volumes ? $shipment->volumes : 0;
                $shipment->service_id           = $serviceId;
                $shipment->department_id        = null;
                $shipment->operator_id          = null;
                $shipment->pickup_operator_id   = null;
                $shipment->obs_internal         = '';
                $shipment->status_id            = 15;
                $shipment->is_blocked           = 0;
                $shipment->ignore_billing       = 0;
                $shipment->price_fixed          = 0;
                $shipment->vehicle              = null;
                $shipment->is_printed           = 0;
                $shipment->conferred            = 0;
                $shipment->invoice_id           = null;
                $shipment->invoice_type         = null;
                $shipment->invoice_draft        = 0;
                $shipment->invoice_key          = null;
                $shipment->parent_tracking_code = $data['collection_tracking_code'] ? $data['collection_tracking_code'] : null;
                $shipment->type                 = $data['collection_tracking_code'] ? 'P' : null;
                $shipment->tracking_code        = null;
                $shipment->setTrackingCode();
            } else {
                echo 'Existe = ' . $shipment->tracking_code . '<br/>';
            }
        }
    }

    /**
     * OK Estafetas
     */
    public function okestafetas($date)
    {


        $url = 'https://ok-estafetas.pt/core/sync/platform?date=' . $date;
        $contents = json_decode(file_get_contents($url));

        foreach ($contents as $content) {

            $shipment = Shipment::firstOrNew([
                'provider_tracking_code' => $content->provider_tracking_code
            ]);


            $data = (array) $content;

            $agencyId   = 96;
            $customerId = 227;
            $providerId = 3;
            $services = [
                '623' => '1', //10h
                '624' => '2', //14h
                '625' => '3', //24h
                '626' => '4', //72h
                '627' => '6', //aereo
                '628' => '5', //terrestre
            ];

            $serviceId = @$services[@$data['service_id']];

            if (!$shipment->exists) {
                echo 'Nao existe = ' . $shipment->tracking_code . '<br/>';
                $shipment->fill($data);
                $shipment->reference            = 'TRK' . $shipment->tracking_code;
                $shipment->agency_id            = $agencyId;
                $shipment->sender_agency_id     = $agencyId;
                $shipment->recipient_agency_id  = $agencyId;
                $shipment->customer_id          = $customerId;
                $shipment->requested_by         = $customerId;
                $shipment->total_price          = 0;
                $shipment->cost_price           = 0;
                $shipment->total_expenses       = $shipment->charge_price ? '1' : null;
                $shipment->recipient_id         = null;
                $shipment->provider_id          = $providerId;
                $shipment->volumes              = $shipment->volumes ? $shipment->volumes : 0;
                $shipment->service_id           = $serviceId;
                $shipment->department_id        = null;
                $shipment->operator_id          = null;
                $shipment->pickup_operator_id   = null;
                $shipment->obs_internal         = '';
                $shipment->status_id            = 15;
                $shipment->is_blocked           = 0;
                $shipment->ignore_billing       = 0;
                $shipment->price_fixed          = 0;
                $shipment->vehicle              = null;
                $shipment->is_printed           = 0;
                $shipment->conferred            = 0;
                $shipment->invoice_id           = null;
                $shipment->invoice_type         = null;
                $shipment->invoice_draft        = 0;
                $shipment->invoice_key          = null;
                $shipment->parent_tracking_code = $data['collection_tracking_code'] ? $data['collection_tracking_code'] : null;
                $shipment->type                 = $data['collection_tracking_code'] ? 'P' : null;
                $shipment->tracking_code        = null;
                $shipment->setTrackingCode();
            } else {
                echo 'Existe = ' . $shipment->tracking_code . '<br/>';
            }
        }
    }

    /**
     * Perfil inteligente
     */
    public function perfilinteligente($date)
    {

        $url = 'https://perfilinteligente.pt/core/sync/platform?date=' . $date;
        $contents = json_decode(file_get_contents($url));

        foreach ($contents as $content) {

            $shipment = Shipment::firstOrNew([
                'provider_tracking_code' => $content->provider_tracking_code
            ]);


            $data = (array) $content;

            $agencyId   = 113;
            $customerId = 119;
            $providerId = 2;
            $services = [
                '2' => '1', //10h
                '3' => '2', //14h
                '1' => '3', //24h
                '4' => '4', //72h
                '8' => '8', //maritimo ilhas
                '9' => '7', //aereo ilhas
                '5' => '5', //int terrestre
            ];

            $serviceId = @$services[@$data['service_id']];

            if (!$shipment->exists) {
                echo 'Nao existe = ' . $shipment->tracking_code . '<br/>';
                $shipment->fill($data);
                $shipment->reference            = 'TRK' . $shipment->tracking_code;
                $shipment->agency_id            = $agencyId;
                $shipment->sender_agency_id     = $agencyId;
                $shipment->recipient_agency_id  = $agencyId;
                $shipment->customer_id          = $customerId;
                $shipment->requested_by         = $customerId;
                $shipment->total_price          = 0;
                $shipment->cost_price           = 0;
                $shipment->total_expenses       = $shipment->charge_price ? '1' : null;
                $shipment->recipient_id         = null;
                $shipment->provider_id          = $providerId;
                $shipment->volumes              = $shipment->volumes ? $shipment->volumes : 0;
                $shipment->service_id           = $serviceId;
                $shipment->department_id        = null;
                $shipment->operator_id          = null;
                $shipment->pickup_operator_id   = null;
                $shipment->obs_internal         = '';
                $shipment->status_id            = 15;
                $shipment->is_blocked           = 0;
                $shipment->ignore_billing       = 0;
                $shipment->price_fixed          = 0;
                $shipment->vehicle              = null;
                $shipment->is_printed           = 0;
                $shipment->conferred            = 0;
                $shipment->invoice_id           = null;
                $shipment->invoice_type         = null;
                $shipment->invoice_draft        = 0;
                $shipment->invoice_key          = null;
                $shipment->parent_tracking_code = $data['collection_tracking_code'] ? $data['collection_tracking_code'] : null;
                $shipment->type                 = $data['collection_tracking_code'] ? 'P' : null;
                $shipment->tracking_code        = null;
                $shipment->setTrackingCode();
            } else {
                echo 'Existe = ' . $shipment->tracking_code . '<br/>';
            }
        }
    }

    /**
     * Transcapital
     */
    public function transcapital($date)
    {

        $url = 'https://transcapital.pt/core/sync/platform?date=' . $date;
        $contents = json_decode(file_get_contents($url));

        foreach ($contents as $content) {

            $shipment = Shipment::firstOrNew([
                'provider_tracking_code' => $content->provider_tracking_code
            ]);


            $data = (array) $content;

            $agencyId   = 140;
            $customerId = 632;
            $providerId = 2;
            $services = [
                '1' => '1', //10h
                '2' => '2', //14h
                '3' => '3', //24h
                '4' => '4', //72h
                '5' => '5', //int terrestre
                '6' => '5', //int aereo
            ];

            $serviceId = @$services[@$data['service_id']];

            if (!$shipment->exists) {
                echo 'Nao existe = ' . $shipment->tracking_code . '<br/>';
                unset($data['packaging_type']);
                $shipment->fill($data);
                $shipment->reference            = 'TRK' . $shipment->tracking_code;
                $shipment->agency_id            = $agencyId;
                $shipment->sender_agency_id     = $agencyId;
                $shipment->recipient_agency_id  = $agencyId;
                $shipment->customer_id          = $customerId;
                $shipment->requested_by         = $customerId;
                $shipment->total_price          = 0;
                $shipment->cost_price           = 0;
                $shipment->total_expenses       = $shipment->charge_price ? '1' : null;
                $shipment->recipient_id         = null;
                $shipment->provider_id          = $providerId;
                $shipment->volumes              = $shipment->volumes ? $shipment->volumes : 0;
                $shipment->service_id           = $serviceId;
                $shipment->department_id        = null;
                $shipment->operator_id          = null;
                $shipment->pickup_operator_id   = null;
                $shipment->obs_internal         = '';
                $shipment->status_id            = 15;
                $shipment->is_blocked           = 0;
                $shipment->ignore_billing       = 0;
                $shipment->price_fixed          = 0;
                $shipment->vehicle              = null;
                $shipment->is_printed           = 0;
                $shipment->conferred            = 0;
                $shipment->invoice_id           = null;
                $shipment->invoice_type         = null;
                $shipment->invoice_draft        = 0;
                $shipment->invoice_key          = null;
                $shipment->parent_tracking_code = $data['collection_tracking_code'] ? $data['collection_tracking_code'] : null;
                $shipment->type                 = $data['collection_tracking_code'] ? 'P' : null;
                $shipment->tracking_code        = null;
                $shipment->setTrackingCode();
            } else {
                echo 'Existe = ' . $shipment->tracking_code . '<br/>';
            }
        }
    }
}
