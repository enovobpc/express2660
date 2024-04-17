<?php

namespace App\Http\Controllers\Account;

use App\Models\ZipCode\AgencyZipCode;
use App\Models\CustomerWebservice;
use App\Models\Map;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShippingExpense;
use App\Models\Webservice\DbSchenker;
use App\Models\Webservice\Fedex;
use App\Models\Webservice\TntExpress;
use App\Models\Webservice\Ups;
use App\Models\WebserviceConfig;
use App\Models\ZipCode;
use Illuminate\Http\Request;
use App\Models\PackType;
use App\Models\Budgeter;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;
use Setting;

class BudgeterController extends \App\Http\Controllers\Controller
{
    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.account';

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'budgeter';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Customer billing index controller
     *
     * @return type
     */
    public function index(Request $request) {

        // $packTypesCollection = PackType::get();
        $packTypesCollection = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get();

        $packTypes = [];
        foreach ($packTypesCollection as $packType) {
            $packTypes[$packType->code] = [
                'type' => @$packType->type,
                'name' => @$packType->name
            ];
        }

        return $this->setContent('account.budgeter.index', compact('packTypes'));
    }

    /**
     * Calc budgeter
     *
     * @param Request $request
     * @return \App\Http\Controllers\type
     */
    public function calc(Request $request) {

        $customer = Auth::guard('customer')->user();

        $vatEnabled = $request->get('vat_enabled', false);
        $params = $request->toArray();
        $params['show_empty_prices'] = $request->get('empty_prices', false);

        $params['customer_id'] = $customer->id;
        $params['weight'] = $params['volumes'] = $params['fator_m3'] = 0;

        foreach ($params['pack_weight'] as $key => $value) {
            $params['volumes']++;
            $params['weight']+= forceDecimal(@$params['pack_weight'][$key]);
        }

        if (empty($params['sender_zip_code']) || empty($params['recipient_zip_code'])) {
            return response()->json([
                'html'            => view('account.budgeter.partials.results_list', ['services' => []])->render(),
                'pickupTypeLabel' => '',
                'countServices'   => 0
            ]);
        }

        $services = Budgeter::calcPrices($params);
        $services = $services->sortBy(function ($service, $key) {
            return $service->prices['shipping_price'];
        });

        /* $shipment = new Shipment();
        $shipment->date = $request->pickup_date;
        $shipment->sender_country     = $request->sender_country;
        $shipment->sender_zip_code    = $request->sender_zip_code;
        $shipment->recipient_country  = $request->recipient_country;
        $shipment->recipient_zip_code = $request->recipient_zip_code;
        $shipment->volumes            = $params['volumes'];
        $shipment->weight             = $params['weight'];
        $shipment->fator_m3           = $request->fator_m3; */

        $pickupType = $this->getPickupType($request);

        $date = $request->get('date', date('Y-m-d'));
        if ($pickupType['type'] == 'pickup' && $date <= date('Y-m-d')) {
            $date = date('Y-m-d', strtotime('+1 day'));
        }
        
        $response = [
            'html'            => view('account.budgeter.partials.results_list', compact('services', 'vatEnabled', 'shipment', 'pickupType'))->render(),
            'pickupTypeLabel' => $pickupType['label'],
            'countServices'   => count($services),
            'date'            => $date
        ];

        return response()->json($response);
    }

    /**
     * Get Pickup type (normal/pickup/intercidades)
     * @param $request
     * @return array
     */
    public function getPickupType($request) {

        // $regionalZones = explode(',', trim(Setting::get('postal_codes_of_operation')));
        // array_filter($regionalZones);

        $regionalZones = [Auth::guard('customer')->user()->zip_code];

        if(!in_array($request->sender_zip_code, $regionalZones) && in_array($request->recipient_zip_code, $regionalZones)) {
            return [
                'label'     => 'Recogida',
                'type'      => 'pickup',
                'intercity' => false
            ];
        } elseif(!in_array($request->sender_zip_code, $regionalZones) && !in_array($request->recipient_zip_code, $regionalZones)) {
            return [
                'label'     => 'Intercidades',
                'type'      => 'pickup',
                'intercity' => true
            ];
        }

        return [
            'label' => 'Normal',
            'type'  => 'shipment'
        ];
    }


    /**
     * Return customer recipient details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function modalBudget(Request $request) {

        $customer = Auth::guard('customer')->user();

        $packTypes = PackType::pluck('name', 'code')->toArray();

        if(empty($customer->enabled_services)) {
            $services = [];
        } else {
            $allServices = Service::remember(config('cache.query_ttl'))
                ->cacheTags(Service::CACHE_TAG)
                ->whereIn('id', $customer->enabled_services)
                ->isShipment()
                ->ordered()
                ->get();

            $shipmentController = new ShipmentsController();
            $services = $shipmentController->listServices($allServices, true);
        }

        $complementarServices = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            ->filterSource()
            ->where('account_complementar_service', 1)
            ->ordered()
            ->get();

        $data = compact(
            'services',
            'packTypes',
            'complementarServices'
        );

        return view('account.budgeter.modal.budget', $data);
    }

    /**
     * Calculate budger prices
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function modalCalc(Request $request) {

        $customer = Auth::guard('customer')->user();

        $params = $request->toArray();
        $params['services']    = [$params['service_id']];
        $params['customer_id'] = $customer->id;
        $params['weight'] = $params['volumes'] = $params['fator_m3'] = 0;

        foreach ($params['pack_weight'] as $key => $value) {
            $params['volumes']++;
            $params['weight']+= forceDecimal(@$params['pack_weight'][$key]);
        }

        //prepara as taxas manuais
        $expensesIds = [];
        if($request->get('optional_fields')) {
            $expensesVals = array_filter($request->get('optional_fields'));
            $expensesIds  = array_keys($expensesVals);
            $expensesIds  = array_filter($expensesIds);
        }

        $subtotal = $vat = $total = 0;
        $costSubtotal = $costVat = $costTotal = 0;
        $manualExpenses = [];
        if(!empty($expensesIds)) {

            $shipment = new Shipment();
            $shipment->fill($params);

            //para cada despesa ativa, vai calcular individualmente o seu preço e adiciona o preço a variavel manualExpenses
            $expenses = ShippingExpense::whereIn('id', $expensesIds)->get();

            foreach ($expenses as $expense) {

                $expense->qty   = $expensesVals[$expense->id];
                $expenseDetails = $shipment->calcExpensePrice($expense);

                if($expenseDetails) {

                    $manualExpenses[] = [
                        'expense_id'      => $expense->id,
                        'qty'             => $expenseDetails['billing']['qty'],
                        'price'           => $expenseDetails['billing']['price'],
                        'subtotal'        => $expenseDetails['billing']['subtotal'],
                        'vat'             => $expenseDetails['billing']['vat'],
                        'total'           => $expenseDetails['billing']['total'],
                        'vat_rate'        => $expenseDetails['billing']['vat_rate'],
                        'vat_rate_id'     => $expenseDetails['billing']['vat_rate_id'],
                        'unity'           => @$expenseDetails['billing']['unity'],

                        'cost_price'      => $expenseDetails['cost']['price'],
                        'cost_subtotal'   => $expenseDetails['cost']['subtotal'],
                        'cost_vat'        => $expenseDetails['cost']['vat'],
                        'cost_total'      => $expenseDetails['cost']['total']
                    ];

                    $subtotal     += (float) @$expenseDetails['billing']['subtotal'];
                    $vat          += (float) @$expenseDetails['billing']['vat'];
                    $total        += (float) @$expenseDetails['billing']['total'];
                    $costSubtotal += (float) @$expenseDetails['cost']['subtotal'];
                    $costVat      += (float) @$expenseDetails['cost']['vat'];
                    $costTotal    += (float) @$expenseDetails['cost']['total'];
                }
            }
        }

        //calcula orçamento
        $servicesPrices = Budgeter::calcPrices($params, $manualExpenses);
        $prices = $servicesPrices->first();
        $prices = @$prices->details;


        if(empty($prices)) { //não existe compatibilidade do serviço.
            $prices = [
                'errors' => ['O serviço selecionado não está disponível para os dados inseridos.<br/><a href="mailto:'.@$customer->agency->email.'" class="m-t-15 btn btn-sm btn-default">Pedir Orçamento</a>']
            ];
        } elseif(!empty(@$prices['errors'])) { //se tiver erros, força a colocar as variaveis a 0
            $prices['billing']['subtotal'] = 0;
            $prices['billing']['vat']      = 0;
            $prices['billing']['total']    = 0;
        }

        $response = [
            'result' => true,
            'html'   => view('account.budgeter.modal.partials.price_preview', compact('prices'))->render()
        ];

        return response()->json($response);
    }

    /**
     * Calculate distance in km
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calcDistance(Request $request) {

        $response = Map::getDistance(
            $request->sender_zip_code,
            $request->recipient_zip_code,
            $request->sender_country,
            $request->recipient_country
        );

        return response()->json($response);
    }

    /**
     * Calculate distance in km
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransitTime(Request $request) {

/*        $request->service = 201;
        $request->provider = 'db_schenker';
$request->recipient_country = 'es';*/

        $customer = Auth::guard('customer')->user();

        $customerWebservice = CustomerWebservice::where('customer_id', $customer->id)
            ->where('method', $request->provider)
            ->first();

        $webserviceConfigs = WebserviceConfig::where('method', $request->provider)->first();
        $providerServices  = @$webserviceConfigs->mapping_services;

        if($customerWebservice || $request->provider == 'db_schenker') {

            // $params = [
            //     'sender_country'    => 'pt', //$request->sender_country,
            //     'sender_zip_code'   => '1000-120', //$request->sender_zip_code,
            //     'recipient_country' => 'es', //$request->recipient_country,
            //     'recipient_zip_code'=> '46001', //$request->recipient_zip_code,
            //     'pickup_date'       => '2022-12-09' //$request->date,
            // ];

            $params = [
                'sender_country'     => $request->sender_country,
                'sender_zip_code'    => $request->sender_zip_code,
                'sender_city'        => $request->sender_city,
                'recipient_country'  => $request->recipient_country,
                'recipient_zip_code' => $request->recipient_zip_code,
                'recipient_city'     => $request->recipient_city,
                'pickup_date'        => $request->date,
                'service'            => $request->service
            ];


            $webservice = null;
            if($request->provider == 'db_schenker') {
                $webservice = new DbSchenker();
            } else if($request->provider == 'tnt_express') {
                $webservice = new TntExpress(@$customerWebservice->agency, @$customerWebservice->user, @$customerWebservice->password);
            } else if($request->provider == 'ups') {
                $webservice = new Ups(@$customerWebservice->agency, @$customerWebservice->user, @$customerWebservice->password, @$customerWebservice->session_id);
            }

           /* else if($request->provider == 'fedex') {
                $webservice = new Fedex();
            }*/

            if($webservice) {

                $webserviceCountry = in_array($request->recipient_country, ['pt', 'es']) ? $request->recipient_country : 'int';
                $params['service'] = @$providerServices[$request->service][$request->zone ? $request->zone : $webserviceCountry];

                $transitTime  = $webservice->calcTransitTime($params);
                $deliveryDate = @$transitTime['delivery_date'];
                $transitTime  = @$transitTime['transit_time'];

                $response = [
                    'html' => $deliveryDate . '<br/>Hasta las 19:00',
                    'delivery_date' => $deliveryDate,
                    'transit_time'  => $transitTime
                ];

                return response()->json($response);
            }
        }

        $response = [
            'html' => null,
            'delivery_date' => null,
            'transit_time'  => null
        ];

        return response()->json($response);
    }
}