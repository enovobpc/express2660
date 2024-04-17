<?php

namespace App\Http\Controllers\Admin\Logistic;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Logistic\Location;
use App\Models\Logistic\Product;
use App\Models\Customer;
use App\Models\Logistic\Devolution;
use App\Models\Logistic\DevolutionItem;
use App\Models\Logistic\ShippingOrder;
use App\Models\Logistic\ShippingOrderLine;
use App\Models\Logistic\ProductHistory;
use App\Models\Logistic\ProductLocation;
use Croppa, Auth, App, Response, Setting, DB;

class DevolutionsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'logistic_devolutions';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',logistic_devolutions']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->setContent('admin.logistic.devolutions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $shippingOrder  = null;
        $pickingMode    = $request->get('picking-mode', null);
        $directCreation = $request->get('direct-creation', false);
        $shippingOrderBarcode = $request->get('order', null);


        $devolution = new Devolution();

        if ($shippingOrderBarcode) {
            $shippingOrder = App\Models\Logistic\ShippingOrder::with('lines')
                ->filterSource()
                ->where(function ($q) use ($shippingOrderBarcode) {
                    $q->where('id', $shippingOrderBarcode);
                    $q->orWhere('code', $shippingOrderBarcode);
                    $q->orWhere('shipment_trk', $shippingOrderBarcode);
                })
                ->where('status_id', App\Models\Logistic\ShippingOrderStatus::STATUS_CONCLUDED)
                ->first();


            if ($shippingOrder) {
                $devolution = Devolution::firstOrNew(['shipping_order_id' => $shippingOrder->id]);
                $devolution->shipping_order      = $shippingOrder;
                $devolution->shipping_order_id   = $shippingOrder->id;
                $devolution->shipping_order_code = $shippingOrder->code;
                $devolution->shipment_id         = $shippingOrder->shipment_id;
                $devolution->shipment_trk        = $shippingOrder->shipment_trk;
                $devolution->customer_id         = $shippingOrder->customer_id;
                $devolution->customer            = $shippingOrder->customer;
            } else {
                return [
                    'result'   => false,
                    'html'     => null,
                    'feedback' => 'Nenhuma ordem de saída ou expedição encontrada.'
                ];
            }
        }

        $locations = Location::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('code', 'id')
            ->toArray();


        $action = 'Devolução de Artigos';

        $formOptions = array('route' => array('admin.logistic.devolutions.store'), 'method' => 'POST', 'class' => 'form-devolution', 'autocomplete' => 'nofill');

        $allowEdit = true;

        $shippingOrderProducts = ShippingOrderLine::where('shipping_order_id', @$devolution->shipping_order_id)
            ->groupBy('product_id')
            ->get([
                '*',
                DB::raw('sum(qty_satisfied) as qty_satisfied')
            ]);

        $data = compact(
            'devolution',
            'action',
            'formOptions',
            'locations',
            'allowEdit',
            'shippingOrderProducts'
        );

        if ($shippingOrderBarcode && $shippingOrder && !$directCreation) {
            return [
                'result' => true,
                'html'   => view('admin.logistic.devolutions.edit', $data)->render()
            ];
        }

        if ($pickingMode && !$directCreation) {
            return view('admin.logistic.devolutions.create', $data)->render();
        }

        return view('admin.logistic.devolutions.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
    public function edit(Request $request, $id)
    {
        $devolution = Devolution::filterSource()
            // ->where('status', '<>', Devolution::STATUS_CONCLUDED)
            ->find($id);

        $locations = Location::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('code', 'id')
            ->toArray();

        $action = 'Devolução #' . $devolution->code;

        $formOptions = array('route' => array('admin.logistic.devolutions.update', $id), 'method' => 'PUT', 'class' => 'form-devolution', 'autocomplete' => 'nofill');

        $allowEdit = true;
        if (!empty($devolution->shipment_id) && ($devolution->status == Devolution::STATUS_CONCLUDED)) {
             $allowEdit = false;
        }

        $shippingOrderProducts = ShippingOrderLine::where('shipping_order_id', @$devolution->shipping_order_id)
            ->groupBy('product_id')
            ->get([
                '*',
                DB::raw('sum(qty_satisfied) as qty_satisfied')
            ]);

        $data = compact(
            'devolution',
            'action',
            'formOptions',
            'locations',
            'allowEdit',
            'shippingOrderProducts'
        );

        return view('admin.logistic.devolutions.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {

        $input = $request->all();

        if ($id) {
            $devolution = Devolution::filterSource()->findOrNew($id);
        } else {
            $devolution = Devolution::filterSource()
                ->firstOrNew([
                    'shipping_order_id' => $input['shipping_order_id']
                ]);
        }

        $shippingOrder = ShippingOrder::filterSource()
            ->find($input['shipping_order_id']);

        if ($devolution->validate($input)) {
            $devolution->fill($input);
            $devolution->shipment_id         = @$shippingOrder->shipment_id;
            $devolution->shipment_trk        = @$shippingOrder->shipment_trk;
            $devolution->shipping_order_code = @$shippingOrder->code;
            $devolution->customer_id         = @$shippingOrder->customer_id;
            $devolution->total_qty_original  = @$shippingOrder->lines->sum('qty_satisfied');
            $devolution->total_items_original= @$shippingOrder->lines->count();
            $devolution->user_id = Auth::user()->id;
            $devolution->status  = @$input['conclude'] ? 'concluded' : 'processing';
            $devolution->source  = config('app.source');
            $devolution->setCode();

            if (@$input['conclude']) {

                //repoe os stocks
                foreach ($devolution->items as $item) {

                    $productLocation = ProductLocation::firstOrNew([
                        'product_id'  => $item->product_id,
                        'location_id' => $item->location_id
                    ]);

                    $productLocation->stock = $productLocation->stock + $item->qty;
                    $productLocation->stock_allocated = $productLocation->stock_allocated ? : 0;
                    $productLocation->setBarcode();

                    $productLocation->product->updateStockTotal();

                    //regista a ordem de saida nas movimentações do artigo
                    $history = ProductHistory::firstOrNew([
                        'action'         => 'devolution',
                        'product_id'     => $item->product_id,
                        'destination_id' => $item->location_id,
                        'document_id'    => $devolution->id
                    ]);
                    //$history->document  = $devolution->code;
                    $history->obs       = 'Devolução N.º ' . $devolution->code;
                    $history->qty       = $item->qty;
                    $history->save();
                }
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->with('error', $devolution->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $devolution = Devolution::filterSource()
            ->whereId($id)
            ->firstOrFail();

        ShippingOrder::where('id', $devolution->shipping_order_id)->update(['qty_devolved' => 0]);

        ShippingOrderLine::where('shipping_order_id', $devolution->shipping_order_id)->update(['qty_devolved' => 0]);

        $result = $devolution->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a devolução.');
        }

        return Redirect::back()->with('success', 'Devolução removida com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        $ids = explode(',', $request->ids);

        $devolutions = Devolution::filterSource()
            ->whereIn('id', $ids)
            ->get();

        foreach ($devolutions as $devolution) {
            ShippingOrder::where('id', $devolution->shipping_order_id)->update(['qty_devolved' => 0]);

            ShippingOrderLine::where('shipping_order_id', $devolution->shipping_order_id)->update(['qty_devolved' => 0]);

            $devolution->delete();
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }


    /**
     * Get product by barcode
     *
     * @return type
     */
    public function storeItem(Request $request)
    {

        $allowEdit       = true;
        $readall         = $request->get('readall', false);
        $readall         = $readall == 'true' ? true : false;
        $barcode         = $request->get('barcode');
        $locationId      = $request->get('location');
        $shippingOrderId = $request->get('orderId');
        $date            = $request->get('date');

        $devolution    = Devolution::firstOrNew(['shipping_order_id' => $shippingOrderId]);
        $shippingOrder = ShippingOrder::find($shippingOrderId);

        if (empty($locationId)) {
            $results = [
                'result'   => false,
                'feedback' => '<i class="fas fa-exclamation-triangle"></i> Tem de selecionar a localização de destino.'
            ];
        } elseif (!$shippingOrder) {

            $results = [
                'result'   => false,
                'feedback' => '<i class="fas fa-exclamation-triangle"></i> Ordem de Envio não encontrada.'
            ];
        } else {

            if ($readall) {
                return $this->storeAllItems($request, $devolution, $shippingOrder);
            }

            $shippingOrderProductsIds = $shippingOrder->lines->pluck('product_id')->toArray();

            $product = Product::where(function ($q) use ($barcode) {
                    $q->where('sku', $barcode);
                    $q->orWhere('barcode', $barcode);
                    $q->orWhere('serial_no', $barcode);
                    $q->orWhere('lote', $barcode);
                })
                ->whereIn('id', $shippingOrderProductsIds)
                ->first();


            if ($product) {

                //cria a devolução caso não exista
                //acontece a 1ª vez que um artigo é picado
                if (!$devolution->exists) {
                    $devolution->source             = @$shippingOrder->source;
                    $devolution->shipping_order_id  = @$shippingOrder->id;
                    $devolution->customer_id        = @$shippingOrder->customer_id;
                    $devolution->date               = $date;
                    $devolution->user_id            = Auth::user()->id;
                    $devolution->setCode();
                }

                //verifica se é possível registar a quantidade
                $totalQtyPossible = ShippingOrderLine::where('shipping_order_id', $devolution->shipping_order_id)
                    ->where('product_id', $product->id)
                    ->sum('qty_satisfied');

                $totalQtyDevolved = DevolutionItem::where('devolution_id', $devolution->id)
                    ->where('product_id', $product->id)
                    ->sum('qty');

                if ($totalQtyDevolved < $totalQtyPossible) {

                    //regista as linhas de devolução
                    $devolutionItem = DevolutionItem::firstOrNew([
                        'devolution_id' => $devolution->id,
                        'location_id'   => $locationId,
                        'product_id'    => $product->id
                    ]);

                    $devolutionItem->product_id = $product->id;
                    $devolutionItem->qty = @$devolutionItem->qty + 1;
                    $devolutionItem->save();

                    //update shipping order products
                    $devolution->updateShippingOrderProducts($shippingOrder);

                    $shippingOrderProducts = ShippingOrderLine::where('shipping_order_id', @$devolution->shipping_order_id)
                        ->groupBy('product_id')
                        ->get([
                            '*',
                            DB::raw('sum(qty_satisfied) as qty_satisfied')
                        ]);

                    $results = [
                        'result'        => true,
                        'feedback'      => '',
                        'html_products' => view('admin.logistic.devolutions.partials.table_products', compact('devolution', 'shippingOrderProducts', 'allowEdit'))->render(),
                        'html_devolved' => view('admin.logistic.devolutions.partials.table_devolved', compact('devolution', 'shippingOrderProducts', 'allowEdit'))->render()
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
    public function storeAllItems(Request $request, $devolution, $shippingOrder)
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

            $shippingOrderProducts = ShippingOrderLine::where('shipping_order_id', @$devolution->shipping_order_id)
                ->groupBy('product_id')
                ->get([
                    '*',
                    DB::raw('sum(qty_satisfied) as qty_satisfied')
                ]);

            //cria a devolução caso não exista
            //acontece a 1ª vez que um artigo é picado
            if (!$devolution->exists) {
                $devolution->source             = @$shippingOrder->source;
                $devolution->shipping_order_id  = @$shippingOrder->id;
                $devolution->customer_id        = @$shippingOrder->customer_id;
                $devolution->date               = $date;
                $devolution->user_id            = Auth::user()->id;
                $devolution->setCode();
            }

            DevolutionItem::where('devolution_id', $devolution->id)->forceDelete();

            foreach ($shippingOrderProducts as $shippingOrderProduct) {

                //regista as linhas de devolução
                $devolutionItem = DevolutionItem::firstOrNew([
                    'devolution_id' => $devolution->id,
                    'location_id'   => $locationId,
                    'product_id'    => $shippingOrderProduct->product_id
                ]);

                $devolutionItem->product_id = $shippingOrderProduct->product_id;
                $devolutionItem->qty        = $shippingOrderProduct->qty_satisfied;
                $devolutionItem->save();
            }

            //update shipping order products
            $devolution->updateShippingOrderProducts($shippingOrder);

            $shippingOrderProducts = ShippingOrderLine::where('shipping_order_id', @$devolution->shipping_order_id)
                ->groupBy('product_id')
                ->get([
                    '*',
                    DB::raw('sum(qty_satisfied) as qty_satisfied')
                ]);

            $results = [
                'result'        => true,
                'feedback'      => '',
                'html_products' => view('admin.logistic.devolutions.partials.table_products', compact('devolution', 'shippingOrderProducts', 'allowEdit'))->render(),
                'html_devolved' => view('admin.logistic.devolutions.partials.table_devolved', compact('devolution', 'shippingOrderProducts', 'allowEdit'))->render()
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
    public function updateItem(Request $request, $devolutionId, $id)
    {

        $allowEdit = true;
        $input     = $request->all();

        $devolutionItem = DevolutionItem::where('devolution_id', $devolutionId)->find($id);
        $devolution     = $devolutionItem->devolution;

        $oldQty = $devolutionItem->qty; //guarda o valor anterior
        $devolutionItem->fill($input);
        $result = $devolutionItem->save();

        //verifica se é possível registar a quantidade
        $maxQty = ShippingOrderLine::where('shipping_order_id', $devolution->shipping_order_id)
            ->where('product_id', $devolutionItem->product_id)
            ->sum('qty_satisfied');

        $totalQtyDevolved = DevolutionItem::where('devolution_id', $devolution->id)
            ->where('product_id', $devolutionItem->product_id)
            ->sum('qty');

        if ($totalQtyDevolved <= $maxQty) {

            if ($result) {

                //update shipping order products
                $devolution->updateShippingOrderProducts();

                $devolution = Devolution::with('items')->find($devolution->id);

                $shippingOrderProducts = ShippingOrderLine::where('shipping_order_id', @$devolution->shipping_order_id)
                    ->groupBy('product_id')
                    ->get([
                        '*',
                        DB::raw('sum(qty_satisfied) as qty_satisfied')
                    ]);

                $results = [
                    'result'        => true,
                    'feedback'      => 'Registo alterado com sucesso.',
                    'html_products' => view('admin.logistic.devolutions.partials.table_products', compact('devolution', 'shippingOrderProducts', 'allowEdit'))->render(),
                    'html_devolved' => view('admin.logistic.devolutions.partials.table_devolved', compact('devolution', 'shippingOrderProducts', 'allowEdit'))->render()
                ];
            } else {

                $results = [
                    'result'   => false,
                    'feedback' => 'Erro ao atualizar o registo',
                ];
            }
        } else {

            $devolutionItem->qty = $oldQty; //repõe valor anterior
            $devolutionItem->save();

            $shippingOrderProducts = ShippingOrderLine::where('shipping_order_id', @$devolution->shipping_order_id)
                ->groupBy('product_id')
                ->get([
                    '*',
                    DB::raw('sum(qty_satisfied) as qty_satisfied')
                ]);

            $results = [
                'result'        => false,
                'feedback'      => 'Quantidade Inválida. Não pode exceder: ' . $maxQty,
                'html_products' => view('admin.logistic.devolutions.partials.table_products', compact('devolution', 'shippingOrderProducts', 'allowEdit'))->render(),
                'html_devolved' => view('admin.logistic.devolutions.partials.table_devolved', compact('devolution', 'shippingOrderProducts', 'allowEdit'))->render()
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
    public function destroyItem($devolutionId, $id)
    {

        $allowEdit  = true;
        $devolution = Devolution::filterSource()->find($devolutionId);

        $result = DevolutionItem::where('devolution_id', $devolutionId)
            ->whereId($id)
            ->delete();

        if ($result) {

            //update shipping order products
            $devolution->updateShippingOrderProducts();

            $shippingOrderProducts = ShippingOrderLine::where('shipping_order_id', @$devolution->shipping_order_id)
                ->groupBy('product_id')
                ->get([
                    '*',
                    DB::raw('sum(qty_satisfied) as qty_satisfied')
                ]);

            $results = [
                'result'        => true,
                'feedback'      => 'Registo removido com sucesso.',
                'html_products' => view('admin.logistic.devolutions.partials.table_products', compact('devolution', 'shippingOrderProducts', 'allowEdit'))->render(),
                'html_devolved' => view('admin.logistic.devolutions.partials.table_devolved', compact('devolution', 'shippingOrderProducts', 'allowEdit'))->render()
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
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $data = Devolution::filterSource()
            ->with('customer', 'shipping_order', 'shipment')
            ->select();

        //filter date
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter status
        $value = $request->status;
        if ($request->has('status')) {
            $data = $data->whereIn('status', $value);
        }

        //filter customer
        $value = $request->customer;
        if ($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('code', function ($row) {
                return view('admin.logistic.devolutions.datatables.code', compact('row'))->render();
            })
            ->edit_column('shipping_order_code', function ($row) {
                return view('admin.logistic.devolutions.datatables.shipping_order', compact('row'))->render();
            })
            ->add_column('shipment_trk', function ($row) {
                return view('admin.logistic.devolutions.datatables.shipment', compact('row'))->render();
            })
            ->edit_column('customer.name', function ($row) {
                return view('admin.logistic.devolutions.datatables.customer', compact('row'))->render();
            })
            ->edit_column('status', function ($row) {
                return view('admin.logistic.devolutions.datatables.status', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.logistic.devolutions.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request)
    {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {
            $customersWithProducts = Product::filterSource()
                ->groupBy('customer_id')
                ->pluck('customer_id')
                ->toArray();

            $customers = Customer::filterAgencies()
                ->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->whereIn('id', $customersWithProducts)
                ->isDepartment(false)
                ->get(['name', 'code', 'id']);

            if ($customers) {
                $results = array();
                foreach ($customers as $customer) {
                    $results[] = array('id' => $customer->id, 'text' => $customer->code . ' - ' . str_limit($customer->name, 40));
                }
            } else {
                $results = [['id' => '', 'text' => 'Nenhum cliente encontrado.']];
            }
        } catch (\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }

    /**
     * Search senders on DB
     *
     * @return type
     */
    public function getShippingOrder(Request $request)
    {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        //try {

        $shippingOrders = App\Models\Logistic\ShippingOrder::filterSource()
            ->with('customer')
            ->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', $search)
                    ->orWhere('shipment_trk', 'LIKE', $search);
            })
            ->where('status_id', App\Models\Logistic\ShippingOrder::STATUS_CONCLUDED)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get(['code', 'id', 'customer_id']);

        if ($shippingOrders) {
            $results = array();
            foreach ($shippingOrders as $shippingOrder) {
                $results[] = array('id' => $shippingOrder->id, 'text' => $shippingOrder->code . ' - ' . @$shippingOrder->customer->name);
            }
        } else {
            $results = [['id' => '', 'text' => 'Nenhuma ordem encontrada.']];
        }

        /* } catch(\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }*/

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
