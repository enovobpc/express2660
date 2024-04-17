<?php

namespace App\Models\Email;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class MailingList extends \App\Models\BaseModel implements Sortable
{
    use SoftDeletes,
        SortableTrait;
    

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_mailing_list';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mailing_lists';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'source', 'name','emails', 'sort', 'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name' => 'required',
        'emails' => 'required',
    );

    /**
     * Default sort column
     * 
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];
}
