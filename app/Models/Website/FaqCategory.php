<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Dimsav\Translatable\Translatable;
use App;

class FaqCategory extends \App\Models\BaseModel implements Sortable
{
    use SoftDeletes,
        SortableTrait,
        Translatable;

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
    protected $table = 'faqs_categories';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['icon', 'sort', 'name', 'slug', 'is_visible'];
    
    /**
     * The attributes that are translated
     *
     * @var array
     */
    public $translatedAttributes = ['name', 'slug'];
    
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
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function faqs()
    {
        return $this->hasMany('App\Models\Website\Faq', 'faq_category_id');
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
    public function scopeVisible($query, $isVisible = true){
        return $query->whereHas('translations', function($q) use($isVisible) {
            $q->where('is_visible', $isVisible);
            $q->where('locale', App::getLocale());
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
}
