<?php

namespace App\Models\CustomerSupport;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth;


class TicketAttachment extends \App\Models\BaseModel
{
    use SoftDeletes,
        FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_support_tickets_attachments';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_support_tickets_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ticket_id', 'name'];


    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/customer_support';

    /**
     * Validator rules
     *
     * @var array
     */
    public $rules = [
        'name' => 'required',
        'file' => 'required',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function ticket()
    {
        return $this->belongsTo('App\Models\CustomerSupport\Ticket', 'ticket_id');
    }

}
