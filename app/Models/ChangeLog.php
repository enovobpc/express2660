<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeLog extends BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logs';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'change_logs';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'source_id', 'action', 'old', 'new', 'user_id'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'source'    => 'required',
        'source_id' => 'required',
        'action'    => 'required',
        'old'       => 'required',
        'new'       => 'required',
        'user_id'   => 'required',
    );

    /**
     * Get changed fields
     *
     * @param $modelData
     * @param array $ignoredFields
     * @return array|bool
     */
    public static function getChanges($modelData, $ignoredFields = []) {

        $original = $modelData->getOriginal();
        $changes  = $modelData->isDirty() ? $modelData->getDirty() : false;

        foreach ($ignoredFields as $ignoredField) {
            unset($changes[$ignoredField]);
        }

        if($changes) {
            /*$keys = array_keys($changes);
            $originalValues = array_intersect_key($original, array_flip($keys));*/

            $keys = array_keys($changes);
            $allowed = $keys;
            $originalValues = array_filter(
                $original,
                function ($key) use ($allowed) {
                    return in_array($key, $allowed);
                },
                ARRAY_FILTER_USE_KEY
            );

            $arr = [
                'old' => $originalValues,
                'new' => $changes
            ];

            foreach($arr['old'] as $key => $old) {

                $oldVal = @$arr['old'][$key];
                $newVal = @$arr['new'][$key];

                if(is_numeric($oldVal)) {
                    $arr['old'][$key] = (float) $oldVal;
                }

                if(is_numeric($newVal)) {
                    $arr['new'][$key] = (float) $newVal;
                }

                if((empty($oldVal) && empty($newVal)) || $oldVal == $newVal) {
                    unset($arr['old'][$key], $arr['new'][$key]);
                }
            }

            if(empty($arr['old']) && empty($arr['new'])) {
                return false;
            }

            return $arr;
        }

        return false;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function source()
    {
        return $this->belongsTo($this->source, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
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
    public function setOldAttribute($value)
    {
        $this->attributes['old'] = empty($value) ? null : json_encode($value);
    }

    public function setNewAttribute($value)
    {
        $this->attributes['new'] = empty($value) ? null : json_encode($value);
    }

    public function getOldAttribute($value)
    {
       return json_decode($value);
    }

    public function getNewAttribute($value)
    {
        return json_decode($value);
    }
}
