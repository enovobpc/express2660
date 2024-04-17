<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\SoftDeletes;
use Dimsav\Translatable\Translatable;
use App\Models\Traits\FileTrait;

class PageSectionContent extends \App\Models\BaseModel
{
    use SoftDeletes,
        Translatable,
        FileTrait;

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
    protected $table = 'pages_sections_contents';
    
    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['page_section_id', 'block', 'content_type', 'include_view','target_blank', 'constrain_proportions','autoplay', 'width', 'height', 'preview',
                            'btn_primary_title', 'btn_primary_url', 'btn_primary_background', 'btn_primary_color',
                            'btn_secundary_title', 'btn_secundary_url', 'btn_secundary_background', 'btn_secundary_color'];
    
    /**
     * The attributes that are translated
     *
     * @var array
     */
    public $translatedAttributes = ['title', 'subtitle', 'content', 'embed', 'url',
                                    'btn_primary_title', 'btn_primary_url', 'btn_primary_background', 'btn_primary_color',
                                    'btn_secundary_title', 'btn_secundary_url', 'btn_secundary_background', 'btn_secundary_color'];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = [
        'block'        => 'required',
        'content_type' => 'required',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function section()
    {
        return $this->belongsTo('App\Models\Website\PageSection', 'page_section_id');
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
    public function setWidthAttribute($value)
    {
        return $this->attributes['width'] = empty($value) ? null : $value;
    }

    public function setHeightAttribute($value)
    {
        return $this->attributes['height'] = empty($value) ? null : $value;
    }
}
