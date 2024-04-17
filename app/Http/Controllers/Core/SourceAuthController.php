<?php

namespace App\Http\Controllers\Core;

use App;
use App\Models\Core\Source;
use App\Models\Core\SourceAuthToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class SourceAuthController extends \App\Http\Controllers\Admin\Controller {
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Set auth to switch between applications
     * 
     * @return type
     */
    public function auth(Request $request, $target)
    {  

        if(empty(Auth::user()) && !Auth::user()->isAdmin()) {
            return App::abort(404);
        }

        if($target == config('app.source') && config('app.env') != 'local') {
            return Redirect::back();
        }

        $target = Source::whereSource($target)->firstOrFail();

        $switcher = SourceAuthToken::firstOrNew([
            'source' => config('app.source'),
            'target' => $target->source
        ]);

        $switcher->source  = config('app.source');
        $switcher->target  = $target->source;
        $switcher->user_id = Auth::user()->id;
        $switcher->email   = Auth::user()->email;
        $switcher->hash    = str_random(50);
        $switcher->save();

        return Redirect::to($target->url . '/core/remote/login/' . $switcher->hash);
    }


    /**
     * Set login into app
     *
     * @return type
     */
    public function login(Request $request, $hash)
    {

        $switcher = SourceAuthToken::where('hash', $hash)->first();

        if(!$switcher || $switcher->target != config('app.source')) {
            return App::abort(403);
        }

        if($switcher->email) {
            $user = User::where('email', $switcher->email)->first();
        } else {
            $user = User::where('id', $switcher->user_id)->first();
        }

        if(empty($user)) {
            App::abort(404);
        }

        Auth::login($user);

        $switcher->forceDelete();

        return Redirect::route('admin.dashboard');
    }


}
