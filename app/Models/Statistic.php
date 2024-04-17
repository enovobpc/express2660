<?php

namespace App\Models;

use App\Models\FleetGest\Cost;
use Auth, Date, DB, Setting;
use Carbon\CarbonPeriod;

class Statistic extends BaseModel
{

    public static function getPeriodDates($metrics, $data)
    {

        $dtMin  = @$data['date_min'];
        $dtMax  = @$data['date_max'];
        $year   = @$data['year'];
        $year   = $year ? $year : date('Y');
        $month  = @$data['month'];
        $month  = $month ? $month : date('m');
        $month  = str_pad($month, 2, '0', STR_PAD_LEFT);
        $period = @$data['period'];

        $periodList = [];
        if ($metrics == 'daily') {
            $startDate = null;
            if (!empty($period)) {
                $period = str_replace('d', '', $period);

                if ($period == '1') { //ontem
                    $startDate = Date::today()->subDays(1);
                    $dtMin = $startDate->format('Y-m-d');
                    $dtMax = $dtMin;
                } else {
                    $startDate   = Date::today()->subDays($period);
                }
            } elseif ($period == '0') {
                $startDate = new Date();
                $dtMin = $startDate->format('Y-m-d');
                $dtMax = $dtMin;
            }

            $startDate  = $dtMin ? $dtMin : $startDate->format('Y-m-d');
            $endDate    = $dtMax ? $dtMax : date('Y-m-d');
            $years      = [];
            $periodList = trans('admin/fleet.stats.daily');
        } elseif ($metrics == 'yearly') {
            $startDate = $dtMin ? $dtMin : $year . '-01-01';
            $endDate   = $dtMax ? $dtMax : $year . '-12-31';
            $years     = yearsArr(2017, null, true);
        } elseif ($metrics == 'monthly') {
            $startDate = $dtMin ? $dtMin : $year . '-' . $month . '-01';
            $endDate   = $dtMax ? $dtMax : $year . '-' . $month . '-31';
            $years     = yearsArr(2017, null, true);
        }

        return [
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'years'      => $years,
            'months'     => trans('datetime.list-month'),
            'period_list' => $periodList,
            'metric'     => $metrics
        ];
    }

    /**
     * Get Shipments for selected Period
     * @return mixed
     */
    public static function getShipmentsForPeriod($startDate, $endDate, $sellerId = null, $agencyId = null, $statusIds = null, $operatorId = null, $vehicle = null)
    {

        if (empty($statusIds)) {
            $statusIds = [
                ShippingStatus::DELIVERED_ID,
                ShippingStatus::INCIDENCE_ID,
                ShippingStatus::PICKUP_FAILED_ID,
                ShippingStatus::DEVOLVED_ID
            ];
        }

        $bindings = [
            'id',
            'volumes',
            'weight',
            'total_price',
            'cost_price',
            'cost_billing_subtotal',
            'total_expenses',
            'fuel_price',
            'total_expenses_cost',
            'total_price_for_recipient',
            'payment_at_recipient',
            'recipient_zip_code',
            'recipient_country',
            'sender_country',
            'service_id',
            'ignore_billing',
            'charge_price',
            'customer_id',
            'operator_id',
            'service_id',
            'recipient_agency_id',
            'provider_id',
            'created_by',
            'status_id',
            'date',
            DB::raw('UPPER(recipient_city) as recipient_city'),
            DB::raw('DAY(billing_date) as day'),
            DB::raw('MONTH(billing_date) as month'),
            DB::raw('YEAR(billing_date) as year'),
            DB::raw('HOUR(created_at) as hour')
        ];

        $allShipments = Shipment::with(['service' => function ($q) {
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(Service::CACHE_TAG);
        }])
            ->with(['operator' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(User::CACHE_TAG);
                $q->select(['id', 'name']);
            }])
            ->with(['provider' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Provider::CACHE_TAG);
                $q->select(['id', 'name', 'color']);
            }])
            ->with(['customer' => function ($q) {
                $q->with('route');
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
                $q->select(['id', 'name', 'avg_cost', 'agency_id', 'seller_id', 'route_id', 'type_id', 'created_at']);
            }])
            ->with(['history' => function ($q) use ($statusIds) {
                $q->whereIn('status_id', $statusIds);
            }])
            ->whereHas('agency', function ($q) {
                $q->where('source', config('app.source'));
            })
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->where('is_collection', 0)
            ->whereBetween('billing_date', [$startDate, $endDate]);

        //filter seller
        if ($sellerId) {
            $allShipments = $allShipments->whereHas('customer', function ($q) use ($sellerId) {
                $q->where('seller_id', $sellerId);
            });
        }

        //filter agency
        if ($agencyId) {
            $allShipments = $allShipments->where('agency_id', $agencyId);
        }

        //filter operator
        if ($operatorId) {
            $allShipments = $allShipments->where('operator_id', $operatorId);
        }

        //filter vehicle
        if ($vehicle) {
            $allShipments = $allShipments->where('vehicle', $vehicle);
        }


        $allShipments = $allShipments->get($bindings);

        return $allShipments;
    }

    /**
     * Return incidences for period
     *
     * @param $dateMin
     * @param $dateMax
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getIncidencesFromShipments($allShipments)
    {

        $incidenceShipments = $allShipments->filter(function ($item) {
            return $item->history->filter(function ($item) {
                return $item->status_id == ShippingStatus::INCIDENCE_ID;
            })->first();
        });

        return $incidenceShipments;
    }

    /**
     * Return statistics by type of service
     * @param null $allShipments
     */
    public static function byTypeOfService($allShipments = null)
    {

        if (empty($allShipments)) {
            $allShipments = self::getShipmentsForPeriod();
        }


        $shipmentsService = $allShipments->groupBy('service.code');
        $services = Service::filterSource()->withTrashed()->pluck('name', 'code')->toArray();

        $shipments = [];
        foreach ($shipmentsService as $serviceId => $shipment) {

            $withVat = $shipment->filter(function ($item) {
                return ($item->sender_country != 'pt' && $item->recipient_country == 'pt') || ($item->sender_country == 'pt' && $item->recipient_country == 'pt');
            });

            $withoutVat = $shipment->filter(function ($item) {
                return $item->sender_country == 'pt' && $item->recipient_country != 'pt';
            });

            $shipments[@$services[$serviceId]] = [
                'count_vat'      => $withVat->count(),
                'total_vat'      => $withVat->sum('total_price') + $withVat->sum('total_expenses') + $withVat->sum('fuel_price'),
                'volumes_vat'    => $withVat->sum('volumes'),
                'count_no_vat'   => $withoutVat->count(),
                'total_no_vat'   => $withoutVat->sum('total_price') + $withoutVat->sum('total_expenses') + $withoutVat->sum('fuel_price'),
                'volumes_no_vat' => $withoutVat->sum('volumes'),
                'count'          => $shipment->count(),
                'volumes'        => $shipment->sum('volumes'),
                'total'          => $shipment->sum('total_price') + $shipment->sum('total_expenses') + $shipment->sum('fuel_price'),
            ];
        }

        aasort($shipments, 'total', SORT_DESC);
        return $shipments;
    }

    /**
     * Return list of nacional destinations
     * @param null $allShipments
     */
    public static function topNacional($allShipments = null)
    {
        $nacionalShipments = $allShipments->filter(function ($item) {
            $zipCode = explode('-', $item->recipient_zip_code);
            $zipCode = @$zipCode[0];
            $regionalZipCodes = explode(',', Setting::get('postal_codes_of_operation'));
            return $item->sender_country == 'pt' && $item->recipient_country == 'pt' && !in_array($zipCode, $regionalZipCodes);
        })->sortBy('recipient_city');

        return self::sortShipmentsArray($nacionalShipments->groupBy('recipient_city'), 'total');
    }

    /**
     * Return list of nacional destinations
     * @param null $allShipments
     */
    public static function topRegional($allShipments = null)
    {
        $regionalShipments = $allShipments->filter(function ($item) {
            $zipCode = explode('-', $item->recipient_zip_code);
            $zipCode = @$zipCode[0];
            $regionalZipCodes = explode(',', Setting::get('postal_codes_of_operation'));
            return $item->sender_country == 'pt' && $item->recipient_country == 'pt' && in_array($zipCode, $regionalZipCodes);
        })->sortBy('recipient_city');

        return self::sortShipmentsArray($regionalShipments->groupBy('recipient_city'), 'total');
    }

    /**
     * Return list of export countries
     * @param null $allShipments
     */
    public static function topExportCountries($allShipments)
    {
        $exportShipments = $allShipments->filter(function ($item) {
            return $item->sender_country == 'pt' && $item->recipient_country != 'pt';
        });
        return self::sortShipmentsArray($exportShipments->groupBy('recipient_country'), 'total');
    }

    /**
     * Return list of import countries
     * @param null $allShipments
     */
    public static function topImportCountries($allShipments)
    {
        $importShipments = $allShipments->filter(function ($item) {
            return $item->sender_country != 'pt' && $item->recipient_country == 'pt';
        });
        return self::sortShipmentsArray($importShipments->groupBy('sender_country'), 'total');
    }

    /**
     * Return top of customers
     * @param $allShipments
     * @return array
     */
    public static function topCustomers($allShipments)
    {
        return self::sortShipmentsArray($allShipments->groupBy('customer.name'), 'total');
    }

    /**
     * Return top of customers
     * @param $allShipments
     * @return array
     */
    public static function topPickupsByRoute($allShipments)
    {
        return self::sortShipmentsArray($allShipments->groupBy('customer.route.name'), 'total');
    }

    /**
     * Return top of customers
     * @param $allShipments
     * @return array
     */
    public static function topShipmentsByRoute($allShipments, $routes)
    {

        foreach ($routes as $route) {
            $routeShipments = $allShipments->filter(function ($item) use ($route) {
                return in_array(zipcodeCP4($item->recipient_zip_code), $route->zip_codes);
            });

            $shipments = self::sortShipmentsArray([$routeShipments], 'total');
            $routesShipments[$route->name] = @$shipments[0];
        }

        aasort($routesShipments, 'total', SORT_DESC);

        return $routesShipments ? $routesShipments : [];
    }
    /**
     * Return top of providers
     * @param $allShipments
     * @return array
     */
    public static function topProviders($allShipments)
    {
        return self::sortShipmentsArray($allShipments->groupBy('provider.name'), 'total');
    }

    /**
     * Return top operator deliveries
     * @param $allShipments
     * @return array
     */
    public static function topOperatorDeliveries($allShipments, $myOperators = null)
    {

        if (empty($myOperators)) {
            $myOperators = User::remember(config('cache.query_ttl'))
                ->cacheTags(User::CACHE_TAG)
                ->filterSource()
                ->pluck('id')
                ->toArray();
        }

        $allDeliveries = $allShipments->filter(function ($item) use ($myOperators) {
            return in_array($item->operator_id, $myOperators);
        });

        return self::sortShipmentsArray($allDeliveries->groupBy('operator.name'), 'total');
    }


    /**
     * Return sales by seller
     *
     * @param $sellers
     * @param $allShipments
     * @param $allCovenants
     * @param $allReceipts
     * @return array
     */
    public static function getSalesBySeller($startDate, $endDate, $sellers, $allShipments, $allCovenants, $allReceipts = null)
    {

        $allShipments = self::sortShipmentsArray($allShipments->groupBy('customer.seller_id'), 'total');
        $allCovenants = $allCovenants->groupBy('customer.seller_id');

        if (empty($allReceipts)) {
            //get all receipts
            $allReceipts = Invoice::with('customer.seller')
                ->whereHas('customer', function ($q) {
                    $q->filterSource();
                })
                ->whereBetween('doc_date', [$startDate, $endDate])
                ->where('doc_type', Invoice::DOC_TYPE_RC)
                ->get();

            $allReceipts  = $allReceipts->groupBy('customer.seller_id');
        }

        $arr = [];
        foreach ($allShipments as $key => $shipments) {
            $sellerId = @$allShipments[$key]['seller_id'];
            $arr[$sellerId] = $shipments;
        }
        $allShipments = $arr;

        $arr = [];
        $sellers = $sellers->toArray();

        if ($sellers) {
            $sellers = array_merge($sellers, ['' => null]);
        }

        foreach ($sellers as $seller) {

            $sellerId   = @$seller['id'];
            $sellerName = @$seller['name'];
            $comission  = @$seller['comission_percent'];
            $comission  = $comission ? $comission : 0;

            $shipments = @$allShipments[$sellerId];

            $receipts = @$allReceipts[$sellerId];
            if ($receipts) {
                $receipts = $receipts->sum('total');
            }

            $covenants = @$allCovenants[$sellerId];
            if ($covenants) {
                $covenants = $covenants->sum('amount');
            }

            $arr[$sellerId] = $shipments;
            $arr[$sellerId]['covenants'] = $covenants;
            $arr[$sellerId]['receipts']  = $receipts;
            $arr[$sellerId]['total']     = @$arr[$sellerId]['covenants'] + @$arr[$sellerId]['total'];
            $arr[$sellerId]['id']        = $sellerId;
            $arr[$sellerId]['name']      = $sellerName;
            $arr[$sellerId]['comission_percent'] = $comission;
            $arr[$sellerId]['comission_total']   = ($comission / 100) * $arr[$sellerId]['total'];
        }
        return $arr;
    }

    /**
     * Return sales by operator
     *
     * @param $operators with commission
     * @param $allShipments
     * @param $allCovenants
     * @param $allReceipts
     * @return array
     */
    public static function getSalesByOperator($startDate, $endDate, $operators, $allShipments)
    {
        $allShipments = $allShipments
            ->where('operator_id', '!=', null)
            ->whereIn('operator_id', $operators->pluck('id'))
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->groupBy('operator_id');

        $arr = [];
        foreach ($operators->toArray() as $operator) {

            $operatorId   = $operator['id'];
            $operatorName = $operator['name'];
            $comission  = $operator['comission_percent'] ?? 0;

            $shipments = $allShipments[$operatorId] ?? collect();

            $arr[$operatorId] = $shipments;
            $arr[$operatorId]['count']     = @$arr[$operatorId]->count();
            $arr[$operatorId]['volumes']   = @$arr[$operatorId]->sum('volumes');
            $arr[$operatorId]['total']     = @$arr[$operatorId]->sum('total_price') + @$arr[$operatorId]->sum('total_expenses') + @$arr[$operatorId]->sum('fuel_price') + @$arr[$operatorId]->sum('total_price_for_recipient');
            $arr[$operatorId]['id']        = $operatorId;
            $arr[$operatorId]['name']      = $operatorName;
            $arr[$operatorId]['comission_percent'] = $comission;
            $arr[$operatorId]['comission_total']   = ($comission / 100) * @$arr[$operatorId]['total'];
        }
        return $arr;
    }

    /**
     * Return sales by users
     *
     * @param $allShipments
     * @param $allCovenants
     * @param $allReceipts
     * @return array
     */
    public static function getSalesByUser($startDate, $endDate, $allShipments, $users)
    {
        $allShipments = $allShipments
            ->where('operator_id', '!=', null)
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->groupBy('created_by');

        $arr = [];
        foreach ($users->toArray() as $user) {

            $userId   = $user['id'];
            $userName = $user['name'];

            $shipments = $allShipments[$userId] ?? collect();

            $arr[$userId] = $shipments;
            $arr[$userId]['count']     = @$arr[$userId]->count();
            $arr[$userId]['volumes']   = @$arr[$userId]->sum('volumes');
            $arr[$userId]['total']     = @$arr[$userId]->sum('total_price') + @$arr[$userId]->sum('total_expenses') + @$arr[$userId]->sum('fuel_price') + @$arr[$userId]->sum('total_price_for_recipient');
            $arr[$userId]['id']        = $userId;
            $arr[$userId]['name']      = $userName;
        }
        return $arr;
    }

    /**
     * Return total Counters
     *
     * @param $allShipments
     */
    public static function getTotalCounters($allShipments, $allCovenants = null, $allCustomers = null, $allProducts = null, $fleetCosts = null, $allInvoices = null, $allPurchaseInvoices = null)
    {

        $countDays = $allShipments->groupBy('date')->count();

        $nacionalShipments = $allShipments->filter(function ($item) {
            return $item->sender_country == 'pt' && $item->recipient_country == 'pt';
        })->sortBy('recipient_zip_code');

        $exportShipments = $allShipments->filter(function ($item) {
            return $item->sender_country == 'pt' && $item->recipient_country != 'pt';
        });

        $importShipments = $allShipments->filter(function ($item) {
            return $item->sender_country != 'pt' && $item->recipient_country == 'pt';
        });

        $incidenceShipments = $allShipments->filter(function ($item) {
            return $item->history->filter(function ($item) {
                return $item->status_id == ShippingStatus::INCIDENCE_ID;
            })->first();
        });

        $deliveries = $allShipments->filter(function ($item) {
            return !$item->history->contains('status_id', ShippingStatus::INCIDENCE_ID) && $item->status_id == ShippingStatus::DELIVERED_ID;
        });

        $devolutions = $allShipments->filter(function ($item) {
            return $item->status_id == ShippingStatus::DEVOLVED_ID;
        });

        $totalShipments   = $allShipments->count();
        $totalIncidences  = $incidenceShipments->count();
        $totalDeliveries  = $deliveries->count();
        $totalDevolutions = $devolutions->count();

        $data = [
            'shipments' => [
                'count'   => $totalShipments,
                'weight' => $allShipments->sum('weight'),
                'weight_avg' => $allShipments->avg('weight'),
                'volumes' => $allShipments->sum('volumes'),
                'volumes_avg' => $allShipments->avg('volumes'),
                'volumes_day_avg' => '',
                'price_avg' => $allShipments->avg('total_price'),
                'total'   => $allShipments->sum('total_price') + $allShipments->sum('total_expenses') + $allShipments->sum('fuel_price') + $allShipments->sum('total_price_for_recipient'),
                'costs'   => $allShipments->sum('cost_billing_subtotal') + $allShipments->sum('total_expenses_cost'),
                'incidences'  => [
                    'count'   => $totalIncidences,
                    'percent' => $totalShipments ? ($totalIncidences * 100) / $totalShipments : 0,
                ],
                'deliveries'  => [
                    'count'   => $totalDeliveries,
                    'percent' => $totalShipments ? ($totalDeliveries * 100) / $totalShipments : 0,
                ],
                'devolutions' => [
                    'count'   => $totalDevolutions,
                    'percent' => $totalShipments ? ($totalDevolutions * 100) / $totalShipments : 0,
                ]
            ],
            'nacional' => [
                'total'   => $nacionalShipments->sum('total_price') + $nacionalShipments->sum('total_expenses') + $nacionalShipments->sum('fuel_price') + $nacionalShipments->sum('total_price_for_recipient'),
                'count'   => $nacionalShipments->count(),
                'volumes' => $nacionalShipments->sum('volumes')
            ],
            'imports' => [
                'total'   => $exportShipments->sum('total_price') + $exportShipments->sum('total_expenses') + $exportShipments->sum('fuel_price') + $exportShipments->sum('total_price_for_recipient'),
                'count'   => $exportShipments->count(),
                'volumes' => $exportShipments->sum('volumes')
            ],
            'exports' => [
                'total'   => $importShipments->sum('total_price') + $importShipments->sum('total_expenses') + $importShipments->sum('fuel_price') + $importShipments->sum('total_price_for_recipient'),
                'count'   => $importShipments->count(),
                'volumes' => $importShipments->sum('volumes')
            ],
            'operators' => [
                'volumes_avg' => 3,
                'weight_avg' => 3
            ]
        ];

        if ($allProducts) {
            $data['products'] = [
                'total' => @$allProducts->sum('subtotal'),
                'count' => @$allProducts->count('subtotal'),
                'costs' => @$allProducts->sum('cost_price'),
            ];
        } else {
            $data['products'] = [
                'total' => 0,
                'count' => 0,
                'costs' => 0,
            ];
        }

        if ($fleetCosts) {
            $data['fleet'] = [
                'total' => 0,
                'count' => $fleetCosts->count('total'),
                'costs' => $fleetCosts->sum('total')
            ];
        } else {
            $data['fleet'] = [
                'total' => 0,
                'count' => 0,
                'costs' => 0
            ];
        }


        if ($allInvoices || $allPurchaseInvoices) {

            $data['other'] = [
                'total' => @$allInvoices->sum('doc_subtotal'),
                'count' => @$allInvoices->count('doc_subtotal') + @$allPurchaseInvoices->count('doc_subtotal'),
                'costs' => @$allPurchaseInvoices->sum('subtotal')
            ];
        } else {
            $data['other'] = [
                'total' => 0,
                'count' => 0,
                'costs' => 0
            ];
        }

        $data['avg'] = [
            'shipments' => $countDays ? @$data['shipments']['count'] / $countDays : 0,
        ];

        if ($allCovenants) {
            $data['covenants'] = [
                'total' => @$allCovenants->sum('amount'),
                'count' => @$allCovenants->count()
            ];
        } else {
            $data['covenants'] = [
                'total' => 0,
                'count' => 0
            ];
        }

        if ($allCustomers) {
            $data['customers'] = [
                'costs'  => @$allCustomers->sum('avg_cost'),
            ];
        } else {
            $data['customers'] = [
                'costs'  => 0
            ];
        }

        $gains = $data['shipments']['total'] + $data['covenants']['total'] + $data['products']['total'] + $data['other']['total'];
        $costs = $data['shipments']['costs'] + $data['products']['costs'] + $data['customers']['costs'] + $data['fleet']['costs'] + $data['other']['costs'];
        $balance = $gains - $costs;
        $percentTotal = $gains + $costs;

        $gainsPercent = $costsPercent = $balancePercent = $balanceRelativePercent = 0;

        if ($balance > 0.00) {
            $gainsPercent = ($gains * 100) / $percentTotal;
            $costsPercent = 100 - $gainsPercent;
            $balanceRelativePercent = ($balance * 100) / $gains;
        } else if ($balance < 0.00) {
            $balancePositive = $balance * -1;
            $gainsPercent = ($gains * 100) / $percentTotal;
            $costsPercent = 100 - $gainsPercent;
            $balanceRelativePercent = ($balancePositive * 100) / $costsPercent;
        }

        $balancePercent = $costs ? ((($gains * 100) / $costs) - 100) : 0; //formula correta de calculo do lucro (ISA Westroutes)

        $data['balance'] = [
            'balance'   => $balance,
            'costs'     => $costs,
            'gains'     => $gains,
            'balance_percent' => $balancePercent,
            'balanceRelativePercent' => $balanceRelativePercent,
            'gains_percent'   => $gainsPercent,
            'costs_percent'   => $costsPercent
        ];

        return $data;
    }

    /**
     * Get stats grouped By Date
     * @param $allShipments
     */
    public static function getShipmentsStatsByDay($allShipments)
    {
        $shipmentsByDay = $allShipments->groupBy('date');
        $statsByDay = [];

        $totalDays = count($shipmentsByDay);
        $totalShipments = $totalVolumes = $totalWeight = $totalPrice = $totalCost = 0;
        foreach ($shipmentsByDay as $day => $shipments) {

            $count     = $shipments->count();
            $volumes   = $shipments->sum('volumes');
            $weight    = $shipments->sum('weight');
            $price     = $shipments->sum('total_price') + $shipments->sum('total_expenses') + $shipments->sum('fuel_price');
            $cost      = $shipments->sum('cost_billing_subtotal') + $shipments->sum('total_expenses_cost');

            $totalShipments += $count;
            $totalVolumes += $volumes;
            $totalWeight += $weight;
            $totalPrice += $price;
            $totalCost += $cost;

            $data = [
                'shipments'   => $count,
                'volumes'     => $volumes,
                'volumes_avg' => $shipments->sum('avg'),
                'weight'      => $weight,
                'weight_avg'  => $shipments->avg('weight'),
                'price'       => $price,
                'price_avg'   => $shipments->avg('total_price') + $shipments->avg('total_expenses') + $shipments->avg('fuel_price'),
                'cost'        => $cost,
                'incidences'  => 0,
                'devolutions' => 0
            ];

            $statsByDay[$day] = $data;
        }

        $statsAvg = [
            'shipments' => $totalDays ? $totalShipments / $totalDays : 0,
            'volumes'   => $totalDays ? $totalVolumes / $totalDays : 0,
            'weight'    => $totalDays ? $totalWeight / $totalDays : 0,
            'price'     => $totalDays ? $totalPrice / $totalDays : 0,
            'cost'      => $totalDays ? $totalCost / $totalDays : 0,
        ];

        $response = [
            'days' => $statsByDay,
            'avg'  => $statsAvg
        ];

        return $response;
    }

    /**
     * Return status counters
     *
     * @param $allShipments
     */
    public static function getStatusTotals($allShipments, $statusIds)
    {

        $totalShipments = $allShipments->count();

        $data = [];
        foreach ($allShipments as $shipment) {
            foreach ($statusIds as $statusId) {

                $count = @$data[$statusId]['count'];

                $count += $shipment->history->filter(function ($item) use ($statusId) {
                    return $item->status_id == $statusId;
                })->count();

                $data[$statusId] = [
                    'count'   => $count,
                    'percent' => $totalShipments ? ($count * 100) / $totalShipments : 0,
                ];
            }
        }

        return $data;
    }

    /**
     * Return chart data for balance chart
     * @param $totals
     * @return array
     */
    public static function getSimpleChartData($allData)
    {

        $pallete = [
            0 => '#1B6EC2',
            1 => '#2F9E44',
            2 => '#F08C00',
            3 => '#E8580B',
            4 => '#E03130',
            5 => '#C2255C',
            6 => '#9C36B5',
            7 => '#6741D9',
            8 => '#4DADF7',
            9 => '#69DB7C',
            10 => '#FFD43B',
            11 => '#FFA94D',
            12 => '#FF8787',
            13 => '#DA77F2',
            14 => '#9775FA'
        ];

        $labels = $values = $colors = [];
        $i = 0;

        foreach ($allData as $label => $data) {
            $labels[] = '"' . ($label ? str_limit($label, 10) : 'Sem associação') . '"';
            $colors[] = '"' . @$pallete[$i] . '"';
            $values[] = @$data->count();
            $i++;
        }

        $chartData = [
            'labels' => implode(',', $labels),
            'values' => implode(',', $values),
            'colors' => implode(',', $colors),
        ];

        return $chartData;
    }

    /**
     * Return chart data for balance chart
     * @param $totals
     * @return array
     */
    public static function getBalanceChartData($totals)
    {

        $values = [
            @$totals['shipments']['total'],
            @$totals['covenants']['total'],
            @$totals['products']['total'],
            @$totals['shipments']['costs'],
        ];

        $chartData = [
            'labels' => '"Envios","Avenças","Outros","Despesas"',
            'values' => implode(',', $values),
            'colors' => '"#6dce44","#17A72D","#a6e08d","#004781"'
        ];

        return $chartData;
    }

    /**
     * Return chart data for balance chart
     * @param $totals
     * @return array
     */
    public static function getStatusChartData($totals, $allStatus, $onlyWithValue = true)
    {

        $labels = $values = $colors = [];

        foreach ($allStatus as $status) {
            $labels[] = '"' . $status->name . '"';
            $colors[] = '"' . $status->color . '"';
            $values[] = @$totals[$status->id]['count'];
        }

        if ($onlyWithValue) {
            foreach ($values as $key => $value) {
                if (empty($value)) {
                    unset($labels[$key], $colors[$key], $values[$key]);
                }
            }
        }

        $chartData = [
            'labels' => implode(',', $labels),
            'values' => implode(',', $values),
            'colors' => implode(',', $colors),
        ];

        return $chartData;
    }


    /**
     * Return chart data for balance chart
     * @param $totals
     * @return array
     */
    public static function getProvidersChartData($allShipments)
    {

        $allShipments = $allShipments->groupBy('provider_id');

        $labels = $values = $colors = [];

        foreach ($allShipments as $providerId => $shipments) {
            $labels[] = '"' . @$shipments->first()->provider->name . '"';
            $colors[] = '"' . @$shipments->first()->provider->color . '"';
            $values[] = @$shipments->count();
        }

        $chartData = [
            'labels' => implode(',', $labels),
            'values' => implode(',', $values),
            'colors' => implode(',', $colors),
        ];

        return $chartData;
    }

    /**
     * Return chart data for balance chart
     * @param $totals
     * @return array
     */
    public static function getAgenciesChartData($allShipments)
    {

        $allShipments = $allShipments->groupBy('agency_id');

        $labels = $values = $colors = [];

        foreach ($allShipments as $providerId => $shipments) {
            $labels[] = '"' . @$shipments->first()->agency->print_name . '"';
            $colors[] = '"' . @$shipments->first()->agency->color . '"';
            $values[] = @$shipments->count();
        }

        $chartData = [
            'labels' => implode(',', $labels),
            'values' => implode(',', $values),
            'colors' => implode(',', $colors),
        ];

        return $chartData;
    }

    /**
     * Return chart data for balance chart
     * @param $totals
     * @return array
     */
    public static function getSellerChartData($salesCommercial)
    {

        $pallete = [
            '#FF5733', '#FFC300', '#C70039', '#2471A3', '#17A589', '#1E8449', '#58508d', '#444e86'
        ];

        $labels = $values = $colors = [];
        $i = 0;
        foreach ($salesCommercial as $seller) {
            if ($i > 7) {
                $i = random_int(0, 7);
            }

            $sellerName = @$seller['name'] ? str_limit($seller['name'], 15) : 'Sem Comercial';

            $labels[] = '"' . $sellerName . '"';
            $values[] = @$seller['total'];
            $colors[] = '"' . $pallete[$i] . '"';
            $i++;
        }

        $chartData = [
            'labels' => implode(',', $labels),
            'values' => implode(',', $values),
            'colors' => implode(',', $colors),
        ];

        return $chartData;
    }

    /**
     * Return chart data for balance chart
     * @param $totals
     * @return array
     */
    public static function getRecipientsChartData($allShipments)
    {

        $nacionalShipments = $allShipments->filter(function ($item) {
            $zipCode = explode('-', $item->recipient_zip_code);
            $zipCode = @$zipCode[0];
            $regionalZipCodes = explode(',', Setting::get('postal_codes_of_operation'));
            return $item->sender_country == 'pt' && $item->recipient_country == 'pt' && !in_array($zipCode, $regionalZipCodes);
        });

        $regionalShipments = $allShipments->filter(function ($item) {
            $zipCode = explode('-', $item->recipient_zip_code);
            $zipCode = @$zipCode[0];
            $regionalZipCodes = explode(',', Setting::get('postal_codes_of_operation'));
            return $item->sender_country == 'pt' && $item->recipient_country == 'pt' && in_array($zipCode, $regionalZipCodes);
        });

        $importShipments = $allShipments->filter(function ($item) {
            return $item->sender_country != 'pt' && $item->recipient_country == 'pt';
        });

        $exportShipments = $allShipments->filter(function ($item) {
            return $item->sender_country == 'pt' && $item->recipient_country != 'pt';
        });

        $values = [
            $regionalShipments->count(),
            $nacionalShipments->count(),
            $importShipments->count(),
            $exportShipments->count()
        ];

        $chartData = [
            'labels' => '"Regional","Nacional","Importação","Exportação"',
            'values' => implode(',', $values),
            'colors' => '"#8f34ad","#00adee","#fdcb20","#ff6384"',
        ];

        return $chartData;
    }

    /**
     * Return Billing chart
     */
    public static function getBillingChart($allShipments, $metric, $dateMin, $dateMax, $allIncidences = null)
    {

        if (is_null($allIncidences)) {
            $bindings = [
                'status_id',
                'incidence_id',
                'obs',
                DB::raw('DATE(created_at) as date'),
                DB::raw('DAY(created_at) as day'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('HOUR(created_at) as hour')
            ];

            $allIncidences = ShipmentHistory::whereBetween('created_at', [$dateMin . ' 00:00:00', $dateMax . ' 23:59:59'])
                ->where('status_id', ShippingStatus::INCIDENCE_ID)
                ->get($bindings);
        }


        $chartLabels = $billing = $shipments = $volumes = $incidences = [];

        if ($metric == 'daily') {
            $period = CarbonPeriod::create($dateMin, $dateMax);

            $dates = [];
            foreach ($period as $date) {
                $dates[] = $date->format('Y-m-d');
            }

            if (count($dates) > 1) {
                $allShipments  = $allShipments->groupBy('date');
                $allIncidences = $allIncidences->groupBy('date');
                $labels = $dates;
            } else {
                $allShipments  = $allShipments->groupBy('hour');
                $allIncidences = $allIncidences->groupBy('hour');
                $metric = 'hour';
                $labels = range(0, 23);
            }
        } elseif ($metric == 'monthly') {
            $dateMax = new Date($dateMin);
            $dateMax = $dateMax->endOfMonth()->format('Y-m-d');
            $period = CarbonPeriod::create($dateMin, $dateMax);
            $dates = [];
            foreach ($period as $date) {
                $dates[] = $date->format('Y-m-d');
            }
            $labels = $dates;
            $allShipments  = $allShipments->groupBy('date');
            $allIncidences = $allIncidences->groupBy('date');
        } elseif ($metric == 'yearly') {
            $labels = range(1, 12);
            $allShipments  = $allShipments->groupBy('month');
            $allIncidences = $allIncidences->groupBy('month');
        } else {
            $allShipments  = $allShipments->groupBy('date');
            $allIncidences = $allIncidences->groupBy('date');
        }

        foreach ($labels as $label) {

            if ($metric == 'monthly') {
                $labelParts = explode('-', $label);
                $chartLabels[] =  '"' . $labelParts['2'] . ' ' . trans('datetime.month-tiny.' . $labelParts['1']) . '"';
            } else if ($metric == 'yearly') {
                $chartLabels[] = '"' . trans('datetime.list-month-tiny.' . $label) . '"';
            } else if ($metric == 'hour') {
                $chartLabels[] = '"' . $label . 'h00"';
            } else {
                $chartLabels[] = '"' . $label . '"';
            }

            if (@$allShipments[$label]) {
                $billing[]    = @$allShipments[$label]->sum('total_price') + @$allShipments[$label]->sum('total_expenses') + @$allShipments[$label]->sum('fuel_price');
                $shipments[]  = @$allShipments[$label]->count();
                $volumes[]    = @$allShipments[$label]->sum('volumes');
                $incidences[] = @$allIncidences[$label] ? $allIncidences[$label]->count() : 0;
            } else {
                $billing[]    = 0;
                $shipments[]  = 0;
                $volumes[]    = 0;
                $incidences[] = 0;
            }
        }

        $chartData = [
            'labels'   => implode(',', $chartLabels),
            'billing'   => implode(',', $billing),
            'shipments' => implode(',', $shipments),
            'volumes'   => implode(',', $volumes),
            'incidences' => implode(',', $incidences),
        ];

        return $chartData;
    }

    /**
     * Return chart data for balance chart
     * @param $totals
     * @return array
     */
    public static function getCustomersChartData($totals)
    {

        $values = [
            @$totals['new']['count'],
            @$totals['active']['count'],
            @$totals['inactive']['count'],
        ];

        $chartData = [
            'labels' => '"Novos","Ativos","Perdidos"',
            'values' => implode(',', $values),
            'colors' => '"#605CA8","#3D9970","#ff851b"'
        ];

        return $chartData;
    }

    /**
     * Return chart data for balance chart
     * @param $totals
     * @return array
     */
    public static function getOperatorsAvgChartData($allShipments)
    {

        $operators = self::sortShipmentsArray($allShipments->groupBy('operator.name'));

        $incidenceShipments = $allShipments->filter(function ($item) {
            return !empty($item->operator_id) && $item->history->filter(function ($item) {
                return $item->status_id == ShippingStatus::INCIDENCE_ID;
            })->first();
        });

        $operatorIncidences = $incidenceShipments->groupBy('operator.name');

        $labels = $shipments = $volumes = $weight = $incidences = [];
        foreach ($operators as $operatorName => $data) {
            if (!empty($operatorName)) {
                $labels[]    = '"' . ($operatorName ? str_limit($operatorName, 15) : 'Sem operador') . '"';
                $shipments[] = $data['count'];
                $volumes[]   = $data['volumes'];
                $weight[]    = $data['weight'];
                $incidences[] = @$operatorIncidences[$operatorName] ? @$operatorIncidences[$operatorName]->count() : 0;
            }
        }

        $chartData = [
            'labels'    => implode(',', $labels),
            'shipments' => implode(',', $shipments),
            'volumes'   => implode(',', $volumes),
            'weight'    => implode(',', $weight),
            'incidences' => implode(',', $incidences),
        ];

        return $chartData;
    }

    /**
     * getProspect History
     */
    public static function getProspectHistory($startDate, $endDate, $sellers)
    {

        $allHistories = CustomerBusinessHistory::with('operator')
            ->whereHas('customer', function ($q) {
                //$q->filterSource();
            })
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();

        $allHistories = $allHistories->groupBy('operator.id');

        $prospectHistory = [];
        foreach ($sellers as $seller) {

            $sellerHistories = @$allHistories[$seller->id];

            if (!empty($sellerHistories)) {

                $sellerHistories = $sellerHistories->sortBy('status')->groupBy('status');

                foreach ($sellerHistories as $status => $histories) {
                    $data[$status] = $histories->count();
                }
            } else {
                $data = [];
            }

            $data['meetings'] = @$seller->meetings->count();

            $prospectHistory[@$seller->name ? $seller->name : 'Sem comercial'] = $data;
        }

        return $prospectHistory;
    }


    /**
     * Return detail for balance
     *
     * @param $allShipments
     * @return array
     */
    public static function getBallanceDetails($billingTotals)
    {

        $totalValue = @$billingTotals['shipments']['total'] +
            @$billingTotals['covenants']['total'] +
            @$billingTotals['products']['total'] +
            @$billingTotals['fleet']['total'] +
            @$billingTotals['employees']['total'] +
            @$billingTotals['other']['total'];


        $data['shipments'] = [
            'name'    => 'Serviços realizados (Envios, Recolhas e Outros)',
            'gain'    => @$billingTotals['shipments']['total'],
            'cost'    => @$billingTotals['shipments']['costs'],
            'balance' => @$billingTotals['shipments']['total'] - @$billingTotals['shipments']['costs'],
            'sense'   => 'all',
            'module'  => true
        ];

        $data['fleet'] = [
            'name'    => 'Despesas com Viaturas, Frota e Colaboradores',
            'gain'    => @$billingTotals['fleet']['total'],
            'cost'    => @$billingTotals['fleet']['costs'],
            'balance' => @$billingTotals['fleet']['total'] - @$billingTotals['fleet']['costs'],
            'sense'   => 'cost',
            'module'  => hasModule('fleet')
        ];

        $data['covenants'] = [
            'name'    => 'Avenças Mensais',
            'gain'    => @$billingTotals['covenants']['total'],
            'cost'    => 0,
            'balance' => @$billingTotals['covenants']['total'],
            'sense'   => 'gain',
            'module'  => true
        ];

        $data['customers'] = [
            'name'    => 'Despesas gerais com clientes',
            'gain'    => @$billingTotals['customers']['total'],
            'cost'    => @$billingTotals['customers']['costs'],
            'balance' => @$billingTotals['customers']['total'] - @$billingTotals['customers']['costs'],
            'sense'   => 'cost',
            'module'  => true
        ];

        /*$data['employees'] = [
            'name'    => 'Funcionários e Motoristas',
            'gain'    => @$billingTotals['employees']['total'],
            'cost'    => @$billingTotals['employees']['costs'],
            'balance' => @$billingTotals['employees']['total'] - @$billingTotals['employees']['costs'],
            'sense'   => 'cost',
            'module'  => hasModule('hresources')
        ];*/

        $data['other'] = [
            'name'    => 'Outras faturas emitidas e despesas',
            'gain'    => @$billingTotals['other']['total'],
            'cost'    => @$billingTotals['other']['costs'],
            'balance' => @$billingTotals['other']['total'] - @$billingTotals['other']['costs'],
            'sense'   => 'all',
            'module'  => hasModule('purchase_invoices')
        ];

        $data['products'] = [
            'name'    => 'Artigos e produtos vendidos',
            'gain'    => @$billingTotals['products']['total'],
            'cost'    => @$billingTotals['products']['costs'],
            'balance' => @$billingTotals['products']['total'] - @$billingTotals['products']['costs'],
            'sense'   => 'all',
            'module'  => hasModule('products')
        ];

        $totalBalances = 0;
        foreach ($data as $key => $item) {
            $totalBalances += $item['balance'] > 0.00 ? $item['balance'] : -1 * $item['balance'];
        }

        $arr = [];
        foreach ($data as $key => $item) {
            $arr[$key] = $item;

            /*$gain = $arr[$key]['gain'];

            if($gain < 0.00) {
                $gain = $gain * -1;
            }

            $impact = $totalValue > 0 ? (($gain * 100) / $totalValue) : 0;*/

            $balance = $arr[$key]['balance'];
            $balance = $balance > 0.00 ? $balance : -1 * $balance;

            if ($totalBalances != 0.0)
                $impact = ($balance * 100) / $totalBalances;
            else
                $impact = 0.0;

            $arr[$key]['impact'] = $impact;
        }

        return $arr;
    }

    public static function getIncidencesTotals($dateMin, $dateMax, $allShipments)
    {

        $bindings = [
            'id',
            'shipment_id',
            'status_id',
            'incidence_id',
            'obs',
            DB::raw('DATE(created_at) as date'),
            DB::raw('DAY(created_at) as day'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('HOUR(created_at) as hour')
        ];

        $incidenceShipmentIds = $allShipments->filter(function ($item) {
            return $item->history->filter(function ($item) {
                return $item->status_id == ShippingStatus::INCIDENCE_ID;
            })->first();
        })->pluck('id');

        // $allIncidences = ShipmentHistory::with(['shipment' => function ($q) {
        //     $q->select([
        //         'id',
        //         'customer_id',
        //         'provider_id',
        //         'operator_id',
        //         'service_id'
        //     ]);
        // }])
        //     ->whereBetween('created_at', [$dateMin . ' 00:00:00', $dateMax . ' 23:59:59'])
        //     ->where('status_id', ShippingStatus::INCIDENCE_ID)
        //     ->get($bindings);

        $allIncidences = ShipmentHistory::with(['shipment' => function ($q) {
            $q->select([
                'id',
                'customer_id',
                'provider_id',
                'operator_id',
                'service_id'
            ]);
        }])
            ->whereIn('shipment_id', $incidenceShipmentIds)
            ->where('status_id', ShippingStatus::INCIDENCE_ID)
            ->get($bindings);

        $types = IncidenceType::remember(config('cache.query_ttl'))
            ->cacheTags(IncidenceType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();


        $totalIncidences = $allIncidences->count();
        $incidencesByType = $allIncidences->groupBy('incidence_id');

        $incidences = [];
        foreach ($incidencesByType as $typeId => $items) {
            $incidences[@$types[$typeId]] = $items->count();
        }
        arsort($incidences);


        $incidenceShipments = $allShipments->filter(function ($item) {
            return $item->history->filter(function ($item) {
                return $item->status_id == ShippingStatus::INCIDENCE_ID;
            })->first();
        });

        $incidencesByProvider = $incidenceShipments->groupBy('provider.name');
        $incidencesByCustomer = $incidenceShipments->groupBy('customer.name')->take(6);
        $incidencesByService  = $incidenceShipments->groupBy('service.display_code');

        $data = [
            'total'     => $totalIncidences,
            'types'     => $incidences,
            'providers' => $incidencesByProvider,
            'customers' => $incidencesByCustomer,
            'services'  => $incidencesByService,
        ];

        return $data;
    }

    /**
     * Return customers totals
     */
    public static function getCustomersTotals($allShipments, $startDate, $endDate)
    {

        $startDate .= ' 00:00:00';
        $endDate .= ' 23:59:59';

        $customers = Customer::filterSource()
            ->filterAgencies()
            ->filterSeller()
            ->isProspect(false)
            ->isDepartment(false)
            ->get([
                'customers.id',
                'customers.created_at',
                'customers.is_active',
                DB::raw('(select max(date) from shipments where shipments.customer_id = customers.id and deleted_at is null limit 0,1) as last_shipment')
            ]);

        $totalCustomers = $customers->filter(function ($item) {
            return $item->is_active == 1;
        })->count();

        $newCustomers = $customers->filter(function ($item) use ($startDate, $endDate) {
            return $item->created_at >= $startDate && $item->created_at <= $endDate;
        })->count();

        $inactiveCustomers = $customers->filter(function ($item) use ($startDate, $endDate) {
            $startDate = new Date($startDate);
            $startDate = $startDate->subDays(Setting::get('alert_max_days_without_shipments'))->format('Y-m-d');

            $endDate = new Date($endDate);
            $endDate = $endDate->subDays(Setting::get('alert_max_days_without_shipments'))->format('Y-m-d');

            return $item->last_shipment >= $startDate && $item->last_shipment <= $endDate;
        })->count();

        $activeCustomers = $allShipments->groupBy('customer_id')->count();

        $data = [
            'total' => [
                'count' => $totalCustomers,
            ],
            'new' => [
                'count'   => $newCustomers,
                'percent' => $totalCustomers ? ($newCustomers * 100) / $totalCustomers : 0
            ],
            'inactive' => [
                'count'   => $inactiveCustomers,
                'percent' => $totalCustomers ? ($inactiveCustomers * 100) / $totalCustomers : 0
            ],
            'active'   => [
                'count'   => $activeCustomers,
                'percent' => $totalCustomers ? ($activeCustomers * 100) / $totalCustomers : 0
            ],
        ];

        return $data;
    }

    /**
     * Sort shipments array
     *
     * @param $dataCollection
     * @param $sortByField
     * @return array
     */
    public static function sortShipmentsArray($dataCollection, $sortByField = null)
    {

        $arr = [];
        foreach ($dataCollection as $key => $values) {
            $arr[$key] = [
                'customerData'  => @$values->first()->customer,
                'seller_id'     => @$values->first()->customer->seller_id,
                'color'         => @$values->first()->provider->color,
                'count'         => $values->count(),
                'volumes'       => $values->sum('volumes'),
                'volumes_avg'   => number($values->avg('volumes')),
                'weight'        => $values->sum('weight'),
                'weight_avg'    => number($values->avg('weight')),
                'price'         => $values->sum('total_price'),
                'price_avg'     => number($values->avg('total_price')),
                'expenses'      => $values->sum('total_expenses'),
                'fuel_price'    => $values->sum('fuel_price'),
                'cost'          => $values->sum('cost_billing_subtotal') + $values->sum('total_expenses_cost'),
                'cost_avg'      => number($values->avg('cost_billing_subtotal') + $values->avg('total_expenses_cost')),
                'total'         => $values->sum('total_price') + $values->sum('total_expenses') + $values->sum('fuel_price') + $values->sum('total_price_for_recipient')
            ];
        }

        if ($sortByField) {
            aasort($arr, $sortByField, SORT_DESC);
        }

        return $arr;
    }

    /**
     * Get fleet balance costs
     * @param $statt
     */
    public static function getFleetBalanceCosts($startDate, $endDate, $ids = [], $groupBy = null)
    {


        //1. obtem custos lançados no módulo de frota
        $vehicleExpenses = Cost::filterSource()
            ->with('provider')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($ids) {
            $vehicleExpenses = $vehicleExpenses->whereIn('id', $ids);
        }

        $vehicleExpenses = $vehicleExpenses->get();


        //2. obtem despesas lançadas no módulo de despesas mas que não estão contempladas no módulo de frota
        $invoices = PurchaseInvoice::filterSource()
            ->with('type')
            ->whereIn('target', ['Vehicle', 'User'])
            ->where('ignore_stats', 0)
            ->whereBetween('doc_date', [$startDate, $endDate]);

        if ($ids) {
            $invoices = $invoices->where('target', 'Vehicle')
                ->whereIn('target_id', $ids);
        }

        $invoices = $invoices->get(['id', 'type_id', 'target', 'target_id', DB::raw('subtotal as total')]);


        //3. Obtem as matrículas únicas das viaturas
        $vehiclesIds1 = $vehicleExpenses->pluck('vehicle_id')->toArray();
        $vehiclesIds2 = $invoices->filter(function ($item) {
            return $item->target == 'Vehicle';
        })->pluck('target_id')->toArray();
        $vehiclesIds  = array_unique(array_merge($vehiclesIds1, $vehiclesIds2));


        //4. Obtem as matrículas únicas das viaturas
        $allVehicles   = \App\Models\FleetGest\Vehicle::whereIn('id', $vehiclesIds)->get(['id', 'license_plate', 'name']);

        //5. Obtem todos os envios executados pelas viaturas selecionadas
        $allShipments = Shipment::whereIn('vehicle', $allVehicles->pluck('license_plate')->toArray())
            ->whereBetween('date', [$startDate, $endDate])
            ->get([
                'vehicle',
                'operator_id',
                'total_price',
                'total_expenses'
            ]);

        //5. A partir dos envios encontrados, obtem a lista de motoristas envolvidos
        $operatorsIds1 = $invoices->filter(function ($item) {
            return $item->target == 'User';
        })->pluck('target_id')->toArray();
        $operatorsIds2 = $allShipments->pluck('operator_id')->toArray();
        $operatorsIds  = array_unique(array_merge($operatorsIds1, $operatorsIds2));
        $allOperators  = User::whereIn('id', $operatorsIds)->pluck('name', 'id');

        $resultData = [];
        foreach ($allVehicles as $vehicle) {

            $licensePlate = $vehicle->license_plate;
            $vehicleId    = $vehicle->id;
            $shipments    = $allShipments->filter(function ($item) use ($licensePlate) {
                return $item->vehicle == $licensePlate;
            });

            $shipmentsByOperator = $shipments->groupBy('operator_id');

            $resultData[$licensePlate] = [
                'id'            => $vehicle->id,
                'license_plate' => $licensePlate,
                'name'          => $vehicle->name
            ];

            //calcula despesas por tipo
            $resultData[$licensePlate]['fuel'] = $vehicleExpenses->filter(function ($item) use ($vehicleId) {
                return $item->source_type == 'FuelLog' && $item->vehicle_id == $vehicleId;
            })->sum('total');

            //manutenções
            $resultData[$licensePlate]['maintenances'] = $vehicleExpenses->filter(function ($item) use ($vehicleId) {
                return $item->source_type == 'Maintenance' && $item->vehicle_id == $vehicleId;
            })->sum('total');

            //portagens
            $resultData[$licensePlate]['tolls'] = $vehicleExpenses->filter(function ($item) use ($vehicleId) {
                return $item->source_type == 'TollLog' && $item->vehicle_id == $vehicleId;
            })->sum('total');

            //despesas fixas
            $resultData[$licensePlate]['fixed'] = $vehicleExpenses->filter(function ($item) use ($vehicleId) {
                return $item->source_type == 'FixedCost' && $item->vehicle_id == $vehicleId;
            })->sum('total');

            //despesas gerais
            $resultData[$licensePlate]['expenses'] = $vehicleExpenses->filter(function ($item) use ($vehicleId) {
                return $item->source_type == 'Expense' && $item->vehicle_id == $vehicleId;
            })->sum('total');

            //outras despesas gerais
            $resultData[$licensePlate]['others'] = $invoices->filter(function ($item) use ($vehicleId) {
                return $item->target == 'Vehicle' && $item->target_id == $vehicleId;
            })->sum('total');
            $resultData[$licensePlate]['expenses'] += $resultData[$licensePlate]['others'];


            $resultData[$licensePlate]['operators'] = $invoices->filter(function ($item) use ($operatorsIds) {
                return $item->target == 'User' && in_array($item->target_id, $operatorsIds);
            })->sum('total');

            $resultData[$licensePlate]['operators'] = 0;

            //obtem detalhes por colaborador
            /*$operatorsDetails = [];
            foreach ($shipmentsByOperator as $operatorId => $operatorData) {

                $operatorsDetails[$operatorId] = [
                    'id'    => $operatorId,
                    'name'  => @$allOperators[$operatorId],
                    'gains' => $operatorData->sum('total_price') + $operatorData->sum('total_expenses')
                ];

                $operatorCosts = $invoices->filter(function($item) use($operatorId) {
                    return $item->target == 'User' && $item->target_id == $operatorId;
                })->groupBy('type.name');

                foreach ($operatorCosts as $typeName => $operatorCost) {
                    $operatorsDetails[$operatorId][$typeName] = $operatorCost->sum('total');
                }
            }

            //dd($operatorsDetails);
            $resultData[$licensePlate]['operator_details'] = $operatorsDetails;*/

            $resultData[$licensePlate]['total_expenses'] = $resultData[$licensePlate]['fuel']
                + $resultData[$licensePlate]['fuel']
                + $resultData[$licensePlate]['maintenances']
                + $resultData[$licensePlate]['tolls']
                + $resultData[$licensePlate]['expenses']
                + $resultData[$licensePlate]['fixed']
                + $resultData[$licensePlate]['others'];

            $resultData[$licensePlate]['total_gains'] = $shipments->sum('total_price') + $shipments->sum('total_expenses');

            $resultData[$licensePlate]['total_balance'] = $resultData[$licensePlate]['total_gains'] - $resultData[$licensePlate]['total_expenses'];
        }

        return $resultData;
    }
}
