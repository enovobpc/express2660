<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterSubscriber extends \App\Models\BaseModel
{

    use SoftDeletes;

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
    protected $table = 'newsletter_subscribers';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'hash', 'active'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'email' => 'required',
        'hash'  => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'email' => 'E-mail',
    );

}
