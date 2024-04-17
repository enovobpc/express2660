<?php

namespace App\Models\Website;

class SliderTranslation extends \App\Models\BaseModel
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
    protected $table = 'sliders_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'caption', 'subcaption', 'url'
    ];
}
