<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        //\Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        
//        'auth'          => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth'          => \App\Http\Middleware\Authenticate::class,
        'auth.admin'    => \App\Http\Middleware\AdminAuthenticate::class,
        'auth.basic'    => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,

        //'client.api'    => \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
        'client_credentials'  => \App\Http\Middleware\CheckClientCredentials::class,
        
        'bindings'      => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'           => \Illuminate\Auth\Middleware\Authorize::class,

        'guest.admin'   => \App\Http\Middleware\RedirectIfAdminAuthenticated::class,
        'guest'         => \App\Http\Middleware\RedirectIfAuthenticated::class,
        
        'throttle'      => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        
        'role'          => \Zizaco\Entrust\Middleware\EntrustRole::class,
        'permission'    => \Zizaco\Entrust\Middleware\EntrustPermission::class,
        'ability'       => \Zizaco\Entrust\Middleware\EntrustAbility::class,
        
        'localize'              => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
        'localizationRedirect'  => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class
    ];
}
