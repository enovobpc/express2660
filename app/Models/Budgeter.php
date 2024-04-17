<?php

namespace App\Models;

use App\Models\Webservice\GlsZeta;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;
use Setting, Session;

class Budgeter extends BaseModel
{


    /**
     * Orçamentador de serviços
     * @param $params
     * @return mixed
     */
    public static function calcPrices($params, $manualExpenses = null) {

        $customer = Auth::guard('customer')->user();

        if (@$params['service_id']) {
            $services = Service::filterSource()
                ->where('id', $params['service_id'])
                ->get();
        } else {
            $services = Shipment::getAvailableServices($params);
        }

        $params['dims_max_side']   = 0;
        $params['dims_max_weight'] = 0;
        $params['fator_m3'] = 0;
        $maxWeight = $maxDimsSum = $maxWidth = $maxLength = $maxHeight = 0;
        $packDimensions = [];
        foreach($params['pack_weight'] as $key => $weight) {

            $width   = (float) @$params['pack_width'][$key];
            $height  = (float) @$params['pack_height'][$key];
            $length  = (float) @$params['pack_length'][$key];
            $dimsSum = $width + $height + $length;

            $maxWeight  = $maxWeight > $weight ? $maxWeight : $weight;
            $maxWidth   = $width > $maxWidth ? $width : $maxWidth;
            $maxLength  = $length > $maxLength ? $length : $maxLength;
            $maxHeight  = $height > $maxHeight ? $height : $maxHeight;
            $maxDimsSum = $dimsSum > $maxDimsSum ? $dimsSum : $maxDimsSum;
            $fatorM3    = (($width * $height * $length) / 1000000);

            $params['fator_m3']+= $fatorM3;

            $packDimensions[] = [
                'type'     => @$params['pack_type'][$key],
                'qty'      => 1,
                'length'   => @$params['pack_length'][$key],
                'width'    => @$params['pack_width'][$key],
                'height'   => @$params['pack_height'][$key],
                'weight'   => @$params['pack_weight'][$key],
                'fator_m3' => $fatorM3,
            ];
        }

        $params['dims_bigger_weight'] = $maxWeight;
        $params['dims_max_sum']       = $maxDimsSum;
        $params['dims_bigger_side']   = $maxWidth > $maxLength ? $maxWidth : $maxLength;
        $params['dims_bigger_side']   = $maxHeight > $params['dims_bigger_side'] ? $maxHeight : $params['dims_bigger_side'];

        $outZone = 0;
        if(config('app.source') == 'baltrans') {
            if (!empty($params['recipient_zip_code'])) {
                $webservice = new GlsZeta('469', '469-66666', '46%Baltrans', '93bcc1e2-1dd1-44c6-8635-ce00060f3823');
                $glsAgencyDetails = $webservice->getAgency($params['recipient_country'] == 'pt' ? 351 : 34, $params['recipient_zip_code']);
                $outZone = $glsAgencyDetails['zone'] ?? 0;
            }
        }
        
        foreach($services as $key => $service) {
            if (config('app.source') == 'baltrans' && $outZone == 1 && $service->provider_id == 2 && in_array($service->id, [2, 28, 71, 11, 194, 190])) {
                unset($services[$key]);
            }

            $shipmentCollection = new Shipment();
            $shipmentCollection->fill($params);
            $shipmentCollection->agency_id   = $customer->agency_id;
            $shipmentCollection->customer_id = $customer->id;
            $shipmentCollection->service_id  = $service->id;
            $shipmentCollection->provider_id = $service->provider_id ? $service->provider_id : Setting::get('shipment_default_provider');
            $shipmentCollection->out_zone    = $outZone;
            $shipmentCollection->pack_dimensions = $packDimensions;

            $prices = Shipment::calcPrices($shipmentCollection, true, $manualExpenses);

            if (!@$prices['result']) {
                unset($services[$key]);
                continue;
            }
            
            /**
             * @author Daniel Almeida
             * 
             * Removido restrição de serviços contractdos
             * na ficha de cliente a pedido do Juan
             */
            if (config('app.source') == 'baltrans') {
                $serviceAllowed = true;
            } else {
                $serviceAllowed = empty($customer->enabled_services) ? true : in_array($service->id, $customer->enabled_services);
            }
            
            if(@$params['show_empty_prices'] || ($serviceAllowed && @$prices['billing']['subtotal'] > 0.00)) {
                $service->details          = $prices;
                $service->prices           = @$prices['fillable'];

                $service->pickup_date      = @$prices['pickup']['date'];
                $service->pickup_hour      = @$prices['pickup']['hour_max'];

                $service->delivery_date    = @$prices['delivery']['date'];
                $service->delivery_hour    = @$prices['delivery']['hour_max'];;

                $service->transit_time     = @$prices['delivery']['transit_time'];
                $service->transit_time_max = @$prices['delivery']['transit_time_max'];

                $service->pack_dimensions    = $packDimensions;
                $service->sender_country     = $shipmentCollection->sender_country;
                $service->sender_zip_code    = $shipmentCollection->sender_zip_code;
                $service->sender_city        = $shipmentCollection->sender_city;
                $service->recipient_country  = $shipmentCollection->recipient_country;
                $service->recipient_zip_code = $shipmentCollection->recipient_zip_code;
                $service->recipient_city     = $shipmentCollection->recipient_city;
                $service->volumes            = $shipmentCollection->volumes;
                $service->weight             = $shipmentCollection->weight;
                $service->fator_m3           = @$params['fator_m3'];
                $service->pack_dimensions    = $packDimensions;
            } else {
                unset($services[$key]); //remove o serviço porque o preço é 0.00
            }
        }

        return $services;
    }

}
