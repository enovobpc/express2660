<?php 

namespace App\Models;

use Auth;
use Zizaco\Entrust\EntrustRole;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Cviebrock\EloquentSluggable\Sluggable;

class Role extends EntrustRole implements Sortable {
    
    use Sluggable, SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_roles';

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'name' => ['source' => 'display_name']
        ];
    }

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];


    /**
     * List roles
     *
     * @return mixed
     */
    public static function listRoles() {

        if(Auth::user()->hasRole([config('permissions.role.admin')])) {
            $roles = Role::filterSource()
                ->where('display_name', '<>', config('permissions.role.operator'))
                ->ordered()
                ->pluck('display_name', 'id')
                ->toArray();
        } else {
            $exceptions[] = config('permissions.role.admin');

            $roles = Role::filterSource()
                ->whereNotIn('name', $exceptions)
                ->ordered()
                ->pluck('display_name', 'id')
                ->toArray();
        }

        return $roles;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    | Eloquent provides a convenient way to transform your model attributes when
    | getting or setting them. Simply define a 'getFooAttribute' method on your model
    | to declare an accessor. Keep in mind that the methods should follow camel-casing,
    | even though your database columns are snake-case.
    |
    */
    public function scopeFilterSource($query) {
        return $query->where(function($q){
           $q->whereNull('source')
             ->orWhere('source', config('app.source'));
        });
    }

}