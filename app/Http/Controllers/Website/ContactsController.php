<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use App\Models\ContactLog;
use Response;
use Setting;
use Mail;

class ContactsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    /**
     * class of main menu to be activated
     * 
     * @var string 
     */
    protected $menuOption = 'contacts';
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->setContent('website.contacts');
    }
    
    /**
     * Submit contact form
     *
     * @return \Illuminate\Http\Response
     */
    public function mail(Request $request)
    {
        $input = $request->all();
        $input['ip'] = $request->ip();
        $result   = true;
        $feedback = 'Enviado com sucesso';

                Mail::send('emails.contact', compact('input'), function($message) use($input) {
                $message->to(Setting::get('contact_form_email'))
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Pedido de Contacto');
                    
            });

        return Response::json([
            'result'   => $result,
            'feedback' => $feedback
        ]);
    }
}
