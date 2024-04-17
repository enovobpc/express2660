<?php

namespace App\Providers;

use App\Models\Agency;
use App\Models\CalendarEvent;
use App\Models\Cashier\Movement;
use App\Models\ChangeLog;
use App\Models\Customer;
use App\Models\CustomerEcommerceGateway;
use App\Models\CustomerWebservice;
use App\Models\Logistic\ProductLocation;
use App\Models\Logistic\ShippingOrder;
use App\Models\Logistic\ShippingOrderLine;
use App\Models\Trip\Trip;
use App\Models\Trip\TripShipment;
use App\Models\FileRepository;
use App\Models\FleetGest\Cost;
use App\Models\FleetGest\Expense;
use App\Models\FleetGest\FuelLog;
use App\Models\FleetGest\Incidence;
use App\Models\FleetGest\Maintenance;
use App\Models\FleetGest\Provider;
use App\Models\FleetGest\TollLog;
use App\Models\FleetGest\Vehicle;
use App\Models\FleetGest\VehicleHistory;
use App\Models\Invoice;
use App\Models\ProductSale;
use App\Models\PurchaseInvoice;
use App\Models\RefundControl;
use App\Models\RefundControlAgency;
use App\Models\Service;
use App\Models\ShipmentExpense;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use View, Auth, Setting, App, File, Image;
use Illuminate\Support\ServiceProvider;
use App\Models\ShipmentHistory;
use App\Models\Shipment;
use App\Models\CustomerType;
use App\Models\Product;
use App\Models\ShippingExpense;
use App\Models\Provider as ShippingProvider;
use App\Models\Trip\TripHistory;
use App\Models\Trip\TripVehicle;
use Webklex\PHPIMAP\Attachment;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        require resource_path() . '/helpers/macros.php';
        require resource_path() . '/helpers/functions.php';

        App\Models\Sms\Sms::created(function ($sms) {
            $pack = App\Models\Sms\Pack::filterSource()
                ->where('is_active', 1)
                ->orderBy('remaining_sms', 'asc')
                ->first();

            if ($pack) {
                $pack->remaining_sms = $pack->remaining_sms - $sms->sms_parts;
                $pack->save();
            }
        });

        ShippingProvider::creating(function ($row) {
            $row->source = config('app.source');
        });

        Service::creating(function ($row) {
            $row->source = config('app.source');
        });

        Customer::creating(function ($customer) {
            $customer->source = config('app.source');

            $agency = Agency::find($customer->agency_id);
            $customer->company_id = @$agency->company_id;
        });

        Customer::created(function ($customer) {

            $webservicesConfig = WebserviceConfig::filterSource()
                ->where(function ($q) use ($customer) {
                    $q->whereNull('agency_id');
                    $q->orWhere('agency_id', $customer->agency_id);
                })
                ->where('auto_enable', 1)
                ->get();

            foreach ($webservicesConfig as $config) {
                $webservice = new CustomerWebservice();
                $webservice->fill($config->toArray());
                $webservice->customer_id = $customer->id;
                $webservice->save();
            }

            if ($webservicesConfig->count()) {
                $customer->has_webservices = true;

                if (empty($customer->code)) {
                    $customer->code = 'N/A';
                }

                $customer->save();
            }
        });


        Customer::updating(function ($customer) {
            $agency = Agency::find($customer->agency_id);
            $customer->company_id = @$agency->company_id;
        });

        Customer::updated(function ($row) {

            $ignoredFields = ['updated_at', 'view_parent_shipments', 'uncrypted_password'];
            $changes = ChangeLog::getChanges($row, $ignoredFields);

            if (isset($changes['new']['submited_at'])) {
                $changes['new']['submited_at'] = $changes['new']['submited_at']->format('Y-m-d H:i:s');
            }

            if (isset($changes['new']['password']) || isset($changes['old']['password'])) {
                $changes['old']['password'] = '******';
                $changes['new']['password'] = '******';
            }

            if (!empty($changes)) {
                $log = new ChangeLog();
                $log->action    = 'update';
                $log->source    = 'Customer';
                $log->source_id = @$row->id;
                $log->old       = $changes['old'];
                $log->new       = $changes['new'];

                if (Auth::check()) {
                    $log->user_id = Auth::user()->id;
                }

                if (Auth::guard('customer')->user()) {
                    $log->customer_id = Auth::guard('customer')->user()->id;
                }

                $log->save();
            }
        });

        Customer::deleting(function ($row) {
            $log = new ChangeLog();
            $log->action    = 'deleted';
            $log->source    = 'Customer';
            $log->source_id = @$row->id;
            $log->new       = ['deleted_at' => date('Y-m-d H:i:s')];

            if (Auth::check()) {
                $log->user_id = Auth::user()->id;
            }

            if (Auth::guard('customer')->user()) {
                $log->customer_id = Auth::guard('customer')->user()->id;
            }

            $log->save();
        });


        App\Models\User::created(function ($row) {
            $ignoredFields = [
                'updated_at', 'remember_token', 'api_token', 'location_lat', 'location_lng',
                'last_login', 'ip', 'uncrypted_password'
            ];
            $changes = ChangeLog::getChanges($row, $ignoredFields);

            if (isset($changes['new']['password'])) {
                $changes['old']['password'] = '******';
                $changes['new']['password'] = '******';
            };

            $source = config('app.source');

            //regista na base de dados central
            if ($row->source) {
                $userAuth = App\Models\Core\UserAuth::firstOrNew([
                    'source'    => $source,
                    'source_id' => $row->id
                ]);

                $userAuth->source = $source;
                $userAuth->fill($row->toArray());
                $userAuth->password = $row->password;
                $userAuth->is_active = $row->active;
                $userAuth->save();
            }

            if (!empty($changes)) {
                $log = new ChangeLog();
                $log->action    = 'update';
                $log->source    = 'User';
                $log->source_id = @$row->id;
                $log->old       = $changes['old'];
                $log->new       = $changes['new'];

                if (Auth::check()) {
                    $log->user_id = Auth::user()->id;
                }

                $log->save();
            }
        });

        App\Models\User::updated(function ($row) {

            $source = config('app.source');

            $ignoredFields = [
                'updated_at', 'remember_token', 'api_token', 'location_lat', 'location_lng',
                'last_login', 'ip', 'uncrypted_password'
            ];
            $changes = ChangeLog::getChanges($row, $ignoredFields);

            if (isset($changes['new']['password'])) { //mudou password
                $changes['old']['password'] = '******';
                $changes['new']['password'] = '******';
            };

            //regista na base de dados central
            if ($row->source) {
                $userAuth = App\Models\Core\UserAuth::firstOrNew([
                    'source'    => $source,
                    'source_id' => $row->id
                ]);

                $userAuth->source = $source;
                $userAuth->fill($row->toArray());
                $userAuth->password = $row->password;
                $userAuth->is_active = $row->active;
                $userAuth->save();
            }


            if (!empty($changes)) {
                $log = new ChangeLog();
                $log->action    = 'update';
                $log->source    = 'User';
                $log->source_id = @$row->id;
                $log->old       = $changes['old'];
                $log->new       = $changes['new'];

                if (Auth::check()) {
                    $log->user_id = Auth::user()->id;
                }

                $log->save();
            }
        });

        App\Models\User::deleting(function ($row) {

            $source = config('app.source');

            //regista na base de dados central
            $userAuth = App\Models\Core\UserAuth::firstOrNew([
                'source'    => $source,
                'source_id' => $row->id
            ]);
            $userAuth->deleted_at = date('Y-m-d H:i:s');
            $userAuth->save();

            $log = new ChangeLog();
            $log->action    = 'deleted';
            $log->source    = 'User';
            $log->source_id = @$row->id;
            $log->new       = ['deleted_at' => date('Y-m-d H:i:s')];

            if (Auth::check()) {
                $log->user_id = Auth::user()->id;
            }

            $log->save();
        });

        ShipmentHistory::creating(function ($history) {
            if (Auth::check()) {
                $history->user_id = Auth::user()->id;
            }

            $now = date('Y-m-d H:i:s');
            $shipment = Shipment::find($history->shipment_id);

            if ($history->status_id == App\Models\ShippingStatus::DELIVERED_ID) {
                Shipment::where('id', $history->shipment_id)
                    ->update(['delivered_date' => $now]);

                //atualiza histórico de viagem se o envio tiver uma viagem atribuida
                //ignora se webservice sincronizados com redes 
                if(!$shipment->webservice_method && $shipment->trip_id) {
                    $tripHistory = TripHistory::firstOrNew([
                        'trip_id' => $shipment->trip_id,
                        'action'  => 'delivery',
                        'target'  => 'Shipment',
                        'target_id' => $shipment->id
                    ]);

                    $tripHistory->date = $history->created_at;
                    $tripHistory->obs  = 'Carga '.$shipment->tracking_code.' entregue';
                    $tripHistory->save();
                }

            } elseif ($history->status_id == App\Models\ShippingStatus::IN_DISTRIBUTION_ID || $history->status_id == App\Models\ShippingStatus::IN_TRANSPORTATION_ID) {
                Shipment::where('id', $history->shipment_id)
                    ->update(['distribution_date' => $now]);
            } elseif ($history->status_id == App\Models\ShippingStatus::INCIDENCE_ID) {
                Shipment::where('id', $history->shipment_id)
                    ->update(['incidence_date' => $now]);
            } elseif ($history->status_id == App\Models\ShippingStatus::SHIPMENT_PICKUPED) {
                Shipment::where('id', $history->shipment_id)
                    ->update(['pickuped_date' => $now]);

                //atualiza histórico de viagem se o envio tiver uma viagem atribuida
                //ignora se webservice sincronizados com redes 
                if(!$shipment->webservice_method && $shipment->trip_id) {
                    $tripHistory = TripHistory::firstOrNew([
                        'trip_id' => $shipment->trip_id,
                        'action'  => 'pickup',
                        'target'  => 'Shipment',
                        'target_id' => $shipment->id
                    ]);

                    $tripHistory->date = $history->created_at;
                    $tripHistory->obs  = 'Carga '.$shipment->tracking_code.' recolhida';
                    $tripHistory->save();
                }

            } elseif ($history->status_id == App\Models\ShippingStatus::INBOUND_ID) {
                Shipment::where('id', $history->shipment_id)
                    ->update(['inbound_date' => $now]);
            }

            /**
             * Update customer ecommerce order
             */
            try {
                if ($shipment && $shipment->ecommerce_gateway_id && $shipment->ecommerce_gateway_order_code) {
                    $customerEcommerceGateway = CustomerEcommerceGateway::find($shipment->ecommerce_gateway_id);
                    
                    if ($customerEcommerceGateway) {
                        $gateway = new \App\Models\EcommerceGateway\Base($customerEcommerceGateway);
    
                        if ($history->status_id == ShippingStatus::DELIVERED_ID
                            && @$customerEcommerceGateway->settings['status']['delivered']) {
                            $gateway->updateOrderStatus(
                                $shipment->ecommerce_gateway_order_code,
                                $customerEcommerceGateway->settings['status']['delivered'],
                                $shipment->ecommerce_gateway_fullfillment_code
                            );
                        } elseif ($history->status_id == ShippingStatus::IN_DISTRIBUTION_ID
                            && @$customerEcommerceGateway->settings['status']['in_distribution']) {
                            $gateway->updateOrderStatus(
                                $shipment->ecommerce_gateway_order_code,
                                $customerEcommerceGateway->settings['status']['in_distribution'],
                                $shipment->ecommerce_gateway_fullfillment_code
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error($e);
            }
            /**-- */


            if ($history->filepath != null) {

                $infoPath = pathinfo(public_path($history->filepath));

                $attachment = new FileRepository();
                $attachment->parent_id      = FileRepository::FOLDER_SHIPMENTS;
                $attachment->source_class   = 'Shipment';
                $attachment->source_id      = $history->shipment_id;
                $attachment->user_id        = $history->user_id;
                $attachment->name           = 'POD Entrega';
                $attachment->extension      = $infoPath['extension'];
                $attachment->filename       = $history->filename;
                $attachment->filepath       = $history->filepath;
                $attachment->customer_visible = 1;
                $attachment->save();
            }
        });

        ShipmentHistory::deleting(function ($history) {
            if ($history->filepath != null) {
                $attachment = FileRepository::where('filepath', $history->filepath)
                    ->where('source_class', 'Shipment')
                    ->where('filename', $history->filename)
                    ->where('source_id', $history->shipment_id)
                    ->first();

                if ($attachment) {
                    $attachment->delete();
                }
            }
        });

        Shipment::creating(function ($shipment) {
            if (Auth::check()) {
                $shipment->created_by   = Auth::user()->id;
            }

            if (empty($shipment->shipping_date)) {
                $hour = $shipment->start_hour ? $shipment->start_hour . ':00' : '00:00:00';
                $shipment->shipping_date = $shipment->date . ' ' . $hour;
            }

            $shipment->billing_date = $shipment->date;

            $shipment->taxable_weight = $shipment->weight > $shipment->volumetric_weight ? $shipment->weight : $shipment->volumetric_weight;

            //check vat rate
            if (is_null($shipment->vat_rate)) {
                $vatRate = $shipment->getVatRate();
                $shipment->vat_rate_id  = $vatRate['id'];
                $shipment->vat_rate     = $vatRate['value'];
            }

            //set prices billing
            $shipment->billing_subtotal     = (float) $shipment->shipping_price + (float) $shipment->expenses_price + (float) $shipment->fuel_price;
            $shipment->billing_vat          = $shipment->billing_vat ? $shipment->billing_vat : $shipment->billing_subtotal * ((float) $shipment->vat_rate / 100);
            $shipment->billing_total        = $shipment->billing_subtotal + $shipment->billing_vat;
            $shipment->billing_zone         = $shipment->zone;
            $shipment->billing_item         = $shipment->getBillingItem();

            //set prices cost
            $shipment->cost_shipping_price   = $shipment->cost_shipping_price ? $shipment->cost_shipping_price : $shipment->cost_price;
            $shipment->cost_expenses_price   = $shipment->cost_expenses_price ? $shipment->cost_expenses_price : $shipment->total_expenses_cost;

            $shipment->cost_billing_subtotal = (float) @$shipment->cost_shipping_price + (float) @$shipment->cost_expenses_price + (float) @$shipment->cost_fuel_price;
            $shipment->cost_billing_vat      = $shipment->cost_billing_vat ? $shipment->cost_billing_vat : @$shipment->cost_billing_subtotal * ((float) @$shipment->vat_rate / 100);
            $shipment->cost_billing_total    = @$shipment->cost_billing_subtotal + @$shipment->cost_billing_vat;
            $shipment->cost_billing_zone     = $shipment->cost_billing_zone ? $shipment->cost_billing_zone : $shipment->zone;

            $shipment->payment_at_recipient      = 0;
            $shipment->total_price_for_recipient = 0;
            if($shipment->cod) {
                $shipment->payment_at_recipient = 1;
                $shipment->total_price_for_recipient = $shipment->billing_subtotal;
            }
        });

        Shipment::updating(function ($shipment) {

            if (!empty($shipment->date)) {
                $hour = $shipment->start_hour ? $shipment->start_hour . ':00' : '00:00:00';
                $shipment->shipping_date = $shipment->date . ' ' . $hour;

                if (empty($shipment->billing_date) || $shipment->billing_date == '0000-00-00') {
                    $shipment->billing_date = $shipment->date;
                }
            }

            $shipment->taxable_weight = $shipment->weight > $shipment->volumetric_weight ? $shipment->weight : $shipment->volumetric_weight;

            //check vat rate
            if (is_null($shipment->vat_rate)) {
                $vatRate = $shipment->getVatRate();
                $shipment->vat_rate_id  = $vatRate['id'];
                $shipment->vat_rate     = $vatRate['value'];
            }

            $shipment->billing_zone         = $shipment->billing_zone ? $shipment->billing_zone : $shipment->zone;
            $shipment->billing_item         = $shipment->billing_item ? $shipment->billing_item : $shipment->getBillingItem();
            $shipment->billing_subtotal     = (float) $shipment->shipping_price + (float) $shipment->expenses_price + (float) $shipment->fuel_price;
            $shipment->billing_vat          = $shipment->billing_vat ? $shipment->billing_vat : $shipment->billing_subtotal * ((float) $shipment->vat_rate / 100);
            $shipment->billing_total        = $shipment->billing_subtotal + $shipment->billing_vat;

            //set prices cost
            $shipment->cost_billing_subtotal = (float) $shipment->cost_shipping_price + (float) $shipment->cost_expenses_price + (float) @$shipment->cost_fuel_price;
            $shipment->cost_billing_vat      = $shipment->cost_billing_vat ? $shipment->cost_billing_vat : $shipment->cost_billing_subtotal * ((float) $shipment->vat_rate / 100);
            $shipment->cost_billing_total    = $shipment->cost_billing_subtotal + $shipment->cost_billing_vat;
            $shipment->cost_billing_zone     = $shipment->cost_billing_zone ? $shipment->cost_billing_zone : $shipment->zone;

            $shipment->payment_at_recipient      = 0;
            $shipment->total_price_for_recipient = 0;
            if($shipment->cod) {
                $shipment->payment_at_recipient = 1;
                $shipment->total_price_for_recipient = $shipment->billing_subtotal;
            }

            if ($shipment->has_assembly) {
                $tags = (array) $shipment->tags;
                if (!in_array('assembly', $tags)) {
                    $tags[] = 'assembly'; //força a ter a tag assembly
                    $shipment->tags = array_values($tags);
                }
            }

        });

        Shipment::updated(function ($row) {

            $ignoredFields = ['updated_at', 'remember_token'];
            $changes = ChangeLog::getChanges($row, $ignoredFields);

            if (isset($changes['new']['submited_at'])) {
                $changes['new']['submited_at'] = $changes['new']['submited_at']->format('Y-m-d H:i:s');
            };


            if (!empty($changes)) {
                $log = new ChangeLog();
                $log->action    = 'update';
                $log->source    = 'Shipment';
                $log->source_id = @$row->id;
                $log->old       = $changes['old'];
                $log->new       = $changes['new'];

                if (Auth::check()) {
                    $log->user_id = Auth::user()->id;
                }

                if (Auth::guard('customer')->user()) {
                    $log->customer_id = Auth::guard('customer')->user()->id;
                }

                if (Auth::guard('api')->user()) {
                    $log->customer_id = Auth::guard('api')->user()->id;
                    $log->is_api      = 1;
                }

                $log->save();
            }
        });

        Shipment::deleting(function ($row) {
            $log = new ChangeLog();
            $log->action    = 'deleted';
            $log->source    = 'Shipment';
            $log->source_id = @$row->id;
            $log->new       = ['deleted_at' => date('Y-m-d H:i:s')];

            if (Auth::check()) {
                $log->user_id = Auth::user()->id;
            }

            if (Auth::guard('customer')->user()) {
                $log->customer_id = Auth::guard('customer')->user()->id;
            }

            if (Auth::guard('api')->user()) {
                $log->customer_id = Auth::guard('api')->user()->id;
                $log->is_api      = 1;
            }

            $log->save();
        });

        ShipmentExpense::creating(function ($shipment) {
            if (Auth::check()) {
                $shipment->created_by = Auth::user()->id;
            }
        });

        Trip::creating(function ($row) {

            $totalKms = null;
            if ($row->end_kms > 0.00) {
                $totalKms = (float) $row->end_kms - $row->start_kms;
                $totalKms = $totalKms == 0.00 ? null : $totalKms;
            }
            $row->kms = $totalKms;

            $pickupDate = null;
            if ($row->start_date) {
                $pickupDate = $row->start_date;
                if(!empty($row->start_hour)) {
                    $pickupDate.= ' '.$row->start_hour.':00';
                }
            }

            $deliveryDate = null;
            if ($row->end_date) {
                $deliveryDate = $row->end_date;
                if(!empty($row->end_hour)) {
                    $deliveryDate.= ' '.$row->end_hour.':00';
                }
            }

            $row->pickup_date   = $pickupDate;
            $row->delivery_date = $deliveryDate;
        });

        Trip::created(function ($row) {
            //cria historico da viatura mais recente
            $row->newVehicleHistory();
        });

        Trip::updating(function ($row) {
            $totalKms = null;
            if ($row->end_kms > 0.00) {
                $totalKms = (float) $row->end_kms - $row->start_kms;
                $totalKms = $totalKms == 0.00 ? null : $totalKms;
            }
            $row->kms = $totalKms;

            $pickupDate = null;
            if ($row->start_date) {
                $pickupDate = $row->start_date;
                if(!empty($row->start_hour)) {
                    $pickupDate.= ' '.$row->start_hour.':00';
                }
            }

            $deliveryDate = null;
            if ($row->end_date) {
                $deliveryDate = $row->end_date;
                if(!empty($row->end_hour)) {
                    $deliveryDate.= ' '.$row->end_hour.':00';
                }
            }

            $row->pickup_date   = $pickupDate;
            $row->delivery_date = $deliveryDate;
        });

        Trip::updated(function ($row) {

            $changedFields = array_keys($row->getDirty());

            if(!empty(array_intersect($changedFields, ['vehicle', 'trailer', 'operator_id']))) { //se campos foram alterados, cria novo registo
                $row->newVehicleHistory(); //Grava novo historico de veículo 
            } else {
                $row->updateVehicleHistory(); //atualiza historico da viatura mais recente
            }

            $ignoredFields = ['updated_at'];
            $changes = ChangeLog::getChanges($row, $ignoredFields);

            if (!empty($changes)) {
                $log = new ChangeLog();
                $log->action    = 'update';
                $log->source    = 'Trip';
                $log->source_id = @$row->id;
                $log->old       = $changes['old'];
                $log->new       = $changes['new'];

                if (Auth::check()) {
                    $log->user_id = Auth::user()->id;
                }

                $log->save();
            }
        });

        Trip::deleting(function ($row) {

            $log = new ChangeLog();
            $log->action    = 'deleted';
            $log->source    = 'Trip';
            $log->source_id = @$row->id;
            $log->new       = ['deleted_at' => date('Y-m-d H:i:s')];

            if (Auth::check()) {
                $log->user_id = Auth::user()->id;
            }

            $log->save();
        });


        TripShipment::deleted(function ($row) {

            $trip = Trip::find($row->trip_id);

            $allShipments = $trip->shipments->get(['sender_country', 'recipient_country']);

            $allCountries = array_merge(array_unique($allShipments->pluck('sender_country')->toArray()), array_unique($allShipments->pluck('recipient_country')->toArray()));

            $isNacional = $isSpain = $isInternacional = 0;
            if (count($allCountries) == 1 && @$allCountries[0] == 'pt') {
                $isNacional = 1;
            }

            if (in_array('es', $allCountries)) {
                $isSpain = 1;
            }

            if (count($allCountries) > 1) {
                $isInternacional = 1;
            }

            $trip->is_nacional      = $isNacional;
            $trip->is_spain         = $isSpain;
            $trip->is_internacional = $isInternacional;
            $trip->save();
        });

        RefundControl::creating(function ($row) {

            $userId = Auth::check() ? Auth::user()->id : null;

            if (!empty($row->received_method)) {
                $row->received_user_id = $userId;
            }

            if (!empty($row->payment_method)) {
                $row->payment_user_id = $userId;
            }
        });

        RefundControl::updating(function ($row) {

            $userId  = Auth::check() ? Auth::user()->id : null;
            $changes = ChangeLog::getChanges($row);

            if (!empty($row->received_method) && @$changes['old']['received_method'] != @$changes['new']['received_method']) {
                $row->received_user_id = $userId;
            }

            if (!empty($row->payment_method) && @$changes['old']['payment_method'] != @$changes['new']['payment_method']) {
                $row->payment_user_id = $userId;
            }
        });

        RefundControl::created(function ($row) {

            $ignoredFields = ['updated_at', 'paid', 'id', 'shipment_id', 'created_at'];
            $changes       = ChangeLog::getChanges($row, $ignoredFields);

            if (!empty($changes)) {
                $log = new ChangeLog();
                $log->action    = 'update';
                $log->source    = 'RefundControl';
                $log->source_id = $row->shipment_id;
                $log->old       = $changes['old'];
                $log->new       = $changes['new'];
                $log->user_id   = Auth::user()->id;
                $log->save();
            }
        });

        RefundControl::updated(function ($row) {

            $ignoredFields = ['updated_at', 'paid'];
            $changes       = ChangeLog::getChanges($row, $ignoredFields);

            if (!empty($changes)) {
                $log = new ChangeLog();
                $log->action    = 'update';
                $log->source    = 'RefundControl';
                $log->source_id = $row->shipment_id;
                $log->old       = $changes['old'];
                $log->new       = $changes['new'];

                if (Auth::check()) {
                    $log->user_id = Auth::user()->id;
                }

                if (Auth::guard('customer')->user()) {
                    $log->customer_id = Auth::guard('customer')->user()->id;
                }

                $log->save();
            }
        });


        Movement::created(function ($row) {

            $ignoredFields = ['updated_at', 'created_at', 'source', 'id'];
            $changes = ChangeLog::getChanges($row, $ignoredFields);

            if (!empty($changes)) {
                $log = new ChangeLog();
                $log->action    = 'update';
                $log->source    = 'CashierMovement';
                $log->source_id = $row->id;
                $log->old       = $changes['old'];
                $log->new       = $changes['new'];
                $log->user_id   = Auth::user()->id;
                $log->save();
            }
        });

        Movement::updated(function ($row) {

            $ignoredFields = ['updated_at', 'created_at', 'source', 'id'];
            $changes = ChangeLog::getChanges($row, $ignoredFields);

            if (!empty($changes)) {
                $log = new ChangeLog();
                $log->action    = 'update';
                $log->source    = 'CashierMovement';
                $log->source_id = $row->id;
                $log->old       = $changes['old'];
                $log->new       = $changes['new'];
                $log->user_id   = Auth::user()->id;
                $log->save();

                if (Auth::check()) {
                    $log->user_id = Auth::user()->id;
                }

                if (Auth::guard('customer')->user()) {
                    $log->customer_id = Auth::guard('customer')->user()->id;
                }
            }
        });

        Movement::deleting(function ($row) {
            $log = new ChangeLog();
            $log->action    = 'deleted';
            $log->source    = 'CashierMovement';
            $log->source_id = @$row->id;
            $log->new       = ['deleted_at' => date('Y-m-d H:i:s')];
            $log->user_id   = Auth::user()->id;
            $log->save();
        });

        RefundControlAgency::created(function ($row) {

            $ignoredFields = ['updated_at', 'paid', 'id', 'shipment_id', 'created_at'];
            $changes = ChangeLog::getChanges($row, $ignoredFields);

            if (!empty($changes)) {
                $log = new ChangeLog();
                $log->action    = 'update';
                $log->source    = 'RefundControlAgency';
                $log->source_id = $row->shipment_id;
                $log->old       = $changes['old'];
                $log->new       = $changes['new'];
                $log->user_id   = Auth::user()->id;
                $log->save();
            }
        });

        RefundControlAgency::updated(function ($row) {

            $ignoredFields = ['updated_at', 'paid'];
            $changes = ChangeLog::getChanges($row, $ignoredFields);

            if (!empty($changes)) {
                $log = new ChangeLog();
                $log->action    = 'update';
                $log->source    = 'RefundControlAgency';
                $log->source_id = $row->shipment_id;
                $log->old       = $changes['old'];
                $log->new       = $changes['new'];
                $log->save();

                if (Auth::check()) {
                    $log->user_id = Auth::user()->id;
                }

                if (Auth::guard('customer')->user()) {
                    $log->customer_id = Auth::guard('customer')->user()->id;
                }
            }
        });

        CustomerType::creating(function ($type) {
            $type->source = config('app.source');
        });

        Product::creating(function ($type) {
            $type->source = config('app.source');
        });

        ShippingExpense::creating(function ($type) {
            $type->source = config('app.source');
        });

        Agency::creating(function ($type) {
            $type->source = config('app.source');
        });

        ProductSale::creating(function ($shipment) {
            if (Auth::check()) {
                $shipment->created_by = Auth::user()->id;
            }
        });

        /**
         * Fleet
         */
        Vehicle::creating(function ($row) {
            $row->source = config('app.source');
        });

        FuelLog::created(function ($row) {

            $row->created_by = $row->created_by ? $row->created_by : @Auth::user()->id;

            VehicleHistory::setOrUpdate(
                'gas_station',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->date,
                $row->km,
                $row->total,
                $row->type_id,
                $row->created_by
            );

            Cost::setOrUpdate(
                'FuelLog',
                'gas_station',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->assigned_invoice_id,
                $row->description,
                $row->date,
                $row->total,
                $row->type_id,
                $row->created_by
            );
        });

        FuelLog::updated(function ($row) {
            VehicleHistory::setOrUpdate(
                'gas_station',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->date,
                $row->km,
                $row->total,
                $row->type_id,
                $row->created_by
            );

            Cost::setOrUpdate(
                'FuelLog',
                'gas_station',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->assigned_invoice_id,
                $row->description,
                $row->date,
                $row->total,
                $row->type_id,
                $row->created_by
            );
        });

        FuelLog::deleting(function ($row) {
            VehicleHistory::remove(
                'gas_station',
                $row->id,
                $row->vehicle_id
            );

            Cost::remove(
                'FuelLog',
                $row->id,
                $row->vehicle_id
            );
        });

        Incidence::created(function ($row) {
            VehicleHistory::setOrUpdate(
                'incidence',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->date,
                $row->km,
                $row->total,
                $row->type_id,
                $row->created_by
            );
        });

        Incidence::updated(function ($row) {
            VehicleHistory::setOrUpdate(
                'incidence',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->date,
                $row->km,
                $row->total,
                $row->type_id,
                $row->created_by
            );
        });

        Incidence::deleting(function ($row) {
            VehicleHistory::remove(
                'incidence',
                $row->id,
                $row->vehicle_id
            );
        });

        Maintenance::created(function ($row) {

            $row->created_by = $row->created_by ? $row->created_by : @Auth::user()->id;

            VehicleHistory::setOrUpdate(
                'maintenance',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->date,
                $row->km,
                $row->total,
                $row->type_id,
                $row->created_by
            );

            Cost::setOrUpdate(
                'Maintenance',
                'maintenance',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->assigned_invoice_id,
                $row->description,
                $row->date,
                $row->total,
                $row->type_id,
                $row->created_by
            );
        });

        Maintenance::updated(function ($row) {

            $row->created_by = $row->created_by ? $row->created_by : @Auth::user()->id;

            VehicleHistory::setOrUpdate(
                'maintenance',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->date,
                $row->km,
                $row->total,
                $row->type_id
            );

            Cost::setOrUpdate(
                'Maintenance',
                'maintenance',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->assigned_invoice_id,
                $row->description,
                $row->date,
                $row->total,
                $row->type_id,
                $row->created_by
            );
        });

        Maintenance::deleting(function ($row) {
            VehicleHistory::remove(
                'maintenance',
                $row->id,
                $row->vehicle_id
            );

            Cost::remove(
                'Maintenance',
                $row->id,
                $row->vehicle_id
            );
        });

        Expense::created(function ($row) {

            $row->created_by = $row->created_by ? $row->created_by : @Auth::user()->id;

            VehicleHistory::setOrUpdate(
                'expense',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->date,
                $row->km,
                $row->total,
                $row->type_id,
                $row->created_by
            );

            Cost::setOrUpdate(
                'Expense',
                'expense',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->assigned_invoice_id,
                $row->description,
                $row->date,
                $row->total,
                $row->type_id,
                $row->created_by
            );
        });

        Expense::updated(function ($row) {

            VehicleHistory::setOrUpdate(
                'expense',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->date,
                $row->km,
                $row->total,
                $row->type_id
            );

            Cost::setOrUpdate(
                'Expense',
                'expense',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->assigned_invoice_id,
                $row->description,
                $row->date,
                $row->total,
                $row->type_id
            );
        });

        Expense::deleting(function ($row) {

            VehicleHistory::remove(
                'expense',
                $row->id,
                $row->vehicle_id
            );

            Cost::remove(
                'Expense',
                $row->id,
                $row->vehicle_id
            );
        });

        TollLog::created(function ($row) {

            $row->created_by = $row->created_by ? $row->created_by : @Auth::user()->id;

            Cost::setOrUpdate(
                'TollLog',
                'toll',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->assigned_invoice_id,
                $row->description,
                $row->entry_date,
                $row->total,
                $row->type_id,
                $row->created_by
            );
        });

        TollLog::updated(function ($row) {
            Cost::setOrUpdate(
                'TollLog',
                'toll',
                $row->id,
                $row->vehicle_id,
                $row->provider_id,
                $row->assigned_invoice_id,
                $row->description,
                $row->entry_date,
                $row->total,
                $row->type_id
            );
        });

        TollLog::deleting(function ($row) {
            Cost::remove(
                'TollLog',
                $row->id,
                $row->vehicle_id
            );
        });

        /**
         * Archive
         */
        FileRepository::creating(function ($row) {
            $row->source = config('app.source');

            if ($row->filepath) {
                try {
                    $row->filesize = \File::size(public_path($row->filepath));
                } catch (\Exception $e) {
                }
            }

            if ($row->parent_id) {

                $repo = FileRepository::where('parent_id', $row->parent_id)->get();
                $countFolders = $repo->filter(function ($item) {
                    return $item->is_folder;
                })->count();
                $countFiles   = $repo->filter(function ($item) {
                    return !$item->is_folder;
                })->count();
                $size         = $repo->filter(function ($item) {
                    return !$item->is_folder;
                })->sum('filesize');

                if ($row->is_folder) {
                    $countFolders++;
                } else {
                    $countFiles++;
                    $size += $row->filesize;
                }

                FileRepository::where('id', $row->parent_id)->update([
                    'count_files'   => $countFiles,
                    'count_folders' => $countFolders,
                    'filesize'      => $size
                ]);
            }
        });

        FileRepository::deleting(function ($row) {
            if ($row->parent_id) {
                $repo = FileRepository::where('parent_id', $row->parent_id)->get();
                $countFolders = $repo->filter(function ($item) {
                    return $item->is_folder;
                })->count();
                $countFiles   = $repo->filter(function ($item) {
                    return !$item->is_folder;
                })->count();

                if ($row->is_folder) {
                    $countFolders--;
                } else {
                    $countFiles--;
                }

                FileRepository::where('id', $row->parent_id)->update([
                    'count_files'   => $countFiles,
                    'count_folders' => $countFolders
                ]);
            }
        });

        /**
         * CALENDAR
         */
        CalendarEvent::creating(function ($calendarEvent) {
            if (Auth::check()) {
                $calendarEvent->created_by = Auth::user()->id;
            }
        });

        /**
         * INVOICES
         */
        Invoice::creating(function ($invoice) {
            if (Auth::check()) {
                $invoice->created_by = Auth::user()->id;
            }
            $invoice->updateBalanceFields();
        });

        Invoice::updating(function ($invoice) {
            $invoice->updateBalanceFields();
        });

        Invoice::created(function ($invoice) {
            $customer = $invoice->customer;
            if($customer && !$invoice->is_draft) {
                $customer->updateBalance();
            }
        });

        Invoice::updated(function ($invoice) {
            $customer = $invoice->customer;
            if($customer && !$invoice->is_draft) {
                $customer->updateBalance();
            }
        });

        Invoice::deleted(function ($invoice) {
            $customer = $invoice->customer;
            if($customer && !$invoice->is_draft) {
                $customer->updateBalance();
            }
        });

        /**
         * PURCHASE INVOICES
         */
        PurchaseInvoice::creating(function ($invoice) {
            if (Auth::check()) {
                $invoice->created_by = Auth::user()->id;
            }
        });

        /**
         * LOGISTIC
         */
        \App\Models\Logistic\Product::creating(function ($data) {
            $data->source = config('app.source');
        });

        \App\Models\Logistic\Product::created(function ($data) {

            $log = new ChangeLog();
            $log->action    = 'created';
            $log->source    = 'Logistic\Product';
            $log->source_id = @$data->id;
            $log->old       = ["id" => ""];
            $log->new       = ["id" => $data->id];

            if (Auth::check()) {
                $log->user_id = Auth::user()->id;
            }

            if (Auth::guard('api')->user()) {
                $log->customer_id = Auth::guard('api')->user()->id;
                $log->is_api      = 1;
            }

            $log->save();
        });

        \App\Models\Logistic\Product::updated(function ($row) {

            $ignoredFields = ['updated_at', 'remember_token', 'last_update'];
            $changes = ChangeLog::getChanges($row, $ignoredFields);

            if (!empty($changes)) {
                $log = new ChangeLog();
                $log->action    = 'update';
                $log->source    = 'Logistic\Product';
                $log->source_id = @$row->id;
                $log->old       = $changes['old'];
                $log->new       = $changes['new'];

                if (Auth::check()) {
                    $log->user_id = Auth::user()->id;
                }

                if (Auth::guard('customer')->user()) {
                    $log->customer_id = Auth::guard('customer')->user()->id;
                }

                if (Auth::guard('api')->user()) {
                    $log->customer_id = Auth::guard('api')->user()->id;
                    $log->is_api      = 1;
                }

                $log->save();
            }
        });

        \App\Models\Logistic\ProductHistory::creating(function ($data) {
            if (Auth::check()) {
                $data->user_id = Auth::user()->id;
            }
        });

        \App\Models\Logistic\ProductHistory::created(function ($row) {
            \App\Models\Logistic\ProductStockHistory::insertFromProductHistory($row);
        });

        \App\Models\Logistic\ProductHistory::updated(function ($row) {
            \App\Models\Logistic\ProductStockHistory::insertFromProductHistory($row);
        });

        \App\Models\Logistic\Product::creating(function ($data) {
            $data->stock_available = $data->stock_allocated < 0 ? 0 : $data->stock_total - $data->stock_allocated;
        });

        \App\Models\Logistic\Product::updating(function ($data) {
            $data->stock_available = $data->stock_allocated < 0 ? 0 : $data->stock_total - $data->stock_allocated;
        });

        \App\Models\Logistic\ProductLocation::creating(function ($data) {
            $data->stock_available = $data->stock_allocated < 0 ? 0 : $data->stock - $data->stock_allocated;
        });

        \App\Models\Logistic\ProductLocation::updating(function ($data) {
            $data->stock_available = $data->stock_allocated < 0 ? 0 : $data->stock - $data->stock_allocated;
        });

        App\Models\Logistic\ShippingOrder::updating(function ($data) {
            $price = App\Models\Logistic\ShippingOrderLine::where('shipping_order_id', $data->id)->sum('price');
            $data->total_price = $price;
        });
        
        App\Models\Logistic\ShippingOrder::deleted(function ($row) {
            ShippingOrderLine::deleteByShippingOrderId($row->id);
        });

        App\Models\Logistic\ShippingOrderLine::deleted(function ($line) {
            $line->updateStockTotals();
        });

        App\Models\Logistic\ReceptionOrder::updating(function ($data) {
            $price = App\Models\Logistic\ReceptionOrderLine::where('reception_order_id', $data->id)->sum('price');
            $data->total_price = $price;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
