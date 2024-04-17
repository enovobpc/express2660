<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\Traits\FileTrait;

class Document extends \App\Models\BaseModel implements Sortable
{
    use SoftDeletes,
        FileTrait,
        SortableTrait, 
        Sluggable;


    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_website';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'documents';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'locales'
    ];
    
    /**
     * Default upload directory
     * 
     * @const string
     */
    const DIRECTORY = 'uploads/documents';
    
    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    
    /**
     * Default sort column
     * 
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];
    
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to easily re-use query logic in your models.
    | To define a scope, simply prefix a model method with scope.
    |
    */
    public function scopeIsVisible($query, $isVisible = true){
        return $this->where('is_visible', $isVisible);
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
    public function setLocalesAttribute($value)
    {
        $this->attributes['locales'] = empty($value) ? null : json_encode($value);
    }

    public function getLocalesAttribute()
    {
        return json_decode(@$this->attributes['locales'], true);
    }
}
