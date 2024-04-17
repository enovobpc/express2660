<?php

namespace App\Http\Controllers\Admin\Logistic;

use App\Models\Logistic\ReceptionOrder;
use App\Models\Logistic\ReceptionOrderLine;
use App\Models\Logistic\ReceptionOrderConfirmation;
use App\Models\Logistic\ReceptionOrderStatus;
use App\Models\Logistic\Location;
use App\Models\Logistic\Product;
use App\Models\Logistic\ProductLocation;
use App\Models\Logistic\ProductHistory;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Mpdf\Mpdf;
use Croppa, Auth, App, Response, Setting;

class ReceptionOrdersController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'logistic_reception_orders';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',logistic_reception_orders']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $locations = Location::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('code', 'id')
            ->toArray();

        $status = ReceptionOrderStatus::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'status',
            'locations'
        );

        return $this->setContent('admin.logistic.reception_orders.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $receptionOrder = new ReceptionOrder();

        $locations = Location::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('code', 'id')
            ->toArray();

        $status = ReceptionOrderStatus::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $formOptions = array('route' => array('admin.logistic.reception-orders.store'), 'method' => 'POST', 'class' => 'form-product-reception', 'autocomplete'=> 'nofill');

        $action = 'Nova ordem de receção';

        $data = compact(
            'receptionOrder',
            'action',
            'formOptions',
            'locations',
            'status'
        );

        return view('admin.logistic.reception_orders.edit', $data)->render();
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
//    public function show($id) {
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $receptionOrder = ReceptionOrder::with('lines.product')
            ->filterSource()
            ->where('id', $id)
            ->firstOrFail();

        $locations = Location::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('code', 'id')
            ->toArray();

        $status = ReceptionOrderStatus::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $formOptions = array('route' => array('admin.logistic.reception-orders.update', $receptionOrder->id), 'method' => 'PUT', 'class' => 'form-exit-order');

        $action = 'Editar ordem de receção';

        $data = compact(
            'receptionOrder',
            'locations',
            'formOptions',
            'action',
            'status'
        );

        return view('admin.logistic.reception_orders.edit', $data)->render();
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
        $id = $id ? $id : $input['id'];

        $receptionOrder = ReceptionOrder::filterSource()
                            ->findOrNew($id);

        if ($receptionOrder->validate($input)) {
            $receptionOrder->fill($input);
            $receptionOrder->user_id = Auth::user()->id;
            $receptionOrder->source  = config('app.source');
            $receptionOrder->setCode();

            $receptionOrder->total_items = @$receptionOrder->lines->count();
            $receptionOrder->total_qty   = @$receptionOrder->lines->sum('qty');
            $receptionOrder->save();

            $receptionOrder->storeOnS3Document();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->with('error', $receptionOrder->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $receptionOrder = ReceptionOrder::filterSource()
            ->whereId($id)
            ->firstOrFail();

        if($receptionOrder->status_id != ReceptionOrderStatus::STATUS_REQUESTED) {
            //repoe stock
            foreach ($receptionOrder->lines as $line) {
                if ($line->location_id) {
                    $productLocation = ProductLocation::firstOrNew([
                        'location_id' => $line->location_id,
                        'product_id'  => $line->product_id
                    ]);
    
                    $productLocation->updateStock($productLocation->stock + $line->qty);
                }

                //update product total stock
                $line->product->updateStockTotal();
            }

            //delete history
            ProductHistory::where('document_id', $receptionOrder->id)->delete();
        }

        $result = $receptionOrder->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::back()->with('success', 'Registo removido com sucesso.');
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

        $result = ReceptionOrder::filterSource()
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

        $data = ReceptionOrder::filterSource()
                    ->with('customer','lines', 'shipment')
                    ->select();

        //filter date
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('requested_date', [$dtMin, $dtMax]);
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

        //filter satisfied
        $value = $request->satisfied;
        if($request->has('satisfied')) {
            if($value) {
                $data = $data->whereRaw('total_qty = total_qty_received');
            } else {
                $data = $data->whereRaw('total_qty > total_qty_received');
            }
        }

        //filter concluded
        $value = $request->hide_concluded;
        if($request->has('hide_concluded')) {
            if($value) {
                $data = $data->whereNotIn('status_id', [ReceptionOrderStatus::STATUS_CONCLUDED, ReceptionOrderStatus::STATUS_CANCELED]);
            }
        }

        return Datatables::of($data)
            ->edit_column('code', function($row) {
                return view('admin.logistic.reception_orders.datatables.code', compact('row'))->render();
            })
            ->edit_column('status_id', function($row) {
                return view('admin.logistic.reception_orders.datatables.status', compact('row'))->render();
            })
            ->edit_column('total_qty', function($row) {
                return view('admin.logistic.reception_orders.datatables.qty', compact('row'))->render();
            })
            ->edit_column('received_date', function($row) {
                return view('admin.logistic.reception_orders.datatables.received_date', compact('row'))->render();
            })
            ->edit_column('customer.name', function($row) {
                return view('admin.logistic.reception_orders.datatables.customer', compact('row'))->render();
            })
            ->edit_column('total_price', function($row) {
                return view('admin.logistic.reception_orders.datatables.price', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.logistic.reception_orders.datatables.actions', compact('row'))->render();
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
        $input['status_id'] = $request->get('status_id', ReceptionOrderStatus::STATUS_REQUESTED);

        try {
            $receptionOrder = ReceptionOrder::filterSource()
                ->findOrNew(@$input['id']);

            $receptionOrder->fill($input);
            $receptionOrder->source = config('app.source');
            $receptionOrder->setCode();
            if($receptionOrder->id) {

                $line = ReceptionOrderLine::firstOrNew([
                    'reception_order_id' => $receptionOrder->id,
                    'product_id'         => $input['product_id']
                ]);

                $line->reception_order_id   = $receptionOrder->id;
                $line->product_id           = $input['product_id'];
                $line->qty                  = @$line->qty + $input['qty'];
                $line->save();
                
                ReceptionOrder::updatePrice($receptionOrder->id);

                $result = [
                    'result'   => true,
                    'feedback' => 'Adicionado ao pedido',
                    'id'       => $receptionOrder->id,
                    'html'     => view('admin.logistic.reception_orders.partials.product_table', compact('receptionOrder'))->render()
                ];
            }

        } catch (\Exception $e) {
            $receptionOrder->forceDelete();

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
    public function updateProduct(Request $request, $receptionOrderId, $lineId) {

        $newQty   = $request->get('qty');
        $newPrice = $request->get('price');

        try {
            $line = ReceptionOrderLine::where('reception_order_id', $receptionOrderId)
                ->where('id', $lineId)
                ->firstOrFail();

            $line->price = $newPrice;
            $line->qty   = $newQty;
            $line->save();

            ReceptionOrder::updatePrice($receptionOrderId);

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
    public function deleteProduct(Request $request, $receptionOrderId, $lineId) {

        try {
            $line = ReceptionOrderLine::where('reception_order_id', $receptionOrderId)
                ->where('id', $lineId)
                ->firstOrFail();

            $line->qty = 0; //stock alocado
            $line->save();

            //remove a linha definitivo
            $line->forceDelete();

            $totalLines = ReceptionOrderLine::where('reception_order_id', $receptionOrderId)->count();
            if(!$totalLines) {
                ReceptionOrder::filterSource()->where('id', $receptionOrderId)->forceDelete();
            }

            ReceptionOrder::updatePrice($receptionOrderId);

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
  /*  public function getProductByBarcode(Request $request) {

        $barcode    = trim($request->get('barcode'));
        $customerId = $request->get('customer');

        $product = Product::where(function($q) use($barcode){
                $q->where('barcode', $barcode);
                $q->orWhere('sku', $barcode);
            })
            ->first();

        if(empty($product)) {
            $results = [
                'result'   => false,
                'feedback' => '<small class="tr-helper text-red"><i class="fas fa-exclamation-triangle"></i> Artigo inexistente</small>'
            ];
        } elseif($customerId != $product->customer_id) {
            $results = [
                'result'   => false,
                'feedback' => '<small class="tr-helper text-red"><i class="fas fa-exclamation-triangle"></i> Não pertence ao cliente</small>'
            ];
        } else {
            $results = [
                'result'       => true,
                'feedback'     => '',
                'productId'    => $product->id,
                'productName'  => $product->name,
                'location'     => @$product->location_id,
                'stock'        => $product->stock_total,
            ];
        }

        return Response::json($results);
    }*/

    /**
     * Get product locations
     *
     * @return type
     */
/*    public function getProductLocations(Request $request) {

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
    }*/

    /**
     * Print product labels
     *
     * @return \Illuminate\Http\Response
     */
    public function printDocument(Request $request, $receptionOrderId) {
        return ReceptionOrder::printSummary([$receptionOrderId]);
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

            $receptionOrder = ReceptionOrder::where('code', $code)->first();

            if(!$receptionOrder) {
                $shipment = Shipment::where('tracking_code', $code)
                    ->orWhere('provider_tracking_code', $code)
                    ->first();

                if($shipment) {
                    $receptionOrder = ReceptionOrder::where('shipment_id', $shipment->id)->first();
                }
            }

            if(!$receptionOrder) {
                return response()->json([
                    'result'    => false,
                    'feedback'  => 'Ordem de recepção não encontrada.'
                ]);
            }

            if($receptionOrder->status_id == ReceptionOrderStatus::STATUS_CONCLUDED) {
                return response()->json([
                    'result'    => false,
                    'feedback'  => 'A ordem de recepção já se encontra finalizada.'
                ]);
            }

            return response()->json([
                'result'    => true,
                'feedback'  => 'Picking in iniciado',
                'html'      => $this->editConfirmation($request, $receptionOrder->id)
            ]);
        }

        return view('admin.logistic.reception_orders.confirmation.create')->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editConfirmation(Request $request, $id) {

        $receptionOrder = ReceptionOrder::with('customer')
            ->with(['lines' => function($q) {
                $q->with('product', 'location');
            }])
            ->filterSource()
            ->where('id', $id)
            ->first();

        $receptionOrder = $receptionOrder ? $receptionOrder : new ReceptionOrder();

        $locations = Location::filterSource()
            ->select('id', 'code', 'barcode')
            ->orderBy('code', 'asc')
            ->get()
            ->pluck('code_barcode', 'id')
            ->toArray();

        $formOptions = array('route' => array('admin.logistic.reception-orders.confirmation.store', $id), 'method' => 'POST', 'class' => 'form-confirmation');

        $allowEdit = $receptionOrder->allow_edit;

        $data = compact(
            'receptionOrder',
            'formOptions',
            'locations',
            'allowEdit'
        );

        return view('admin.logistic.reception_orders.confirmation.edit', $data)->render();
    }

    /**
     * Store confirmation
     * @param Request $request
     */
    function storeConfirmation(Request $request, $id) {
        $input       = $request->all();
        $conclude    = $request->get('conclude', false);

        $receptionOrder = ReceptionOrder::with('lines')
            ->filterSource()
            ->where('id', $id)
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
        return response()->json($result);
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function confirmationSearchBarcode(Request $request, $id) {

        $barcode = trim($request->barcode);

        $receptionOrder = ReceptionOrder::with('customer')
            ->with(['lines' => function($q) {
                $q->with('product', 'location');
            }])
            ->filterSource()
            ->where('id', $id)
            ->first();

        $productIds = $receptionOrder->lines->pluck('product_id')->toArray();

        $product = ProductLocation::with('product', 'location')
            ->whereIn('product_id', $productIds)
            ->where(function($q) use($receptionOrder, $barcode) {
                $q->whereHas('product', function($q) use($receptionOrder, $barcode) {
                    $q->where('customer_id', $receptionOrder->customer_id);
                    $q->where(function($q) use($barcode) {
                        $q->where('sku', $barcode);
                        $q->orWhere('serial_no', $barcode);
                        $q->orWhere('lote', $barcode);
                        $q->orWhere('barcode', $barcode);
                    });
                });
                $q->orWhere('barcode', $barcode);
            })
            ->first();


        if(!$product) {
            $result = [
                'result'   => false,
                'feedback' => 'Nenhum artigo encontrado.'
            ];
        } else {

            $product = $product->first();
            $result = [
                'result'    => true,
                'singleLocation' => true,
                'sku'       => @$product->product->sku,
                'product'   => @$product->product_id,
                'location'  => @$product->location_id,
                'barcode'   => @$product->barcode
            ];
        }

        return response()->json($result);
    }


    /**
     * Get product by barcode
     *
     * @return type
     */
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
                'feedback' => '<i class="fas fa-exclamation-triangle"></i> Tem de selecionar a localização de destino.'
            ];
        } elseif (!$receptionOrder) {
            $results = [
                'result'   => false,
                'feedback' => '<i class="fas fa-exclamation-triangle"></i> Ordem de recepção não encontrada.'
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
                    'feedback' => '<i class="fas fa-exclamation-triangle"></i> Localização não encontrada.'
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
                    $receptionOrder->save();
                
                    $receptionOrder = ReceptionOrder::find($receptionOrder->id);

                    $results = [
                        'result'        => true,
                        'feedback'      => '',
                        'html_products' => view('admin.logistic.reception_orders.partials.confirmation_table_products', compact('receptionOrder', 'allowEdit'))->render(),
                        'html_devolved' => view('admin.logistic.reception_orders.partials.confirmation_table_received', compact('receptionOrder', 'allowEdit'))->render()
                    ];
                } else {
                    $results = [
                        'result'        => false,
                        'feedback'      => 'Todas as quantidades já foram satisfeitas.',
                        'html_products' => null,
                        'html_devolved' => null
                    ];
                }
            } else {
                $results = [
                    'result'   => false,
                    'feedback' => '<i class="fas fa-exclamation-triangle"></i> Artigo não encontrado no pedido.'
                ];
            }
        }

        return Response::json($results);
    }


    /**
     * Get product by barcode
     *
     * @return type
     */
    public function storeAllItems(Request $request, $receptionOrder)
    {

        $allowEdit  = true;
        $locationId = $request->get('location');
        $date       = $request->get('date');

        if (empty($locationId)) {
            $results = [
                'result'   => false,
                'feedback' => '<i class="fas fa-exclamation-triangle"></i> Tem de selecionar a localização de destino.'
            ];
        } else {


            $receptionOrderProducts = $receptionOrder->lines;

            foreach ($receptionOrderProducts as $receptionOrderProduct) {

                //regista as linhas de confirmação de recepção
                $receptionConfirmation = ReceptionOrderConfirmation::firstOrNew([
                    'reception_order_id' => $receptionOrder->id,
                    'location_id'        => $locationId,
                    'product_id'         => $receptionOrderProduct->product_id
                ]);

                $receptionConfirmation->product_id   = $receptionOrderProduct->product_id;
                $receptionConfirmation->qty_received = $receptionOrderProduct->qty;
                $receptionConfirmation->save();
            }

            //update shipping order products
            $receptionOrder->updateReceptionOrderProducts();

            //obtem os dados atualizados para enviar para a view
            $receptionOrder = ReceptionOrder::find($receptionOrder->id);

            $results = [
                'result'        => true,
                'feedback'      => '',
                'html_products' => view('admin.logistic.reception_orders.partials.confirmation_table_products', compact('receptionOrder', 'allowEdit'))->render(),
                'html_devolved' => view('admin.logistic.reception_orders.partials.confirmation_table_received', compact('receptionOrder', 'allowEdit'))->render()
            ];
        }

        return Response::json($results);
    }


    /**
     * Update devolution Item
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateConfirmationItem(Request $request, $receptionOrderId, $id)
    {
        $input     = $request->all();
        
        $receptionConfirmation = ReceptionOrderConfirmation::where('reception_order_id', $receptionOrderId)->find($id);
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
                    'feedback'      => 'Registo alterado com sucesso.',
                    'html_products' => view('admin.logistic.reception_orders.partials.confirmation_table_products', compact('receptionOrder', 'allowEdit'))->render(),
                    'html_devolved' => view('admin.logistic.reception_orders.partials.confirmation_table_received', compact('receptionOrder', 'allowEdit'))->render()
                ];
            } else {

                $results = [
                    'result'   => false,
                    'feedback' => 'Erro ao atualizar o registo',
                ];
            }
        } else {

            $allowEdit = $receptionOrder->allow_edit;

            $receptionConfirmation->qty_received = $oldQty; //repõe valor anterior
            $receptionConfirmation->save();

            $results = [
                'result'        => false,
                'feedback'      => 'Quantidade Inválida. Não pode exceder: ' . $maxQty,
                'html_products' => view('admin.logistic.reception_orders.partials.confirmation_table_products', compact('receptionOrder', 'allowEdit'))->render(),
                'html_devolved' => view('admin.logistic.reception_orders.partials.confirmation_table_received', compact('receptionOrder', 'allowEdit'))->render()
            ];
        }


        return Response::json($results);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyConfirmationItem($receptionOrderId, $id)
    {
        $receptionOrder = ReceptionOrder::filterSource()->find($receptionOrderId);

        $result = ReceptionOrderConfirmation::where('reception_order_id', $receptionOrderId)
            ->whereId($id)
            ->delete();

        if ($result) {

            //update shipping order products
            $receptionOrder->updateReceptionOrderProducts();

            $allowEdit = $receptionOrder->allow_edit;

            $results = [
                'result'        => true,
                'feedback'      => 'Registo removido com sucesso.',
                'html_products' => view('admin.logistic.reception_orders.partials.confirmation_table_products', compact('receptionOrder', 'allowEdit'))->render(),
                'html_devolved' => view('admin.logistic.reception_orders.partials.confirmation_table_received', compact('receptionOrder', 'allowEdit'))->render()
            ];
        } else {
            $results = [
                'result'   => false,
                'feedback' => 'Erro ao remover o registo',
            ];
        }

        return Response::json($results);
    }

    /**
     * Get product locations
     *
     * @return type
     */
    public function getProductLocations(Request $request)
    {

        $productId = $request->get('product');

        $product = Product::filterSource()
            ->where('id', $productId)
            ->firstOrFail();

        $locations = '';
        foreach ($product->locations as $location) {
            $locations .= '<option value="' . $location->id . '" data-barcode="' . $location->pivot->barcode . '" data-qty="' . $location->pivot->stock . '">' . $location->code . '</option>';
        }

        $results = [
            'countLocations' => $product->locations->count(),
            'locations'      => $locations,
        ];

        return Response::json($results);
    }
}
