<?php
namespace App\Models;

use Auth;
use Pusher\Pusher;

class BroadcastPusher {

    /**
     * Pusher instance
     * @var
     */
    private $pusher;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){

        $options = array(
            'cluster'   => env('PUSHER_CLUSTER'),
            'encrypted' => env('PUSHER_ENCRYPTION')
        );

        $this->pusher = new Pusher(
            env('PUSHER_KEY'),
            env('PUSHER_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );
    }

    /**
     * Return channel name
     * @param null $userId
     * @return string
     */
    public static function getChannel($userId = null) {
        $userId = empty($userId) ? Auth::user()->id : $userId;
        return 'channel-' . $userId;
    }

    /**
     * Return channel name
     * @param null $userId
     * @return string
     */
    public static function getOperatorGpsChannel($userId = null) {
        $userId = empty($userId) ? Auth::user()->id : $userId;
        return 'gps-operator-' . $userId;
    }

    /**
     * Return global channel name
     * @param null $userId
     * @return string
     */
    public static function getOperatorsChannel($source = null) {
        $source = empty($source) ? config('app.source') : $source;
        return 'channel-operators-' . $source;
    }

    /**
     * Return global channel name
     * @param null $userId
     * @return string
     */
    public static function getGlobalChannel($source = null, $operators = false) {
        $source = empty($source) ? config('app.source') : $source;
        if($operators) {
            $source = 'operators-' . $source; //channel para notificar todos os telemóveis
        }

        return 'channel-' . $source;
    }

    /**
     * Return global channel name
     * @param null $userId
     * @return string
     */
    public static function getOperatorChannel($operatorId) {
        return 'channel-operator-' . $operatorId;
    }

    /**
     * Trigger pusher event
     *
     * @param $channel
     * @param $event
     * @param $data
     * @return array|bool
     */
    public function trigger($data, $channel = null, $event = null) {
        $channel = empty($channel) ? self::getChannel() : $channel;
        $event   = empty($event)   ? 'notifications-event' : $event;

        return $this->pusher->trigger($channel, $event, $data);
    }

}
