<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Logistic\Location;
use App\Models\Logistic\Product;
use App\Models\Logistic\ProductHistory;
use App\Models\Logistic\ProductLocation;
use App\Models\Logistic\ReceptionOrder;
use App\Models\Logistic\ReceptionOrderLine;
use App\Models\Logistic\ReceptionOrderConfirmation;
use App\Models\Logistic\ReceptionOrderStatus;
use App\Models\Logistic\ShippingOrder;
use App\Models\Logistic\ShippingOrderStatus;
use App\Models\Logistic\ShippingOrderLine;
use function foo\func;
use Illuminate\Http\Request;

use Auth, Validator, Setting, Mail, Date, DB;

class LogisticController extends \App\Http\Controllers\Api\Mobile\BaseController
{

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
    protected $productBindings = [
        'id',
        'barcode',
        'sku',
        'name',
        'serial_no',
        'lote',
        'production_date',
        'expiration_date',
        'model_id',
        'description',
        'price',
        'vat',
        'weight',
        'width',
        'length',
        'photo_url',
        'stock_min',
        'stock_max',
        'stock_total',
        'stock_allocated',
        'stock_status',
        'unity',
        'unity_type',
        'filehost',
        'filepath',
        'filename',
        'customer_id',
        'brand_id',
        'family_id',
        'category_id',
        'subcategory_id',
        'obs',
        'is_obsolete',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $locationsBindings = [
        '*'
    ];


    protected $shippingOrdersBindings = [
       '*'
    ];

    protected $receptionOrdersBindings = [
        '*'
    ];

    /**
     * Lists all customers
     *
     * @param Request $request
     * @return mixed
     */
    public function listsProducts(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $products = Product::with(['customer' => function($q){
                $q->select(['id', 'code', 'name']);
            }])
            ->with(['brand' => function($q){
                $q->select(['id', 'name']);
            }])
            ->with('images')
            ->with(['product_location' => function($q){
                $q->with(['location' => function($q){
                    $q->select(['id','code','warehouse_id']);
                }]);
            }]);
            /*->with(['locations' => function($q){
                $q->select(['product_id', 'location_id', 'stock']);
            }]);*/
            //->where('source', config('app.source'))
        
        //filter customer
        if($request->has('customer')) {
            $products = $products->where('customer_id', $request->get('customer'));
        }

        //filter last update
        
        if($request->has('last_update')) {
            $value = $request->last_update;
            $value = $value.' 00:00:00';
            $products = $products->where('updated_at', '<=', $value);
        }
        

        //filter unity
        
        if($request->has('images')) {
            $value = $request->images;
            if($value) {
                $products = $products->has('images');
            } else {
                $products = $products->has('images', '=', 0);
            }
        }

        //filter lote
        
        if($request->has('lote')) {
            $value = $request->lote;
            if($value) {
                $products = $products->where('lote', '<>', '');
            } else {
                $products = $products->where(function($q){
                    $q->whereNull('lote');
                    $q->orWhere('lote', '');
                });
            }
        }

        //filter serial_no
        
        if($request->has('serial_no')) {
            $value = $request->serial_no;
            if($value) {
                $products = $products->where('serial_no', '<>', '');
            } else {
                $products = $products->where(function($q){
                    $q->whereNull('serial_no');
                    $q->orWhere('serial_no', '');
                });
            }
        }

        //filter unity
        
        if($request->has('unity')) {
            $value = $request->unity;
            $products = $products->where('unity', $value);
        }

        //filter customer
        
        if($request->has('customer')) {
            $value = $request->customer;
            $products = $products->where('customer_id', $value);
        }

        //filter location
        
        if($request->has('location')) {
            $value = $request->location;
            $products = $products->whereHas('locations', function($q) use($value) {
                $q->whereIn('location_id', $value);
            });
        }

        //filter brand
        
        if($request->has('brand')) {
            $value = $request->brand;
            $products = $products->whereIn('brand_id', $value);
        }

        //filter group
        
        if($request->has('model')) {
            $value = $request->model;
            $products = $products->whereIn('model_id', $value);
        }

        //filter group
        
        if($request->has('family')) {
            $value = $request->family;
            $products = $products->whereIn('family_id', $value);
        }

        //filter category
        $value = $request->category;
        if($request->has('category')) {
            $products = $products->whereIn('category_id', $value);
        }

        //filter subcategory
        
        if($request->has('subcategory')) {
            $value = $request->subcategory;
            $products = $products->where('subcategory_id', $value);
        }

        $products = $products->orderBy('name', 'asc')
            ->get($this->productBindings);
            
        
        $dataArr = [];
        foreach ($products as $row) {

            $locs = [];
            if($row->product_location) {
                foreach ($row->product_location as $loc) {
                    $locs[] = [
                        "id"              => $loc->id,
                        "location_id"     => $loc->location_id,
                        "location_code"   => @$loc->location->code,
                        "warehouse_id"    => @$loc->location->warehouse_id,
                        "stock"           => $loc->stock,
                        "stock_allocated" => $loc->stock_allocated,
                        "stock_available" => $loc->stock_available,
                        "barcode"         => $loc->barcode,
                        "created_at"      => $loc->created_at->format('Y-m-d H:i:s'),
                        "updated_at"      => $loc->updated_at->format('Y-m-d H:i:s'),
                    ];
                }
            }

            $row->locations = $locs;
            unset($row->product_location);
            $dataArr[] = $row;
        }
        
        $products = $dataArr;

        if(!$products) {
            return $this->responseError('lists', '-001') ;
        }

        return response($products, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Permite consultar os dados de um envio dado o seu c贸digo.
     *
     * @param Request $request
     * @return mixed
     */
    public function showProduct(Request $request, $id = null) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $filterById = true;
        $barcode    = null;
        if($request->has('qrcode') && $request->get('qrcode')) {
            $filterById = false;
            $barcode = $request->get('qrcode');
        }

        $product = Product::with('images')
            ->with(['product_location' => function($q){
                $q->with(['location' => function($q){
                    $q->select(['id','code', 'warehouse_id']);
                }]);
            }])
            ->with(['history' => function($q){
                $q->orderBy('id', 'desc')->take(50);
            }])
            ->with(['customer' => function($q){
                $q->select(['id','code','name']);
            }])
            ->with(['brand' => function($q){
                $q->select(['id','name']);
            }]);


        if($filterById) {
            $product = $product->whereId($id)
                ->select($this->productBindings)
                ->get();
        } else {
            $product = $product->where(function($q) use($barcode) {
                $q->where('sku', $barcode);
                $q->orWhere('barcode', $barcode);
                $q->orWhere('serial_no', $barcode);
                $q->orWhere('lote', $barcode);
            })
            ->orWhereHas('product_location', function($q) use($barcode) {
                $q->where('barcode', $barcode);
            })
            ->select($this->productBindings)
            ->get();
        }


        $dataArr = [];
        foreach ($product as $item) {

            $locs = [];
            if($item->product_location) {
                foreach ($item->product_location as $loc) {
                    $locs[] = [
                        "id"              => $loc->id,
                        "location_id"     => $loc->location_id,
                        "location_code"   => @$loc->location->code,
                        "warehouse_id"    => @$loc->location->warehouse_id,
                        "stock"           => $loc->stock,
                        "stock_allocated" => $loc->stock_allocated,
                        "stock_available" => $loc->stock_available,
                        "barcode"         => $loc->barcode,
                        "created_at"      => $loc->created_at->format('Y-m-d H:i:s'),
                        "updated_at"      => $loc->updated_at->format('Y-m-d H:i:s'),
                    ];
                }
            }

            $item->locations = $locs;
            unset($item->product_location);
            $dataArr[] = $item;
        }

        $product = $dataArr;

        if(!$product) {
            return $this->responseError('show', '-001');
        }

        return response($product, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Move location
     *
     * @param Request $request
     * @return mixed
     */
    public function moveLocation(Request $request) {
        
        $user = $this->getUser($request->get('user'));
        
        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $input = $request->all();
        
        $productId = $input['product_id'];

        if(empty($input['source']) || empty($input['destination'])) {
            return $this->responseError('lists', '-001', 'A localização de origem e destino é obrigatória.');
        } else if($input['source'] == $input['destination']) {
            return $this->responseError('lists', '-001', 'A localização de origem e de destino não podem ser a mesma.');
        } else if($input['qty'] <= 0) {
            return $this->responseError('lists', '-001', 'A quantidade a mover deve ser superior a 0');
        } else {
            $product = Product::filterSource()
                ->whereId($productId)
                ->first();

            $destLocation = Location::filterSource()
                ->where('code', $input['destination'])
                ->first();
            
            
            $sourceLocation = Location::filterSource()
                ->where('code', $input['source'])
                ->first();
            
            $ProductLocation = ProductLocation::where('product_id', $productId)
                ->where('location_id', $sourceLocation->id)
                ->firstOrFail();
            
            
            $newLocation = ProductLocation::firstOrNew([
                'location_id' => $destLocation->id,
                'product_id' => $productId
            ]);

            $newLocation->product_id = $productId;
            $newLocation->location_id = $destLocation->id;
            $newLocation->stock = $newLocation->stock + @$input['qty'];
            $newLocation->setBarcode();

            //change location to filled
            $newLocation->location->status = 'filled';
            $newLocation->location->save();

            //Save History
            $history = new ProductHistory();
            $history->product_id = $product->id;
            $history->action = 'transfer';
            $history->source_id = $ProductLocation->location_id;
            $history->destination_id = $newLocation->location_id;
            $history->qty = @$input['qty'];
            $history->save();

            //remove stock from current location
            $ProductLocation->updateStock($ProductLocation->stock - @$input['qty']);

            $product->updateStockTotal();

            $locations = [
                'result' => true,
                'feedback' => 'Localização movida com sucesso.'
            ];

            return response($locations, 200)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Lists all customers
     *
     * @param Request $request
     * @return mixed
     */
    public function listsLocations(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $locations = Location::with(['warehouse' => function($q){
                $q->select(['id', 'code', 'name']);
            }]);

        //filter customer
        if($request->has('warehouse')) {
            $locations = $locations->where('warehouse_id', $request->get('warehouse'));
        }

        $locations = $locations->get($this->locationsBindings);

        if(!$locations) {
            return $this->responseError('lists', '-001') ;
        }

        return response($locations, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Lists all customers
     *
     * @param Request $request
     * @return mixed
     */
    public function listsShippingOrders(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $products = ShippingOrder::with(['customer' => function($q){
                $q->select(['id', 'code', 'name']);
            }])
            ->with(['status' => function($q){
                $q->select(['id', 'color', 'name']);
            }])
            ->orderBy('status_id', 'asc')
            ->orderBy('id', 'desc')
            ->take(50);

        //filter customer
        if($request->has('customer')) {
            $products = $products->where('customer_id', $request->get('customer'));
        }

        //filter status
        if($request->has('status')) {
            $products = $products->where('status_id', $request->get('status'));
        }

        //filter date
        if($request->has('date')) {
            $products = $products->where('date', $request->get('date'));
        }

        $products = $products->get($this->shippingOrdersBindings);

        if(!$products) {
            return $this->responseError('lists', '-001') ;
        }

        return response($products, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Permite consultar os dados de uma ordem de saida
     *
     * @param Request $request
     * @return mixed
     */
    public function showShippingOrder(Request $request, $id = null) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $filterById = true;
        $barcode    = null;
        if($request->has('qrcode') && $request->get('qrcode')) {
            $filterById = false;
            $barcode = $request->get('qrcode');
        }

        $shippingOrder = ShippingOrder::with(['customer' => function($q){
                $q->select(['id', 'code', 'name']);
            }])
            ->with(['status' => function($q){
                $q->select(['id', 'color', 'name']);
            }])
            ->with(['lines' => function($q){
                $q->with(['location' => function($q){
                    $q->select(['id', 'code', 'warehouse_id']);
                }]);
                $q->with(['product' => function($q){
                    $q->select(['id', 'sku', 'name', 'serial_no', 'lote']);
                }]);
            }]);


        if($filterById) {
            $shippingOrder = $shippingOrder->whereId($id)
                ->select($this->shippingOrdersBindings)
                ->get();
        } else {
            $shippingOrder = $shippingOrder->where(function($q) use($barcode) {
                    $q->where('code', $barcode);
                    $q->orWhere('shipment_trk', $barcode);
                    $q->orWhere('document', $barcode);
                })
                ->select($this->shippingOrdersBindings)
                ->get();
        }

        if(!$shippingOrder) {
            return $this->responseError('show', '-001');
        }

        return response($shippingOrder, 200)->header('Content-Type', 'application/json');
    }


    /**
     * Permite consultar os dados de uma ordem de saida
     *
     * @param Request $request
     * @return mixed
     */
    public function storePickingLine(Request $request) {
        $input = $request->all();
        
        $quantities = json_decode($input['qty'], true);
        
        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        if(!@$input['shipping_order']) {
            return $this->responseError('login', '-001', 'Parâmetro "shipping_order" obrigatório.') ;
        }
        
        $conclude = false;
        if(@$input['conclude']) {
            $conclude = $input['conclude'];
        }
        
        //$location = Location::where('code', $input['location'])->first();
        
        $shippingOrder = ShippingOrder::with('customer')
            ->filterSource()
            ->where('code', $input['shipping_order'])
            ->first();

        if(!$shippingOrder) {
            return $this->responseError('login', '-005', 'Ordem de saída inexistente.') ;
        }

        if($shippingOrder->status_id == ShippingOrderStatus::STATUS_CONCLUDED) {
            return $this->responseError('login', '-004', 'A ordem de saída já se encontra finalizada.') ;
        }
        
        $lines = $shippingOrder->lines;
        $productsIds = [];
    
        $totalQtySat = 0;
        
        if($quantities) {
            foreach ($quantities as $quantity) {
                foreach ($quantity as $lineId => $qtySat) {
                    $line = $lines->filter(function ($item) use ($lineId) {
                        return $item->id == $lineId;
                    })->first();
                    
                    $line->qty_satisfied = $qtySat;
                    $line->save();
    
                    $totalQtySat+= $line->qty_satisfied;
    
                    //cria array com todos os ID de artigo e quantidade total expedidas efetivamente
                    $productsIds[$line->product_id] = @$productsIds[$line->product_id] + $qtySat;   
                }
            }
        }
        
        if($conclude) {
            $shippingOrder->status_id     = ShippingOrderStatus::STATUS_CONCLUDED;
            $shippingOrder->qty_satisfied = $totalQtySat;
            $shippingOrder->save();


            //desconta stocks dos produtos
            foreach ($shippingOrder->lines as $line) {

                if(@$line->product_id) {
                    $line->product->stock_total -= $line->qty_satisfied; //remove do stock total a quantidade enviada
                    $line->product->stock_allocated -= $line->qty; //anula completamente a totalidade do que havia sido alocada
                    $line->product->stock_available = $line->product->stock_total - $line->product->stock_allocated; //restablece a quantidade disponivel
                    $line->product->save();
                }

                if(@$line->location_id) {
                    $productLocation = ProductLocation::where('location_id', $line->location_id)
                        ->where('product_id', $line->product_id)
                        ->first();

                    $productLocation->stock -= $line->qty_satisfied;
                    $productLocation->stock_available -= $line->qty_satisfied;
                    $productLocation->stock_allocated -= $line->qty;
                    $productLocation->save();
                }

                //apaga todas as localizações do artigo que estejam com stock total = 0
                ProductLocation::where('product_id', $line->product_id)
                    ->where('stock', 0)
                    ->forceDelete();

                //regista a ordem de saida nas movimentações do artigo
                $history = ProductHistory::firstOrNew([
                    'action'      => 'order_out',
                    'product_id'  => $line->product_id,
                    'source_id'   => $line->location_id,
                    'document_id' => $shippingOrder->id
                ]);
                $history->qty = $line->qty_satisfied;
                $history->save();
            }


            //Recaulcula os stocks alocados para os artigos envolvidos na transação.
            //para isso, obtem todas as linhas de pedidos de saída onde esses produtos estejam e soma as quantidades
            $productsAllocated = ShippingOrderLine::with('product')
                ->whereIn('product_id', array_keys($productsIds))
                ->whereHas('shipping_order', function($q){
                    $q->whereIn('status_id', [ShippingOrderStatus::STATUS_PROCESSING, ShippingOrderStatus::STATUS_PENDING]);
                })
                ->groupBy('product_id')
                ->get([DB::raw('sum(qty) as total_allocated'), 'product_id']);

            foreach ($productsAllocated as $line) {

                $stockPerProduct = ShippingOrderLine::whereHas('shipping_order', function($q){
                        $q->whereIn('status_id', [ShippingOrderStatus::STATUS_PENDING, ShippingOrderStatus::STATUS_PROCESSING]);
                    })
                    ->where('product_id', $line->product_id)
                    ->sum('qty');

                $line->product->stock_allocated = $stockPerProduct;
                $line->product->save();
            }

        } else {
            $shippingOrder->status_id     = ShippingOrderStatus::STATUS_PROCESSING;
            $shippingOrder->qty_satisfied = $totalQtySat;
            $shippingOrder->save();
        }
        
        // $result = [
        //     'result'    => $product->count() == 1 ? true : false,
        //     'singleLocation' => false,
        //     'sku'       => @$product->first()->product->sku,
        //     'product'   => @$product->first()->product_id,
        //     'locations' => $locationsList,
        // ];
        
        $result = [
            'result'    =>true
        ];
    
        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Lists all customers
     *
     * @param Request $request
     * @return mixed
     */
    public function listsReceptionOrders(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $data = ReceptionOrder::with(['customer' => function($q){
            $q->select(['id', 'code', 'name']);
        }])
            ->with(['status' => function($q){
                $q->select(['id', 'color', 'name']);
            }]);

        //filter customer
        if($request->has('customer')) {
            $data = $data->where('customer_id', $request->get('customer'));
        }

        //filter status
        if($request->has('status')) {
            $data = $data->where('status_id', $request->get('status'));
        }

        //filter date
        if($request->has('date')) {
            $data = $data->where('date', $request->get('date'));
        }

        $data = $data->get($this->shippingOrdersBindings);

        if(!$data) {
            return $this->responseError('lists', '-001') ;
        }

        return response($data, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Permite consultar os dados de uma ordem de saida
     *
     * @param Request $request
     * @return mixed
     */
    public function showReceptionOrder(Request $request, $id = null) {
        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $filterById = true;
        $barcode    = null;
        if($request->has('qrcode') && $request->get('qrcode')) {
            $filterById = false;
            $barcode = $request->get('qrcode');
        }


        $data = ReceptionOrder::with(['customer' => function($q){
            $q->select(['id', 'code', 'name']);
        }])
        ->with(['status' => function($q){
            $q->select(['id', 'color', 'name']);
        }])
        ->with(['lines' => function($q){
            $q->select(['id', 'reception_order_id', 'product_id', 'qty']);
        }])
        ->with(['confirmation' => function($q){
            $q->with(['location' => function($q){
                $q->select(['id', 'code', 'warehouse_id']);
            }]);
            $q->with(['product' => function($q){
                $q->select(['id', 'sku', 'name', 'serial_no', 'lote']);
            }]);
        }]);

        if($filterById) {
            $data = $data->whereId($id)
                ->select($this->shippingOrdersBindings)
                ->get();
        } else {
            $data = $data->where(function($q) use($barcode) {
                $q->where('code', $barcode);
                $q->orWhere('document', $barcode);
            })
            ->select($this->shippingOrdersBindings)
            ->get();
        }

        if(!$data) {
            return $this->responseError('show', '-001');
        }

        return response($data, 200)->header('Content-Type', 'application/json');
    }
    
    public function storeReceptionOrder(Request $request) {
        $input       = $request->all();
        $conclude    = $request->get('conclude', false);

        $receptionOrder = ReceptionOrder::with('lines')
            ->filterSource()
            ->where('id', $input['reception_order_id'])
            ->firstOrFail();
                
        if($receptionOrder->status_id != ReceptionOrderStatus::STATUS_CONCLUDED){
            
            $lines = $receptionOrder->confirmation;
            
            if($conclude && !$lines->isEmpty()) {
    
                foreach ($lines as $line) {
                    if(!$line->location_id) {
                        return response()->json([
                            'result'   => false,
                            'feedback' => 'Existem artigos que não estão associados a uma localização de destino.'
                        ]);
                    }
                }
    
                //adiciona stock ao artigo
                $confirmationLines = $receptionOrder->confirmation;
    
                foreach ($confirmationLines as $line) {
                    $product = $line->product;
    
                    $product->stock_total+= $line->qty_received;
                    $product->stock_available+= $line->qty_received;
                    $product->save();
    
                    if($line->location_id) {
                        $productLocation = ProductLocation::firstOrNew([
                            'product_id'  => $line->product_id,
                            'location_id' => $line->location_id
                        ]);
    
                        $productLocation->product_id  = $line->product_id;
                        $productLocation->location_id = $line->location_id;
                        $productLocation->stock+= $line->qty_received;
                        $productLocation->stock_available+= $line->qty_received;
                        $productLocation->stock_allocated = $productLocation->stock - $productLocation->stock_available;
                        $productLocation->setBarcode();
                    }
    
                    //regista a ordem de entrada nas movimentações do artigo
                    $history = ProductHistory::firstOrNew([
                        'action'         => 'add',
                        'product_id'     => $line->product_id,
                        'destination_id' => $line->location_id,
                        'document_id'    => $line->reception_order_id
                    ]);
                    $history->qty = $line->qty_received;
                    $history->save();
                }
    
                $receptionOrder->status_id = ReceptionOrderStatus::STATUS_CONCLUDED;
            } else {
                $receptionOrder->status_id = ReceptionOrderStatus::STATUS_WAINTING;
            }
    
            $receptionOrder->fill($input);
            $receptionOrder->save();
    
            $result = [
                'result'   => true,
                'feedback' => 'Ordem de recepção gravada com sucesso.'
            ];
        }else{
            $result = [
                'result'   => false,
                'feedback' => 'A Ordem de recepção já se encontra concluída!'
            ];
        }
    
        return response($result, 200)->header('Content-Type', 'application/json');
    }
    
    public function getArticlesReceptionOrder(Request $request, $id){
        $receptionOrder = ReceptionOrder::with(['lines' => function($q){
                $q->with(['product' => function($q){
                    $q->select(['id', 'sku', 'name']);
                }]);
            }])->filterSource()
            ->where('id', $id)
            ->firstOrFail();
            
        
        
        $result = $receptionOrder;
        
        return response($result, 200)->header('Content-Type', 'application/json');
    }
    
    public function storeConfirmationItem(Request $request)
    {
        $allowEdit        = true;
        $readall          = $request->get('readall', false);
        $readall          = $readall == 'false' ? false : $readall;
        $barcode          = $request->get('barcode');
        $locationId       = $request->get('location');
        $receptionOrderId = $request->get('orderId');
        $date             = $request->get('date');
        $qtyReceived      = $request->get('qty', 1);
        
        $receptionOrder = ReceptionOrder::find($receptionOrderId);

        if (empty($locationId)) {
            $results = [
                'result'   => false,
                'feedback' => 'Tem de selecionar a localização de destino.'
            ];
        } elseif (!$receptionOrder) {
            $results = [
                'result'   => false,
                'feedback' => 'Ordem de recepção não encontrada.'
            ];
        } else {
            // CHECK IF LOCATION EXISTS
            $location = Location::where('id', $locationId)
                ->orWhere('code', $locationId)
                ->orWhere('barcode', $locationId)
                ->first();

            if (!$location) {
                return Response::json([
                    'result'   => false,
                    'feedback' => 'Localização não encontrada.'
                ]);
            }
            //--

            // SET LOCATION ID FROM FOUND LOCATION
            $locationId = $location->id;
            $request->merge(['location' => $locationId]);
            //--

            if ($readall) {
                return $this->storeAllItems($request, $receptionOrder);
            }

            $orderProductsIds = $receptionOrder->lines->pluck('product_id')->toArray();

            $product = Product::where(function ($q) use ($barcode) {
                    $q->where('sku', $barcode);
                    $q->orWhere('barcode', $barcode);
                    $q->orWhere('serial_no', $barcode);
                    $q->orWhere('lote', $barcode);
                })
                ->whereIn('id', $orderProductsIds)
                ->first();

            if ($product) {
                
                //verifica se é possível registar a quantidade
                $totalQtyPossible = ReceptionOrderLine::where('reception_order_id', $receptionOrder->id)
                    ->where('product_id', $product->id)
                    ->sum('qty');

                $totalQtyReceived = ReceptionOrderConfirmation::where('reception_order_id', $receptionOrder->id)
                    ->where('product_id', $product->id)
                    ->sum('qty_received');
                
                $totalQtyReceptionOrder = ReceptionOrderConfirmation::where('reception_order_id', $receptionOrder->id)
                ->sum('qty_received');

                if ($totalQtyReceived < $totalQtyPossible) {

                    //regista as linhas de confirmação e atualiza os totais
                    $receptionOrderConfirmation = ReceptionOrderConfirmation::firstOrNew([
                        'reception_order_id' => $receptionOrder->id,
                        'location_id'        => $locationId,
                        'product_id'         => $product->id
                    ]);

                    $receptionOrderConfirmation->reception_order_id = $receptionOrder->id;
                    $receptionOrderConfirmation->product_id         = $product->id;
                    $receptionOrderConfirmation->location_id        = $locationId;
                    $receptionOrderConfirmation->qty_received       = @$receptionOrderConfirmation->qty_received + $qtyReceived;
                    $receptionOrderConfirmation->save();

                    //update shipping order products
                    $receptionOrder->updateReceptionOrderProducts();
                    
                    //update shipping order
                    $receptionOrder->total_qty_received = $totalQtyReceptionOrder + $qtyReceived;
                    //dd($receptionOrder->total_qty_received);
                    $receptionOrder->save();
                
                    $receptionOrder = ReceptionOrder::find($receptionOrder->id);

                    $results = [
                        'result'        => true,
                        'feedback'      => 'Linha da ordem de receção criada com sucesso!'
                    ];
                } else {
                    $results = [
                        'result'        => false,
                        'feedback'      => 'Todas as quantidades já foram satisfeitas.'
                    ];
                }
            } else {
                $results = [
                    'result'   => false,
                    'feedback' => 'Artigo não encontrado no pedido.'
                ];
            }
        }

        return response($results, 200)->header('Content-Type', 'application/json');
    }
    
    public function updateConfirmationItem(Request $request)
    {
        $input     = $request->all();
        //dd($input);
        $receptionConfirmation = ReceptionOrderConfirmation::where('reception_order_id', $input['reception_order_id'])->find($input['line_id']);
        //dd($receptionConfirmation);
        $receptionOrder        = $receptionConfirmation->reception_order;

        $oldQty = $receptionConfirmation->qty_received; //guarda o valor anterior
        $receptionConfirmation->qty_received = @$input['qty'];
        $result = $receptionConfirmation->save();

        //verifica se é possível registar a quantidade
        $maxQty = ReceptionOrderLine::where('reception_order_id', $receptionConfirmation->reception_order_id)
            ->where('product_id', $receptionConfirmation->product_id)
            ->sum('qty');

        $totalQtyReceived = ReceptionOrderConfirmation::where('reception_order_id', $receptionConfirmation->reception_order_id)
            ->where('product_id', $receptionConfirmation->product_id)
            ->sum('qty_received');

        if ($totalQtyReceived <= $maxQty) {

            if ($result) {

                //update reception order products
                $receptionOrder->updateReceptionOrderProducts();

                //para obter a variavel atualizada
                $receptionOrder = ReceptionOrder::with('lines')->find($receptionOrder->id);

                $allowEdit = $receptionOrder->allow_edit;
                
                $results = [
                    'result'        => true,
                    'feedback'      => 'Registo alterado com sucesso.'
                ];
            } else {

                $results = [
                    'result'   => false,
                    'feedback' => 'Erro ao atualizar o registo'
                ];
            }
        } else {

            $allowEdit = $receptionOrder->allow_edit;

            $receptionConfirmation->qty_received = $oldQty; //repõe valor anterior
            $receptionConfirmation->save();

            $results = [
                'result'        => false,
                'feedback'      => 'Quantidade Inválida. Não pode exceder: ' . $maxQty
            ];
        }


        return response($results, 200)->header('Content-Type', 'application/json');
    }
}