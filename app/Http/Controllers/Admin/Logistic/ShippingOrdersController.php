<?php

namespace App\Http\Controllers\Admin\Logistic;

use App\Models\Agency;
use App\Models\Logistic\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Logistic\Location;
use App\Models\Logistic\Product;
use App\Models\Logistic\ProductLocation;
use App\Models\Logistic\ProductHistory;
use App\Models\Customer;
use App\Models\Logistic\ShippingOrderStatus;
use App\Models\Logistic\ShippingOrder;
use App\Models\Logistic\ShippingOrderLine;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Provider;
use Html, Auth, App, Response, Setting, DB;

class ShippingOrdersController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'logistic_shipping_orders';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',logistic_shipping_orders']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

         $warehouses = Warehouse::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $agencies = Agency::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $locations = Location::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('code', 'id')
            ->toArray();

        $status = ShippingOrderStatus::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();
            
        $providers = Provider::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->isOperator()
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'warehouses',
            'agencies',
            'locations',
            'status',
            'operators',
            'providers'
        );

        return $this->setContent('admin.logistic.shipping_orders.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $shippingOrder = new ShippingOrder();

        $status = ShippingOrderStatus::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $formOptions = array('route' => array('admin.logistic.shipping-orders.store'), 'method' => 'POST', 'class' => 'form-exit-order');

        $action = 'Criar Ordem de Saída';

        $data = compact(
            'shippingOrder',
            'formOptions',
            'action',
            'status'
        );

        return view('admin.logistic.shipping_orders.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return $this->update($request, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        $shippingOrder = ShippingOrder::filterSource()
            ->with('lines')
            ->where('id', $id)
            ->firstOrFail();

        $data = compact(
            'shippingOrder'
        );

        return view('admin.logistic.shipping_orders.show', $data)->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $shippingOrder = ShippingOrder::filterSource()
                        ->with('lines')
                        ->where('id', $id)
                        ->firstOrFail();

        $lines = $shippingOrder->lines();

        $barcodes = $lines->pluck('barcode')->toArray();
        
        $productLocations = ProductLocation::whereIn('barcode', $barcodes)
            ->pluck('stock', 'barcode')
            ->toArray();

        $status = ShippingOrderStatus::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $formOptions = array('route' => array('admin.logistic.shipping-orders.update', $shippingOrder->id), 'method' => 'PUT', 'class' => 'form-exit-order');

        $action = 'Editar Ordem de Saída';

        $data = compact(
            'shippingOrder', 
            'lines', 
            'productLocations',
            'formOptions',
            'action',
            'status'
        );
        
        return view('admin.logistic.shipping_orders.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $input = $request->all();
        $input['create_shipment'] = $request->get('create_shipment');

        $id = $id ? $id : $input['id'];

        $shippingOrder = ShippingOrder::filterSource()
                            ->findOrNew($id);

        $exists = $shippingOrder->exists;

        $shipment = Shipment::where('tracking_code', @$input['shipment_trk'])->first();
        if($shipment) {
            $input['shipment_id'] = $shipment->id;
        }

        if ($shippingOrder->validate($input)) {
            $shippingOrder->fill($input);
            $shippingOrder->user_id = Auth::user()->id;
            $shippingOrder->source  = config('app.source');
            $shippingOrder->setCode();

            $count = $qty = $qtySat = $vols = $weight = $price = $volume = 0;
            foreach ($shippingOrder->lines as $line) {

                $volume = number(($line->product->width * $line->product->height * $line->product->length) / 1000000, 3);

                $count++;
                $qty+= $line->qty;
                $qtySat+= $line->qty_satisfied;
                $vols+= $line->qty;
                $weight+= @$line->product->weight * $line->qty;
                $volume+= $volume * $line->qty;
                $price+= @$line->product->price * $line->qty;
            }

            $shippingOrder->total_items   = $count;
            $shippingOrder->qty_total     = $qty;
            $shippingOrder->qty_satisfied = $qtySat;
            $shippingOrder->total_volumes = $vols;
            $shippingOrder->total_weight  = $weight;
            $shippingOrder->total_price   = $price;
            $shippingOrder->total_volume  = $volume;
            $shippingOrder->save();

            if($input['create_shipment']) {
                return Redirect::route('admin.logistic.shipping-orders.index', ['edit-shipment' => $shippingOrder->id ])->with('success', 'Dados gravados com sucesso.');
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->with('error', $shippingOrder->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $shippingOrder = ShippingOrder::filterSource()
                    ->whereId($id)
                    ->firstOrFail();

        $lines = $shippingOrder->lines;

        foreach ($lines as $line) {

            if(@$line->product_id) {
                $line->product->stock_allocated -= $line->qty; //anula completamente a totalidade do que havia sido alocada
                $line->product->stock_available = $line->product->stock_total - $line->product->stock_allocated; //restablece a quantidade disponivel
                $line->product->save();
            }

            if(@$line->location_id) {
                $productLocation = ProductLocation::where('location_id', $line->location_id)
                    ->where('product_id', $line->product_id)
                    ->first();

                $productLocation->stock_available -= $line->qty;
                $productLocation->stock_allocated -= $line->qty;
                $productLocation->save();
            }
        }

        $result = $shippingOrder->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o produto.');
        }

        return Redirect::back()->with('success', 'Produto removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        $ids = explode(',', $request->ids);

        $result = ShippingOrder::filterSource()
                    ->whereIn('id', $ids)
                    ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = ShippingOrder::filterSource()
                    ->with('customer','shipment', 'lines')
                    ->select();

        //filter date
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter status
        $value = $request->status;
        if($request->has('status')) {
            $data = $data->whereIn('status_id', $value);
        }

        //filter customer
        $value = $request->customer;
        if($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter hide concluded
        $value = $request->hide_concluded;
        if($request->has('hide_concluded')) {
            if($value) {
                $data = $data->whereNotIn('status_id', [ShippingOrder::STATUS_CONCLUDED, ShippingOrder::STATUS_CANCELED]);
            }
        }
        
        //filter operator
        $value = $request->get('operator');
        if($value) {
            //sim, está uma caca isto, até tenho vergonha...não consegui pensar em alternativa de momento, por favor, não me batam!
           /*  $pendingShipmentsIds = ShippingOrder::where('status_id', 1)->pluck('shipment_id')->toArray();
            $shipmentsIds = Shipment::whereIn('id', $pendingShipmentsIds)->whereIn('operator_id', $value)->pluck('id')->toArray();
            $data = $data->whereIn('shipment_id', $shipmentsIds);
 */
            $data = $data->whereHas('shipment', function($q) use($value){
                $q->whereIn('operator_id', $value);
            });
        }
        
        //filter provider
         $value = $request->get('provider');
         if($value) {
             //sim, está uma caca isto, até tenho vergonha...não consegui pensar em alternativa de momento, por favor, não me batam!
             $pendingShipmentsIds = ShippingOrder::where('status_id', 1)->pluck('shipment_id')->toArray();
             $shipmentsIds = Shipment::whereIn('id', $pendingShipmentsIds)->whereIn('provider_id', $value)->pluck('id')->toArray();
             $data = $data->whereIn('shipment_id', $shipmentsIds);
         }

        return Datatables::of($data)
            ->edit_column('code', function($row) {
                return view('admin.logistic.shipping_orders.datatables.code', compact('row'))->render();
            })
            ->edit_column('shipment_id', function($row) {
                return view('admin.logistic.shipping_orders.datatables.shipment', compact('row'))->render();
            })
            ->edit_column('status_id', function($row) {
                return view('admin.logistic.shipping_orders.datatables.status', compact('row'))->render();
            })
            ->edit_column('customer.name', function($row) {
                return view('admin.logistic.shipping_orders.datatables.customer', compact('row'))->render();
            })
             ->edit_column('total_items', function($row) {
                return view('admin.logistic.shipping_orders.datatables.total_items', compact('row'))->render();
            })
            ->edit_column('qty_satisfied', function($row) {
                return view('admin.logistic.shipping_orders.datatables.qty_satisfied', compact('row'))->render();
            })
            ->edit_column('total_price', function($row) {
                return view('admin.logistic.shipping_orders.datatables.price', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.logistic.shipping_orders.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Add product
     *
     * @return type
     */
    public function storeProduct(Request $request) {

        $input = $request->toArray();
        $input['status_id'] = $request->get('status_id', ShippingOrderStatus::STATUS_PENDING);

        try {
            $shippingOrder = ShippingOrder::filterSource()
                ->findOrNew(@$input['id']);

            $shippingOrder->fill($input);
            $shippingOrder->source = config('app.source');
            $shippingOrder->setCode();

            if($shippingOrder->id) {

                $productLocations = ProductLocation::getAutomaticLocation($input['product_id'], $input['qty'], $input['location_id']);


                foreach ($productLocations as $productLocation) {
                    $line = ShippingOrderLine::firstOrNew([
                        'shipping_order_id'   => $shippingOrder->id,
                        'product_location_id' => $productLocation['id']
                    ]);

                    $line->shipping_order_id    = $shippingOrder->id;
                    $line->product_id           = $productLocation['product_id'];
                    $line->location_id          = $productLocation['location_id'];
                    $line->product_location_id  = $productLocation['id'];
                    $line->qty                  = @$line->qty + $productLocation['qty'];
                    $line->save();

                    //atualiza as quantidades totais do artigo
                    $line->updateStockTotals();
                }

                ShippingOrder::updatePrice($shippingOrder->id);

                $result = [
                    'result'   => true,
                    'feedback' => 'Adicionado ao pedido',
                    'id'       => $shippingOrder->id,
                    'html'     => view('admin.logistic.shipping_orders.partials.product_table', compact('shippingOrder'))->render()
                ];
            }

        } catch (\Exception $e) {
            $shippingOrder->forceDelete();

            $result = [
                'result'   => false,
                'feedback' => $e->getMessage()
            ];
        }

        return Response::json($result);
    }

    /**
     * Update product
     *
     * @return type
     */
    public function updateProduct(Request $request, $shippingOrderId, $lineId) {

        $newQty   = $request->get('qty');
        $newPrice = $request->get('price');

        try {
            $line = ShippingOrderLine::where('shipping_order_id', $shippingOrderId)
                ->where('id', $lineId)
                ->firstOrFail();
            $line->qty   = $newQty;
            $line->price = $newPrice;
            $line->save();

            ShippingOrder::updatePrice($shippingOrderId);

            //atualiza as quantidades totais do artigo
            $line->updateStockTotals();

            $results = [
                'result'   => true,
                'feedback' => 'Quantidade alterada com sucesso.',
            ];
        } catch (\Exception $e) {
            $results = [
                'result'   => true,
                'feedback' => $e->getMessage()
            ];
        }

        return Response::json($results);
    }

    /**
     * Update product
     *
     * @return type
     */
    public function deleteProduct(Request $request, $shippingOrderId, $lineId) {

        try {
            $line = ShippingOrderLine::where('shipping_order_id', $shippingOrderId)
                ->where('id', $lineId)
                ->firstOrFail();

            $line->qty = 0; //stock alocado
            $line->save();
            
            $line->updateStockTotals();

            //remove a linha definitivo
            $line->forceDelete();


            $totalLines = ShippingOrderLine::where('shipping_order_id', $shippingOrderId)->count();
            if(!$totalLines) {
                ShippingOrder::filterSource()->where('id', $shippingOrderId)->forceDelete();
            }

            ShippingOrder::updatePrice($shippingOrderId);

            $result = [
                'result'   => true,
                'feedback' => 'Artigo removido do pedido.',
            ];

        } catch (\Exception $e) {
            $result = [
                'result'   => false,
                'feedback' => $e->getMessage()
            ];
        }

        return Response::json($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createConfirmation(Request $request) {

        if($request->has('code')) {

            $code = $request->code;

            $shippingOrder = ShippingOrder::where('code', $code)->first();

            if(!$shippingOrder) {
                
                if (strlen($code) == 18) {
                    $code = substr($code, 0, 12);
                } elseif(substr($code, 0, 3) == 'EXP' && config('app.source') == '2660express') { //envios viadireta 2660express
                    $code = substr($code, 3);
                }
       
                $shipment = Shipment::where(function($q) use($code) {
                        $q->where('tracking_code', $code);
                        $q->orWhere('provider_tracking_code', $code);
                        $q->orWhere('reference', $code);
                    })
                    ->first();
                    

                if($shipment) {
                    $shippingOrder = ShippingOrder::where('shipment_id', $shipment->id)->first();
                }
            }

            if(!$shippingOrder) {
                return response()->json([
                    'result'    => false,
                    'feedback'  => 'Ordem de saída não encontrada.'
                ]);
            }

            if($shippingOrder->status_id == ShippingOrderStatus::STATUS_CONCLUDED) {
                return response()->json([
                    'result'    => false,
                    'feedback'  => 'A ordem de saída já se encontra finalizada.'
                ]);
            }

            return response()->json([
                'result'    => true,
                'feedback'  => 'Picking out iniciado',
                'html'      => $this->editConfirmation($request, $shippingOrder->id)
            ]);
        }

        return view('admin.logistic.shipping_orders.confirmation.create')->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editConfirmation(Request $request, $id) {

        $shippingOrder = ShippingOrder::with('customer')
            ->with(['lines' => function($q) {
                $q->with('product', 'location');
            }])
            ->filterSource()
            ->where('id', $id)
            ->first();

        $shippingOrder = $shippingOrder ? $shippingOrder : new ShippingOrder();

        $formOptions = array('route' => array('admin.logistic.shipping-orders.confirmation.store', $id), 'method' => 'POST', 'class' => 'form-confirmation');

        $data = compact(
            'shippingOrder',
            'formOptions'
        );

        return view('admin.logistic.shipping_orders.confirmation.edit', $data)->render();
    }

    /**
     * Store confirmation
     * @param Request $request
     */
    public function storeConfirmation(Request $request, $id) {

        $input       = $request->all();
        $quantities  = @$input['qty'];
        $conclude    = $request->get('conclude', false);
        $totalQtySat = 0;

        $shippingOrder = ShippingOrder::with('lines')
            ->filterSource()
            ->where('id', $id)
            ->firstOrFail();

        $lines = $shippingOrder->lines;
        foreach ($lines as $line) {
            $qtySat = @$quantities[$line->id] ?? 0;

            $line->qty_satisfied = (int) $qtySat;
            $line->save();

            $totalQtySat += $line->qty_satisfied;
        }

        if($conclude) {
            $shippingOrder->status_id     = ShippingOrderStatus::STATUS_CONCLUDED;
            $shippingOrder->qty_satisfied = $totalQtySat;
            $shippingOrder->save();

            unset($shippingOrder->lines); // Reset da relação
            //desconta stocks dos produtos
            foreach ($shippingOrder->lines as $line) {
                if(@$line->product) {
                    $line->product->stock_total     -= $line->qty_satisfied; //remove do stock total a quantidade enviada
                    $line->product->stock_allocated -= $line->qty; //anula completamente a totalidade do que havia sido alocada
                    $line->product->stock_available = $line->product->stock_total - $line->product->stock_allocated; //restablece a quantidade disponivel
                    $line->product->save();
                }

                if(@$line->location_id) {
                    $productLocation = ProductLocation::where('location_id', $line->location_id)
                        ->where('product_id', $line->product_id)
                        ->first();

                    $productLocation->stock           -= $line->qty_satisfied;
                    $productLocation->stock_available -= $line->qty_satisfied;
                    $productLocation->stock_allocated -= $line->qty;
                    $productLocation->save();
                }

                // Atualiza alocados com base nas ordens de saída
                $line->updateStockTotals();

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
        } else {
            $shippingOrder->status_id     = ShippingOrderStatus::STATUS_PROCESSING;
            $shippingOrder->qty_satisfied = $totalQtySat;
            $shippingOrder->save();
        }

        $result = [
            'result'   => true,
            'feedback' => 'Ordem de saída gravada com sucesso.'
        ];

        return response()->json($result);
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function confirmationSearchBarcode(Request $request, $id) {

        $barcode = trim($request->barcode);

        $shippingOrder = ShippingOrder::with('customer')
            ->with(['lines' => function($q) {
                $q->with('product', 'location');
            }])
            ->filterSource()
            ->where('id', $id)
            ->first();

        $productIds = $shippingOrder->lines->pluck('product_id')->toArray();
        $locationIds = $shippingOrder->lines->pluck('location_id')->toArray();

       $product = ProductLocation::with('product', 'location')
            ->whereIn('product_id', $productIds);
            
        if(config('app.source') == '2660express') { //PODE SER EXTENDIDO A +CLIENTES OU DEFINICAO GERAL FUTURA
            //limita a pesquisa só aos artigos do pedido. 
            //Se só tiver 1 artigo que esteja em varias localizações, como a consulta só devolverá 1 artigo, não vai abrir
            //o popup de confirmação da localização de saída.
            $product = $product->whereIn('location_id', $locationIds); 
        }

        $product = $product->where(function($q) use($shippingOrder, $barcode) {
                $q->whereHas('product', function($q) use($shippingOrder, $barcode) {
                    $q->where('customer_id', $shippingOrder->customer_id);
                    $q->where(function($q) use($barcode) {
                        $q->where('sku', $barcode);
                        $q->orWhere('serial_no', $barcode);
                        $q->orWhere('lote', $barcode);
                        $q->orWhere('barcode', $barcode);
                    });
                });
                $q->orWhere('barcode', $barcode);
            })
            ->get();


        if($product->isEmpty()) {
            $result = [
                'result'   => false,
                'feedback' => 'Nenhum artigo encontrado.'
            ];
        } else {

            if($product->count() == 1) {
                //coloca a ordem em processamento
                $shippingOrder->update(['status_id' => ShippingOrderStatus::STATUS_PROCESSING]);

                $product = $product->first();
                $result = [
                    'result'    => true,
                    'singleLocation' => true,
                    'sku'       => @$product->product->sku,
                    'product'   => @$product->product_id,
                    'location'  => @$product->location_id,
                    'barcode'   => @$product->barcode
                ];
            } else {

                $locationsList = [];
                foreach ($product as $item) {
                    $locationsList[@$item->barcode] = [
                        'id'        => @$item->location->id,
                        'product'   => @$item->product_id,
                        'barcode'   => @$item->barcode,
                        'code'      => @$item->location->code,
                        'qty'       => @$item->stock,
                        'location_barcode' => @$item->location->barcode,
                    ];
                }

                $result = [
                    'result'    => true,
                    'singleLocation' => false,
                    'sku'       => @$product->first()->product->sku,
                    'product'   => @$product->first()->product_id,
                    'locations' => $locationsList,
                ];
            }
        }

        return response()->json($result);
    }


    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request) {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {
            $customersWithProducts = Product::filterSource()
                                        ->groupBy('customer_id')
                                        ->pluck('customer_id')
                                        ->toArray();

            $customers = Customer::filterAgencies()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->whereIn('id', $customersWithProducts)
                ->isDepartment(false)
                ->get(['name', 'code', 'id']);

            if($customers) {
                $results = array();
                foreach($customers as $customer) {
                    $results[]=array('id'=> $customer->id, 'text' => $customer->code. ' - '.str_limit($customer->name, 40));
                }

            } else {
                $results = [['id' => '', 'text' => 'Nenhum cliente encontrado.']];
            }

        } catch(\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }

    /**
     * Search senders on DB
     *
     * @return type
     */
    public function searchProduct(Request $request) {

        $customerId = $request->get('customer');
        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

       try {
            $products = Product::with('product_location')
                ->filterSource()
                ->where('customer_id', $customerId)
                ->where(function($q) use($search){
                    $q->where('barcode', 'LIKE', $search)
                    ->orWhere('sku', 'LIKE', $search)
                    ->orWhere('lote', 'LIKE', $search)
                    ->orWhere('serial_no', 'LIKE', $search)
                    ->orWhere('name', 'LIKE', $search);
                })
                ->orderBy('stock_available', 'desc')
                ->orderBy('sku')
                ->take(50)
                ->get(['name', 'barcode', 'id', 'sku', 'lote', 'serial_no', 'expiration_date', 'stock_total', 'stock_available']);

            if($products) {
                $results = array();
                foreach($products as $product) {

                    $class = 'text-green';
                    if($product->stock_total <= 0) {
                        $class = 'text-red';
                    } elseif($product->stock_total > 0 && $product->stock_total <= $product->stock_min) {
                        $class = 'text-yellow';
                    }

                    $lote = '';
                    if($product->serial_no) {
                        $lote = '&bull; SN: '.$product->serial_no;
                    } else if($product->lote) {
                        $lote = '&bull; Lote: '.$product->lote;
                    }

                    $locations = [];
                    if($product->product_location) {

                        if($product->product_location->count() > 1) {
                            $locations[] = [
                                'id'   => '',
                                'text' => 'Automático'
                            ];
                        }

                        foreach ($product->product_location as $location) {
                            $locations[] = [
                                'id'   => $location->location_id,
                                'text' => @$location->location->code.' ('.$location->stock_available.')'
                            ];
                        }
                    }

                    $results[] = [
                        'id'          => $product->id,
                        'text'        => $product->name,
                        'sku'         => $product->sku,
                        'stock'       => $product->stock_available,
                        'lote'        => $lote,
                        'stock_class' => $class,
                        'locations'   => $locations
                    ];
                }

            } else {
                $results = [['id' => '', 'text' => 'Nenhum produto encontrado.']];
            }

        } catch(\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }

    /**
     * Get product by barcode
     *
     * @return type
     */
    /*public function getProductByBarcode(Request $request) {

        $barcode    = $request->get('barcode');
        $customerId = $request->get('customer');

        $productLocation = ProductLocation::with('product')
            ->where('barcode', $barcode)
            ->orWhereHas('product', function($q) use($barcode) {
                $q->where(function($q) use($barcode) {
                    $q->where('barcode', $barcode);
                    $q->orWhere('sku', $barcode);
                    $q->orWhere('lote', $barcode);
                    $q->orWhere('serial_no', $barcode);
                });
            })
            ->first();

        if(empty($productLocation)) {

            $results = [
                'result'   => false,
                'feedback' => '<i class="fas fa-exclamation-triangle"></i> Código Inativo ou Inexistente'
            ];

        } elseif($customerId != $productLocation->product->customer_id) {

            $results = [
                'result'   => false,
                'feedback' => '<i class="fas fa-exclamation-triangle"></i> Não pertence ao cliente'
            ];

        } else {
            $results = [
                'result'       => true,
                'feedback'     => '',
                'product'      => $productLocation->product_id,
                'productName'  => $productLocation->product->name,
                'serial_no'    => $productLocation->product->serial_no,
                'lote'         => $productLocation->product->lote,
                'expiration'   => $productLocation->expiration_date,
                'location'     => $productLocation->location_id,
                'locationCode' => $productLocation->location->code,
                'qty'          => $productLocation->stock,
            ];
        }

        return Response::json($results);
    }*/

    /**
     * Get product locations
     *
     * @return type
     */
    public function getProductLocations(Request $request) {

        $productId = $request->get('product');

        $product = Product::filterSource()
                        ->where('id', $productId)
                        ->firstOrFail();

        $locations = '';
        foreach ($product->locations as $location) {
            $locations.= '<option value="'.$location->id.'" data-barcode="'.$location->pivot->barcode.'" data-qty="'.$location->pivot->stock.'">'.$location->code.'</option>';
        }

        $results = [
            'countLocations' => $product->locations->count(),
            'locations'      => $locations,
        ];

        return Response::json($results);
    }

    /**
     * Edit print of labels
     *
     * @return \Illuminate\Http\Response
     */
    public function printLabel($id) {
        return ShippingOrder::printLabels([$id]);
    }

    /**
     * Print product labels
     *
     * @return \Illuminate\Http\Response
     */
    public function printSummary(Request $request, $shippingOrderId) {
        try {
            $ids = $request->get('id');

            if($shippingOrderId) {
                $ids = [$shippingOrderId];
            }

            return ShippingOrder::printSummary($ids);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Throwable
     */
    public function printWavePicking(Request $request) {
        try {
            $ids = $request->get('id');
            return ShippingOrder::printWavePicking($ids);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }
}
