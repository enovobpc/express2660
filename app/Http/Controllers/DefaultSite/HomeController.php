<?php

namespace App\Http\Controllers\DefaultSite;

use App\Models\Customer;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShippingExpense;
use Illuminate\Http\Request;
use Setting;

class HomeController extends \App\Http\Controllers\Controller
{

    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    public $layout = 'layouts.default';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Login index controller
     *
     * @return \App\Http\Controllers\type
     */
    public function index() {

        $loginMode = strtolower(env('LOGIN_MODE'));
        $loginMode = $loginMode ? $loginMode : 'default';

        if($loginMode != 'default') {
            return view('default.login.'.$loginMode);
        }

        return $this->setContent('default.login.'.$loginMode);
    }

    /**
     * Login index controller
     *
     * @return \App\Http\Controllers\type
     */
    public function register() {
        $loginMode = strtolower(env('LOGIN_MODE'));
        $loginMode = $loginMode ? $loginMode : 'default';

        return $this->setContent('default.register.'.$loginMode);
    }

    /**
     * Return customer recipient details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function budget(Request $request) {

        $customer = Customer::filterSource()->where('code', 'CFINAL')->first();

        $customerId = $customer->id;
        if($customer->customer_id) {
            $customerId = $customer->customer_id;
        }

        $chargePrice = $request->get('charge');
        $chargePrice = $chargePrice == 'false' ? null : $chargePrice;

        $providerId = Setting::get('shipment_default_provider');
        $providerId = empty($providerId) ? 1 : $providerId;

        if($request->get('service')) {
            $service = Service::whereId($request->get('service', 8))->first();
        } else {
            $service = Service::whereCode('24H')->first();
        }

        $isCollection = false;
        if($request->get('sender_country') != Setting::get('app_country') && $request->get('recipient_country') == 'pt') {
            $isCollection = true;
        }

        $billingZone    = Shipment::getBillingCountry($request->get('sender_country', Setting::get('app_country')), $request->get('recipient_country', Setting::get('app_country')), $isCollection);
        $billingZipCode = Shipment::getBillingZipCode($request->get('sender_zip_code'), $request->get('recipient_zip_code'), $isCollection);

        $allExpenses = ShippingExpense::filterSource()->get(['id', 'code', 'name', 'price', 'zones', 'type']);

        $volumes = $request->get('volumes', 1);
        $weight  = $request->get('weight', 1);
        $fatorM3 = $request->get('fatorM3', 0);

        $tmpShipment = new Shipment([
            'agency_id'          => $customer->agency_id,
            'service_id'         => $service->id,
            'customer_id'        => $customerId,
            'provider_id'        => $providerId,
            'weight'             => $weight,
            'volumes'            => $volumes,
            'volume_m3'          => $request->get('volumeM3', 0),
            'fator_m3'           => $fatorM3,
            'sender_zip_code'    => $request->sender_zip_code,
            'recipient_zip_code' => $request->recipient_zip_code,
            'sender_country'     => $request->sender_country,
            'recipient_country'  => $request->recipient_country,
            'kms'                => $request->get('kms', 0),
            'extra_fields'       => $request->extra_fields,
            'hours'              => $request->hours,
            'cod'                => $request->cod,
            'ldm'                => $request->ldm,
            'goods_value'        => $request->goods_value,
            'packs'              => $request->packs
        ]);

        $prices = Shipment::calcPrices($tmpShipment);
        $basePrice = $prices['total'];

        $calcExpensesData = [
            'customer'  => $customer->id,
            'zone'      => $billingZone,
            'volumes'   => $volumes,
            'weight'    => $weight,
            'fatorM3'   => $fatorM3,
            'basePrice' => $basePrice
        ];

        //CHARGE PRICE
        if(!empty($chargePrice)) {
            $allExpenses   = ShippingExpense::filterSource()->get(['id', 'code', 'name', 'price', 'zones', 'type']);
            $chargeExpense = Shipment::getChargeExpense($allExpenses, $request->get('service'));
            //$chargePrice   = ShippingExpense::getBudget($chargeExpense, $calcExpensesData);
        } else {
            $chargePrice = 0;
        }

        $price = $prices['total'] + $chargePrice;

        //FUEL TAX
        $fuelTax = $prices['fuel_tax'];;
        $fuelTaxValue = Setting::get('fuel_tax_budgets') ? Setting::get('fuel_tax_budgets') : $fuelTax;
        if(!empty($fuelTaxValue)) {
            $fuelTax = $price * ($fuelTaxValue / 100);
        }

        //PICKUP
        $pickupPrice = 0;
        $hasPickup = $request->get('pickup');
        $hasPickup = $hasPickup == 'false' ? false : $hasPickup;

        if($hasPickup) {

            if(empty($service->assigned_service_id)) {
                $pickupService = Service::filterSource()->where('code', 'REC24')->first();
            } else {
                $pickupService = Service::find($service->assigned_service_id);
            }

            $weight   = $request->get('weight', 0);
            $volumes  = $request->get('volumes', 1);
            $volumeM3 = $request->get('volumeM3', 0);
            $fatorM3  = $request->get('fatorM3', 0);
            $kms      = $request->get('kms', 0);

            $tmpShipment = new Shipment([
                'agency_id'          => $customer->agency_id,
                'service_id'         => @$pickupService->id,
                'customer_id'        => $customerId,
                'provider_id'        => $providerId,
                'weight'             => $weight,
                'volumes'            => $volumes,
                'volume_m3'          => $volumeM3,
                'fator_m3'           => $fatorM3,
                'sender_zip_code'    => $request->sender_zip_code,
                'recipient_zip_code' => $request->recipient_zip_code,
                'sender_country'     => $request->sender_country,
                'recipient_country'  => $request->recipient_country,
                'kms'                => $kms,
                'extra_fields'       => $request->extra_fields,
                'hours'              => $request->hours,
                'cod'                => $request->cod,
                'ldm'                => $request->ldm,
                'goods_value'        => $request->goods_value,
                'packs'              => $request->packs
            ]);

            $pickupPrice = Shipment::calcPrices($tmpShipment);
            $pickupPrice = $pickupPrice['total'];
        }

        //RGUIDE
        $rguidePrice = 0;
        $hasRguide = $request->get('rguide');
        $hasRguide = $hasRguide == 'false' ? false : $hasRguide;
        if($hasRguide) {
            $rguideExpense = Shipment::getExpenseId($allExpenses, 'rguide');
            $rguidePrice   = ShippingExpense::getBudget($rguideExpense, $calcExpensesData);
            $price+= $rguidePrice;
        }

        //OUT OF STANDARD
        $outStandardPrice = 0;
        $hasOutStandard = $request->get('outstandard');
        $hasOutStandard = $hasOutStandard == 'false' ? false : $hasOutStandard;
        if($hasOutStandard) {
            $outStandardExpense = Shipment::getOutOfStandardExpense($allExpenses);
            $outStandardPrice   = ShippingExpense::getBudget($outStandardExpense, $calcExpensesData);
            $price+= $outStandardPrice;
        }


        $hasAdicionalService = false;
        if($chargePrice > 0.00 || $pickupPrice > 0.00 || $rguidePrice > 0.00 || $outStandardPrice > 0.00 || $fuelTax > 0.00) {
            $hasAdicionalService = true;
        }

        $priceVat = $price * (1 + (Setting::get('vat_rate_normal') /100));

        $result = [
            'hasAdicionalService' => $hasAdicionalService,
            'basePrice'         => money($basePrice, Setting::get('app_currency')),
            'price'             => money($price, Setting::get('app_currency')),
            'priceVat'          => money($priceVat, Setting::get('app_currency')),
            'vatRate'           => Setting::get('vat_rate_normal'),
            'charge'            => money($chargePrice, Setting::get('app_currency')),
            'hasCharge'         => $chargePrice > 0.00 ? true : false,
            'volumetricWeight'  => $prices['volumetricWeight'],
            'fuelTax'           => money($fuelTax, Setting::get('app_currency')),
            'hasFuelTax'        => $fuelTax > 0.00 ? true : false,
            'pickup'            => money($pickupPrice, Setting::get('app_currency')),
            'hasPickup'         => $pickupPrice > 0.00 ? true : false,
            'rguide'            => money($rguidePrice, Setting::get('app_currency')),
            'hasRguide'         => $rguidePrice > 0.00 ? true : false,
            'outStandard'       => money($outStandardPrice, Setting::get('app_currency')),
            'hasOutStandard'    => $outStandardPrice > 0.00 ? true : false,
            'isParticular'      => @$customer->is_particular ? 1 : 0,
            'serviceName'       => $service->name,
            'serviceDescription'=> $service->description
        ];

        return $result;
    }
}