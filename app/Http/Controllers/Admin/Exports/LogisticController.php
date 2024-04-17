<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Models\Logistic\Product;
use App\Models\Logistic\ProductStockHistory;
use App\Models\Logistic\ShippingOrder;
use App\Models\Logistic\ReceptionOrder;
use Illuminate\Http\Request;
use App\Models\Logistic\Location;
use Auth, Excel, File, DB, Date, Response;

class LogisticController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = '';

    /**
     * Store last row of each iteration
     *
     * @var type
     */
    protected $lastRow = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',logistic_products|logistic_shipping_orders']);
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function productsList(Request $request)
    {

        $ids = $request->id;

        $header = [
            'SKU',
            'Cód. Barras',
            'Designação',
            'Lote',
            'N.Serie',
            'Cod. Cliente',
            'Cliente',
            'Armazém',
            'Localização',
            'Estado',
            'Stock Total',
            'Stock Alocado',
            'Stock Min',
            'Stock Max',
            'Unidade',
            'Peso',
            'Comprimento',
            'Largura',
            'Altura',
            'Data Produção',
            'Data Validade',
            'Marca',
            'Modelo',
            'Categoria',
            'Uni./Pack',
            'Packs/Caixa',
            'Caixas/Palete',
            'Observações'
        ];

        $bindings = [
            'products.*',
            'warehouses.name as warehouse',
            'locations.code as location_code',
            'products_locations.stock as location_stock'
        ];

        $data = Product::where('products.source', config('app.source'))
            ->with('customer')
            ->leftJoin('products_locations', 'products.id', '=', 'products_locations.product_id')
            ->leftJoin('locations', 'products_locations.location_id', '=', 'locations.id')
            ->leftJoin('warehouses', 'locations.warehouse_id', '=', 'warehouses.id')
            ->select($bindings);

        if ($ids) {
            $data = $data->whereIn('products.id', $ids);
        } else {
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

            //filter location
            $value = $request->location;
            if ($request->has('location')) {
                $data = $data->whereHas('locations', function ($q) use ($value) {
                    $q->where('location_id', $value);
                });
            }
        }

        $data = $data->get();

        Excel::create('Listagem de Artigos', function ($file) use ($data, $header) {

            $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });
                $sheet->setColumnFormat([
                    'A' => '@'
                ]);

                foreach ($data as $product) {

                    $rowData = [
                        $product->sku,
                        $product->barcode,
                        $product->name,
                        $product->serial_no,
                        $product->lote,
                        @$product->customer->code,
                        @$product->customer->name,
                        $product->warehouse,
                        $product->location_code,
                        $product->stock_status,
                        $product->stock_total,
                        $product->stock_allocated,
                        $product->stock_min,
                        $product->stock_max,
                        $product->unity,
                        $product->weight,
                        $product->width,
                        $product->length,
                        $product->height,
                        $product->production_date,
                        $product->expiration_date,
                        @$product->brand->name,
                        @$product->brand_model->name,
                        $product->unities_by_pack,
                        $product->packs_by_box,
                        $product->boxes_by_pallete,
                        $product->obs,
                    ];
                    $sheet->appendRow($rowData);
                }
            });
        })->export('xls');
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function shippingOrders(Request $request)
    {
        
        ini_set('memory_limit', '-1');

        $ids = $request->id;

        $header = [
            'Ordem Saida',
            'Cod. Cliente',
            'Cliente',
            'Data',
            'Referência',
            'TRK Envio',
            'Estado',
            'Artigo Ref.',
            'Artigo Designação',
            'Artigo Localização',
            'Artigo Qtd Pedida',
            'Artigo Qtd Satif.',
            'Artigo Preço',
            'Obs',
        ];


        $data = ShippingOrder::where('source', config('app.source'))
            ->with('customer', 'lines.product', 'status')
            ->select();

        if ($ids) {
            $data = $data->whereIn('id', $ids);
        } else {
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
                $value = explode(',', $value);
                $data = $data->whereIn('status_id', $value);
            }

            //filter customer
            $value = $request->dt_customer;
            
            if($request->has('dt_customer')) {
                $data = $data->where('customer_id', $value);
            }
            
            //filter hide concluded
            $value = $request->hide_concluded;
            if($request->has('hide_concluded')) {
                if($value) {
                    $data = $data->whereNotIn('status_id', [ShippingOrder::STATUS_CONCLUDED, ShippingOrder::STATUS_CANCELED]);
                }
            }
        }

        $data = $data->get();

        Excel::create('Listagem de Ordens Saída', function ($file) use ($data, $header) {

            $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });
                $sheet->setColumnFormat([
                    'A' => '@'
                ]);

                $fileRowId = 2;
                foreach ($data as $key => $product) {

                    if ($product->lines) {

                        $color = '#cccccc';
                        if ($key % 2) {
                            $color = '#eeeeee';
                        }

                        foreach ($product->lines as $line) {
                            $rowData = [
                                $product->code,
                                @$product->customer->code,
                                @$product->customer->name,
                                $product->date,
                                $product->document,
                                $product->shipment_trk,
                                @$product->status->name,
                                @$line->product->sku,
                                @$line->product->name,
                                @$line->location->code,
                                $line->qty,
                                $line->qty_satisfied,
                                $line->price,
                                $product->obs,
                            ];
                            $sheet->appendRow($rowData);

                            $sheet->row($fileRowId, function ($row) use ($color) {
                                $row->setBackground($color);
                            });

                            $fileRowId++;
                        }
                    }
                }
            });
        })->export('xls');
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function receptionOrders(Request $request)
    {

        $ids = $request->id;

        $header = [
            'Ordem Receção',
            'Cod. Cliente',
            'Cliente',
            'Data',
            'Referência',
            'TRK Envio',
            'Estado',
            'Artigo Ref.',
            'Artigo Designação',
            'Artigo Localização',
            'Artigo Qtd Pedida',
            'Artigo Qtd Satif.',
            'Artigo Preço',
            'Obs',
            'Paletes',
            'Caixas',
            'Preço',
        ];


        $data = ReceptionOrder::where('source', config('app.source'))
            ->with('customer', 'lines.product')
            ->select();

        if ($ids) {
            $data = $data->whereIn('id', $ids);
        } else {
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
                $value = explode(',', $value);
                $data = $data->whereIn('status_id', $value);
            }

            //filter customer
            $value = $request->dt_customer;
            if($request->has('dt_customer')) {
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

            //filter hide concluded
            $value = $request->hide_concluded;
            if($request->has('hide_concluded')) {
                if($value) {
                    $data = $data->whereNotIn('status_id', [ReceptionOrderStatus::STATUS_CONCLUDED, ReceptionOrderStatus::STATUS_CANCELED]);
                }
            }
        }

        $data = $data->get();

        Excel::create('Listagem de Ordens de Recepção', function ($file) use ($data, $header) {

            $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });
                $sheet->setColumnFormat([
                    'A' => '@'
                ]);

                $fileRowId = 2;
                foreach ($data as $key => $product) {

                    if ($product->lines) {

                        $color = '#cccccc';
                        if ($key % 2) {
                            $color = '#eeeeee';
                        }

                        foreach ($product->lines as $line) {
                            $rowData = [
                                $product->code,
                                @$product->customer->code,
                                @$product->customer->name,
                                $product->date,
                                $product->reference,
                                $product->shipment_trk,
                                @$product->status->name,
                                @$line->product->sku,
                                @$line->product->name,
                                @$line->location->code,
                                $line->qty,
                                $line->qty_satisfied,
                                $line->price,
                                $product->obs,
                                $product->pallets,
                                $product->boxs,
                                $product->price,
                            ];
                            $sheet->appendRow($rowData);

                            $sheet->row($fileRowId, function ($row) use ($color) {
                                $row->setBackground($color);
                            });

                            $fileRowId++;
                        }
                    }
                }
            });
        })->export('xls');
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function inventories(Request $request, $map)
    {
        if ($map == 'stocks') {
            return $this->inventoryStocks($request);
        }
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function inventoryStocks(Request $request)
    {


        $groupByProduct  = $request->get('group_product', false);
        $date            = $request->get('date', date('Y-m-d'));

        if ($groupByProduct) {
            $stockHistories = ProductStockHistory::with('customer', 'product')
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


            $header = [
                'SKU',
                'Produto',
                'Lote',
                'N.Serie',
                'Cod. Cliente',
                'Cliente',
                'Criado em',
                'Último Movimento',
                'Stock Total',
                'Stock Alocado',
                'Stock Disponível'
            ];
        } else {
            $stockHistories = ProductStockHistory::with('customer', 'product', 'location')
                ->join(
                    DB::raw('(
                        select max(date) as LatestDate, unique_hash
                        from products_stocks_history
                        group by unique_hash
                    ) SubMax'),
                    function ($q) use ($date) {
                        $q->on('products_stocks_history.date', '=', 'SubMax.LatestDate');
                        $q->whereRaw('products_stocks_history.unique_hash = SubMax.unique_hash');
                        $q->whereRaw('date <= "' . $date . '"');
                    }
                )
                ->orderBy('date', 'desc')
                ->orderBy('product_id')
                ->select();


            $header = [
                'SKU',
                'Produto',
                'Lote',
                'N.Serie',
                'Cod. Cliente',
                'Cliente',
                'Armazém',
                'Localização',
                'Criado em',
                'Último Movimento',
                'Stock Total',
                'Stock Alocado',
                'Stock Disponível'
            ];
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
        if ($request->has('customer')) {
            $stockHistories = $stockHistories->where('customer_id', $value);
        }

        //filter warehouse
        $value = $request->get('warehouse');
        if ($request->has('warehouse')) {
            $stockHistories = $stockHistories->where('warehouse_id', $value);
        }

        $stockHistories = $stockHistories->get();


        Excel::create('Inventário de Existências', function ($file) use ($stockHistories, $header, $groupByProduct) {

            $file->sheet('Listagem', function ($sheet) use ($stockHistories, $header, $groupByProduct) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });
                $sheet->setColumnFormat([
                    'A' => '@'
                ]);

                foreach ($stockHistories as $history) {

                    if ($groupByProduct) {
                        $rowData = [
                            @$history->product->sku . ' ',
                            @$history->product->name,
                            @$history->product->lote,
                            @$history->product->serial_no,
                            @$history->customer->code,
                            @$history->customer->name,
                            @$history->product->created_at ? $history->product->created_at->format('Y-m-d') : '',
                            $history->date,
                            $history->stock_total,
                            $history->stock_allocated,
                            $history->stock_available,
                        ];
                    } else {
                        $rowData = [
                            @$history->product->sku,
                            @$history->product->name,
                            @$history->product->lote,
                            @$history->product->serial_no,
                            @$history->customer->code,
                            @$history->customer->name,
                            @$history->warehouse->name,
                            @$history->location->code,
                            @$history->product->created_at ? $history->product->created_at->format('Y-m-d') : '',
                            $history->date,
                            $history->stock_total,
                            $history->stock_allocated,
                            $history->stock_available,
                        ];
                    }
                    $sheet->appendRow($rowData);
                }
            });
        })->export('xls');
    }


    public function location(Request $request, $key)
    {
        if ($key == 'all') {
            $locations = Location::with('warehouse', 'type')
                ->get();
        } else {
            // $ids = explode(',', $request->ids);

            $locations = Location::whereIn('id', $request->id)
                ->with('warehouse', 'type')
                ->get();
        }

        $header = [
            'Armazém',
            'Localização',
            'Código de Barras',
            'Tipologia',
            'Altura',
            'Comprimento',
            'Largura',
            'Paletes',
            'Peso Max.',
            'Estado',
            'Data Criação'
        ];

        Excel::create('Listagem de Localizacoes', function ($file) use ($locations, $header) {

            $file->sheet('Listagem', function ($sheet) use ($locations, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });
                $sheet->setColumnFormat([
                    'A' => '@'
                ]);

                foreach ($locations as $location) {
                    $rowData = [
                        @$location->warehouse->name,
                        @$location->code,
                        @$location->barcode,
                        @$location->type->name,
                        @$location->height,
                        @$location->length,
                        @$location->width,
                        @$location->max_pallets,
                        @$location->max_weight,
                        trans('admin/logistic.locations.status.' . $location->status),
                        $location->created_at,
                    ];

                    $sheet->appendRow($rowData);
                }
            });
        })->export('xls');
    }
}
