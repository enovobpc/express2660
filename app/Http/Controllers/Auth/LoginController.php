<?php

namespace App\Http\Controllers\Auth;

use App\Models\Service;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Lang, Auth;

class LoginController extends \App\Http\Controllers\Controller
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
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/area-cliente';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Custom login guard
     *
     * @return type
     */
    protected function guard()
    {
        return Auth::guard('customer');
    }

    /**
     *
     * Login index controller
     *
     */
    public function index()
    {
        return $this->setContent('auth.login');
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
        return [
            $this->username() => trim($request->get($this->username())),
            'password'    => trim($request->get('password')),
            'active'      => 1,
            'source'      => config('app.source')
        ];
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->route('account.login')
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([$this->username() => Lang::get('auth.failed')]);
    }
}
