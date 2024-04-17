<?php

namespace App\Models\Website;

class PageTranslation extends \App\Models\BaseModel
{

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
    protected $table = 'pages_translations';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url', 'title', 'description', 'meta_title', 'meta_description'
    ];
}
