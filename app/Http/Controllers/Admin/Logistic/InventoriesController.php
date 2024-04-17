<?php

namespace App\Http\Controllers\Admin\Logistic;

use App\Models\Customer;
use App\Models\Logistic\Inventory;
use App\Models\Logistic\InventoryLine;
use App\Models\Logistic\Location;
use App\Models\Logistic\Product;
use App\Models\Logistic\ProductLocation;
use App\Models\Logistic\Warehouse;
use App\Models\Logistic\ProductStockHistory;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Mpdf\Mpdf;
use Croppa, Auth, App, Response, Setting, DB;

class InventoriesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'logistic_inventories';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',logistic_inventories']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $warehouses = Warehouse::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $status = [
            '0' => 'Aberto',
            '1' => 'Fechado'
        ];

        $data = compact(
            'status',
            'warehouses'
        );

        return $this->setContent('admin.logistic.inventories.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $action = 'Novo Inventário';

        $inventory = new Inventory();

        $formOptions = array('route' => array('admin.logistic.inventories.store'), 'method' => 'POST', 'class' => 'form-inventory', 'autocomplete'=> 'nofill');

        $locations = Location::filterSource()
            ->pluck('code', 'id')
            ->toArray();

        $warehouses = Warehouse::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'inventory',
            'action',
            'formOptions',
            'locations',
            'warehouses'
        );

        return view('admin.logistic.inventories.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return $this->update($request, $request->get('id'));
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

        $inventory = Inventory::filterSource()
                        ->where('id', $id)
                        ->firstOrFail();

        $formOptions = array('route' => array('admin.logistic.inventories.update', $inventory->id), 'method' => 'PUT', 'class' => 'form-inventory');

        $action = 'Editar Inventário #'.$inventory->code;

        $locations = Location::filterSource()
            ->pluck('code', 'id')
            ->toArray();

        $warehouses = Warehouse::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'inventory',
            'formOptions',
            'action',
            'locations',
            'warehouses'
        );

        return view('admin.logistic.inventories.edit', $data)->render();
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

        $inventory = Inventory::filterSource()
                            ->findOrNew($id);

        if ($inventory->validate($input)) {
            $inventory->customer_id = @$input['customer_id'];
            $inventory->description = $input['description'];
            $inventory->date        = $input['date'];
            $inventory->user_id     = Auth::user()->id;
            $inventory->source      = config('app.source');

            if($request->get('conclude')) {
                $inventory->status_id = Inventory::STATUS_CONCLUDED;
            } else {
                $inventory->status_id = Inventory::STATUS_PROCESSING;
            }

            $inventory->setCode();

            $inventory->updateProductStocks();


            return response()->json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.'
            ]);
        }

        return response()->json([
            'result'   => false,
            'feedback' => $inventory->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $inventory = Inventory::filterSource()
                        ->whereId($id)
                        ->firstOrFail();

        $result = $inventory->delete();

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

        $result = Inventory::filterSource()
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

        $data = Inventory::filterSource()
                    ->with('customer')
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

        return Datatables::of($data)
            ->edit_column('code', function($row) {
                return view('admin.logistic.inventories.datatables.code', compact('row'))->render();
            })
            ->edit_column('description', function($row) {
                return view('admin.logistic.inventories.datatables.description', compact('row'))->render();
            })
            ->edit_column('status_id', function($row) {
                return view('admin.logistic.inventories.datatables.status', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.logistic.inventories.datatables.actions', compact('row'))->render();
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
        $sku   = $request->get('sku');

        try {

            $locations = Location::filterSource()
                ->pluck('code', 'id')
                ->toArray();

            $warehouses = Warehouse::filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $inventory = Inventory::filterSource()
                ->findOrNew(@$input['id']);

            $inventory->fill($input);
            $inventory->source = config('app.source');
            $inventory->setCode();

            if($inventory->id) {

                $products = Product::with('locations')
                    ->filterSource()
                    ->where(function($q) use($sku) {
                        $q->where('sku', $sku);
                    })
                    ->get();

                if($products->isEmpty()) {
                    $result = [
                        'result'   => false,
                        'feedback' => 'Não foi encontrado nenhum artigo com o SKU '. $input['sku'],
                    ];
                } else {

                    foreach ($products as $product) {
                        if($product->locations->isEmpty()) {

                            $line = InventoryLine::firstOrNew([
                                'inventory_id'  => $inventory->id,
                                'product_id'    => $product->id,
                                'location_id'   => null
                            ]);

                            $line->inventory_id = $inventory->id;
                            $line->product_id   = $product->id;
                            $line->qty_existing = 0;
                            $line->save();

                        } else {
                            foreach ($product->locations as $productLocation) {

                                $line = InventoryLine::firstOrNew([
                                    'inventory_id'  => $inventory->id,
                                    'product_id'    => @$productLocation->pivot->product_id,
                                    'location_id'   => @$productLocation->pivot->location_id
                                ]);

                                $line->inventory_id = $inventory->id;
                                $line->product_id   = @$productLocation->pivot->product_id;
                                $line->location_id  = @$productLocation->pivot->location_id;
                                $line->qty_existing = @$productLocation->pivot->stock;
                                $line->save();
                            }
                        }
                    }

                    Inventory::updateTotals($inventory->id);

                    $result = [
                        'result' => true,
                        'feedback' => 'Adicionado ao pedido',
                        'id' => $inventory->id,
                        'html' => view('admin.logistic.inventories.partials.table', compact('inventory', 'locations', 'warehouses'))->render()
                    ];
                }
            }

        } catch (\Exception $e) {
            $inventory->forceDelete();

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
    public function updateProduct(Request $request, $inventoryId, $lineId) {

        $location   = $request->get('location');
        $qtyDamaged = $request->get('qty_damaged');
        $qtyReal    = $request->get('qty_real');

        try {

            $line = InventoryLine::where('id', $lineId)
                ->firstOrFail();

            $productLocation = ProductLocation::where('product_id', $line->product_id)
                ->where('location_id', $line->location_id)
                ->first();

            $oldQtyReal = $line->qty_real;

            $line->location_id  = $location;
            $line->qty_damaged  = $qtyDamaged;
            $line->qty_real     = $qtyReal;
            $line->qty_existing = @$productLocation->stock ? @$productLocation->stock : $line->qty_existing;
            $line->save();

            Inventory::updateTotals($inventoryId);

            $results = [
                'result'   => true,
                'feedback' => 'Quantidade alterada com sucesso.',
            ];

        } catch (\Exception $e) {
            $results = [
                'result'   => false,
                'feedback' => $e->getMessage(),
                'qty_real' => $oldQtyReal
            ];
        }

        return Response::json($results);
    }

    /**
     * Update product
     *
     * @return type
     */
    public function destroyProduct(Request $request, $inventoryId, $lineId) {

        try {
            InventoryLine::where('inventory_id', $inventoryId)
                ->where('id', $lineId)
                ->forceDelete();

            Inventory::updateTotals($inventoryId);

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
     * Import products
     *
     * @return type
     */
    public function importProducts(Request $request) {

        $input = $request->toArray();
        $input['import_ids'] = empty(@$input['import_ids']) ? [] : $input['import_ids'];
        $id    = $request->get('id');


        try {

            $locations = Location::filterSource()
                ->pluck('code', 'id')
                ->toArray();

            $warehouses = Warehouse::filterSource()
                ->pluck('name', 'id')
                ->toArray();

            $inventory = Inventory::filterSource()->findOrNew($id);
            $inventory->fill($input);
            $inventory->source = config('app.source');
            $inventory->setCode();

            if($inventory->id) {

                $products = Product::with('locations')
                    ->filterSource()
                    ->whereIn('id', $input['import_ids'])
                    ->get();

                if($products->isEmpty()) {
                    $result = [
                        'result'   => false,
                        'feedback' => 'Não foi encontrado nenhum artigo para a pesquisa indicada',
                    ];
                } else {

                    foreach ($products as $product) {
                        if($product->locations->isEmpty()) {

                            $line = InventoryLine::firstOrNew([
                                'inventory_id'  => $inventory->id,
                                'product_id'    => $product->id,
                                'location_id'   => null
                            ]);

                            $line->inventory_id = $inventory->id;
                            $line->product_id   = $product->id;
                            $line->qty_existing = 0;
                            $line->save();

                        } else {
                            foreach ($product->locations as $productLocation) {

                                $line = InventoryLine::firstOrNew([
                                    'inventory_id'  => $inventory->id,
                                    'product_id'    => @$productLocation->pivot->product_id,
                                    'location_id'   => @$productLocation->pivot->location_id
                                ]);

                                $line->inventory_id = $inventory->id;
                                $line->product_id   = @$productLocation->pivot->product_id;
                                $line->location_id  = @$productLocation->pivot->location_id;
                                $line->qty_existing = @$productLocation->pivot->stock;
                                $line->save();
                            }
                        }
                    }

                    Inventory::updateTotals($inventory->id);

                    $result = [
                        'result' => true,
                        'feedback' => 'Importado com sucesso',
                        'id' => $inventory->id,
                        'html' => view('admin.logistic.inventories.partials.table', compact('inventory', 'locations', 'warehouses'))->render()
                    ];
                }
            }

        } catch (\Exception $e) {
            $inventory->forceDelete();

            $result = [
                'result'   => false,
                'feedback' => $e->getMessage()
            ];
        }

        return Response::json($result);
    }

    /**
     * Import products
     *
     * @return type
     */
    public function previewImportProducts(Request $request) {

        $products = Product::with('locations')
            ->filterSource()
            ->where(function($q) use($request) {

                //filter lote
                $value = $request->import_lote;
                if($request->has('import_lote')) {
                    if($value) {
                        $q = $q->where('lote', '<>', '');
                    } else {
                        $q = $q->where(function($q){
                            $q->whereNull('lote');
                            $q->orWhere('lote', '');
                        });
                    }
                }

                //filter serial_no
                $value = $request->import_serial_no;
                if($request->has('import_serial_no')) {
                    if($value) {
                        $q = $q->where('serial_no', '<>', '');
                    } else {
                        $q = $q->where(function($q){
                            $q->whereNull('serial_no');
                            $q->orWhere('serial_no', '');
                        });
                    }
                }

                //filter customer
                $value = $request->import_customer;
                if($request->has('import_customer')) {
                    $q = $q->where('customer_id', $value);
                }

                //filter location
                $value = $request->import_warehouse;
                if($request->has('import_warehouse')) {
                    $q = $q->whereHas('locations', function($q) use($value) {
                        $q->where('warehouse_id', $value);
                    });
                }

                //filter status
                $value = $request->import_status;
                if($request->has('import_status')) {
                    if($value == 'outstock') {
                        $q = $q->where('stock_total', '<=', '0');
                    } elseif($value == 'lowstock') {
                        $q = $q->where(function($q) {
                            $q->where('stock_total', '>', '0');
                            $q->whereRaw('stock_min >= stock_total');
                        });
                    } elseif($value == 'available') {
                        $q = $q->where('stock_total', '>', '0');
                    } elseif($value == 'blocked') {
                        $q = $q->where('stock_status', 'blocked');
                    }
                }

                //filter date min
                $dtMin = $request->get('date_min');
                if($request->has('date_min')) {

                    $dtMax = $dtMin;

                    if($request->has('date_max')) {
                        $dtMax = $request->get('date_max');
                    }

                    if($request->has('date_unity') && !empty($request->has('date_unity'))) { //filter by shipment status date
                        $dtMin = $dtMin . ' 00:00:00';
                        $dtMax = $dtMax . ' 23:59:59';
                        $unity = $request->get('date_unity');

                        if($unity == '3') { //data validade
                            $q->whereBetween('expiration_date', [$dtMin, $dtMax]);
                        }
                        elseif($unity == '4') { // data criação
                            $q->whereBetween('created_at', [$dtMin, $dtMax]);
                        }

                    } else { //filter by last update
                        $q = $q->whereBetween('last_update', [$dtMin, $dtMax]);
                    }
                }
            })
            ->get();


        $productsArr = [];
        foreach ($products as $product) {
            if($product->locations->isEmpty()) {

                $productsArr[] = [
                    'id'        => $product->id,
                    'sku'       => $product->sku,
                    'name'      => $product->name,
                    'location'  => '--',
                    'stock'     => '--'
                ];

            } else {
                foreach ($product->locations as $productLocation) {
                    $productsArr[] = [
                        'id'        => $product->id,
                        'sku'       => @$product->sku,
                        'name'      => @$product->name,
                        'location'  => @$productLocation->code,
                        'stock'     => @$productLocation->pivot->stock_available
                    ];
                }
            }
        }

        $products = $productsArr;

        $result = [
            'result'    => true,
            'feedback'  => 'Listado com sucesso',
            'counter'   => count($productsArr),
            'html'      => view('admin.logistic.inventories.partials.import_preview', compact('products'))->render()
        ];

        return Response::json($result);
    }

    /**
     * Print product labels
     *
     * @return \Illuminate\Http\Response
     */
    public function printSummary(Request $request, $inventoryId) {

        $inventory = Inventory::with('customer')
            ->with(['lines' => function($q){
                $q->with('product', 'location');
            }])
            ->filterSource()
            ->findOrFail($inventoryId);

        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'orientation'   => 'P',
            'format'        => 'A4',
            'margin_left'   => 11,
            'margin_right'  => 5,
            'margin_top'    => 18,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'inventory'        => $inventory,
            'documentTitle'    => 'Inventário ' . $inventory->code,
            'documentSubtitle' => '',
            'view'             => 'admin.printer.logistic.inventories.summary'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Inventário ' . $inventory->code . '.pdf', 'I'); //output to screen

        exit;

    }

    /**
     * Print product labels
     *
     * @return \Illuminate\Http\Response
     */
    public function printMap(Request $request, $mapType) {

        if($mapType == 'stocks') {
            return $this->printMapStocks($request);
        }

        return Redirect::back()->with('error', 'Não selecionou nenhum mapa a imprimir');

    }

    /**
     * Print product labels
     *
     * @return \Illuminate\Http\Response
     */
    public function printMapStocks(Request $request) {
        $groupByProduct  = $request->get('group_product', false);
        $groupByCustomer = $request->get('group_customer', false);
        $date            = $request->get('date', date('Y-m-d'));
        $subtitle        = 'À data de ' . $date;


        if($request->has('customer')) {
            $customer = Customer::find($request->get('customer'));
            $subtitle.= "<br/>".$customer->code .' - '. $customer->name;
        }

        if($groupByProduct) {
            $stockHistories = ProductStockHistory::with('customer', 'product')
                ->whereHas('product')
                ->where('date', '<=', $date)
                ->groupBy('customer_id')
                ->groupBy('product_id')
                ->select([
                    'customer_id',
                    'product_id',
                    DB::raw('max(date) as date'),
                    DB::raw('sum(stock_total) as stock_total'),
                    DB::raw('sum(stock_allocated) as stock_allocated'),
                    DB::raw('sum(stock_available) as stock_available')
                ]);

        } else {
            $stockHistories = ProductStockHistory::with('customer', 'product', 'location')
                ->where('product')
                ->join(DB::raw('(
                        select max(date) as LatestDate, unique_hash
                        from products_stocks_history
                        group by unique_hash
                    ) SubMax'), function ($q) use ($date) {
                        $q->on('products_stocks_history.date', '=', 'SubMax.LatestDate');
                        $q->whereRaw('products_stocks_history.unique_hash = SubMax.unique_hash');
                        $q->whereRaw('date <= "' . $date . '"');
                    }
                )
                ->orderBy('date', 'desc')
                ->orderBy('product_id')
                ->select();
        }

        /*
        select *
        from products_stocks_history
        inner join
        (
            select max(date) as LatestDate, unique_hash
            from products_stocks_history
            group by unique_hash
        ) SubMax
        on products_stocks_history.date = SubMax.LatestDate
        and products_stocks_history.unique_hash = SubMax.unique_hash and date <= '2022-03-09'
        order by date desc, product_id
        */

        //filter customer
        $value = $request->get('customer');
        if($request->has('customer')) {
            $stockHistories = $stockHistories->where('customer_id', $value);
        }

        //filter warehouse
        $value = $request->get('warehouse');
        if($request->has('warehouse')) {
            $stockHistories = $stockHistories->where('warehouse_id', $value);
        }

        $stockHistories = $stockHistories->get();

        if($groupByCustomer) {
            $stockHistories = $stockHistories->groupBy('customer.name');
        }

        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'orientation'   => 'L',
            'format'        => 'A4',
            'margin_left'   => 11,
            'margin_right'  => 5,
            'margin_top'    => 18,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO TMS");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'stockHistories'   => $stockHistories,
            'documentTitle'    => 'Inventário Existências',
            'documentSubtitle' => $subtitle,
            'groupByProduct'   => $groupByProduct,
            'groupByCustomer'  => $groupByCustomer,
            'view'             => 'admin.printer.logistic.inventories.map_stocks'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Inventário Existências.pdf', 'I'); //output to screen

        exit;

    }
}
