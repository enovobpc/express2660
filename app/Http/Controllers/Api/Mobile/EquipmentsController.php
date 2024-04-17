<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Equipment\Equipment;
use App\Models\Equipment\Location;
use App\Models\Equipment\Category;
use App\Models\Equipment\History;
use App\Models\Equipment\Warehouse;
use Illuminate\Http\Request;

class EquipmentsController extends \App\Http\Controllers\Api\Mobile\BaseController{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Bindings
     *
     * @var array
     */
    protected $equipmentsBindings = [
        'id',
        'sku',
        'name',
        'customer_id',
        'category_id',
        'warehouse_id',
        'location_id',
        'description',
        'serial_no',
        'lote',
        'width',
        'height',
        'length',
        'weight',
        'stock_total',
        'filepath',
        'filename',
        'is_active',
        'status',
        'last_update',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Lists all customers
     *
     * @param Request $request
     * @return mixed
     */

    public function listEquipments(Request $request){
         
        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $equipments = Equipment::with(['category' => function($q){
            $q->select(['id', 'code', 'name']);
        }])
        ->with(['location' => function($q){
            $q->select(['id', 'operator_id', 'code', 'name', 'color']);
        }])
        ->whereNotIn('status', ['outstock', 'reserved'])
        ->whereHas('location', function($q) use($user){
            $q->where('operator_id', $user->id);
        });

        $value = $request->last_update;
        if($request->has('last_update')) {
            $equipments = $equipments->where('updated_at', '>=', $value);
        }

        $value = $request->delete;
        if($request->has('delete')) {
            $equipments = $equipments->withTrashed()->where('deleted_at', '>=', $value);
        }
        
        $equipments = $equipments->get();

        $dataArr = [];
        foreach($equipments as $row){
            $dataArr[] = $row;
        }
        $equipments = $dataArr;
   

        return response($equipments, 200)->header('Content-Type', 'application/json');

    }

    public function listLocations(Request $request){
         
        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $locations = Location::with(['warehouse' => function($q){
            $q->select(['id', 'name']);
        }]);

         //filter last update
         $value = $request->last_update;
         if($request->has('last_update')) {
             $locations = $locations->where('updated_at', '>=', $value);
         }

         $value = $request->delete;
         if($request->has('delete')) {
             $locations = $locations->withTrashed()->Where('deleted_at', '>=', $value);  
        }

         $location_aux = $locations->get();
    
         $dataArr = [];
         foreach($location_aux as $row){
             $dataArr[] = $row;
             
         }
         $location_aux = $dataArr;

        return response($location_aux, 200)->header('Content-Type', 'application/json');

    }
    
    public function listOutEquipmets(Request $request){
        

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $history = History::where(function ($q) use($user){
                                    $q->where('operator_id', $user->id);
                                    $q->where('action', 'out');
                            })
                            ->with(['equipment' => function($q){
                                 $q->select(['id', 'name', 'sku', 'category_id']);
                                 $q->with(['category' => function($q){
                                        $q->select(['id','code', 'name']);
                                     
                                 }]);
                            }])
                            ->orderby('created_at', 'DESC')
                            ->take(50)
                            ->get();
                            

         //filter last update
         $value = $request->last_update;
         if($request->has('last_update')) {
             $history = $history->where('updated_at', '>=', $value);
         }

         $value = $request->delete;
         if($request->has('delete')) {
             $history = $history->withTrashed()->where('deleted_at', '>=', $value);  
        }


         $dataArr = [];
         foreach($history as $row){
             $dataArr[] = $row;
         }
         $history = $dataArr;

        return response($history, 200)->header('Content-Type', 'application/json');

    }

    public function listCategories(Request $request){

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $categories = Category::all();

        //filter last update
        $value = $request->last_update;
        if($request->has('last_update')) {
            $categories = $categories->where('updated_at', '>=', $value);
        }

        $value = $request->delete;
        if($request->has('delete')) {
        
            $categories = $categories->withTrashed()->where('deleted_at', '>=', $value);
        }

        $categoriesAux = $categories->all();
        
        $dataArr = [];
        foreach($categoriesAux as $row){
            $dataArr[] = $row;
        }
        $categories = $dataArr;

       return response($categories, 200)->header('Content-Type', 'application/json');

     }

     /*
     Parâmetros:
        - motorista
        - localização
        - operacao
     */

    public function checkEquipments(Request $request, $trackingCode){

       $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $action = $request->get('action');

        if($action == 'out'){

            $equipment = Equipment::where(function ($q) use($trackingCode){
                $q->where('sku', $trackingCode);
                $q->orwhere('name', $trackingCode);
             })
             ->whereNotIn('status', ['outstock', 'reserved'])
             ->with(['category' => function($q){
                 $q->select(['id', 'code', 'name']);
             }])
             ->whereHas('location', function($q) use($user){
                 $q->where('operator_id', $user->id);
             })
             ->where('stock_total', '>=', 1)
             ->get();

        }else{

            $equipment = Equipment::where(function ($q) use($trackingCode){
                $q->where('sku', $trackingCode);
                $q->orwhere('name', $trackingCode);
            })
            ->whereNotIn('status', ['outstock', 'reserved'])
            ->with(['category' => function($q){
                $q->select(['id', 'code', 'name']);
            }])
            ->where('stock_total', '>=', 1)
            ->get();

        }
       
        if(!empty($equipment->toArray())){
            $result = ['feedback'  => 'Validado com sucesso',
                       'equipment' => $equipment
                      ];     
        }else{
            $result = ['feedback'  => 'Inválido',
                       'equipment' => $equipment
                      ];
        }
              
        return $result;
        
    }


    public function pickingEquipments(Request $request){
        
        $bindings = [
            'id',
            'name',
            'sku',
            'warehouse_id',
            'location_id',
            'stock_total',
            'status',
            'ot_code',
            'last_update'
        ];

        $content    = $request->get('content');
        $action     = $request->get('action');
        $otCode     = $request->get('ot_code');
        $locationId = $request->get('location_id');
            
        if(empty($content)){
            return $this->responseErrorEquipment('picking', '-001');
        }
       
        if(!is_array($content)) {
            $content = [$content];
            if (empty($content)) {
                return $this->responseErrorEquipment('picking', '-001');
            }
        }

        $user = $this->getUser($request->get('user'));
        if(!$user) {
            return $this->responseError('login', '-002') ;
        }
       
        $location = Location::filterSource()->find($locationId);
        if(empty($location)){
            return $this->responseErrorEquipment('picking', '-002');
        }

        $equipmentsSkus = [];
        foreach ($content as $data) {
            if($data['sku']){
                $equipmentsSkus[] = @$data['sku'];
            }
        }

        $equipmentsIds = Equipment::whereIn('sku', $equipmentsSkus)
            ->whereIn('name', $equipmentsSkus)
            ->whereNotIn('status', ['outstock', 'reserved'])
            ->pluck('id')
            ->toArray();
            
     
        $equipmentsStocks = null;
        $equipmentsStocks = Equipment::whereIn('sku', $equipmentsSkus)
                                      ->whereIn('name', $equipmentsSkus)
                                      ->whereNotIn('status', ['outstock', 'reserved'])
                                      ->select($bindings)
                                      ->get();
                                     

        if(empty($equipmentsIds)){
            return $this->responseErrorEquipment('picking', '-003');
        }
        
        $equipment = null;
        $equipment = Equipment::whereIn('id', array_values($equipmentsIds))->get()->toArray();
        

        if($action == 'transfer'){
            Equipment::whereIn('id', array_values($equipmentsIds))->update([
                'location_id'   => $location->id,
                'warehouse_id'  => $location->warehouse_id,
                'ot_code'       => $otCode,
                'last_update'   => date('Y-m-d H:i:s'),
            ]);
        }

        if($action == 'out'){
            if(!empty($equipmentsStocks)){
                foreach($equipmentsStocks as $equipment){   
                    foreach($content as $data){
                        if((strtolower($data['sku']) == strtolower($equipment->sku)) && ($equipment->stock_total > $data['qty'])){
                            
                            $stock = null;
                            $stock = $equipment->stock_total -= $data['qty'];
                            Equipment::where('id', $equipment->id)->update([
                                'ot_code'       => $otCode,
                                'stock_total'   => $stock,
                                'last_update'   => date('Y-m-d H:i:s'),
                            ]);
                            
                        }else if ((strtolower($data['sku']) == strtolower($equipment->sku)) && ($equipment->stock_total < $data['qty'])){
                            
                            return $this->responseErrorEquipment('picking', '-004', 'A quantidade do equipamento '.$data['sku'].' é superior ao stock');
                            
                        }else{
                            if((strtolower($data['sku']) == strtolower($equipment->sku))){
                             
                                Equipment::where('id', $equipment->id)->update([
                                    'status'        => "outstock",
                                    'location_id'   => null,
                                    'warehouse_id'  => null,
                                    'ot_code'       => $otCode,
                                    'stock_total'   => 0,
                                    'last_update'   => date('Y-m-d H:i:s'),
                                ]);
                
                            }
                        }
                                
                    }
                }
                                    
            }
        }
                 
    
        foreach($equipmentsStocks as $value){
            $equipmentHistory = new History();
            $equipmentHistory->equipment_id = $value->id;
            $equipmentHistory->ot_code      = $otCode;
            $equipmentHistory->action       = $action;
            $equipmentHistory->location_id  = $location->id;
            $equipmentHistory->operator_id  = $user->id;
            $equipmentHistory->stock        = $value->stock_total;
            foreach($content as $data){
                if(strtolower($value->sku) == strtolower($data['sku'])){
                    
                    $equipmentHistory->stock_low = $data['qty'];
                    
                }
            }
            $equipmentHistory->save();
        }

        $result = [
            'code'     => '',
            'feedback' => 'Picking finalizado',
        ];

        return response($result, 200)->header('Content-Type', 'application/json');
    }
    
    
    public function responseErrorEquipment($method, $code, $message = null) {

        $errors = trans('api.equipments.errors');
        
        $data = [
            'code'     => $code,
            'feedback' => $message ? $message : $errors[$method][$code],

            'error'    => $code,
            'message'  => $message ? $message : $errors[$method][$code]
        ];

        return response($data, 200)->header('Content-Type', 'application/json');
    }

}