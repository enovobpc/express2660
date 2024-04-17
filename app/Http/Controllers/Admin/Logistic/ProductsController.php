<?php

namespace App\Http\Controllers\Admin\Logistic;

use App\Models\Logistic\Warehouse;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Customer;
use App\Models\Logistic\Location;
use App\Models\Logistic\Product;
use App\Models\Logistic\ProductLocation;
use App\Models\Logistic\ProductHistory;
use App\Models\Logistic\ProductImage;
use App\Models\User;
use App\Models\Logistic\Brand;
use App\Models\Logistic\Model;
use App\Models\Logistic\Family;
use App\Models\Logistic\Category;
use App\Models\Logistic\SubCategory;
use Croppa, Auth, App, Response, Excel, Setting, File, DB, Form, Artisan;

class ProductsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'logistic_products';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',logistic_products']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $locations = Location::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('code', 'id')
            ->toArray();

        $brands = Brand::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $models = Model::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $families = Family::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $categories = Category::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $subcategories = SubCategory::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $warehouses = Warehouse::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'locations',
            'brands',
            'models',
            'families',
            'categories',
            'subcategories',
            'warehouses'
        );

        return $this->setContent('admin.logistic.products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $action = 'Novo artigo';

        $product = new Product();

        $formOptions = array('route' => array('admin.logistic.products.store'), 'method' => 'POST', 'class' => 'form-product');

        $locations = Location::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('code', 'id')
            ->toArray();
        
        $families = Family::filterSource()
            ->filterCustomer($product->customer_id)
            ->pluck('name', 'id')
            ->toArray();

        $categories = Category::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $subcategories = SubCategory::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();
        

        $data = compact(
            'product',
            'action',
            'formOptions',
            'locations',
            'families',
            'categories',
            'subcategories'
        );

        return view('admin.logistic.products.create', $data)->render();
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
    public function show($id)
    {

        $product = Product::filterSource()
            ->with('locations')
            ->findOrfail($id);

        $operators = User::filterAgencies()->pluck('name', 'id')->toArray();

        $locations = Location::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('code', 'id')
            ->toArray();

        $allItems = Product::where('customer_id', $product->customer_id)
            ->where('sku', $product->sku)
            ->whereNotIn('stock_status', [Product::STATUS_OUTSTOCK, Product::STATUS_BLOCKED])
            ->get([
                'stock_total',
                'stock_allocated'
            ]);

        $globalStock = $allItems->sum('stock_total') - $allItems->sum('stock_allocated');

        $brands = Brand::filterSource()
            ->filterCustomer($product->customer_id)
            ->pluck('name', 'id')
            ->toArray();

        $models = Model::filterSource()
            ->filterCustomer($product->customer_id)
            ->where(function ($q) use ($product) {
                $q->whereNull('brand_id');
                $q->orWhere('brand_id', $product->brand_id);
            })
            ->pluck('name', 'id')
            ->toArray();

        $families = Family::filterSource()
            ->filterCustomer($product->customer_id)
            ->pluck('name', 'id')
            ->toArray();

        $categories = Category::filterSource()
            ->filterCustomer($product->customer_id)
            ->where(function ($q) use ($product) {
                $q->whereNull('family_id');
                $q->orWhere('family_id', $product->family_id);
            })
            ->pluck('name', 'id')
            ->toArray();

        $subcategories = SubCategory::filterSource()
            ->filterCustomer($product->customer_id)
            ->where(function ($q) use ($product) {
                $q->whereNull('category_id');
                $q->orWhere('category_id', $product->category_id);
            })
            ->pluck('name', 'id')
            ->toArray();

        $allocations = App\Models\Logistic\ShippingOrderLine::with('shipping_order', 'location')
            ->whereHas('shipping_order', function ($q) {
                $q->whereIn('status_id', [App\Models\Logistic\ShippingOrder::STATUS_PENDING, App\Models\Logistic\ShippingOrder::STATUS_PROCESSING]);
            })
            ->where('product_id', $product->id)
            ->get();

        $data = compact(
            'product',
            'operators',
            'locations',
            'globalStock',
            'brands',
            'models',
            'families',
            'categories',
            'subcategories',
            'allocations'
        );

        return $this->setContent('admin.logistic.products.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //    public function edit($id) {
    //    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $input = $request->all();
        $input['is_active']   = $request->get('is_active', false);
        $input['is_obsolete'] = $request->get('is_obsolete', false);
        $input['need_validation'] = $request->get('need_validation', 0);

        $product = Product::filterSource()
            ->findOrNew($id);

        if ($product->validate($input)) {
            $product->fill($input);
            $product->source = config('app.source');

            //delete image
            if (@$input['delete_photo'] && !empty($product->filepath)) {
                ProductImage::where('product_id', $product->id)
                    ->where('filepath', $product->filepath)
                    ->delete();

                Croppa::delete($product->filepath);
                $product->filepath = null;
                $product->filename = null;
                $product->filehost = null;
            }

            //upload image
            if ($request->hasFile('image')) {


                if ($product->exists && !empty($product->filepath) && File::exists(public_path() . '/' . $product->filepath)) {
                    ProductImage::where('product_id', $product->id)
                        ->where('filepath', $product->filepath)
                        ->delete();

                    Croppa::delete($product->filepath);
                }

                if (!$product->upload($request->file('image'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem do produto.');
                } else {
                    ProductImage::where('product_id', $product->id)->update(['is_cover' => 0]);
                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->filehost   = $product->filehost;
                    $productImage->filepath   = $product->filepath;
                    $productImage->filename   = $product->filename;
                    $productImage->is_cover   = 1;
                    $productImage->save();
                }
            } else {
                $product->save();
            }


            if (!empty(@$input['location'])) {
                foreach ($input['location'] as $key => $locationId) {

                    $availableQty = (int) @$input['qty'][$key];
                    $allocatedQty = (int) @$input['allocated'][$key];

                    if (!empty($locationId) && !empty($availableQty)) {

                        $productLocation = ProductLocation::firstOrNew([
                            'location_id' => $locationId,
                            'product_id'  => $product->id
                        ]);

                        if ($productLocation->exists) {
                            $productLocation->stock          += $availableQty + $allocatedQty;
                            $productLocation->stock_available += $availableQty;
                            $productLocation->stock_allocated += $allocatedQty;
                        } else {
                            $productLocation->product_id      = $product->id;
                            $productLocation->location_id     = $locationId;
                            $productLocation->stock           = ($availableQty + $allocatedQty);
                            $productLocation->stock_available = $availableQty;
                            $productLocation->stock_allocated = $allocatedQty;
                            $productLocation->setBarcode();
                        }

                        $productLocation->save();

                        $productLocation->location->status = 'filled';
                        $productLocation->location->save();

                        //Save History
                        $history = new ProductHistory();
                        $history->product_id     = $product->id;
                        $history->action         = 'add';
                        $history->source_id      = null;
                        $history->destination_id = $locationId;
                        $history->qty            = $availableQty;
                        $history->save();
                    }
                }

                $product->updateStockTotal();
            }

            if ($request->ajax()) {
                return response()->json([
                    'result'   => true,
                    'feedback' => 'Dados gravados com sucesso.'
                ]);
            }
            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        if ($request->ajax()) {
            return response()->json([
                'result'   => false,
                'feedback' => $product->errors()->first()
            ]);
        }
        return Redirect::back()->with('error', $product->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $product = Product::with('locations')
            ->filterSource()
            ->whereId($id)
            ->firstOrFail();

        try {
            ProductLocation::whereProductId($product->id)->delete();
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o produto.');
        }

        $result = $product->delete();

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
    public function massDestroy(Request $request)
    {

        $ids = explode(',', $request->ids);

        $result = Product::filterSource()
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
    public function datatable(Request $request)
    {

        $data = Product::filterSource()
            ->with('customer', 'locations')
            ->select();

        //filter unity
        $value = $request->images;
        if ($request->has('images')) {
            if ($value) {
                $data = $data->has('images');
            } else {
                $data = $data->has('images', '=', 0);
            }
        }

        //filter lote
        $value = $request->lote;
        if ($request->has('lote')) {
            if ($value) {
                $data = $data->where('lote', '<>', '');
            } else {
                $data = $data->where(function ($q) {
                    $q->whereNull('lote');
                    $q->orWhere('lote', '');
                });
            }
        }

        //filter serial_no
        $value = $request->serial_no;
        if ($request->has('serial_no')) {
            if ($value) {
                $data = $data->where('serial_no', '<>', '');
            } else {
                $data = $data->where(function ($q) {
                    $q->whereNull('serial_no');
                    $q->orWhere('serial_no', '');
                });
            }
        }

        //filter unity
        $value = $request->unity;
        if ($request->has('unity')) {
            $data = $data->where('unity', $value);
        }

        //filter customer
        $value = $request->customer;
        if ($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter warehouse
        $value = $request->warehouse;
        if ($request->has('warehouse')) {
            $data = $data->whereHas('locations', function ($q) use ($value) {
                $q->whereIn('warehouse_id', $value);
            });
        }

        //filter location
        $value = $request->location;
        if ($request->has('location')) {
            $data = $data->whereHas('locations', function ($q) use ($value) {
                $q->whereIn('location_id', $value);
            });
        }

        //filter brand
        $value = $request->brand;
        if ($request->has('brand')) {
            $data = $data->whereIn('brand_id', $value);
        }

        //filter group
        $value = $request->model;
        if ($request->has('model')) {
            $data = $data->whereIn('model_id', $value);
        }

        //filter group
        $value = $request->family;
        if ($request->has('family')) {
            $data = $data->whereIn('family_id', $value);
        }

        //filter category
        $value = $request->category;
        if ($request->has('category')) {
            $data = $data->whereIn('category_id', $value);
        }

        //filter subcategory
        $value = $request->subcategory;
        if ($request->has('subcategory')) {
            $data = $data->where('subcategory_id', $value);
        }

        // //filter brand
        // $value = $request->brand_id;
        // if ($request->has('brand')) {
        //     $data = $data->where('brand_id', $value);
        // }

        //filter status
        $value = $request->status;
        if ($request->has('status')) {
            if ($value == 'outstock') {
                $data = $data->where('stock_total', '<=', '0');
            } elseif ($value == 'lowstock') {
                $data = $data->where(function ($q) {
                    $q->where('stock_total', '>', '0');
                    $q->whereRaw('stock_min >= stock_total');
                });
            } elseif ($value == 'available') {
                $data = $data->where('stock_total', '>', '0');
            } elseif ($value == 'blocked') {
                $data = $data->where('stock_status', 'blocked');
            }
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {

            $dtMax = $dtMin;

            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            if ($request->has('date_unity') && !empty($request->has('date_unity'))) { //filter by shipment status date
                $dtMin = $dtMin . ' 00:00:00';
                $dtMax = $dtMax . ' 23:59:59';
                $unity = $request->get('date_unity');

                if ($unity == '3') { //data validade
                    $data->whereBetween('expiration_date', [$dtMin, $dtMax]);
                } elseif ($unity == '4') { // data criação
                    $data->whereBetween('created_at', [$dtMin, $dtMax]);
                }
            } else { //filter by last update
                $data = $data->whereBetween('last_update', [$dtMin, $dtMax]);
            }
        }

        return Datatables::of($data)
            ->add_column('photo', function ($row) {
                return view('admin.logistic.products.datatables.photo', compact('row'))->render();
            })
            ->edit_column('sku', function ($row) {
                return view('admin.logistic.products.datatables.sku', compact('row'))->render();
            })
            ->add_column('pallets', function ($row) {
                return $row->available_pallets;
            })
            ->edit_column('price', function ($row) {
                return view('admin.logistic.products.datatables.price', compact('row'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.logistic.products.datatables.name', compact('row'))->render();
            })
            ->edit_column('lote', function ($row) {
                return view('admin.logistic.products.datatables.lote', compact('row'))->render();
            })
            ->edit_column('stock_total', function ($row) {
                return view('admin.logistic.products.datatables.stock', compact('row'))->render();
            })
            ->edit_column('locations', function ($row) {
                return view('admin.logistic.products.datatables.locations', compact('row'))->render();
            })
            ->edit_column('last_update', function ($row) {
                return view('admin.logistic.products.datatables.last_update', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.logistic.products.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableLocations(Request $request)
    {

        $data = Location::with('warehouse', 'products')
            ->filterSource()
            ->select();

        //filter status
        $value = $request->status;
        if ($request->has('status')) {
            $data = $data->where('status', $value);
        }

        return Datatables::of($data)
            ->edit_column('warehouse.name', function ($row) {
                return view('admin.logistic.products.datatables.locations_view.name', compact('row'))->render();
            })
            ->edit_column('code', function ($row) {
                return view('admin.logistic.products.datatables.locations_view.code', compact('row'))->render();
            })
            ->edit_column('products', function ($row) {
                return view('admin.logistic.products.datatables.locations_view.products', compact('row'))->render();
            })
            ->add_column('stock', function ($row) {
                return view('admin.logistic.products.datatables.locations_view.stock', compact('row'))->render();
            })
            ->edit_column('status', function ($row) {
                return view('admin.logistic.products.datatables.locations_view.status', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.logistic.locations.datatables.locations.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableHistory(Request $request, $productId)
    {

        $data = ProductHistory::with('source', 'destination', 'user')
            ->whereProductId($productId)
            ->select();

        //filter action
        $value = $request->action;
        if ($request->has('action')) {
            $data = $data->where('action', $value);
        }

        //filter source
        $value = $request->source;
        if ($request->has('source')) {
            $data = $data->where('source_id', $value);
        }

        //filter destination
        $value = $request->destination;
        if ($request->has('destination')) {
            $data = $data->where('destination_id', $value);
        }

        //filter user
        $value = $request->operator;
        if ($request->has('operator')) {
            $data = $data->where('user_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('id', function ($row) {
                return view('admin.logistic.products.datatables.history.action', compact('row'))->render();
            })
            ->edit_column('document', function ($row) {
                return view('admin.logistic.products.datatables.history.document', compact('row'))->render();
            })
            ->edit_column('qty', function ($row) {
                return '<div class="text-center">' . $row->qty . '</div>';
            })
            ->edit_column('source.name', function ($row) {
                return '<div class="text-center">' . @$row->source->code . '</div>';
            })
            ->edit_column('destination.name', function ($row) {
                return '<div class="text-center">' . @$row->destination->code . '</div>';
            })
            ->edit_column('user.name', function ($row) {
                return @$row->user->name;
            })
            ->make(true);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableSerials(Request $request, $productId)
    {

        $product = Product::find($productId);

        $data = Product::filterSource()
            ->with('locations')
            ->where('customer_id', $product->customer_id)
            ->where('sku', $product->sku)
            ->select();

        //filter location
        $value = $request->location;
        if ($request->has('location')) {
            $data = $data->whereHas('locations', function ($q) use ($value) {
                $q->whereIn('location_id', $value);
            });
        }

        //filter status
        $value = $request->status;
        if ($request->has('status')) {
            if ($value == 'outstock') {
                $data = $data->where('stock_total', '<=', '0');
            } elseif ($value == 'lowstock') {
                $data = $data->where(DB::raw('stock_min >= stock_total'));
            } else {
                $data = $data->where('stock_status', $value);
            }
        }

        return Datatables::of($data)
            ->edit_column('sku', function ($row) {
                return view('admin.logistic.products.datatables.sku', compact('row'))->render();
            })
            ->edit_column('lote', function ($row) {
                return view('admin.logistic.products.datatables.lote', compact('row'))->render();
            })
            ->edit_column('stock_total', function ($row) {
                return view('admin.logistic.products.datatables.stock', compact('row'))->render();
            })
            ->edit_column('locations', function ($row) {
                return view('admin.logistic.products.datatables.locations', compact('row'))->render();
            })
            ->edit_column('last_update', function ($row) {
                return view('admin.logistic.products.datatables.last_update', compact('row'))->render();
            })
            /*->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })*/
            ->add_column('actions', function ($row) {
                return view('admin.logistic.products.datatables.actions', compact('row'))->render();
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
            $dbname = env('DB_DATABASE_LOGISTIC');
            $customers = Customer::filterAgencies()
                //->whereRaw('exists (select * from '.$dbname.'.products where '.$dbname.'.products.customer_id = customers.id and '.$dbname.'.products.deleted_at is null)')
                ->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->isDepartment(false)
                ->get(['name', 'code', 'id']);

            if (!$customers->isEmpty()) {
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
    public function searchProduct(Request $request)
    {

        $customer = trim($request->get('customer'));
        $search   = trim($request->get('query'));
        $search   = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $products = Product::filterSource()
                ->where('customer_id', $customer)
                ->where(function ($q) use ($search) {
                    $q->where('sku', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('barcode', 'LIKE', $search);
                })
                ->take(10)
                ->get(['name', 'barcode', 'sku', 'id']);

            if ($products) {

                $results = array();
                foreach ($products as $product) {
                    $results[] = ['data' => $product->id, 'value' => $product->name, 'customerRef' => $product->sku, 'barcode' => $product->barcode];
                }
            } else {
                $results = ['Nenhum produto encontrado.'];
            }
        } catch (\Exception $e) {
            $results = ['Erro interno ao processar o pedido.'];
        }

        $results = [
            'suggestions' => $results
        ];

        return Response::json($results);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchProductSelect2(Request $request)
    {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {
            $products = Product::filterSource()
                ->where(function ($q) use ($search) {
                    $q->where('barcode', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('sku', 'LIKE', $search)
                        ->orWhere('serial_no', 'LIKE', $search)
                        ->orWhere('lote', 'LIKE', $search);
                })
                ->get(['name', 'id', 'sku']);

            if (!$products->isEmpty()) {
                $results = array();
                foreach ($products as $product) {
                    $results[] = array('id' => $product->id, 'text' => $product->sku . ' - ' . str_limit($product->name, 40));
                }
            } else {
                $results = [['id' => '', 'text' => 'Nenhum produto encontrado.']];
            }
        } catch (\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }

    /**
     * Edit print of labels
     *
     * @return \Illuminate\Http\Response
     */
    public function editLabels($productId)
    {

        $product = Product::findOrFail($productId);

        $locations = ProductLocation::with('location.warehouse')
            ->where('product_id', $productId)
            ->get();

        return view('admin.logistic.products.modals.print_labels', compact('product', 'locations'))->render();
    }

    /**
     * Print product labels
     *
     * @return \Illuminate\Http\Response
     */
    public function printLabels(Request $request, $productId)
    {

        if ($request->get('type')) {
            return Product::printLabels($productId);
        }

        $printQty   = $request->get('qty', 1);
        $barcodes   = array_keys($printQty);

        return Product::printLabelsLocations($productId, $barcodes, $printQty);
    }

    /**
     * Sync stocks
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync()
    {

        try {

            Artisan::call('sync:activos24');

            return response()->json([
                'result'   => true,
                'feedback' => 'Artigos sincronizados com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result'   => false,
                'feedback' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request)
    {

        Brand::flushCache(Brand::CACHE_TAG);

        try {
            Brand::setNewOrder($request->ids);
            $response = [
                'result'  => true,
                'message' => 'Ordenação gravada com sucesso.',
            ];
        } catch (\Exception $e) {
            $response = [
                'result'  => false,
                'message' => 'Erro ao gravar ordenação. ' . $e->getMessage(),
            ];
        }

        return Response::json($response);
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSelect2List(Request $request, $type)
    {

        if ($type == 'models') {
            $response = $this->getModelsList($request);
        } elseif ($type == 'categories') {
            $response = $this->getCategoriesList($request);
        } elseif ($type == 'subcategories') {
            $response = $this->getSubcategoriesList($request);
        }

        return Response::json($response);
    }

    /**
     * Return brands list
     *
     * @param Request $request
     */
    public function getModelsList(Request $request)
    {

        $items = Model::filterSource()
            ->ordered()
            ->where(function ($q) use ($request) {
                $q->whereNull('customer_id');
                $q->orWhere('customer_id', $request->customer);
            })
            ->where(function ($q) use ($request) {
                $q->whereNull('brand_id');
                $q->orWhere('brand_id', $request->parent_id);
            })
            ->pluck('name', 'id')
            ->toArray();

        return [
            'html' => Form::select('model_id', ['' => ''] + $items, null, ['class' => 'form-control select2', 'data-placeholder' => ''])->toHtml()
        ];
    }

    /**
     * Return brands list
     *
     * @param Request $request
     */
    public function getCategoriesList(Request $request)
    {

        $items = Category::filterSource()
            ->ordered()
            ->where(function ($q) use ($request) {
                $q->whereNull('customer_id');
                $q->orWhere('customer_id', $request->customer);
            })
            ->where(function ($q) use ($request) {
                $q->whereNull('family_id');
                $q->orWhere('family_id', $request->parent_id);
            })
            ->pluck('name', 'id')
            ->toArray();

        return [
            'html' => Form::select('category_id', ['' => ''] + $items, null, ['class' => 'form-control select2', 'data-placeholder' => '', 'data-child' => 'subcategories'])->toHtml()
        ];
    }

    /**
     * Return brands list
     *
     * @param Request $request
     */
    public function getSubcategoriesList(Request $request)
    {

        $items = SubCategory::filterSource()
            ->ordered()
            ->where(function ($q) use ($request) {
                $q->whereNull('customer_id');
                $q->orWhere('customer_id', $request->customer);
            })
            ->where(function ($q) use ($request) {
                $q->whereNull('category_id');
                $q->orWhere('category_id', $request->parent_id);
            })
            ->pluck('name', 'id')
            ->toArray();

        return [
            'html' => Form::select('subcategory_id', ['' => ''] + $items, null, ['class' => 'form-control select2', 'data-placeholder' => ''])->toHtml()
        ];
    }

    /**
     * Edit stock adjustment
     *
     * @param Request $request
     * @return string
     * @throws \Throwable
     */
    public function editAdjustment(Request $request)
    {

        $barcode   = trim($request->barcode);;
        $barcode   = empty($barcode) ? '##########' : $barcode; //para impedir qd vazio

        if ($request->id) {
            $barcode = trim($request->id);
        }

        $locations = null;
        $product   = new Product();

        $products = Product::with('locations', 'warehouse')
            ->where(function ($q) use ($barcode) {
                $q->where(function ($q) use ($barcode) {
                    $q->where('id', $barcode);
                    $q->orWhere('sku', $barcode);
                    $q->orWhere('barcode', $barcode);
                    $q->orWhere('lote', $barcode);
                    $q->orWhere('serial_no', $barcode);
                })
                    ->orWhereHas('product_location', function ($q) use ($barcode) {
                        $q->where('barcode', $barcode);
                    });
            })
            ->get();

        if ($products->count() == 1) {
            $product   = $products->first();
            $locations = ProductLocation::where('product_id', $product->id)->get();

            //verifica se é o codigo de uma localização em especifico
            $barcodeLocation = $product->locations->filter(function ($item) use ($barcode) {
                return $item->pivot->barcode == $barcode;
            })->first();

            if ($barcodeLocation) {
                $product->locations = [$barcodeLocation];
            }
        }

        if ($request->has('barcode')) {
            return response()->json([
                'result' => true,
                'html'   => view('admin.logistic.products.modals.edit_adjustment', compact('product', 'locations', 'products'))->render()
            ]);
        }

        return view('admin.logistic.products.modals.edit_adjustment', compact('product', 'locations', 'products'))->render();
    }

    /**
     * Store stock adjustment
     * @param Request $request
     * @return string
     * @throws \Throwable
     */
    public function storeAdjustment(Request $request)
    {

        $input = $request->get('qty');

        $barcodes  = array_keys($input);
        $locations = ProductLocation::whereIn('barcode', $barcodes)->get();
        $oldQtys   = $locations->pluck('stock', 'barcode')->toArray();
        $products  = $locations->pluck('product_id', 'barcode')->toArray();
        $locations = $locations->pluck('location_id', 'barcode')->toArray();

        foreach ($input as $barcode => $qty) {
            if ($qty != '') {

                $oldQty = @$oldQtys[$barcode];

                //atualiza localização
                $productLocation = ProductLocation::firstOrNew([
                    'location_id' => @$locations[$barcode],
                    'product_id'  => @$products[$barcode]
                ]);
                $productLocation->updateStock($qty);

                //regista historico
                $history = new ProductHistory();
                $history->action     = 'adjustment';
                $history->product_id = $products[$barcode];
                $history->source_id  = $locations[$barcode];
                $history->qty        = $qty - $oldQty;
                $history->obs        = 'Ajuste ' . $oldQty . ' para ' . $qty;
                $history->user_id    = Auth::user()->id;
                $history->save();
            }
        }

        //update products total stocks
        $products = Product::filterSource()
            ->whereIn('id', array_values($products))
            ->get();

        foreach ($products as $product) {
            $product->updateStockTotal();
        }

        return Redirect::back()->with('success', 'Ajuste de stock concluído com sucesso.');
    }

    /**
     * Edit stock move location
     *
     * @param Request $request
     * @return string
     * @throws \Throwable
     */
    public function editMoveLocation(Request $request)
    {

        $barcode   = trim($request->barcode);
        $barcode   = empty($barcode) ? '##########' : $barcode; //para impedir qd vazio

        if ($request->id) {
            $barcode = trim($request->id);
        }

        $locations       = null;
        $sourceLocation  = null;
        $sourceLocations = '';
        $selectedSourceLocation = null;

        $product = Product::with('locations')
            ->where(function ($q) use ($barcode) {
                $q->where('id', $barcode);
                $q->orWhere('sku', $barcode);
                $q->orWhere('barcode', $barcode);
                $q->orWhere('lote', $barcode);
                $q->orWhere('serial_no', $barcode);
            })
            ->orWhereHas('product_location', function ($q) use ($barcode) {
                $q->where('barcode', $barcode);
            })
            ->where('stock_total', '>', 0)
            ->first();

        if (@$product->locations) {
            $sourceLocations = $product->locations->pluck('code', 'id')->toArray();
            $sourceLocations = implode(',', $sourceLocations);

            //verifica se é o codigo de uma localização em especifico
            $barcodeLocation = $product->locations->filter(function ($item) use ($barcode) {
                return $item->pivot->barcode == $barcode;
            })->first();

            if ($barcodeLocation) {
                $selectedSourceLocation = $barcodeLocation;
            }
        }

        if ($request->has('barcode')) {
            return response()->json([
                'result' => true,
                'html'   => view('admin.logistic.products.modals.move_stock', compact('product', 'sourceLocations', 'selectedSourceLocation'))->render()
            ]);
        }

        return view('admin.logistic.products.modals.move_stock', compact('product', 'sourceLocations', 'selectedSourceLocation'))->render();
    }

    /**
     * Store stock move location
     * @param Request $request
     * @return string
     * @throws \Throwable
     */
    public function storeMoveLocation(Request $request)
    {

        $input = $request->all();

        $productId = $input['product_id'];

        if (empty($input['move_source_id']) || empty($input['move_destination_id'])) {
            $result = [
                'result'   => false,
                'feedback' => 'A localização de origem e destino é obrigatória.'
            ];
        } else if ($input['move_source_id'] == $input['move_destination_id']) {
            $result = [
                'result'   => false,
                'feedback' => 'A localização de origem e de destino não podem ser a mesma.'
            ];
        } else if ($input['qty'] <= 0) {
            $result = [
                'result'   => false,
                'feedback' => 'A quantidade a mover deve ser superior a 0'
            ];
        } else {


            $product   = Product::filterSource()
                ->whereId($productId)
                ->first();

            $destLocation = Location::filterSource()
                ->where('id', $input['move_destination_id'])
                ->first();

            $sourceLocation = ProductLocation::where('product_id', $productId)
                ->where('location_id', $input['move_source_id'])
                ->firstOrFail();

            $newLocation = ProductLocation::firstOrNew([
                'location_id' => $destLocation->id,
                'product_id'  => $productId
            ]);

            $newLocation->product_id  = $productId;
            $newLocation->location_id = $destLocation->id;
            $newLocation->stock       = $newLocation->stock + @$input['qty'];
            $newLocation->setBarcode();

            //change location to filled
            $newLocation->location->status = 'filled';
            $newLocation->location->save();

            //Save History
            $history = new ProductHistory();
            $history->product_id     = $product->id;
            $history->action         = 'transfer';
            $history->source_id      = $sourceLocation->location_id;
            $history->destination_id = $newLocation->location_id;
            $history->qty            = @$input['qty'];
            $history->save();

            //remove stock from current location
            $sourceLocation->updateStock($sourceLocation->stock - @$input['qty']);

            $product->updateStockTotal();

            $result = [
                'result'   => true,
                'feedback' => 'Localização movida com sucesso.'
            ];
        }

        return response()->json($result);
    }

    /**
     * Get location
     * @param Request $request
     * @return string
     * @throws \Throwable
     */
    public function getLocation(Request $request)
    {

        $barcode = trim($request->get('barcode'));

        $location = Location::filterSource()
            ->where('barcode', $barcode)
            ->orWhere('code', $barcode)
            ->first();

        if ($location) {
            $result = [
                'result'   => true,
                'id'       => $location->id,
                'barcode'  => $location->barcode,
                'code'     => $location->code,
            ];
        } else {

            $productLocation = ProductLocation::with('location')
                ->where('barcode', $barcode)
                ->first();

            if ($productLocation) {
                $result = [
                    'result'   => true,
                    'id'       => @$productLocation->location->id,
                    'barcode'  => @$productLocation->location->barcode,
                    'code'     => @$productLocation->location->code,
                ];
            } else {
                $result = [
                    'result'   => false,
                    'feedback' => 'Localização não encontrada.'
                ];
            }
        }


        return response()->json($result);
    }
}
