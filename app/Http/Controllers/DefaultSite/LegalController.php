<?php

namespace App\Http\Controllers\DefaultSite;

use App, View;

class LegalController extends \App\Http\Controllers\Controller
{

    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.default';

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
        
        return $this->setContent('default.legal', compact('include', 'slug'));
    } 
}
