<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Models\Equipment\Equipment;
use Illuminate\Http\Request;
use Auth, Excel, File, DB, Date, Response;

class EquipmentsController extends \App\Http\Controllers\Admin\Controller {

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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',equipments']);
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function productsList(Request $request) {

        $ids = $request->id;

        $header = [
            'SKU',
            'Designação',
            'N.Serie',
            'Lote',
            'Categoria',
            'Cod. Cliente',
            'Cliente',
            'Armazém',
            'Localização',
            'Stock Total',
            'Unidade',
            'Peso',
            'Comprimento',
            'Largura',
            'Altura',
            'Observações'
        ];

        $data = Equipment::where('equipments.source', config('app.source'))
            ->with('customer', 'location', 'warehouse', 'category')
            ->select();

        if($ids) {
            $data = $data->whereIn('equipments.id', $ids);
        } else {
            //filter status
            $value = $request->status;
            if($request->has('status')) {
                $data = $data->where('status', $value);
            }

            //filter warehouse
            $value = $request->images;
            if($request->has('images')) {
                if($value) {
                    $data = $data->whereNotNull('filepath');
                } else {
                    $data = $data->whereNull('filepath');
                }
            }

            //filter location
            $value = $request->location;
            if($request->has('location')) {
                $data = $data->where('location_id', $value);
            }

            //filter warehouse
            $value = $request->category;
            if($request->has('category')) {
                $data = $data->where('category_id', $value);
            }

            //filter customer
            $value = $request->customer;
            if($request->has('customer')) {
                $data = $data->where('customer_id', $value);
            }

            //filter date
            $dtMin = $request->get('date_min');
            if($request->has('date_min')) {

                $dtMax = $dtMin;

                if($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                }

                if($request->has('date_unity') && !empty($request->has('date_unity'))) { //filter by shipment status date
                    $dtMin = $dtMin . ' 00:00:00';
                    $dtMax = $dtMax . ' 23:59:59';
                    $dateType = $request->get('date_unity');

                    if($dateType == 'creation') {
                        $data->whereBetween('created_at', [$dtMin, $dtMax]);
                    }

                } else { //filter by shipment date
                    $data = $data->whereBetween('last_update', [$dtMin, $dtMax]);
                }
            }
        }

        $data = $data->get();

        Excel::create('Listagem de Equipamentos', function($file) use($data, $header){

            $file->sheet('Listagem', function($sheet) use($data, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function($row){
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });
                $sheet->setColumnFormat([
                    'A' => '@'
                ]);

                foreach($data as $product) {

                    $rowData = [
                        $product->sku,
                        $product->name,
                        $product->serial_no,
                        $product->lote,
                        @$product->category->name,
                        @$product->customer->code,
                        @$product->customer->name,
                        @$product->warehouse->name,
                        @$product->location->name,
                        $product->stock_total,
                        $product->unity,
                        $product->weight,
                        $product->width,
                        $product->length,
                        $product->height,
                        $product->obs,
                    ];
                    $sheet->appendRow($rowData);
                }
            });

        })->export('xls');
    }

    public function filterExportFile(Request $request, string $group = null){
        
        $defaultDtMin = new Date();
        $defaultDtMin = $defaultDtMin->subDays(7)->format('Y-m-d');

        $dtMin = $request->get('date_start') ? $request->get('date_start') : date('Y-m-d');
        $dtMax = $request->get('date_end') ? $request->get('date_end') : $defaultDtMin;

        $dtMin = $dtMin.' 00:00:00';
        $dtMax = $dtMax.' 23:59:59';
        

        if($group == 'consumption'){
            $this->lowConsumption($dtMin, $dtMax);
        }else if($group == 'warehouse'){
            $this->warehouseStock();
        }else if($group == 'stock-location-category'){
            $this->listStock();
        }else if($group == 'resume-movements'){
            $this->resumeMovements($dtMin, $dtMax);
        }
        
    }
    
    //agrupado por categorias e localizações - Baixas | num período de tempo
    public function lowConsumption($dateStart, $dateEnd){
        
        $listCategories = Category::get()->pluck('code', 'id')->toArray();
        $listLocations  = Location::orderBy('code', 'asc')->get()->pluck('name', 'id')->toArray();
        
        
        $equipmentHistories = History::with('equipment', 'equipment.category', 'location')
            ->whereHas('equipment', function($q) {
                $q->filterSource();
            })
            ->where('action', 'out')
            ->whereBetween('created_at', [$dateStart, $dateEnd])
            ->get();
            
        
        $categoryLocation = $equipmentHistories->groupBy('location.id')->transform(function($item){
            return $item->groupBy('equipment.category.id')->transform(function($item){
                return $item->count('equipment_id');
            });
        });
        

        $header = [
            'Localização Motorista',
        ];
        
        foreach($listCategories as $value => $category){
            $categorytest[] = $category;
            $header[] = $category;
                 
        }
        
        $header[] = 'Total';
        

        Excel::create('Listagem de Consumo '.$dateStart.' a '.$dateEnd.' [Baixas]', function($file) use($listLocations, $categoryLocation, $listCategories, $header){
        
                $file->sheet('Listagem', function($sheet) use($listLocations, $header, $listCategories, $categoryLocation) {

                $sheet->row(1, $header);
                $sheet->row(1, function($row){
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });
                $sheet->setColumnFormat([
                    'A' => '@'
                ]);
                

                foreach($categoryLocation as $locationId => $locationCategories){
                    
                        $rowTotal = 0;

                        
                        $locationName = @$listLocations[$locationId];
                        $rowData = [ $locationName ];
                        
                            foreach($listCategories as $value => $category){
                                $rowData [] = @$locationCategories[$value] ? @$locationCategories[$value] : '';
                                $rowTotal   +=  @$locationCategories[$value] ? @$locationCategories[$value] : 0;

                            }
                            
                            $rowData [] = $rowTotal;
                            
                        $sheet->appendRow($rowData);
                   
                }

                
            });

        })->export('xls');
        
    }
    
    //agrupado por categorias e armazéns - Stock
    public function warehouseStock(){
        
        $listCategories = Category::get()->pluck('code', 'id')->toArray();
        $listWarehouse  = Warehouse::get()->pluck('name', 'id')->toArray();
        
        
        $equipments = Equipment::with('category', 'warehouse')
            ->filterSource()
            ->where('status', 'available')
            ->whereNotNull('location_id');
      
        $equipments = $equipments->get();
            
        $categoryWarehouse = $equipments->groupBy('category.id')->transform(function($item){
            return $item->groupBy('warehouse.id')->transform(function($item){
                return $item->sum('stock_total');
            });
        });
        
        $categoryWarehouse = $equipments->groupBy('warehouse.id')->transform(function($item){
            return $item->groupBy('category.id')->transform(function($item){
                return $item->sum('stock_total');
            });
        });
        
             
        $header = [
            'Armazém',
        ];
        
        foreach($listCategories as $value => $category){
            $categorytest[] = $category;
            $header[] = $category;
                 
        }
        $header[] = 'Total';
        


        Excel::create('Inventário por Armazém + Categoria', function($file) use($listWarehouse, $categoryWarehouse, $listCategories, $header){
        
                $file->sheet('Listagem', function($sheet) use($listWarehouse, $header, $listCategories, $categoryWarehouse) {

                $sheet->row(1, $header);
                $sheet->row(1, function($row){
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });
                $sheet->setColumnFormat([
                    'A' => '@'
                ]);
                
               

                foreach($categoryWarehouse as $key => $categoriesWarehouse){
                        
                         $rowTotal = 0;
                        
                        $warehouseName = @$listWarehouse[$key];
                        $rowData = [ $warehouseName ];
                        
                            foreach($listCategories as $categoryId => $category){
                                
                                $rowData [] = @$categoriesWarehouse[$categoryId] ? @$categoriesWarehouse[$categoryId] : '';
                                
                                $rowTotal += @$categoriesWarehouse[$categoryId] ? @$categoriesWarehouse[$categoryId] : 0;


                            }
                            
                           $rowData [] = $rowTotal;

                            
                        $sheet->appendRow($rowData);
                   
                }

                
            });

        })->export('xls');
        
        
    }
    
    //agrupado por categorias e localizações
    public function listStock(){
        

        $listCategories = Category::get()->pluck('code', 'id')->toArray();
        $listLocations  = Location::orderBy('code', 'asc')->get()->pluck('name', 'id')->toArray();
        
        
        $equipments = Equipment::with('category', 'location')
            ->filterSource()
            ->whereNotNull('location_id');
    
        $equipments = $equipments->get();

        $categories = $equipments->groupBy('category.name')->transform(function($item) {
            return $item->groupBy('status')->transform(function($item) {
                return $item->sum('stock_total');
            });
        });

        $categoryLocation = $equipments->groupBy('location.id')->transform(function($item) {
            return $item->groupBy('category.id')->transform(function($item) {
                return $item->sum('stock_total');
            });
        });
        
        
        $header = [
            'Localização Motorista',
        ];
        
        foreach($listCategories as $value => $category){
            $categorytest[] = $category;
            $header[] = $category;
                 
        }
        
        $header[] = 'Total';
        

        Excel::create('Categoria de Equipamento por Localização', function($file) use($listLocations, $categoryLocation, $listCategories, $header){
        
                $file->sheet('Listagem', function($sheet) use($listLocations, $header, $listCategories, $categoryLocation) {

                $sheet->row(1, $header);
                $sheet->row(1, function($row){
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });
                $sheet->setColumnFormat([
                    'A' => '@'
                ]);
                

                foreach($categoryLocation as $locationId => $locationCategories){
                    
                       $rowTotal = 0;

                        
                        $locationName = @$listLocations[$locationId];
                        $rowData = [ $locationName ];
                        
                            foreach($listCategories as $value => $category){
                                $rowData [] = @$locationCategories[$value] ? @$locationCategories[$value] : '';
                                
                                $rowTotal += @$locationCategories[$value] ? @$locationCategories[$value] : 0;
                                
                            }
                            
                            $rowData [] = $rowTotal;

                            
                            
                        $sheet->appendRow($rowData);
                   
                }

                
            });

        })->export('xls');
        
    }
    
    //resumo de Movimentos por Categoria num período de tempo
    public function resumeMovements($dateStart, $dateEnd){
        
        $listCategories = Category::get()->pluck('code', 'id')->toArray();
        
        $equipmentHistories = History::with('equipment')
            ->whereHas('equipment', function($q) {
                $q->filterSource();
            })
            ->whereBetween('created_at', [$dateStart, $dateEnd])
            ->get();

          
        $equipmentHistories = $equipmentHistories->groupBy('equipment.category_id')->transform(function($item) {
            return $item->groupBy('action')->transform(function($item) {
                return $item->count();
            });
        });
        
    
        $header = [
            'Categoria',
        ];
        
        foreach(trans('admin/equipments.equipments.actions') as $key => $action){
            $header[]     = $action;
                 
        }
        
        $header [] = 'Total';
        

        Excel::create('Resumo de Movimentos '.$dateStart.' a '.$dateEnd, function($file) use($listCategories, $header, $equipmentHistories){
        
                $file->sheet('Listagem', function($sheet) use($header, $listCategories, $equipmentHistories) {

                $sheet->row(1, $header);
                $sheet->row(1, function($row){
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });
                $sheet->setColumnFormat([
                    'A' => '@'
                ]);
                

                foreach($listCategories as $categoryId => $category){
                    
                        $rowTotal = 0;

                        
                        $rowData = [ $category ];

                            foreach(trans('admin/equipments.equipments.actions') as $key => $action){
                                $rowData [] = @$equipmentHistories[$categoryId][$key];
                                
                                $rowTotal += @$equipmentHistories[$categoryId][$key];

                            }
                            
                        $rowData[] = $rowTotal;
                            
                        $sheet->appendRow($rowData);
                   
                }

                
            });

        })->export('xls');
        
    }

}
