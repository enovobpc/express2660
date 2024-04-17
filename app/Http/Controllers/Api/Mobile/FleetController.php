<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Agency;
use App\Models\FleetGest\FuelLog;
use App\Models\FleetGest\UsageLog;
use App\Models\FleetGest\Vehicle;
use App\Models\IncidenceType;
use App\Models\OperatorTask;
use App\Models\Provider;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth, Validator, Setting, Mail, Date;

class FleetController extends \App\Http\Controllers\Api\Mobile\BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Lists all customers
     *
     * @param Request $request
     * @return mixed
     */
    public function lists(Request $request) {

        $bindings = [
            'id',
            'license_plate',
            'code',
            'name',
            'brand_id',
        ];

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $vehicles = Vehicle::with(['brand' => function($q){
                $q->select(['id', 'name']);
            }])
            ->isActive()
            ->where('source', config('app.source'))
            ->orderBy('name', 'asc')
            ->get($bindings);

        if($vehicles->isEmpty()) {
            return $this->responseError('fleet', '-001') ;
        }

        return response($vehicles, 200)->header('Content-Type', 'application/json');
    }


    /**
     * Lists all customers
     *
     * @param Request $request
     * @return mixed
     */
    public function providers(Request $request) {

        $bindings = ['id','code','name'];

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002');
        }

        if(empty($request->type)) {
            return $this->responseError('fleet', '-005');
        }

        $categories = [$request->type];
        if($request->type == 'all') {
            $categories = [
                'mechanic',
                'gas_station',
                'insurer',
                'car_inspection',
                'tolls'
            ];
        }

        $providers = Provider::where('source', config('app.source'))
            ->whereIn('category', $categories)
            ->orderBy('name', 'asc')
            ->get($bindings);

        if($providers->isEmpty()) {
            return $this->responseError('fleet', '-001') ;
        }

        return response($providers, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Store fuel
     *
     * @param Request $request
     * @return mixed
     */
    public function fuel(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        //store User Location
        $lat = $request->get('latitude');
        $lng = $request->get('longitude');
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($user, $lat, $lng);
        }

        try {

            $authorizedFields = [
                'total',
                'liters',
                'price_liter',
                'kms',
                'provider',
                'vehicle',
                'date',
                'product',
                'obs'
            ];

            $input = $request->only($authorizedFields);

            if($input['vehicle']) {
                $vehicle = Vehicle::where('source', config('app.source'))
                    ->where('license_plate', $input['vehicle'])
                    ->first();

                if(!$vehicle) {
                    return $this->responseError('fleet', '-001', 'Não existe nenhuma viatura com a matrícula '. $input['vehicle']) ;
                }
            }


            $input['km']     = (int) $input['kms'];
            $input['liters'] = forceDecimal($input['liters']);
            $input['total']  = forceDecimal($input['total']);
            $input['price_per_liter'] = $input['total'] / $input['liters'];
            $input['provider_id'] = $input['provider'];
            $input['vehicle_id']  = $vehicle->id;
            $input['date'] = empty($input['date']) ? date('Y-m-d') : $input['date'];
            //$input['product'] = empty($input['product']) ? 'fuel' : $input['product'];

            //até os clientes terem a nova versão da aplicação depois substituir pelo if de cima 
            if($input['product']){
                if($input['product'] == 'fuel' || $input['product'] == 'diesel'){
                    $input['product'] = 'fuel';
                }else{
                    $input['product'] = $input['product'];
                }
            }



            $lastFuel = FuelLog::where('vehicle_id', $vehicle->id)
                ->orderBy('km', 'desc')
                ->first();

            /*if($input['km'] < $lastFuel->km) {
                return $this->responseError('fleet', '-002', 'Os KM atuais são inferiores ao ultimo registo inserido (' .$lastFuel->km.')') ;
            }*/


     /*       Mail::raw(print_r($input, true), function($message) {
                $message->to('paulo.costa@enovo.pt')
                    ->subject('RESPONSE');
            });*/

            $fuelLog = new FuelLog();
            if ($fuelLog->validate($input)) {
                $fuelLog->fill($input);
                $fuelLog->operator_id = $user->id;
                $fuelLog->created_by  = $user->id;
                $fuelLog->save();

                FuelLog::updateVehicleCounters($fuelLog->vehicle_id);

                $result = [
                    'code' => '',
                    'feedback' => 'Registo gravado com sucesso.'
                ];
            } else {
                return $this->responseError('fleet', '-999', $fuelLog->errors()->first());
            }

        } catch (\Exception $e) {
            return $this->responseError('fleet', '-999', $e->getMessage().' file '. $e->getFile(). ' line ' . $e->getLine());
        }

        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Store shipment status
     *
     * @param Request $request
     * @return mixed
     */
    public function storeDriveLog(Request $request) {


        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002');
        }

        if(empty($request->vehicle)) {
            return $this->responseError('fleet', '-002');
        }

        if(empty($request->kms)) {
            return $this->responseError('fleet', '-003');
        }

        //store User Location
        $lat = $request->get('latitude');
        $lng = $request->get('longitude');
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($user, $lat, $lng);
        }

        if($request->vehicle) {
            $vehicle = Vehicle::where('source', config('app.source'))
                ->where('license_plate', $request->vehicle)
                ->first();

            if(!$vehicle) {
                return $this->responseError('fleet', '-001', 'Não existe nenhuma viatura com a matrícula '. $request->vehicle) ;
            }
        }

        //verify if customer has active logs
        $log = UsageLog::firstOrNew([
                'vehicle_id'  => $vehicle->id,
                'operator_id' => $user->id
            ]);

        if($log->exists) {

            if($request->kms <= $log->start_km) {
                return $this->responseError('fleet', '-004');
            }

            $log->end_date = date('Y-m-d H:i:s');
            $log->end_km   = $request->kms;
            $log->save();
        } else {
            $log->start_date = date('Y-m-d H:i:s');
            $log->start_km   = $request->kms;
            $log->save();
        }

        $result = [
            'code' => '',
            'feedback' => 'Log saved sucessfully'
        ];

        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * List of driver usage history logs 
     * @param Request $request
    */
    public function listUsagesLogs(Request $request){

        $bindings = [
            'id',
            'vehicle_id',
            'operator_id',
            'type',
            'start_date',
            'end_date',
            'start_km',
            'end_km',

        ];

        $dateNow      = (new Carbon)->now()->endOfDay()->toDateString();
        $dateLastFive = (new Carbon())->subDays(5)->startOfDay()->toDateString();

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $usageLog = UsageLog::with(['vehicle' =>  function($q){
                    $q->select(['id', 'name']);
                    }])
                    ->where('operator_id', $user->id)
                    ->whereBetween('start_date', [$dateLastFive.' '.'00:00:00', $dateNow.' '.'23:59:59'])
                    ->orderBy('start_date', 'desc')
                    ->get($bindings);
                    
               
        $dataArr = [];
        
        
        foreach($usageLog as $item){
            
            $item->{'start_hour'} = $item->start_date->format('H:i');
            $item->{'end_hour'}   = $item->end_date->format('H:i');
            $item->{'color_type'} = trans('admin/fleet.usages-logs.types-color.'.$item->type);
            $item->{'km_total'}   = number($item->end_km - $item->start_km);
            $item->{'hour_total'} = $item->start_date->diff($item->end_date)->format('%H:%I:%S');
            
            $dataArr [] = $item;
           
        }
        
        $usageLog = $dataArr;

   
        return response($usageLog, 200)->header('Content-Type', 'application/json');

    }
    
    /**
     * Store usage log 
     * @param Request $request
     */

     public function storeUsagesLogs(Request $request){

        UsageLog::flushCache(UsageLog::CACHE_TAG);

        $input = $request->all();

        $input['start_date'] = $input['start_date'] . ' ' .$input['start_hour'].':00';
        $input['end_date']   = $input['end_date'] . ' ' .$input['end_hour'].':00';

        $user = $this->getUser($input['user']);

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        if($input['vehicle']){

            $vehicle = Vehicle::where('source', config('app.source'))
            ->where('license_plate', $input['vehicle'])
            ->first();

            if(!$vehicle) {
                return $this->responseError('fleet', '-001', 'Não existe nenhuma viatura com a matrícula '. $request->vehicle) ;
            }
        }

        if(($input['type'] == 'driving' || $input['type'] == 'outsourced') &&  ($input['end_km'] <= $input['start_km'])) {
            return $this->responseError('fleet', '-004');
        }

        //verify if customer has active logs
        $log = new UsageLog();

        if ($log->validate($input)) {
            
            $log->fill($input);
            
            $log->operator_id = $user->id;
            $log->vehicle_id  = @$vehicle->id;
            
            $log->save();
        }

        $result = [
            'code' => '',
            'feedback' => 'Log saved sucessfully'
        ];

        return response($result, 200)->header('Content-Type', 'application/json');

     }
}