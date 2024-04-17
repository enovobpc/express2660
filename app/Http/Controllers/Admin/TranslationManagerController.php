<?php

namespace App\Http\Controllers\Admin;

use Barryvdh\TranslationManager\Manager;

class TranslationManagerController extends \Barryvdh\TranslationManager\Controller {
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;

        $this->middleware(['ability:' . config('permissions.role.admin') . ',admin_translations']);
    }
    
    

}
