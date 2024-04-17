<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class WebserviceLog extends BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logs';
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'webservices_log';
    
    /**
     * Default status
     */
    const STATUS_SUCCESS = 'success';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR   = 'error';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'webservice', 'method', 'response', 'status'
    ];
    
    /**
     * Set webservice log
     * 
     * @param type $webservice
     * @param type $method
     * @param type $response
     * @param type $status
     */
    public static function set($webservice, $method, $response, $status = 'success', $duration = null) {
        
        $log = new self;
        $log->source = config('app.source');
        $log->webservice = $webservice;
        $log->method = $method;
        $log->response = $response;
        $log->status = $status;
        if($duration) {

        }
        $log->save();
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
    public function scopeFilterSource($query) {
        return $query->where('source', config('app.source'));
    }
}
