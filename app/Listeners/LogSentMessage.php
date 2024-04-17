<?php

namespace App\Listeners;

use App\Models\Email\Email;
use Illuminate\Mail\Events\MessageSending;
use Log, Auth;

class LogSentMessage
{
    public function handle(MessageSending $event)
    {
        $message = $event->message;

        $now  = date('Y-m-d H:i:s');
        $from = $message->getFrom(); //formato retornado: [email=>nome];
        $to   = $message->getTo();
        $cc   = $message->getCC();
        $bcc  = $message->getBcc();
        $userId = @Auth::user()->id;

        if($from) {
            $from = implode(';', array_keys($from));
        }

        if($to) {
            $to = implode(';', array_keys($to));
        }

        if($cc) {
            $cc = implode(';', array_keys($cc));
        }
  
        if($bcc) {
            $bcc = implode(';', array_keys($bcc));
        }
        
        Email::insert([
            'source'     => config('app.source'),
            'subject'    => $event->message->getSubject(),
            'from'       => $from,
            'to'         => $to,
            'cc'         => $cc,
            'bcc'        => $bcc,
            'message'    => $event->message->getBody(),
            'headers'    => $event->message->getHeaders(),
            'message_id' => $event->message->getId(),
            'sended_by'  => $userId,
            'sended_at'  => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}