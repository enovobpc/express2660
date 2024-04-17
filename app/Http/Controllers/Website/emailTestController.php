<?php

namespace App\Http\Controllers\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Mail;

class emailTestController  extends Controller
{
      public function index()
    {
        try {
            Mail::send('emails.test.testEmail', [], function($message) {
                $message->to('diogo.tojal@enovo.pt')
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Teste de email');
            });

            dd('Sucesso'); // Isso ser谩 executado ap贸s o email ser enviado
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}