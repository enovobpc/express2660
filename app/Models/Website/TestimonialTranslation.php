<?php

namespace App\Models\Website;

class TestimonialTranslation extends \App\Models\BaseModel
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
    protected $table = 'testimonials_translations';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message', 'summary', 'author_role'
    ];
}
