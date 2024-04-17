<?php

namespace App\Models;

use DB, Date;
use App\Models\Customer;
use App\Models\Shipment;

class ShipmentAnalytics extends BaseModel
{

    /**
     * Get shipment analytics for a given perido
     * 
     * @param type $dateMin start date
     * @param type $dateMax end date
     * @param type $metrics [day, month, year]
     * @return type
     */
    public static function getForPeriod($dateMin = null, $dateMax = null, $metrics = 'day', $customerId = null)
    {

        $arr = [];
        $dateMin = empty($dateMin) ? Date::now()->subDays(30)->format('Y-m-d') : $dateMin;
        $dateMax = empty($dateMax) ? date('Y-m-d') : $dateMax;
        $date = $dateMin;

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))->cacheTags(Agency::CACHE_TAG)->filterSource()->pluck('id')->toArray();

        $bindings = [
            'date',
            DB::raw('MONTH(date) as month'),
            DB::raw('YEAR(date) as year'),
            DB::raw('sum(cost_price) as costs'),
            DB::raw('sum(volumes) as volumes'),
            DB::raw('SUM(IF(payment_at_recipient = 0, total_price, 0)) as billed'),
            DB::raw('sum(total_expenses) as expenses'),
            DB::raw('count(*) as shipments'),
            DB::raw('SUM(IF(is_collection = 1, 1, 0)) as collections'),
        ];

        if (is_null($customerId)) {
            $shipments = Shipment::filterAgencies($sourceAgencies)
                ->select($bindings)
                ->whereBetween('date', [$dateMin, $dateMax])
                ->where('status_id', '<>', ShippingStatus::CANCELED_ID);
        } else {
            $shipments = Shipment::select($bindings)
                ->where('customer_id', $customerId)
                ->whereBetween('date', [$dateMin, $dateMax])
                ->where('status_id', '<>', ShippingStatus::CANCELED_ID);
        }

        if ($metrics == 'year') {

            $shipments = $shipments->groupBy(DB::raw('YEAR(date)'))->get();
            $shipments = $shipments->groupBy('year')->toArray();

            $result = [];
            foreach ($shipments as $year => $shipment) {
                $result[$year] = self::createReturnArray(@$shipment);
            }
        }

        if ($metrics == 'month') {

            $shipments = $shipments->groupBy(DB::raw('MONTH(date)'))->get();
            $shipments = $shipments->groupBy('month')->toArray();

            $result = [];
            for ($i = 1; $i <= 12; $i++) {
                $result[$i] = self::createReturnArray(@$shipments[$i]);
            }

            $arr = $result;
        }

        if ($metrics == 'day') {

            $shipments = $shipments->groupBy('date')->get();
            $shipments = $shipments->groupBy('date')->toArray();

            $result = [];
            foreach ($shipments as $date => $shipment) {
                $result[$date] = self::createReturnArray($shipment);
            }

            $date = Date::parse($date);
            $endOfMonth = $date->endOfMonth()->day;
            $year = $date->year;
            $month = $date->month;

            $arr = [];
            for ($i = 1; $i <= $endOfMonth; $i++) {

                $date = $year . '-' . str_pad($month, 2, "0", STR_PAD_LEFT) . '-' . str_pad($i, 2, "0", STR_PAD_LEFT);

                if (isset($result[$date])) {
                    $arr[$date] = $result[$date];
                } else {
                    $arr[$date] = [
                        'costs'         => 0,
                        'billed'        => 0,
                        'expenses'      => 0,
                        'volumes'       => 0,
                        'shipments'     => 0,
                        'shipments_avg' => 0,
                        'collections'   => 0,
                    ];
                }
            }
        }

        return $arr;
    }

    /**
     * Get total counters of all status
     * 
     * @param type $status
     * @return type
     */
    public static function getStatusTotal($status = null, $customerId = null)
    {
        $sourceAgencies = Agency::remember(config('cache.query_ttl'))->cacheTags(Agency::CACHE_TAG)->filterSource()->pluck('id')->toArray();

        $allStatus = ShippingStatus::remember(config('cache.query_ttl'))->cacheTags(ShippingStatus::CACHE_TAG)->get(['id', 'slug']);

        foreach ($allStatus as $itm) {
            $bindings[] = DB::raw('SUM(IF(status_id = ' . $itm->id . ', 1, 0)) as "' . $itm->slug . '"');
        }

        if (is_null($customerId)) {
            $shipments = Shipment::filterAgencies($sourceAgencies)
                ->select($bindings)
                ->remember(5)
                ->get()
                ->toArray();
        } else {
            $shipments = Shipment::select($bindings)
                ->where('customer_id', $customerId)
                ->remember(5)
                ->get()
                ->toArray();
        }

        if (!is_null($status)) {
            return $shipments[0][$status];
        }

        return $shipments[0];
    }


    /**
     * Get total counters of all status grouped by operator
     * 
     * @param type $status
     * @return type
     */
    public static function getStatusTotalByOperator($operatorId = null, $status = null)
    {

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))->cacheTags(Agency::CACHE_TAG)->filterSource()->pluck('id')->toArray();

        $allStatus = ShippingStatus::remember(config('cache.query_ttl'))->cacheTags(ShippingStatus::CACHE_TAG)->get(['id', 'slug']);

        $bindings[] = 'operator_id';
        foreach ($allStatus as $itm) {
            $bindings[] = DB::raw('SUM(IF(status_id = ' . $itm->id . ', 1, 0)) as "' . $itm->slug . '"');
        }


        $shipments = Shipment::filterAgencies($sourceAgencies)
            ->select($bindings)
            ->groupBy('operator_id')
            ->get();

        $shipments = $shipments->groupBy('operator_id')->toArray();

        $result = null;
        foreach ($shipments as $key => $shipment) {
            $result[$key] = $shipment[0];
        }

        if (!is_null($operatorId) && is_null($status)) {
            return $result[$operatorId];
        } elseif (!is_null($operatorId) && !is_null($status)) {
            return $result[$operatorId][$status];
        }

        return $result;
    }

    /**
     * 
     * @param type $dataArr
     * @return type
     */
    public static function createReturnArray($dataArr)
    {


        return [
            'costs'         => @$dataArr[0]['costs'],
            'billed'        => @$dataArr[0]['billed'] + @$dataArr[0]['expenses'],
            'shipments'     => @$dataArr[0]['shipments'],
            'expenses'      => @$dataArr[0]['expenses'],
            'volumes'       => @$dataArr[0]['volumes'],
            'shipments_avg' => (@$dataArr[0]['shipments'] / 12),
            'collections'   => @$dataArr[0]['collections'],
        ];
    }
}
