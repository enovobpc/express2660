<?php

namespace App\Models\Website;


class BlogPostTag extends \App\Models\BaseModel {

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
    protected $table = 'blog_posts_tags';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    

    /**
     * Relationships
     */
    public function article() {
        return $this->belongsTo('App\Models\Website\BlogPost');
    }
    
}