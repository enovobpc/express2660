<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Dimsav\Translatable\Translatable;
use Date;

class BlogPost extends \App\Models\BaseModel
{
    use SoftDeletes,
        FileTrait,
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
    protected $table = 'blog_posts';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'summary', 'content', 'date', 'is_highlight', 'is_published', 'slug'
    ];
    
   /**
     * The attributes that are translated
     *
     * @var array
     */
    public $translatedAttributes = [
        'title', 'summary', 'content', 'slug'
    ];
    
    /**
     * Default upload directory
     * 
     * @const string
     */
    const DIRECTORY = 'uploads/blog_posts';
    

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = [
        'image' => 'image',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function images() {
        return $this->hasMany('App\Models\Website\BlogPostImage', 'blog_post_id');
    }

    public function tags() {
        return $this->hasMany('App\Models\Website\BlogPostTag', 'blog_post_id');
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
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = trim($value);
    }
    
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = trim($value);
    }

    public function getFilepathAttribute($value)
    {
        return empty($value) ? null : $value;
    }
    
    public function getDateAttribute($value)
    {
        return new Date($value);
    }
    
    public function getCreatedAtAttribute($value)
    {
        return new Date($value);
    }
}
