<?php

namespace App\Http\Controllers\Admin;

use App\Models\Core\Source;
use App\Models\CustomerSupport\Ticket;
use Request, Html, Form, View, Auth, Config, File;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use App\Models\BroadcastPusher;
use App\Models\Notification;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * The layout that should be used for responses
     * 
     * @var string 
     */
    protected $layout = 'admin.layouts.master';

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'dashboard';

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            View::share('sidebarActiveOption', $this->sidebarActiveOption);

            if(!Request::ajax()) {
                $totalNotifications = 0;
                $totalSupportNotifications = 0;
                $scheduledNotifications = [];
                $pusherChannel = 'web-channel';
                if (Auth::check()) {

                    if(hasPermission('shipments')) {
                        $pusherChannel = BroadcastPusher::getChannel();

                        $now = Carbon::now();
                        $notifications = Notification::where('recipient', Auth::user()->id)
                            ->where(function($q) use($now){
                                $q->where(function($q){
                                    $q->whereRaw('alert_at <= "' . date('Y-m-d H:i:s') . '"')
                                        ->where('read', 0);
                                });
                                $q->orWhere(function($q) use($now) {
                                    $q->whereBetween('alert_at', [$now->format('Y-m-d H:i:s'), $now->addDay(1)->format('Y-m-d H:i:s')]);
                                    $q->whereRaw('alert_at >= "' . date('Y-m-d H:i:s') . '"');
                                });
                            })
                            ->take(30)
                            ->get();

                        $scheduledNotifications = $notifications->filter(function($item) {
                            return $item->alert_at >= date('Y-m-d H:i:s');
                        });

                        $totalNotifications = $notifications->filter(function($item) {
                            return !$item->read && $item->alert_at <= date('Y-m-d H:i:s');
                        })->count();

                        View::share('totalNotifications', $totalNotifications);
                    }


                    if(hasPermission('customer_support')) {
                        $totalSupportNotifications = Ticket::filterSource()
                            ->whereIn('status', [Ticket::STATUS_PENDING, Ticket::STATUS_WAINTING])
                            ->count();

                        View::share('totalSupportNotifications', $totalSupportNotifications);
                    }
                }


                $filename = storage_path() . '/license.json';
                $license  = File::exists($filename);
                View::share('license', !$license);

                $enovoPayments = null;
                if(Auth::check()) {
                    $enovoPayments = json_decode(Auth::user()->popup_notification);
                }
                View::share('enovoPayments', $enovoPayments);

                View::share('sources', Source::remember(20)->orderBy('name', 'asc')->pluck('name', 'source')->toArray());
            }

            $this->layout = view($this->layout, compact('totalNotifications', 'totalSupportNotifications', 'scheduledNotifications', 'pusherChannel', 'license'));
        }
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
     * @param string $view
     * @param array $data
     * @return string|void
     */
    public function setContent($view, $data = [])
    {
        try {
            if (!is_null($this->layout))
            {
                return $this->layout->nest('child', $view, $data);
            }

            return view($view, $data);
        } catch (\Exception $e) {

            $text = $e->getMessage().' ---- FILE: ' . $e->getFile().' | LINHA: ' . $e->getLine().'<br/>';

            Mail::raw($text, function($message) {
                $message->to('geral@enovo.pt')
                    ->subject('Erro');
            });
        }

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
