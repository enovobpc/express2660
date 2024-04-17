<?php

namespace App\Http\Controllers\Admin;

use App\Models\Core\HelpcenterAuthToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use File;

class KnowledgeController extends \App\Http\Controllers\Admin\Controller {
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.admin');
    }

    /**
     * Redirect to helpcenter
     */
    public function helpcenter(Request $request, $target) {
        $targetUrl = HelpcenterAuthToken::redirect2Helpcenter($request, $target);
        return Redirect::to($targetUrl);
    }
}
