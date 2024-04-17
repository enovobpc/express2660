<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use View;

class LegalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}
    
    /**
     * Show legal notices index
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug = 'politica-privacidade')
    {   
        $include = 'legal.partials.'.snake_case(str_replace('-', '_', $slug));

        if (!View::exists($include)) {
            App::abort(404);
        }
        
        return $this->setContent('legal.index', compact('include', 'slug'));
    } 
}
