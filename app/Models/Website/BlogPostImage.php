<?php

namespace App\Models\Website;

use App\Models\Traits\FileTrait;

class BlogPostImage extends \App\Models\BaseModel {

    use FileTrait;

    public $timestamps = false;

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
    protected $table = 'blog_posts_images';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    
    /**
     * Default directory
     * 
     * @const string
     */
    const DIRECTORY = 'uploads/blog_posts';

    /**
     * Relationships
     */
    public function news() {
        return $this->belongsTo('App\Models\Website\BlogPost');
    }
    
}