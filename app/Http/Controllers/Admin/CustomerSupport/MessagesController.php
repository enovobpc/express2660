<?php

namespace App\Http\Controllers\Admin\CustomerSupport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\CustomerSupport\Ticket;
use App\Models\CustomerSupport\Message;
use App\Models\CustomerSupport\MessageAttachment;
use Html, Croppa, Auth, App, Mail, Setting;

class MessagesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'customer_support';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customer_support']);
        validateModule('customer_support');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($ticketId) {

        $ticket = Ticket::filterAgencies()->findOrFail($ticketId);

        $defaultMsg = Setting::get('tickets_mail_default_answer');

        if(Setting::get('tickets_mail_signature')) {
            if(Setting::get('tickets_mail_signature_html')) {
                $defaultMsg.= Setting::get('tickets_mail_signature');
            } else {
                $defaultMsg.= nl2br(Setting::get('tickets_mail_signature'));
            }
        }

        return view('admin.customer_support.modals.message', compact('ticket', 'defaultMsg'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $ticketId) {
        return $this->update($request, $ticketId, null);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $ticketId, $id) {

        $input = $request->all();
        $input['message'] = $input['message'];

        $ticket = Ticket::filterAgencies()->findOrFail($ticketId);

        if(empty($ticket->user_id)) { //auto assign ticket
            $input['user_id'] = Auth::user()->id;
        }

        $message = Message::where('ticket_id', $ticket->id)->findOrNew($id);

        if ($message->validate($input)) {
            $message->fill($input);
            $message->ticket_id = $ticketId;
            $message->subject   = $message->getSubject($input['subject'], $ticket->code);
            $message->from      = Setting::get('tickets_mail') ? Setting::get('tickets_mail') : env('MAIL_FROM');
            $message->from_name = Auth::user()->name;
            $message->to_name   = $ticket->customer_id ? $ticket->customer->name : $ticket->name;
            $message->save();

            //upload files if exists
            if($request->hasFile('attachments')) {
                $attachments = $request->file('attachments');
                foreach ($attachments as $file) {
                    $attachment = new MessageAttachment();
                    $attachment->message_id = $message->id;
                    if (!$attachment->upload($file, true, 40)) {
                        return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o documento.');
                    }
                }
            }

            //last message
            $lastMessage = Message::where('ticket_id', $ticketId)
                ->where('id', '<>', $message->id)
                ->orderBy('id', 'desc')
                ->first();

            if(!$lastMessage) {
                $lastMessage = new Message();
                $lastMessage->created_at = $ticket->created_at;
                $lastMessage->message    = $ticket->message;
                $lastMessage->from_name  = $ticket->name;
            }

            if($lastMessage) {
                $message->subject = 'Re: ' . $message->subject;
                $lastMessageHtml = "<blockquote style='border-left: 5px solid #ddd; padding-left: 15px'>";
                $lastMessageHtml.= 'Às '.@$lastMessage->created_at->format('H:i').' de '.@$lastMessage->created_at->format('d-m-Y').', '.@$lastMessage->from_name.' escreveu:<br/><br/>';
                $lastMessageHtml.= $lastMessage->message;

                $message->message = $message->message . '<br/>' . $lastMessageHtml . '</blockquote>';
            }

            $ticket->user_id = $ticket->user_id ? $ticket->user_id : Auth::user()->id;
            $ticket->status  = Ticket::STATUS_WAINTING_CUSTOMER;
            $ticket->save();

            try {
                $data = $message->toArray();

                $emails = validateNotificationEmails($data['to']);
                $emails = $emails['valid'];

                $fromEmail  = Setting::get('tickets_mail') ? Setting::get('tickets_mail') : env('MAIL_FROM');
                $replyEmail = Setting::get('tickets_reply_mail');

                Mail::send('emails.customer_support.ticket', compact('data'), function ($message) use($data, $emails, $fromEmail, $replyEmail) {
                    $message->to($emails)
                        ->from($fromEmail, config('mail.from.name'));

                    if($replyEmail) {
                        $message = $message->replyTo($replyEmail);
                    }

                    $message = $message->subject($data['subject']);
                });

            } catch (\Exception $e) {
                Message::where('ticket_id', $ticket->id)->whereId($message->id)->forceDelete();
                return Redirect::back()->with('error', 'Erro ao enviar e-mail. ' . $e->getMessage());
            }

            return Redirect::back()->with('success', 'Mensagem enviada com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $message->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($ticketId, $id) {

        $result = Message::filterAgencies()
                        ->where('ticket_id', $ticketId)
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a mensagem.');
        }

        return Redirect::back()->with('success', 'Mensagem removida com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable($ticketId) {

        $data = Message::with('attachments')
                    ->where('ticket_id', $ticketId)
                    ->select();

        return Datatables::of($data)
                ->edit_column('id', function($row) {
                    return view('admin.customer_support.datatables.messages.from', compact('row'))->render();
                })
                ->edit_column('subject', function($row) {
                    return view('admin.customer_support.datatables.messages.subject', compact('row'))->render();
                })
                ->make(true);
    }

    /**
     * Download attachment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attachment($id, $nameSlug) {

        $ticket = Message::findOrfail($id);

        $data = null;
        $filename = null;
        foreach ($ticket->inline_attachments as $attachment) {
            //$filename = $attachment->name;

            $filenameSlug = str_slug($attachment->name);
            if($filenameSlug == $nameSlug) {
                $contentType = $attachment->content_type;
                $data = $attachment->content;
            }
        }

        header('content-type:' . $contentType);
        echo base64_decode($data);
    }
}
