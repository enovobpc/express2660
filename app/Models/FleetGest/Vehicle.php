<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth, Jenssegers\Date\Date, DB, Setting, Mail;
use Mpdf\Mpdf;

class Vehicle extends \App\Models\BaseModel
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
    const CACHE_TAG = 'cache_fleet_vehicles';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_vehicles';


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['registration_date', 'manufacturing_date', 'insurance_date', 'ipo_date', 'iuc_date', 'tachograph_date'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'agency_id', 'trailer_id', 'name', 'brand_id', 'model_id', 'version', 'license_plate',
        'code', 'reference', 'fuel', 'power', 'chassis', 'class', 'km', 'km_initial',
        'average_consumption', 'manufacturing_date', 'registration_date',
        'next_review_date', 'next_review_km', 'status', 'operator_id', 'ipo_date', 'iuc_date', 'tachograph_date',
        'obs', 'gross_weight', 'usefull_weight', 'is_default', 'type', 'trailer_type', 'insurance_date',
        'axles', 'axles_distance', 'transmission', 'buy_date', 'box_max_width',
        'engine_capacity', 'category', 'color', 'seats', 'contract', 'co2', 'deposit_capacity',
        'width', 'height', 'length', 'tires_front', 'tires_rear', 'tires_others', 'increase_roof',
        'titular_name', 'titular_address', 'titular_city', 'titular_zip_code', 'titular_country',
        'proprietary_name', 'proprietary_address', 'proprietary_city', 'proprietary_zip_code', 'proprietary_country', 'insurer', 'insurer_number',
        'gps_id', 'last_location', 'latitude', 'longitude', 'is_ignition_on', 'speed', 'fuel_level', 'gps_zip_code',
        'gps_city', 'gps_country', 'fuel_1', 'pin_1', 'fuel_2', 'pin_2', 'fuel_3', 'pin_3', 'provider_id', 'assistants'
    ];

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/fleet';

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name'          => 'required',
        'brand_id'      => 'required',
        'licence_plate' => 'required'
    );

    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'name'          => 'Designação',
        'brand'         => 'Marca',
        'licence_plate' => 'Matrícula'
    );

    /**
     * Return next model ID
     * @param $query
     * @return mixed
     */
    public function nextId()
    {
        return Vehicle::filterSource()
            ->filterAgencies()
            ->where('id', '>', $this->id)
            ->min('id');
    }

    /**
     * Return previous model ID
     * @param $query
     * @return mixed
     */
    public function previousId()
    {
        return Vehicle::filterSource()
            ->filterAgencies()
            ->where('id', '<', $this->id)
            ->max('id');
    }

    public function assistants()
    {
        $assistants = $this->assistants;
        if(!empty($assistants)) {
            return User::whereIn('id', $assistants)->get();
        }

        return [];
    }

    /**
     * Print validities
     *
     * @param $ids
     * @param string $outputFormat
     * @return mixed
     */
    public static function printValidities($startDate, $endDate, $otherData = [], $outputFormat = 'I')
    {

        ini_set("memory_limit", "-1");

        $params = $otherData;
        $params['start_date'] = $startDate;
        $params['end_date']   = $endDate;

        $vehicles  = Vehicle::getNotifications(null, $params);
        $startDate = new Date($startDate);
        $endDate   = new Date($endDate);

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
            'vehicles'          => $vehicles,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'documentTitle'     => 'Validades a Expirar',
            'documentSubtitle'  => 'Resumo de ' . $startDate->format('Y-m-d') . ' até ' . $endDate->format('Y-m-d'),
            'view'              => 'admin.fleet.printer.validities'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Validades a Expirar.pdf', $outputFormat); //output to screen

        exit;
    }

    /**
     * Return all notifications grouped by vehicle
     * @param null $vehicleId
     * @param array $params
     * @return array|null
     */
    public static function getNotifications($vehicleId = null, $params = [])
    {

        $today     = Date::today();
        $startDate = @$params['start_date'] ? $params['start_date'] : $today->format('Y-m-d');
        $endDate   = @$params['end_date'] ? $params['end_date'] : Date::today()->addDays(30)->format('Y-m-d');
        $includeExpireds = isset($params['expireds']) ? @$params['expireds'] : false;

        //get all reminders
        $allReminders = Reminder::getNotifications($vehicleId, $includeExpireds);
        $remindersVehiclesIds = [];
        if ($allReminders) {
            foreach ($allReminders as $key => $items) {
                $ids = array_column($items, 'vehicle_id');
                $remindersVehiclesIds = array_merge($remindersVehiclesIds, $ids);
            }
        }

        //get all vehicles
        $vehicles = Vehicle::with('operator', 'brand')
            ->with(['reminders' => function ($q) {
                $q->where('is_active', 1);
            }])
            ->filterSource()
            ->isActive()
            ->where(function ($q) use ($startDate, $endDate, $remindersVehiclesIds) {
                $q->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('ipo_date', [$startDate, $endDate]);
                    $q->orWhereBetween('iuc_date', [$startDate, $endDate]);
                    $q->orWhereBetween('insurance_date', [$startDate, $endDate]);
                    $q->orWhereBetween('tachograph_date', [$startDate, $endDate]);
                });
                $q->orWhereIn('id', $remindersVehiclesIds); //inclui todos os veiculos que tem lembretes criados
            });

        if ($vehicleId) {
            $vehicles = $vehicles->where('id', $vehicleId);
        }

        $vehicles = $vehicles->orderBy('name', 'asc')
            ->get([
                'license_plate',
                'name',
                'ipo_date',
                'iuc_date',
                'insurance_date',
                'tachograph_date',
                'type',
                'km',
                'category',
                'brand_id',
                'id'
            ]);

        $resultList = [];
        $startDate = new Date($startDate);
        $endDate   = new Date($endDate);
        $totalWarnings = 0;
        $totalExpireds = 0;
        foreach ($vehicles as $vehicle) {

            $countWarnings = 0;
            $countExpireds = 0;
            $reminders = [];


            //dd($startDate .' - '. $endDate);
            if (!empty($vehicle->ipo_date) && ($vehicle->ipo_date->between($startDate, $endDate) || ($includeExpireds && $vehicle->ipo_date->lt($today)))) {

                $diffDays = $vehicle->ipo_date->diffInDays($today);
                if ($vehicle->ipo_date->lt($today)) {
                    $diffDays = -1 * $diffDays;
                }

                if ($diffDays >= 0) {
                    $status = 'warning';
                    $countWarnings++;
                } else {
                    $status = 'expired';
                    $countExpireds++;
                }

                $reminders[] = [
                    'type'       => 'ipo',
                    'title'      => 'Inspeção Periódica Obrigatória',
                    'date'       => $vehicle->ipo_date,
                    'km'         => $vehicle->km_counter,
                    'days_alert' => 30,
                    'km_alert'   => 0,
                    'days_left'  => $diffDays,
                    'km_left'    => 0,
                    'obs'        => '',
                    'status'     => $status,
                ];
            }

            if (!empty($vehicle->iuc_date) && ($vehicle->iuc_date->between($startDate, $endDate) || ($includeExpireds && $vehicle->iuc_date->lt($today)))) {

                $diffDays = $vehicle->iuc_date->diffInDays($today);
                if ($vehicle->iuc_date->lt($today)) {
                    $diffDays = -1 * $diffDays;
                }

                if ($diffDays >= 0) {
                    $status = 'warning';
                    $countWarnings++;
                } else {
                    $status = 'expired';
                    $countExpireds++;
                }

                $reminders[] = [
                    'type'       => 'iuc',
                    'title'      => 'Imposto Único de Circulação',
                    'date'       => $vehicle->iuc_date,
                    'km'         => $vehicle->km_counter,
                    'days_alert' => 30,
                    'km_alert'   => 0,
                    'days_left'  => $diffDays,
                    'km_left'    => 0,
                    'obs'        => '',
                    'status'     => $status
                ];
            }

            if (!empty($vehicle->insurance_date) && ($vehicle->insurance_date->between($startDate, $endDate)  || ($includeExpireds && $vehicle->insurance_date->lt($today)))) {
                $diffDays = $vehicle->insurance_date->diffInDays($today);
                if ($vehicle->insurance_date->lt($today)) {
                    $diffDays = -1 * $diffDays;
                }

                if ($diffDays >= 0) {
                    $status = 'warning';
                    $countWarnings++;
                } else {
                    $status = 'expired';
                    $countExpireds++;
                }

                $reminders[] = [
                    'type'       => 'insurance',
                    'title'      => 'Seguro automóvel',
                    'date'       => $vehicle->insurance_date,
                    'km'         => $vehicle->km_counter,
                    'days_alert' => 30,
                    'km_alert'   => 0,
                    'days_left'  => $diffDays,
                    'km_left'    => 0,
                    'obs'        => '',
                    'status'     => $status
                ];
            }

            if (!empty($vehicle->tachograph_date) && ($vehicle->tachograph_date->between($startDate, $endDate)  || ($includeExpireds && $vehicle->tachograph_date->lt($today)))) {
                $diffDays = $vehicle->tachograph_date->diffInDays($today);
                if ($vehicle->tachograph_date->lt($today)) {
                    $diffDays = -1 * $diffDays;
                }

                if ($diffDays >= 0) {
                    $status = 'warning';
                    $countWarnings++;
                } else {
                    $status = 'expired';
                    $countExpireds++;
                }

                $reminders[] = [
                    'type'       => 'tachograph',
                    'title'      => 'Aferição Tacógrafo',
                    'date'       => $vehicle->tachograph_date,
                    'km'         => $vehicle->km_counter,
                    'days_alert' => 30,
                    'km_alert'   => 0,
                    'days_left'  => $diffDays,
                    'km_left'    => 0,
                    'obs'        => '',
                    'status'     => $status
                ];
            }

            if ($vehicle->reminders) {
                foreach ($vehicle->reminders as $reminder) {
                    $today    = new Date();
                    $diffKm   = $reminder->km - $reminder->vehicle->counter_km;
                    $diffDays = $reminder->date->diffInDays($today);

                    if ($reminder->date->lt($today)) {
                        $diffDays = -1 * $diffDays;
                    }

                    if ($diffDays < 0 || $diffKm < 0) {
                        $status = 'expired';
                        $countExpireds++;
                    } else {
                        $status = 'warning';
                        $countWarnings++;
                    }

                    $reminders[] = [
                        'type'       => 'reminder',
                        'title'      => $reminder->title,
                        'date'       => $reminder->date,
                        'km'         => $reminder->km,
                        'days_alert' => $reminder->days_alert,
                        'km_alert'   => $reminder->km_alert,
                        'days_left'  => $diffDays,
                        'km_left'    => ($reminder->type == 'tachograph') ? 0 : $diffKm,
                        'obs'        => $reminder->obs,
                        'status'     => $status
                    ];
                }
            }

            $vehicle->notifications = $reminders;
            $vehicle->notifications_warning = $countWarnings;
            $vehicle->notifications_expired = $countExpireds;
            $totalWarnings += $countWarnings;
            $totalExpireds += $countExpireds;
            $resultList[] = $vehicle;
        }

        if (@$params['return_totals']) {
            return [
                'vehicles' => collect($resultList),
                'warnings' => $totalWarnings,
                'expireds' => $totalExpireds
            ];
        }

        if ($vehicleId) {
            return @$resultList[0];
        }

        $resultList = collect($resultList);
        return $resultList;
    }

    /**
     * Return all notifications grouped by vehicle
     * @param null $vehicleId
     * @param array $params
     * @return array|null
     */
    public static function sendNotificationsEmail($vehicleId = null, $params = [], $emails = null)
    {

        $vehicles = self::getNotifications($vehicleId, $params);

        if ($vehicles) {

            if (empty($emails)) {
                $emails = \App\Models\User::where('source', config('app.source'))
                    ->whereHas('roles.perms', function ($query) {
                        $query->whereIn('name', ['fleet_vehicles']);
                    })
                    ->pluck('email')
                    ->toArray();

                $emails = implode(';', $emails);
            }

            $emails = validateNotificationEmails($emails);
            $emails = $emails['valid'];

            if (!empty($emails)) {

                Mail::send('emails.fleet.notify_validities', compact('vehicles'), function ($message) use ($emails) {
                    $message->to($emails);
                    $message->subject('Viaturas - Alerta de validades e manutenções');
                });

                if (count(Mail::failures()) > 0) {
                    throw new \Exception('Falhou o envio do e-mail. Tente de novo.');
                }

                return true;
            }
        }

        return false;
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
    public function scopeIsActiveSource($query) {
        return $query->whereNotIn('status', ['inactive', 'sould', 'slaughter']);
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function agency()
    {
        return $this->belongsTo('App\Models\Agency', 'agency_id');
    }

    public function trailer()
    {
        return $this->belongsTo('App\Models\FleetGest\Vehicle', 'trailer_id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\FleetGest\Brand', 'brand_id');
    }

    public function model()
    {
        return $this->belongsTo('App\Models\FleetGest\BrandModel', 'model_id');
    }

    public function fuelLog()
    {
        return $this->hasMany('App\Models\FleetGest\FuelLog', 'vehicle_id');
    }

    public function reminders()
    {
        return $this->hasMany('App\Models\FleetGest\Reminder', 'vehicle_id');
    }

    public function checklist_answers()
    {
        return $this->hasMany('App\Models\FleetGest\ChecklistAnswer', 'vehicle_id');
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

    public function scopeIsActive($query, $isActive = true)
    {

        if ($isActive) {
            return $query->whereNotIn('status', ['inactive', 'sold', 'slaughter']);
        } else {
            return $query->whereIn('status', ['inactive', 'sold', 'slaughter']);
        }
    }

    public function getBalanceTotal($startDate = null, $endDate = null)
    {

        $today = Date::today();

        if (empty($endDate)) {
            $endDate = $today->format('Y-m-d');
        }

        if (empty($startDate)) {
            $startDate = $today->subDay(30)->format('Y-m-d');
        }

        // $gains = \App\Models\Shipment::whereBetween('date', [$startDate, $endDate])
        //     ->where('vehicle', $this->license_plate)
        //     ->select([DB::raw('(sum(total_price) + sum(total_expenses)) as total')])
        //     ->first();
        
        $total_price = \App\Models\Shipment::whereBetween('date', [$startDate, $endDate])
            ->where('vehicle', $this->license_plate)
            ->select([DB::raw('sum(total_price) as total')])
            ->first();
        
        $total_expenses = \App\Models\Shipment::whereBetween('date', [$startDate, $endDate])
            ->where('vehicle', $this->license_plate)
            ->select([DB::raw('sum(total_expenses) as total')])
            ->first();
        $gains = 0;
        
        if($total_price != null){
            $gains += $total_price->total;
        }
        
        if($total_expenses != null){
            $gains += $total_expenses->total;
        }
        

        $costs = Cost::where('vehicle_id', $this->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy(DB::raw('CONCAT(type, DATE_FORMAT(date, \'%Y-%m\'))'))
            ->orderBy('date', 'asc')
            ->get([
                'type',
                DB::raw('sum(total) as total')
            ]);

        $costsTotal  = $costs->sum('total');
        //$balance = @$gains->total - $costsTotal;
        
        $balance = $gains - $costsTotal;

        return [
            'gains'   => $gains,
            'costs'   => $costsTotal,
            'balance' => $balance,
            'detail'  => $costs->toArray()
        ];
    }

    /**
     * Get vehicle statistics
     *
     * @param null $startDate
     * @param null $endDate
     * @return array
     */
    public static function getStatistics($startDate = null, $endDate = null, $vehicleId = null)
    {

        $today = Date::today();

        $vehicle = Vehicle::find($vehicleId);

        if (empty($endDate)) {
            $endDate = $today->format('Y-m-d');
        }

        if (empty($startDate)) {
            $startDate = $today->subDay(30)->format('Y-m-d');
        }


        $gains = \App\Models\Shipment::whereBetween('date', [$startDate, $endDate])
            ->where('vehicle', $vehicle->license_plate)
            ->groupBy(DB::raw('DATE_FORMAT(date, \'%Y-%m\')'))
            ->select([
                DB::raw('(sum(total_price) + sum(total_expenses)) as total'),
                DB::raw('DATE_FORMAT(date, \'%Y-%m\') as label'),
            ])
            ->get();

        $gains = $gains->groupBy('label');

        $costs = Cost::where('vehicle_id', $vehicle->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy(DB::raw('CONCAT(type, DATE_FORMAT(date, \'%Y-%m\'))'))
            ->orderBy('date', 'asc')
            ->get([
                'type',
                DB::raw('DATE_FORMAT(date, \'%Y-%m\') as label'),
                DB::raw('sum(total) as total')
            ]);
        $costs = $costs->groupBy('label');


        $arr = [];
        foreach ($costs as $key => $cost) {

            $data = $cost->groupBy('type')->toArray();

            $fuel        = (float) @$data['gas_station'][0]['total'];
            $maintenance = (float) @$data['maintenance'][0]['total'];
            $expenses    = (float) @$data['expenses'][0]['total'];
            $tolls       = (float) @$data['tolls'][0]['total'];
            $gains       = (float) @$gains[$key][0]['total'];

            $costs   = $fuel + $maintenance + $expenses + $tolls + $gains;
            $balance = $gains - $costs;

            $arr[$key] = [
                'label'       => $key,
                'gains'       => $gains,
                'costs'       => $costs,
                'balance'     => $balance,
                'fuel'        => $fuel,
                'maintenance' => $maintenance,
                'expenses'    => $expenses,
                'tolls'       => $tolls
            ];
        }

        return $arr;
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


    public function getConsumptionColorAttribute($value)
    {
        $diff = $this->attributes['counter_consumption'] - $this->attributes['average_consumption'];
        $avg  = $this->attributes['average_consumption'];

        $class = 'text-green';
        if ($this->attributes['counter_consumption'] > $avg && $diff <= 1) {
            $class = 'text-yellow';
        } else if ($this->attributes['counter_consumption'] > $avg) {
            $class = 'text-red';
        }

        return $class;
    }

    public function setAssistantsAttribute($value)
    {
        $this->attributes['assistants'] = empty($value) ? null : json_encode($value);
    }

    public function getAssistantsAttribute($value)
    {
        return empty($value) ? [] : array_map('intval', json_decode($value, true));
    }

    public function setLicensePlateAttribute($value)
    {
        $this->attributes['license_plate'] = strtoupper($value);
    }

    public function setModelIdAttribute($value)
    {
        $this->attributes['model_id'] = empty($value) ? null : $value;
    }

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setRegistrationDateAttribute($value)
    {
        $this->attributes['registration_date'] = empty($value) ? null : $value;
    }

    public function setManufacturingDateAttribute($value)
    {
        $this->attributes['manufacturing_date'] = empty($value) ? null : $value;
    }

    public function setBuyDateAttribute($value)
    {
        $this->attributes['buy_date'] = empty($value) ? null : $value;
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = empty($value) ? null : $value;
    }

    public function setNextReviewDateAttribute($value)
    {
        $this->attributes['next_review_date'] = empty($value) ? null : $value;
    }

    public function setKmInitialAttribute($value)
    {
        $this->attributes['km_initial'] = empty($value) ? null : $value;
    }

    public function setPowerAttribute($value)
    {
        $this->attributes['power'] = empty($value) ? null : $value;
    }

    public function setAverageConsumptionAttribute($value)
    {
        $this->attributes['average_consumption'] = empty($value) ? null : $value;
    }

    public function setNextReviewKmAttribute($value)
    {
        $this->attributes['next_review_km'] = empty($value) ? null : $value;
    }

    public function getMarkerIconAttribute($value)
    {
        $icon = asset('assets/maps/vehicle_off.png');
        if ($this->is_ignition_on) {
            $icon = asset('assets/maps/vehicle_on.png');
        }
        return $icon;
    }

    public function getMarkerHtmlAttribute($value)
    {
        $vehicle = $this;
        /*$html = view('admin.maps.partials.vehicle_infowindow', compact('vehicle'))->render();
        //$html = preg_replace('~>\s+<~', '><', $html);*/

        $url = '/uploads/fleet_brands/' . str_slug(@$vehicle->brand->name) . '.jpg';

        $html = '<div class="m-b-5 w-250px">';
        $html .= '<div style="width: 50px; float: left">';
        $html .= '<img src="' . asset($vehicle->filepath ? $vehicle->filepath : $url) . '" style="height: 40px;padding: 10px; border: 1px solid #ddd; margin-bottom: 4px;"/>';
        $html .= '</div>';
        $html .= '<div style="width: 190px; float: left">';
        $html .= '<div class="bold m-b-5">' . $vehicle->name . '</div>';

        if ($vehicle->is_ignition_on) {
            $html .= '<i class="fas fa-circle text-green"></i>';
        } else {
            $html .= '<i class="fas fa-circle text-muted"></i>';
        }
        $html .= '<span> ' . $vehicle->license_plate . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vmap-local">';
        $html .= '<i class="flag-icon flag-icon-' . $vehicle->gps_country . '"></i><span> ' . $vehicle->gps_city . '</span>';
        $html .= '</div>';
        $html .= '<table class="table table-condensed m-0">';
        $html .= '<tr>';
        $html .= '<td><i class="fas fa-fw fa-tachometer-alt"></i> Velocidade</td>';
        $html .= '<td>' . $vehicle->speed . 'km/h</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><i class="fas fa-fw fa-gas-pump"></i> Depósito</td>';
        $html .= '<td>' . $vehicle->fuel_level_html . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><i class="fas fa-fw fa-road"></i> Quilómetros</td>';
        $html .= '<td>' . $vehicle->counter_km . 'km</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><i class="fas fa-fw fa-clock"></i> Último Registo</td>';
        $html .= '<td>Há ' . human_time($vehicle->last_location) . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';


        return $html;
    }

    public function getFuelLevelHtmlAttribute($value) {
        if (Setting::get('gps_gateway') == 'Cartrack') {
            return $this->fuel_level . 'l';
        }

        return $this->fuel_level . '%';
    }

}
