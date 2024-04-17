<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Mpdf, Setting;

class Location extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logistic';

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_logistic_locations';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'warehouse_id', 'type_id', 'code', 'rack', 'bay', 'level', 'position', 'color', 'status', 'obs',
        'width', 'height', 'length', 'max_weight', 'max_pallets'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'warehouse_id'  => 'required',
        'code'          => 'required',
        'hall'          => 'required',
        'rack'          => 'required',
        'shelf'         => 'required',
    );

    public static function printLabels($locationIds, $returnMode = 'I')
    {

        $locations = Location::filterSource()
            ->whereIn('id', $locationIds)
            ->get();

        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'orientation'   => 'L',
            'format'        => [100, 145],
            'margin_left'   => 2,
            'margin_right'  => 2,
            'margin_top'    => 2,
            'margin_bottom' => 2,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($locations as $location) {

            $code = $location->barcode;

            $qrCode = new \Mpdf\QrCode\QrCode($code);
            $qrCode->disableBorder();
            $output = new \Mpdf\QrCode\Output\Png();
            $qrCode = 'data:image/png;base64,' . base64_encode($output->output($qrCode, 120));

            $data = [
                'qrCode' => $qrCode,
                'location' => $location,
            ];

            $data['view'] = 'admin.printer.logistic.locations.label';
            $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write
        }

        if (Setting::get('open_print_dialog_labels')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Etiquetas.pdf', $returnMode); //output to screen

        exit;
    }

    public static function printLocations($locationIds)
    {
        if (empty($locationIds)) {
            $locations = Location::with('warehouse', 'type')->get();
        } else {
            $locations = Location::filterSource()
                ->whereIn('id', $locationIds)
                ->with('warehouse', 'type')
                ->get();
        }


        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_right'  => 12,
            'margin_top'    => 30,
            'margin_bottom' => 10,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($locations as $key => $location) {
            $data = [
                'locations'      => $locations,
                'documentTitle' => 'Listagem de Localizações',
                'documentSubtitle'  => '',
                'view' => 'admin.printer.logistic.locations.locations'
            ];
        }

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        //output pdf
        $mpdf->debug = true;
        return $mpdf->Output('Listagem de Localizacoes.pdf', 'I'); //output to screen

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
        return $this->belongsTo('App\Models\Logistic\Warehouse', 'warehouse_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Logistic\LocationType', 'type_id');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Logistic\Product', 'products_locations', 'location_id');
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
    public function scopeFilterSource($query)
    {
        return $query->whereHas('warehouse', function ($q) {
            $q->filterSource();
        });
    }

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
    public function setBarcodeAttribute($value)
    {
        $this->attributes['barcode'] = strtoupper($value);
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    public function setWidthAttribute($value)
    {
        $this->attributes['width'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setLengthAttribute($value)
    {
        $this->attributes['length'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setHeightAttribute($value)
    {
        $this->attributes['height'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setMaxWeightAttribute($value)
    {
        $this->attributes['max_weight'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setMaxPalletsAttribute($value)
    {
        $this->attributes['max_pallets'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function getCodeBarcodeAttribute() {
        return sprintf('%s [%s]', $this->code, $this->barcode);
    }
}
