<?php

namespace App\Http\Controllers\Website;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * The layout that should be used for responses
     * 
     * @var string 
     */
    protected $layout = 'layouts.master';

    /**
     * The sidebar menu option that should be used for responses
     *
     * @var string
     */
    protected $bodyClass = '';

    /**
     * The main menu option that should be used for responses
     * 
     * @var string 
     */
    protected $menuOption = 'home';
    
    /**
     * The sidebar menu option that should be used for responses
     * 
     * @var string
     */
    protected $sidebarActiveOption = '';

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)){
            $this->layout = view($this->layout);
        }
        
        view()->share('auth', Auth::guard('customer')->user());

    }

    public function callAction($method, $parameters)
    {
        $this->setupLayout();

        $response = call_user_func_array(array($this, $method), $parameters);

        if (is_null($response) && ! is_null($this->layout))
        {
            $response = $this->layout;
        }

        return $response;
    }
        
    /**
     * Set content used by the controller.
     * 
     * @param type $view
     * @param type $data
     * @return type
     */
    public function setContent($view, $data = [])
    {
        if (!is_null($this->layout))
        {
            view()->share('menuOption', $this->menuOption);
            view()->share('sidebarActiveOption', $this->sidebarActiveOption);
            view()->share('bodyClass', $this->bodyClass);
            return $this->layout->nest('child', $view, $data);
        }

        return view($view, $data);
    }

    /**
     * Set the layout used by the controller.
     *
     * @param $name
     * @return void
     */
    protected function setLayout($name)
    {
        $this->layout = $name;
    }
}
