<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\SoftDeletes;
use Dimsav\Translatable\Translatable;
use App\Models\Traits\FileTrait;
use Auth, File;

class Page extends \App\Models\BaseModel
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
    protected $table = 'pages';

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
    protected $fillable = ['code', 'url', 'title', 'description', 'meta_title', 'meta_description', 'css', 'js','published', 'show_title', 'show_breadcrumb'];
    
    /**
     * The attributes that are translated
     *
     * @var array
     */
    public $translatedAttributes = ['url', 'title', 'description', 'meta_title', 'meta_description'];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = [
        'url'   => 'required',
        'title' => 'required'
    ];

    /**
     * Save storage routes
     */
    public function storeRoutes() {

        $path = storage_path() . '/pages_routes.json';

        $pages = Page::with('translations')->get();

        $content = [];
        foreach ($pages as $page) {
            $locales = $page->translations->groupBy('locale')->toArray();

            foreach ($locales as $locale => $value) {
                $url  = $value[0]['url'];
                @$content[$locale][$page->code ? $page->code : $page->id] = $url;
            }
        }

        File::put($path, json_encode($content));
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function sections()
    {
        return $this->hasMany('App\Models\Website\PageSection', 'page_id');
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
    public function scopePublished($query, $published = true){
        if(!Auth::check()) {
            return $query->where('published', $published);
        }
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
