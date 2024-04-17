<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Allowance extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_allowances';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'allowances';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year', 'month', 'operator_id', 'allowance_price', 'weekend_price', 'total_price', 'shipments', 'trips'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'year'  => 'required',
        'month' => 'required',
        'operator_id' => 'required',
    );

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
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }



    /**
     * Calculate delivery manifest allowances
     * 
     * @param Trip $trip 
     * @return array
     */
    public static function calculateTrip($trip) {
        $defaultReturn = [
            'allowances_price' => 0.0,
            'weekend_price'    => 0.0,
            'fuel_consumption' => 0.0
        ];

        //CALCULO CONSUMO COMBUSTÍVEL
        $fuelConsumption = 0.0;
        if ($trip->vehicle && $trip->start_kms && $trip->end_kms) {
            $vehicle = $trip->vehicleData;
            if ($vehicle && $vehicle->average_consumption) {
                $diffKms = $trip->end_kms - $trip->start_kms;

                $fuelConsumption = ($vehicle->average_consumption * $diffKms) / 100.0;
                $defaultReturn['fuel_consumption'] = $fuelConsumption;
            }
        }
        //--

        if (!$trip->start_date || !$trip->start_hour || !$trip->end_date || !$trip->end_hour) {
            return $defaultReturn;
        }

        //AJUDA DIÁRIA MINIMA
        $minimumDailyAllowance = 0.0;
        $lunchPrice = 9.90;
        if ($trip->start_country == 'pt' && $trip->end_country == $trip->start_country) {
            $minimumDailyAllowance = 23; //NACIONAL
            $lunchPrice = 8.40;
        } else if (in_array($trip->start_country, ['pt', 'es']) && in_array($trip->end_country, ['pt', 'es'])) {
            $minimumDailyAllowance = 26; //IBÉRICO
        } else if (!in_array($trip->start_country, ['pt', 'es']) || !in_array($trip->end_country, ['pt', 'es'])) {
            $minimumDailyAllowance = 36.4; //INTERNACIONAL
        }
        //--

        //CALCULO DIAS DE VIAGEM
        $startDate = Carbon::createFromTimeString($trip->start_date . ' ' . $trip->start_hour);
        $endDate   = Carbon::createFromTimeString($trip->end_date . ' ' . $trip->end_hour);
        $diffDate  = $endDate->diff($startDate);

        $daysDriving = $diffDate->d;
        if ($trip->start_date == $trip->end_date) {
            $daysDriving = 1;
        }
        //--

        //CALCULO REFEIÇÃO
        $mealsPrice = 0.0;
        if ($endDate->hour >= 6) {
            $mealsPrice += 2.9; //PEQUENO ALMOÇO
        }

        if ($endDate->hour >= 11) {
            $mealsPrice += $lunchPrice; //ALMOÇO
        }

        if ($endDate->hour >= 18) {
            $mealsPrice += $lunchPrice; //JANTAR
        }
        //--

        //CALCULO FIM DE SEMANAS
        $weekends = 0;
        $auxStartDate = $startDate;
        while ($auxStartDate <= $endDate) {
            if (in_array($auxStartDate->dayOfWeek, [6, 7])) {
                $weekends++;
                $auxStartDate->addDays(2);
            } else {
                $auxStartDate->addDays(1);
            }
        }
        //--

        return [
            'allowances_price' => round(($daysDriving * $minimumDailyAllowance) + $mealsPrice, 2),
            'weekend_price'    => round(($weekends * 157.5), 2),
            'fuel_consumption' => $fuelConsumption
        ];
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
    public function setShipmentsAttribute($value)
    {
        $this->attributes['shipments'] = empty($value) ? null : json_encode($value);
    }

    public function setTripsAttribute($value)
    {
        $this->attributes['trips'] = empty($value) ? null : json_encode($value);
    }

    public function getShipmentsAttribute()
    {
        return json_decode(@$this->attributes['shipments'], true);
    }

    public function getTripsAttribute()
    {
        return json_decode(@$this->attributes['trips'], true);
    }
}
