<?php 

namespace App\Listeners;

use App\Models\LoginLog;
use App\Models\LogViewer;
use Date, Request;
use Illuminate\Support\Facades\Log;

class LoginLogSubscriber {

    /**
     * Handle the event.
     *
     * @param  Failed  $event
     * @return void
     */
    public function handle($event)
    {
        $trace = LogViewer::getTrace(null, 'Failed login attempt to user: ' . Request::get('email'));
        Log::info(br2nl($trace));
    }

    /**
     * Handle user login events. 
     */ 
    public function onUserLogin($event) {

        $user = $event->user;

        $user->last_login = Date::now();
        $user->ip = client_ip();
        $user->save();

        LoginLog::insert([
            'source'     => config('app.source'),
            'target'     => $user->getTable(),
            'user_id'    => $user->id,
            'ip'         => $user->ip,
            'remember'   => $event->remember,
            'created_at' => $user->last_login
        ]);
    }

    /**
     * Handle user logout events.
     */
    public function onUserLogout($event) {
        //
    }
   
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            'App\Listeners\LoginLogSubscriber@onUserLogin'
        );
    
        $events->listen(
            'Illuminate\Auth\Events\Logout',
            'App\Listeners\LoginLogSubscriber@onUserLogout'
        );
    }
}