<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Watson\Rememberable\Rememberable;
use Auth;

class BaseModel extends Model {
    
    use Rememberable;
        
    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array();
    
    /**
     * Validator friendly attributes
     * 
     * @var array 
     */
    protected $customAttributes = array();
    
    /**
     * Validator custom messages
     * 
     * @var array 
     */
    protected $customMessages = array();

    /**
     * Validator errors
     * 
     * @var array 
     */
    protected $errors;

    /**
     * Validate rules for this model
     * 
     * @param array $data
     * @return boolean
     */
    public function validate($data)
    {   

        $rules = array_intersect_key($this->rules, $data);

        // Make a new validator object
        $validator = Validator::make($data, $rules, $this->customMessages, $this->customAttributes);

        // Check for failure
        if ($validator->fails())
        {
            // Set errors and return false
            $this->errors = $validator->errors();
            return false;
        }

        // Validation pass, return true 
        return true;
    }

    /**
     * Get errors from validator
     * 
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }    
    
    /**
     * Limit query to user agencies
     * Atenção! Existe uma cópia desta função no modelo "Customers"
     * 
     * @return type
     */
    public function scopeFilterAgencies($query){
    
        $user = Auth::user();
        $agencies = $user->agencies;

        if(!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
            return $query->where(function($q){
                $q->whereNull('agency_id');
                $q->orWhereIn('agency_id', Auth::user()->agencies);
            });
        }
    }
    
    public function scopeFilterSource($query) {
        return $query->where('source', config('app.source'));
    }

}