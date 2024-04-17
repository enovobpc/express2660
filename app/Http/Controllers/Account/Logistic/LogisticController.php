<?php

namespace App\Http\Controllers\Account\Logistic;

use App\Models\Logistic\Product;
use App\Models\Logistic\ProductHistory;
use App\Models\Logistic\CartProduct;
use App\Models\Logistic\Brand;
use App\Models\Logistic\Model;
use App\Models\Logistic\Family;
use App\Models\Logistic\Category;
use App\Models\Logistic\SubCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;
use App\Models\Shipment;
use App\Models\Customer;
use App\Models\Agency;
use App\Models\Service;
use Carbon\Carbon;
use DB, View, Excel, Mail;
use Illuminate\Support\Facades\Response;

class LogisticController extends \App\Http\Controllers\Controller
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
    protected $sidebarActiveOption = 'logistic';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Customer billing index controller
     *
     * @return type
     */
    public function index(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        $categories = Category::filterSource()
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $subcategories = SubCategory::filterSource()
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $brands = Brand::filterSource()
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $models = Model::filterSource()
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $families = Family::filterSource()
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'categories',
            'subcategories',
            'families',
            'brands',
            'models'
        );

        return $this->setContent('account.logistic.index', $data);
    }

    /**
     * Show the form for consult the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $customer = Auth::guard('customer')->user();

        $product = Product::filterSource()
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->findOrNew($id);

        return view('account.logistic.show', compact('product'))->render();
    }

    /**
     * Show the details of the product
     * 
     * @param int $id 
     * @return string
     */
    public function details($id)
    {
        $customer = Auth::guard('customer')->user();

        $product = Product::filterSource()
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->findOrNew($id);

        return view('account.logistic.details', compact('product'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $customer = Auth::guard('customer')->user();

        $product = Product::filterSource()
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->findOrNew($id);

        $formOptions = array('route' => array('account.logistic.products.update', $product->id), 'method' => 'PUT');

        return view('account.logistic.edit', compact('product', 'formOptions'))->render();
    }

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

        $customer = Auth::guard('customer')->user();

        $product = Product::filterSource()
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->findOrNew($id);


        if ($product->validate($input)) {
            $product->customer_id = $customer->id;
            $product->fill($input);
            $product->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $product->errors()->first());
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        $data = Product::filterSource()
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->select();

        if (@$customer->settings['logistic_stock_only_available'] || @$customer->parent_customer->settings['logistic_stock_only_available']) {
            $data = $data->where('stock_total', '>', '0');
        }

        //filter category
        $value = $request->category;
        if ($request->has('category')) {
            $data = $data->where('category_id', $value);
        }

        //filter subcategory
        $value = $request->subcategory;
        if ($request->has('subcategory')) {
            $data = $data->where('subcategory_id', $value);
        }

        //filter family
        $value = $request->family;
        if ($request->has('family')) {
            $data = $data->where('family_id', $value);
        }

        //filter unity
        $value = $request->brand;
        if ($request->has('unity')) {
            $data = $data->where('unity', $value);
        }

        //filter brand
        $value = $request->brand;
        if ($request->has('brand')) {
            $data = $data->where('brand_id', $value);
        }

        //filter model
        $value = $request->model;
        if ($request->has('model')) {
            $data = $data->where('model_id', $value);
        }

        //filter stock
        $value = $request->stock;
        if ($request->has('stock')) {
            if ($value == '0') {
                $data = $data->where('stock_total', '<=', 0);
            } elseif ($value == '1') {
                $data = $data->where('stock_total', '>', 0);
            } elseif ($value == '2') {
                $data = $data->where('stock_total', '>', 0)
                    ->whereRaw('stock_total <= stock_min');
            } elseif ($value == '3') {
                $data = $data->where('stock_status', 'blocked');
            }
        }

        //filter unity
        $value = $request->unity;
        if ($request->has('unity')) {
            $data = $data->where('unity', $value);
        }

        $datatables = Datatables::of($data)
            ->edit_column('sku', function ($row) {
                return view('account.logistic.datatables.sku', compact('row'))->render();
            })
            ->add_column('image', function ($row) {
                return view('account.logistic.datatables.image', compact('row'))->render();
            })
            ->edit_column('unity', function ($row) {
                return view('account.logistic.datatables.unity', compact('row'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('account.logistic.datatables.name', compact('row'))->render();
            })
            ->edit_column('stock_status', function ($row) {
                return view('account.logistic.datatables.status', compact('row'))->render();
            })
            ->edit_column('stock_total', function ($row) {
                return view('account.logistic.datatables.stock', compact('row'))->render();
            })
            ->edit_column('stock_min', function ($row) {
                return $row->stock_min ? $row->stock_min : '0';
            })
            ->add_column('cart', function ($row) {
                return view('account.logistic.datatables.cart', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('account.logistic.datatables.actions', compact('row'))->render();
            });

        if (config('app.source') == 'activos24') {
            $datatables->add_column('subcategory', function ($row) {
                return $row->subcategory ? $row->subcategory->name : 'N/A';
            });
        }

        if (config('app.source') == 'corridadotempo') {
            $datatables->add_column('location', function ($row) {
                return view('account.logistic.datatables.locations', compact('row'))->render();
            });
        }

        return $datatables->make(true);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableHistory(Request $request, $productId)
    {

        $customer = Auth::guard('customer')->user();

        $data = ProductHistory::with('source', 'destination', 'user')
            ->whereHas('product', function ($q) use ($customer) {
                $q->where(function ($q) use ($customer) {
                    $q->where('customer_id', $customer->id);
                    $q->orWhere('customer_id', $customer->customer_id);
                });
            })
            ->whereProductId($productId)
            ->select();

        //filter action
        $value = $request->action;
        if ($request->has('action')) {
            $data = $data->where('action', $value);
        }

        return Datatables::of($data)
            ->edit_column('id', function ($row) {
                return view('admin.logistic.products.datatables.history.action', compact('row'))->render();
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
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $ids = $request->id;

        $header = [
            'SKU',
            'Designação',
            'Nº Série',
            'Lote',
            'Validade',
            'Marca',
            'Comprimento',
            'Largura',
            'Altura',
            'Peso',
            'Stock',
            'Stock Min',
            'Unidade',
            'Marca',
            'Modelo',
        ];

        if (config('app.source') == 'activos24') {
            $header = array_merge($header, [
                'Categoria',
                'Subcategoria',
                'Referência'
            ]);
        }

        $header = array_merge($header, [
            'Estado',
            'Observações'
        ]);

        try {
            $data = Product::filterSource()
                ->with('brand', 'brand_model', 'category', 'subcategory')
                ->where(function ($q) use ($customer) {
                    $q->where('customer_id', $customer->id);
                    $q->orWhere('customer_id', $customer->customer_id);
                });

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            } else {
                //filter active
                /*$value = $request->active;
                if ($request->has('active')) {
                    $data = $data->where('active', $value);
                }*/
            }

            $data = $data->get();

            Excel::create('Listagem de Produtos', function ($file) use ($data, $header) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    $sheet->setColumnFormat(array(
                        'A' => '@', //SKU
                        'C' => '@', //Serial
                        'D' => '@', //Lote
                    ));


                    foreach ($data as $product) {

                        $rowData = [
                            $product->sku,
                            $product->name,
                            $product->serial_no,
                            $product->lote,
                            $product->expiration_date,
                            $product->brand_name,
                            $product->width,
                            $product->length,
                            $product->height,
                            $product->weight,
                            $product->stock_total,
                            $product->stock_min,
                            trans('admin/global.measure-units.' . ($product->unity ?: 'un')),
                            @$product->brand->name,
                            @$product->brand_model->name,
                        ];

                        if (config('app.source') == 'activos24') {
                            $rowData = array_merge($rowData, [
                                $product->category->name ?? '',
                                $product->subcategory->name ?? '',
                                $product->customer_ref
                            ]);
                        }

                        $rowData = array_merge($rowData, [
                            trans('admin/logistic.products.status.' . $product->stock_status),
                            $product->obs
                        ]);

                        $sheet->appendRow($rowData);
                    }
                });
            })->export('xls');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }

    /**
     * Show shopping cart
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showCart()
    {

        $customer = Auth::guard('customer')->user();

        $products = CartProduct::with('product')
            ->where('customer_id', $customer->id)->where(function ($q) {
                $q->where('reference', NULL)->orWhere('closed', 0);
            })
            ->get();

        return view('account.logistic.cart', compact('products'))->render();
    }

    /**
     * Add logistic item to cart
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addCart(Request $request, $id)
    {
        $customer = Auth::guard('customer')->user();


        $product = Product::filterSource()
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->findOrNew($id);

        $cart = CartProduct::where('customer_id', $customer->id)->where(function ($q) {
            $q->where('reference', NULL)->orWhere('closed', 0);
        })
            ->firstOrNew([
                'customer_id' => $customer->id,
                'product_id' => $id
            ]);

        $cart->customer_id = $customer->id;
        $cart->product_id   = $id;
        $cart->qty = $cart->qty + 1;

        $cart->save();

        $totalItems = CartProduct::where('customer_id', $customer->id)->where(function ($q) {
            $q->where('reference', NULL)->orWhere('closed', 0);
        })->sum('qty');

        if ($product->exists && $product->stock_available > 0.00) {
            $result = [
                'result' => true,
                'cart_total' => $totalItems,
                'feedback' => 'Adicionado com sucesso.'
            ];
        } else {
            $result = [
                'result' => false,
                'feedback' => 'Artigo indisponível.'
            ];
        }

        return response()->json($result);
    }

    /**
     * Destroy shopping cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyCart(Request $request)
    {

        $customer = Auth::guard('customer')->user();

        CartProduct::where('customer_id', $customer->id)->where(function ($q) {
            $q->where('reference', NULL)->orWhere('closed', 0);
        })->forceDelete();



        $result = [
            'result' => false,
            'feedback' => 'Pedido anulado com sucesso..'
        ];
        return response()->json($result);
    }

    /**
     * Conclude shopping cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function concludeCart(Request $request)
    {

        $request = $request->all();
        $customer = Auth::guard('customer')->user();

        $products = CartProduct::with('product')
            ->where('customer_id', $customer->id)->where(function ($q) {
                $q->where('reference', NULL)->orWhere('closed', 0);
            })
            ->get();

        $reference = str_random(5);
        $currentTime = Carbon::now();

        $referenceCurrentTime = $currentTime->format('is');
        $reference .= $referenceCurrentTime;
        $reference = strtoupper($reference);

        //verificar se algum dos produtos precisa de validação
        $need_validate = false;
        foreach ($products as $product) {
            if ($product->product->need_validation) {
                $need_validate = true;
                break;
            }
        }

        CartProduct::where('customer_id', $customer->id)->where(function ($q) {
            $q->where('reference', NULL)->orWhere('closed', 0);
        })
            ->update([
                'reference'         => $reference,
                'status'            => 'pending',
                'closed'            =>  1,

                'origin_name'               => $request['origin_name'] ?? '',
                'origin_address'            => $request['origin_address'] ?? '',
                'origin_zip_code'           => $request['origin_zip_code'] ?? '',
                'origin_city'               => $request['origin_city'] ?? '',
                'origin_country'            => $request['origin_country'] ?? '',
                'origin_phone_number'       => $request['origin_phone_number'] ?? '',

                'destination_name'              => $request['destination_name'] ?? '',
                'destination_address'           => $request['destination_address'] ?? '',
                'destination_zip_code'          => $request['destination_zip_code'] ?? '',
                'destination_city'              => $request['destination_city'] ?? '',
                'destination_country'           => $request['destination_country'] ?? '',
                'destination_phone_number'      => $request['destination_phone_number'] ?? '',


                'obs'    => $request['obs'] ?? '',

                'submitted_by'      => $customer->id,
                'submitted_at'      => $currentTime->format('Y-m-d H:i')
            ]);


        //SEND EMAIL FOR ALL COMMERCIALS
        if (!empty($customer->customer_id)) {
            $mainCustomer = $customer->customer_id;
        } else {
            $mainCustomer = $customer->id;
        }

        $emailCommercials = Customer::where('customer_id', $mainCustomer)->where('is_commercial', 1)->pluck('contact_email')->toArray();;
        $emailCommercials = array_filter($emailCommercials);
        $emailCommercials = validateNotificationEmails($emailCommercials);

        foreach ($emailCommercials['valid'] as $email) {
            Mail::send('emails.logistic.cart', compact('products', 'customer'), function ($message)  use ($email) {
                $message->to($email);
                $message->subject('Novo pedido encomenda');
            });
        }



        return Redirect::back()->with('success', 'Pedido submetido com sucesso.');
    }

    public function searchProducts(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $search   = trim($request->get('query'));
        $search   = '%' . str_replace(' ', '%', $search) . '%';

        try {
            $products = Product::filterSource()
                ->where('customer_id', $customer->id)
                ->where(function ($q) use ($search) {
                    $q->where('sku', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search);
                })
                ->take(10)
                ->get(['id', 'name', 'sku']);

            if ($products) {
                $results = array();
                foreach ($products as $product) {
                    $results[] = [
                        'data'  => $product->id,
                        'value' => $product->name,
                        'sku'   => $product->sku,
                    ];
                }
            } else {
                $results = ['Nenhum produto encontrado.'];
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            $results = ['Erro interno ao processar o pedido.'];
        }

        $results = [
            'suggestions' => $results
        ];

        return Response::json($results);
    }


    /**
     * Get modal to set locations
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setLocations(Request $request)
    {
        $action = 'Dados de Entrega';
        $cartProduct = new CartProduct;
        $formOptions = array('route' => array('account.logistic.cart.conclude'), 'method' => 'POST');
        $data = compact(
            'action',
            'cartProduct',
            'formOptions'
        );

        return view('account.logistic.cart.partials.locations', $data)->render();
    }


    /**
     * Create Shipping by shopping cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createShipment(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $products = CartProduct::with('product')
            ->where('customer_id', $customer->id)->where(function ($q) {
                $q->where('reference', NULL)->orWhere('closed', 0);
            })
            ->get();

        $reference = str_random(5);
        $currentTime = Carbon::now();

        $referenceCurrentTime = $currentTime->format('is');
        $reference .= $referenceCurrentTime;
        $reference = strtoupper($reference);

        //verificar se algum dos produtos precisa de validação
        $need_validate = false;
        foreach ($products as $product) {
            if ($product->product->need_validation) {
                $need_validate = true;
                break;
            }
        }
        if (!$need_validate) {
            CartProduct::where('customer_id', $customer->id)->where(function ($q) {
                $q->where('reference', NULL)->orWhere('closed', 0);
            })
                ->update([
                    'reference'         => $reference,
                    // 'status'            => 'accept',
                    'submitted_by'      => $customer->id,
                    'submitted_at'      => $currentTime->format('Y-m-d H:i')
                ]);


            $cartProduct    = CartProduct::where('reference', $reference)->first();
            $customerCart   = Customer::where('id', $cartProduct->customer_id)->first();

            $products = CartProduct::with('product')
                ->with('product')
                ->where('reference', $reference)
                ->get();

            $mainCustomer = Customer::where('id', $customerCart->customer_id)->first();
            if (!isset($mainCustomer)) {
                $mainCustomer = $customer;
            }

            $agency = Agency::filterSource()->first();

            $service = Service::filterSource()->where('id', 1)->first();

            $shipment = new Shipment();

            $shipment->date                 = Carbon::today()->format('Y-m-d');
            $shipment->service_id           = @$service->id;
            $shipment->service              = @$service->display_code;
            $shipment->agency_id            = @$agency->id;
            $shipment->recipient_agency_id  = @$agency->id;
            $shipment->sender_agency_id     = @$agency->id;
            $shipment->customer_id          = $mainCustomer->id;
            $shipment->provider_id          = '1';
            $shipment->reference            = $reference;

            $shipment->sender_name          = $mainCustomer->name;
            $shipment->sender_address       = $mainCustomer->address;
            $shipment->sender_zip_code      = $mainCustomer->zip_code;
            $shipment->sender_city          = $mainCustomer->city;
            $shipment->sender_country       = $mainCustomer->country;
            $shipment->sender_phone         = $mainCustomer->phone;

            $shipment->recipient_name       = $customerCart->name;
            $shipment->recipient_address    = $customerCart->address;
            $shipment->recipient_zip_code   = $customerCart->zip_code;
            $shipment->recipient_city       = $customerCart->city;
            $shipment->recipient_country    = $customerCart->country;
            $shipment->recipient_phone      = $customerCart->phone;
            $shipment->recipient_email      = $customerCart->email;

            $qty                = [];
            $sku                = [];
            $serial_no          = [];
            $lote               = [];
            $stock              = [];
            $product            = [];
            $box_type           = [];
            $box_description    = [];
            $length             = [];
            $width              = [];
            $height             = [];
            $box_weight         = [];
            $fator_m3_row       = [];
            $totalWeight = 0;
            $count = 0;
            foreach ($products as $productInfo) {
                array_push($qty, $productInfo->qty);
                array_push($sku, $productInfo->product->sku ?? '');
                array_push($serial_no, $productInfo->product->serial_no ?? '');
                array_push($lote, $productInfo->product->lote ?? '');
                array_push($stock, $productInfo->product->stock_available ?? '');
                array_push($product, $productInfo->product->id ?? '');
                array_push($box_type, 'box');
                array_push($box_description, $productInfo->product->name ?? '');
                array_push($length, $productInfo->product->length ?? '');
                array_push($width, $productInfo->product->width ?? '');
                array_push($height, $productInfo->product->height ?? '');
                array_push($box_weight, $productInfo->product->weight ?? '');
                array_push($fator_m3_row, '');
                $totalWeight .= $productInfo->product->weight;
                $count++;
            }
            $shipment->volumes = $count;
            $shipment->weight = $totalWeight;
            $shipment->qty = $qty;
            $shipment->sku = $sku;
            $shipment->serial_no = $serial_no;
            $shipment->lote = $lote;
            $shipment->stock = $stock;
            $shipment->product = $product;
            $shipment->box_type = $box_type;
            $shipment->box_description = $box_description;
            $shipment->length = $length;
            $shipment->width = $width;
            $shipment->height = $height;
            $shipment->box_weight = $box_weight;
            $shipment->fator_m3_row = $fator_m3_row;

            $request = new Request([
                'shipment' => $shipment,
                'cart'     => true,
                'source'   => 'cart'
            ]);

            $controller  = new \App\Http\Controllers\Account\ShipmentsController;
            return $controller->create($request);
        }
    }
}
