<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'name' => env('APP_TITLE', 'Plataforma Logística - Demonstração'),

    /*
    |--------------------------------------------------------------------------
    | Application core
    |--------------------------------------------------------------------------
    */
    'core' => env('APP_CORE'),

    /*
    |--------------------------------------------------------------------------
    | Application Source
    |--------------------------------------------------------------------------
    */
    'source' => env('APP_SOURCE'),

    /*
    |--------------------------------------------------------------------------
    | Application Layout Style
    |--------------------------------------------------------------------------
    */
    'style_class' => env('APP_STYLE_CLASS'),

    /*
    |--------------------------------------------------------------------------
    | Invoice Software
    |--------------------------------------------------------------------------
    */
    'invoice_software' => env('APP_INVOICE_SOFTWARE', 'KeyInvoice'),

    /*
    |--------------------------------------------------------------------------
    | SMS
    |--------------------------------------------------------------------------
    */
    'sms_token'   => env('SMS_TOKEN', '1wh91UNCnNNaj9hvamaUb4dbh7BIo8UISoKMG6NX'),
    'sms_gateway' => env('SMS_GATEWAY', 'SmsApi'),

    /*
    |--------------------------------------------------------------------------
    | Application Active Modules
    |--------------------------------------------------------------------------
    */
    'modules' => env('APP_MODULES'),

    /*
    |--------------------------------------------------------------------------
    | Application Logos
    |--------------------------------------------------------------------------
    */
    'logo_sm'     => env('APP_LOGO_SM'),
    'logo_xs'     => env('APP_LOGO_XS'),
    'logo_square' => env('APP_LOGO_XS_SQUARE'),

    /*
    |--------------------------------------------------------------------------
    | Application Colors
    |--------------------------------------------------------------------------
    */
    'color_primary'   => env('APP_COLOR_PRIMARY'),
    'color_secundary' => env('APP_COLOR_SECUNDARY'),

    /*
    |--------------------------------------------------------------------------
    | Application Backup Login
    |--------------------------------------------------------------------------
    */
    'backup_url' => env('APP_BACKUP_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    //'debug' => env('APP_DEBUG', false),
    'debug' => value(function () {
        $filepath = storage_path() . "/framework/debug_ips";

        if (file_exists($filepath)) {

            $ips = fgets(fopen($filepath, 'r'));

            if ($ips) {
                $ips = explode(';', $ips);
            } else {
                $ips = [];
            }

            try {
                if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
                } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
                    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
                } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
                    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
                } else if (isset($_SERVER['HTTP_FORWARDED'])) {
                    $ipaddress = $_SERVER['HTTP_FORWARDED'];
                } else if (isset($_SERVER['REMOTE_ADDR'])) {
                    $ipaddress = $_SERVER['REMOTE_ADDR'];
                } else {
                    $ipaddress = false;
                }
            } catch (\Exception $e) {
            }

            return in_array($ipaddress, $ips) ? true : false;
        } else {
            env('APP_DEBUG', 'false');
        }
    }),


    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url'    => env('APP_URL', env('APP_URL')),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'Europe/Lisbon'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'pt',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'pt',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => env('APP_LOG', 'daily'),

    'log_level' => env('APP_LOG_LEVEL', 'debug'),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers
         */
        Barryvdh\Debugbar\ServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,
        'Devfactory\Minify\MinifyServiceProvider',
        Cviebrock\EloquentSluggable\ServiceProvider::class,
        'Bkwld\Croppa\ServiceProvider',
        'Spatie\EloquentSortable\SortableServiceProvider',
        'Jenssegers\Date\DateServiceProvider',
        Zizaco\Entrust\EntrustServiceProvider::class,
        'Collective\Html\HtmlServiceProvider',
        Yajra\Datatables\DatatablesServiceProvider::class,
        'Barryvdh\TranslationManager\ManagerServiceProvider',
        Spatie\LaravelAnalytics\LaravelAnalyticsServiceProvider::class,
        Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider::class,
        'anlutro\LaravelSettings\ServiceProvider',
        Unikent\Cache\TaggableFileCacheServiceProvider::class, //tag file cache
        Maatwebsite\Excel\ExcelServiceProvider::class,
        LynX39\LaraPdfMerger\PdfMergerServiceProvider::class,
        Laravel\Passport\PassportServiceProvider::class,
        //Webklex\IMAP\Providers\LaravelServiceProvider::class,
        Brotzka\DotenvEditor\DotenvEditorServiceProvider::class,

        /*
         * Application Service Providers
         */
        App\Providers\AppServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App'           => Illuminate\Support\Facades\App::class,
        'Artisan'       => Illuminate\Support\Facades\Artisan::class,
        'Auth'          => Illuminate\Support\Facades\Auth::class,
        'Blade'         => Illuminate\Support\Facades\Blade::class,
        'Cache'         => Illuminate\Support\Facades\Cache::class,
        'Config'        => Illuminate\Support\Facades\Config::class,
        'Cookie'        => Illuminate\Support\Facades\Cookie::class,
        'Crypt'         => Illuminate\Support\Facades\Crypt::class,
        'DB'            => Illuminate\Support\Facades\DB::class,
        'Eloquent'      => Illuminate\Database\Eloquent\Model::class,
        'Event'         => Illuminate\Support\Facades\Event::class,
        'File'          => Illuminate\Support\Facades\File::class,
        'Gate'          => Illuminate\Support\Facades\Gate::class,
        'Hash'          => Illuminate\Support\Facades\Hash::class,
        'Lang'          => Illuminate\Support\Facades\Lang::class,
        'Log'           => Illuminate\Support\Facades\Log::class,
        'Mail'          => Illuminate\Support\Facades\Mail::class,
        'Notification'  => Illuminate\Support\Facades\Notification::class,
        'Password'      => Illuminate\Support\Facades\Password::class,
        'Queue'         => Illuminate\Support\Facades\Queue::class,
        'Redirect'      => Illuminate\Support\Facades\Redirect::class,
        //'Redis'         => Illuminate\Support\Facades\Redis::class,
        'Request'       => Illuminate\Support\Facades\Request::class,
        'Response'      => Illuminate\Support\Facades\Response::class,
        'Route'         => Illuminate\Support\Facades\Route::class,
        'Schema'        => Illuminate\Support\Facades\Schema::class,
        'Session'       => Illuminate\Support\Facades\Session::class,
        'Storage'       => Illuminate\Support\Facades\Storage::class,
        'URL'           => Illuminate\Support\Facades\URL::class,
        'Validator'     => Illuminate\Support\Facades\Validator::class,
        'View'          => Illuminate\Support\Facades\View::class,

        /**
         * Packages
         */
        'LaravelAnalytics'      => 'Spatie\LaravelAnalytics\LaravelAnalyticsFacade',
        'LaravelLocalization'   => Mcamara\LaravelLocalization\Facades\LaravelLocalization::class,
        'Debugbar'              => Barryvdh\Debugbar\Facade::class,
        'Image'                 => Intervention\Image\Facades\Image::class,
        'Minify'                => 'Devfactory\Minify\Facades\MinifyFacade',
        'Croppa'                => 'Bkwld\Croppa\Facade',
        'Date'                  => Jenssegers\Date\Date::class,
        'Entrust'               => Zizaco\Entrust\EntrustFacade::class,
        'Form'                  => 'Collective\Html\FormFacade',
        'Html'                  => 'Collective\Html\HtmlFacade',
        'Setting'               => 'anlutro\LaravelSettings\Facade',
        'Excel'                 => Maatwebsite\Excel\Facades\Excel::class,
        'PdfMerger'             => LynX39\LaraPdfMerger\Facades\PdfMerger::class,
        'Client'                => Webklex\IMAP\Facades\Client::class,
        'DotenvEditor'          => Brotzka\DotenvEditor\DotenvEditorFacade::class,

        /**
         * Carriers
         */
        'EnovoTms'              => App\Models\Webservice\EnovoTms::class,
        'Envialia'              => App\Models\Webservice\Envialia::class,
        'Tipsa'                 => App\Models\Webservice\Tipsa::class,
        'GlsZeta'               => App\Models\Webservice\GlsZeta::class,
        'Ctt'                   => App\Models\Webservice\Ctt::class,
        'Chronopost'            => App\Models\Webservice\Chronopost::class,
        'Fedex'                 => App\Models\Webservice\Fedex::class,
        'Nacex'                 => App\Models\Webservice\Nacex::class,
        'TntExpress'            => App\Models\Webservice\TntExpress::class,
        'Seur'                  => App\Models\Webservice\Seur::class,
        'Dhl'                   => App\Models\Webservice\Dhl::class,
        'Palibex'               => App\Models\Webservice\Palibex::class,
        'Ups'                   => App\Models\Webservice\Ups::class,
        'Vasp'                  => App\Models\Webservice\Vasp::class,
        'CorreosExpress'        => App\Models\Webservice\CorreosExpress::class,
        'Skynet'                => App\Models\Webservice\Skynet::class,
        'DbSchenker'            => App\Models\Webservice\DbSchenker::class,
        'Mrw'                   => App\Models\Webservice\Mrw::class,
        'Delnext'               => App\Models\Webservice\Delnext::class,
        'ViaDirecta'            => App\Models\Webservice\ViaDirecta::class,
        'Dachser'               => App\Models\Webservice\Dachser::class,
        'Integra2'              => App\Models\Webservice\Integra2::class,
        'CttCorreios'           => App\Models\Webservice\CttCorreios::class,
        'Ontime'                => App\Models\Webservice\Ontime::class,
        'Sending'               => App\Models\Webservice\Sending::class,
        'Wexpress'              => App\Models\Webservice\Wexpress::class,
        'Lfm'                   => App\Models\Webservice\Lfm::class,
        'WePickup'              => App\Models\Webservice\WePickup::class,

        /**
         * Payment Gateways
         */
        'Easypay'               => App\Models\GatewayPayment\Easypay::class,
        'Eupago'                => App\Models\GatewayPayment\Eupago::class,
        'IfThenPay'             => App\Models\GatewayPayment\IfThenPay::class,
    ],

];
