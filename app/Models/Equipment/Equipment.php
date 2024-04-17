<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth, Date, Setting, DB;
use Mpdf\Mpdf;

class Equipment extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'equipments';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku', 'name', 'description', 'serial_no', 'lote', 'location_id', 'category_id', 'customer_id', 'warehouse_id',
        'width', 'height', 'length', 'weight', 'is_active', 'status_id', 'stock_total', 'created_by',
        'last_history', 'status', 'ot_code'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'sku'   => 'required',
        'name'  => 'required',
    );

    /**
     * Print inventory
     *
     * @param null $ids
     * @param string $outputFormat
     * @return mixed
     * @throws \Throwable
     */
    public static function printInventory($ids = null, $groupResults = null, $categorySummary = true, $showDetails = true, $outputFormat='I'){

        ini_set("memory_limit", "-1");

        $locations = Location::get();

        $equipments = Equipment::with('customer', 'category')
            ->with(['location'=> function($q) {
                $q->orderBy('code');
            }])
            ->filterSource();

        if($ids) {
            $equipments = $equipments->whereIn('id', $ids);
        }

        $equipments = $equipments->groupBy('sku')
            ->get(['equipments.*', DB::raw('sum(stock_total) as stock_total')]);


        if($groupResults) {
            if($groupResults == 'category'){
                if($showDetails) {
                    $equipments = $equipments->sortBy('location_id')->groupBy('category.name');
                } else {
                    $equipments = $equipments->sortBy('location_id')->groupBy('category.name')
                        ->transform(function ($item) {
                            return $item->groupBy('status')->transform(function ($item) {
                                return $item->sum('stock_total');
                            });
                        });
                }
            } elseif($groupResults == 'location') {
                if($showDetails) {
                    $equipments = $equipments->sortBy('category_id')->groupBy('location.name');
                } else {
                    $equipments = $equipments->sortBy('category_id')->groupBy('location.name')
                        ->transform(function ($item) {
                            return $item->groupBy('status')->transform(function ($item) {
                                return $item->sum('stock_total');
                            });
                        });
                }
            } elseif($groupResults == 'location-category') {

                if($showDetails) {
                    $equipments = $equipments->groupBy('location.name')->transform(function($item) {
                        return $item->groupBy('category.name');
                    });

                } else {
                    $equipments = $equipments->groupBy('location.name')->transform(function($item) {
                        return $item->groupBy('category.name')
                            ->transform(function ($item) {
                                return $item->groupBy('status')->transform(function ($item) {
                                    return $item->sum('stock_total');
                                });
                            });
                    });

                }
            }
        }

        //dd($equipments->toArray());

        $categories = Equipment::with('category')
            ->filterSource();

            if($ids) {
                $categories = $categories->whereIn('id', $ids);
            }
    
            $categories = $categories->get();

        $categories = $categories->groupBy('category.name')->transform(function($item) {
            return $item->groupBy('status')->transform(function($item) {
                return $item->sum('stock_total');
            });
        });

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 14,
            'margin_right'  => 5,
            'margin_top'    => 25,
            'margin_bottom' => 15,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'equipments'        => $equipments,
            'categories'        => $categories,
            'locations'         => $locations,
            'categorySummary'   => $categorySummary,
            'showDetails'       => $showDetails, //No PDF não há nenhuma opção para mostrar as listas agrupadas por detalhe
            'groupResults'      => $groupResults,
            'allStatus'         => trans('admin/equipments.equipments.status'),
            'documentTitle'     => 'Inventário de Equipamentos',
            'documentSubtitle'  => 'Emitido em ' . date('Y-m-d'),
            'view'              => 'admin.printer.equipments.inventory'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Inventário de Equipamentos.pdf', $outputFormat); //output to screen

        exit;
    }


    /**
     * Print labels
     *
     * @param null $ids
     * @param string $outputFormat
     * @return mixed
     * @throws \Throwable
     */
    public static function printLabels($ids = null, $groupResults = null, $categorySummary = true, $showDetails = true, $outputFormat='I'){

        ini_set("memory_limit", "-1");


        $equipments = Equipment::with('customer', 'category')
            ->with(['location'=> function($q) {
                $q->orderBy('code');
            }])
            ->filterSource();

        if($ids) {
            $equipments = $equipments->whereIn('id', $ids);
        }

        $equipments = $equipments->groupBy('sku')
            ->get();


        $mpdf = new Mpdf([
            'format'        => [100,40],
            'margin_left'   => 5,
            'margin_right'  => 5,
            'margin_top'    => 5,
            'margin_bottom' => 5,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        foreach($equipments as $product) {
            $data = [
                'product'           => $product,
                'view'              => 'admin.printer.equipments.label'
            ];

            $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write
        }
        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Inventário de Equipamentos.pdf', $outputFormat); //output to screen

        exit;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function warehouse()
    {
        return $this->belongsTo('App\Models\Equipment\Warehouse', 'warehouse_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Equipment\Location', 'location_id');
    }

    public function history()
    {
        return $this->hasMany('App\Models\Equipment\History', 'equipment_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Equipment\Category', 'category_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to easily re-use query logic in your models.
    | To define a scope, simply prefix a model method with scope.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    | Eloquent provides a convenient way to transform your model attributes when
    | getting or setting them. Simply define a 'getFooAttribute' method on your model
    | to declare an accessor. Keep in mind that the methods should follow camel-casing,
    | even though your database columns are snake-case.
    |
    */
    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }

    public function setCategoryIdAttribute($value)
    {
        $this->attributes['category_id'] = empty($value) ? null : $value;
    }
    
    public function setLocationIdAttribute($value)
    {
        $this->attributes['location_id'] = empty($value) ? null : $value;
    }

}
