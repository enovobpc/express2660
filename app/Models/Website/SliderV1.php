<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use App\Models\Traits\FileTrait;

class SliderV1 extends \App\Models\BaseModel implements Sortable
{
    use SoftDeletes,
        FileTrait,
        SortableTrait;

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
    protected $table = 'sliders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'caption', 'subcaption', 'url', 'target_blank', 'visible', 'source'
    ];

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/sliders';

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = [
        'image' => 'required|image',
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

    public function scopeVisible($query) {
        return $query->where('visible', true);
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
}
