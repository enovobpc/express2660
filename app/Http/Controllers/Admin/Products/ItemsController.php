<?php

namespace App\Http\Controllers\Admin\Products;

use App\Models\ProductSale;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Setting, Response;
use App\Models\Product;
use App\Models\Customer;

class ItemsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'products_sales';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',products_sales']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.products.sales.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Nova Venda';
        
        $productSale = new ProductSale;
                
        $formOptions = array('route' => array('admin.products.sales.store'), 'method' => 'POST');

        $data = compact(
            'productSale',
            'action',
            'formOptions'
        );

        return view('admin.products.sales.edit', $data)->render();
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $action = 'Editar Venda';
        
        $productSale = ProductSale::with('customer','product')
                        ->whereHas('customer', function($q){
                            $q->filterAgencies();
                        })
                        ->findOrfail($id);

        $formOptions = ['route' => array('admin.products.sales.update', $productSale->id), 'method' => 'PUT'];

        $data = compact(
            'productSale',
            'action',
            'formOptions'
        );

        return view('admin.products.sales.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        ProductSale::flushCache(ProductSale::CACHE_TAG);

        $devolvedQty = 0;
        $input = $request->all();

        $productSale = ProductSale::whereHas('customer', function($q){
                $q->filterAgencies();
            })
            ->findOrNew($id);

        $originalQty = $productSale->qty;
        $product = Product::filterSource()
                        ->findOrFail($input['product_id']);

        if(!isset($input['qty'])
            || $input['qty'] <= 0
            || (!$productSale->exists && $product->stock <= 0)
            || (!$productSale->exists && $input['qty'] > $product->stock)
        ) {
            return Redirect::back()->withInput()->with('error', 'Não foi possível concluir a compra. A quantidade indicada está indisponível.');
        }

        if($productSale->exists) {
            if($productSale->qty > $input['qty']) {
                $devolvedQty = $productSale->qty - $input['qty'];
                $devolvedQty = $devolvedQty < 0 ? $devolvedQty * -1 : $devolvedQty;
            }
        }

        if ($productSale->validate($input)) {
            $productSale->fill($input);
            $result = $productSale->save();

            if($result) {
                if($devolvedQty) {
                    $product->stock = $product->stock + $devolvedQty;
                } else {
                    $product->stock = $product->stock - ($productSale->qty - $originalQty);
                }

                $product->save();
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $productSale->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $productSale = ProductSale::whereHas('customer', function($q){
                            $q->filterAgencies();
                        })
                        ->findOrFail($id);

        $product = Product::filterSource()
                        ->findOrFail($productSale->product_id);

        $product->stock = $product->stock + $productSale->qty;
        $product->save();

        $result = $productSale->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo de venda.');
        }

        return Redirect::back()->with('success', 'Registo de venda removido com sucesso.');
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
        
        $productSales = ProductSale::whereHas('customer', function($q){
                            $q->filterAgencies();
                        })
                        ->whereIn('id', $ids)
                        ->delete();

        foreach ($productSales as $productSale) {
            $product = Product::filterSource()
                ->findOrFail($productSale->product_id);

            $product->stock = $product->stock + $productSale->qty;
            $product->save();

            $productSale->delete();
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $data = ProductSale::with('customer', 'product')
                        ->whereHas('customer', function ($q) {
                            $q->filterAgencies();
                        })
                        ->select();

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter customer
        $value = $request->get('customer');
        if($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter product
        $value = $request->get('product');
        if($request->has('product')) {
            $data = $data->where('product_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('products.name', function ($row) {
                return view('admin.products.sales.datatables.name', compact('row'))->render();
            })
            ->edit_column('price', function ($row) {
                return money(@$row->price, Setting::get('app_currency'));
            })
            ->edit_column('subtotal', function ($row) {
                return money($row->subtotal, Setting::get('app_currency'));
            })
            ->edit_column('vat_rate', function ($row) {
                return view('admin.products.sales.datatables.vat', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.products.sales.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request) {

        $search = $request->get('q');
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $customers = Customer::filterAgencies()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
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
     * Search products on DB
     *
     * @return type
     */
    public function searchProduct(Request $request) {

        $search = $request->get('q');
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $products = Product::filterSource()
                ->where(function($q) use($search){
                    $q->where('ref', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search);
                })
                ->get(['name', 'ref', 'id']);

            if($products) {

                $results = array();
                foreach($products as $product) {
                    $results[]=array('id'=> $product->id, 'text' => $product->ref. ' - '.str_limit($product->name, 40));
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
     * Return product details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProduct(Request $request) {

        $product = Product::filterSource()
            ->findOrFail($request->id);

        return Response::json($product);
    }
}
