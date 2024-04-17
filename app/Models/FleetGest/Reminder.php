<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth, Setting;
use Mpdf\Mpdf;

class Reminder extends \App\Models\BaseModel
{

    use SoftDeletes,
        FileTrait;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_fleet';

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_fleet_reminders';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_reminders';


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['date'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicle_id', 'title', 'date', 'days_alert', 'km', 'km_alert', 'is_active'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'vehicle_id'    => 'required',
        'title'         => 'required'
    );

    /**
     * Get all reminder notifications
     *
     * @param null $vehicleId
     * @return array|null
     */
    public static function getNotifications($vehicleId = null, $includeExpireds = true) {

        $reminders = Reminder::with('vehicle')
            ->filterSource()
            ->where('is_active', 1);

        if($vehicleId) {
            $reminders = $reminders->where('vehicle_id', $vehicleId);
        }

        $reminders = $reminders->get();

        if(!$reminders->isEmpty()) {

            $response = [];
            foreach ($reminders as $reminder) {
                $diffKm   = $reminder->km - $reminder->vehicle->counter_km;
                $diffDays = $reminder->date->diffInDays(\Carbon\Carbon::today());

                $vehicles = [];
                if($reminder->date) {
                    if ($reminder->date->gt(\Carbon\Carbon::today()) && $diffDays <= $reminder->days_alert) {
                        $response['warnings'][] = $reminder->toArray();
                        $vehicles[] = $reminder->vehicle_id;
                    } else if ($reminder->date->lte(\Carbon\Carbon::today())) {
                        $response['expireds'][] = $reminder->toArray();
                        $vehicles[] = $reminder->vehicle_id;
                    }
                }

                if(!in_array($reminder->vehicle_id, $vehicles)) { //evita ficar duplicado
                    if($reminder->km) {
                        if ($diffKm <= $reminder->km_alert && $reminder->km > $reminder->vehicle->counter_km) {
                            $response['warnings'][] = $reminder->toArray();
                        } else if ($reminder->vehicle->counter_km > $reminder->km) {
                            $response['expireds'][] = $reminder->toArray();
                        }
                    }
                }
            }

            if(!$includeExpireds) {
                unset($response['expireds']);
            }
            return $response;
        }

        return null;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function vehicle()
    {
        return $this->belongsTo('App\Models\FleetGest\Vehicle', 'vehicle_id');
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
    public function scopeFilterSource($query) {
        return $query->whereHas('vehicle', function($q){
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

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = empty($value) ? null : $value;
    }

    public function setKmAttribute($value)
    {
        $this->attributes['km'] = empty($value) ? null : $value;
    }

    public function setKmAlertAttribute($value)
    {
        $this->attributes['km_alert'] = empty($value) ? null : $value;
    }

    /**
     * Print shipments
     *
     * @param Request $request
     * @param null $reminders
     * @return \Illuminate\Http\Response|string
     */
    public static function printReminders($remindersIds, $outputFormat = 'I', $groupByCustomer = false)
    {

        $reminders = Reminder::with('vehicle')
            ->whereIn('id', $remindersIds)
            ->orderBy('date', 'asc')
            ->get();

        $docTitle     = "Resumo de Lembretes";

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 30,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'reminders'         => $reminders,
            'documentTitle'     => $docTitle,
            'documentSubtitle'  => '',
            'view'              =>  'admin.fleet.printer.reminders'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Lembretes.pdf', $outputFormat); //output to screen

        exit;
    }
}
