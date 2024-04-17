<?php

namespace App\Models\Website;

class PageSectionContentTranslation extends \App\Models\BaseModel
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
    protected $table = 'pages_sections_contents_translations';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'subtitle', 'content', 'embed', 'url',
        'btn_primary_title', 'btn_primary_url', 'btn_primary_background', 'btn_primary_color',
        'btn_secundary_title', 'btn_secundary_url', 'btn_secundary_background', 'btn_secundary_color'
    ];
}
