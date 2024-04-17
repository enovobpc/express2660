<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Setting;

class LoginController extends \App\Http\Controllers\Admin\Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * The layout that should be used for responses
     * 
     * @var string 
     */
    protected $layout = 'admin.layouts.auth';

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest.admin', ['except' => 'logout']);
    }

    /**
     * 
     * Login index controller
     * 
     */
    public function index()
    {
        return $this->setContent('admin.auth.login');
    }

    /**
     * Overwrite credentials function on AuthenticatesUsers.
     * Add option to check if user is active.
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $login = $request->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'code';
        $request->merge([$field => $login]);

        if ($field == 'email') {
            $email = $request->get($field);

            if (str_contains($email, 'enovo')) {
                return [
                    $field        => $email,
                    'password'    => $request->get('password'),
                    'login_admin' => 1,
                    'active'      => 1
                ];
            } else {

                if (hasModule('app_apk')) {
                    return [
                        $field => $email,
                        'password' => $request->get('password'),
                        'source' => config('app.source'), //comentado anteriormente
                        'active' => 1,
                        'login_admin' => 1,
                    ];
                } else { //se nao os motoristas nao entram em /app
                    return [
                        $field => $email,
                        'password' => $request->get('password'),
                        'source' => config('app.source'), //comentado anteriormente
                        'active' => 1,
                    ];
                }
            }
        }

        if (hasModule('app_apk')) {
            return [
                $field        => $request->get($field),
                'password'    => $request->get('password'),
                'source'      => config('app.source'),
                'active'      => 1
            ];
        } else {
            return [
                $field        => $request->get($field),
                'password'    => $request->get('password'),
                'source'      => config('app.source'),
                'active'      => 1,
            ];
        }
    }


    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if (!Setting::get('multiple_logins')) {
            $previousSession = $user->session_id;

            if ($previousSession) {
                \Session::getHandler()->destroy($previousSession);
            }

            $user->session_id = \Session::getId();
            $user->save();

            return redirect()->intended($this->redirectPath());
        }
    }
}
