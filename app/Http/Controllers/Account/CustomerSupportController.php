<?php

namespace App\Http\Controllers\Account;

use App\Models\CustomerSupport\Message;
use App\Models\CustomerSupport\MessageAttachment;
use App\Models\CustomerSupport\Ticket;
use App\Models\CustomerSupport\TicketAttachment;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;
use App\Models\Customerticket;
use DB, Excel, Setting, Mail, Response;


class CustomerSupportController extends \App\Http\Controllers\Controller
{
    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.account';

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'customers-support';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Customer billing index controller
     *
     * @return type
     */
    public function index(Request $request) {

        $customer = Auth::guard('customer')->user();

        return $this->setContent('account.customer_support.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $action = 'Adicionar Pedido de Suporte';

        $ticket = new Ticket();

        $formOptions = array('route' => array('account.customer-support.store'), 'method' => 'POST', 'files' => true);

        if($request->has('shipment')) {
            $ticket->shipment = Shipment::filterCustomer()->find($request->get('shipment'));
            $ticket->shipment_id = $ticket->shipment->id;
        }

        return view('account.customer_support.edit', compact('ticket', 'action', 'formOptions', 'shipment'))->render();
    }

    public function store(Request $request) {
        return $this->update($request, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        $customer = Auth::guard('customer')->user();

        $ticket = Ticket::filterSource()
            ->where('customer_id', $customer->id)
            ->where('code', $id)
            ->firstOrFail();

        return $this->setContent('account.customer_support.show', compact('ticket'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $action = 'Editar Pedido de Suporte';

        $customer = Auth::guard('customer')->user();

        $ticket = Ticket::filterSource()
            ->where('customer_id', $customer->id)
            ->where('status', Ticket::STATUS_PENDING)
            ->where('code', $id)
            ->firstOrFail();

        $formOptions = array('route' => array('account.customer-support.update', $ticket->code), 'method' => 'PUT', 'files' => true);

        $shipment = $ticket->shipment;

        return view('account.customer_support.edit', compact('ticket', 'action', 'formOptions', 'shipment'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $input = $request->all();

        $customer = Auth::guard('customer')->user();

        $ticket = Ticket::filterSource()
            ->where('customer_id', $customer->id)
            ->where('code', $id)
            ->first();

        $ticket = $ticket ? $ticket : new Ticket();

        $exists = $ticket->exists;
        if ($ticket->validate($input)) {
            $ticket->customer_id = $customer->id;
            $ticket->fill($input);
            $ticket->name   = $customer->display_name;
            $ticket->email  = $customer->email;
            $ticket->source = config('app.source');
            $ticket->status = Ticket::STATUS_PENDING;
            $ticket->date   = date('Y-m-d');

            if($exists) {
                $ticket->save();
            } else {
                $ticket->setCode();
                $ticket->setNotification();
            }

            //upload files if exists
            if($request->hasFile('attachments')) {
                $attachments = $request->file('attachments');
                foreach ($attachments as $file) {
                    $attachment = new TicketAttachment();
                    $attachment->ticket_id = $ticket->id;

                    if (!$attachment->upload($file, true, 40)) {
                        return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o documento.');
                    }
                }
            }


            if($exists) {
                return Redirect::back()->with('success', 'Dados gravados com sucesso.');
            } else {
                $ticket->sendEmailToAgency();
            }

            return Redirect::route('account.customer-support.show', $ticket->code)->with('success', 'Pedido registado com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $ticket->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $customer = Auth::guard('customer')->user();

        $ticket = Ticket::filterSource()
            ->where('customer_id', $customer->id)
            ->where('code', $id)
            ->firstOrFail();

        if($ticket->status != Ticket::STATUS_PENDING) {
            return Redirect::back()->with('error', 'Não é possível eliminar o pedido porque já não se encontra no estado Pendente.');
        }

        $result = $ticket->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o pedido.');
        }

        return Redirect::back()->with('success', 'Pedido removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        /*$customer = Auth::guard('customer')->user();

        $ids = explode(',', $request->ids);

        $result = Customerticket::where('customer_id', $customer->id)
                    ->whereIn('id', $ids)
                    ->delete();

        if (!$result) {
            return Redirect::back()->with('error', trans('account.feedback.mass-destroy.error'));
        }

        return Redirect::back()->with('success', trans('account.feedback.mass-destroy.success'));*/
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request) {

        $customer = Auth::guard('customer')->user();

        $data = Ticket::where(function($q) use($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->select();

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('created_at', [$dtMin.' 00:00:00', $dtMax.' 23:59:59']);
        }

        //filter status
        $value = $request->get('status');
        if($request->has('status')) {
            $data = $data->where('status', $value);
        }

        //filter category
        $value = $request->get('category');
        if($request->has('category')) {
            $data = $data->where('category', $value);
        }

        return Datatables::of($data)
            ->edit_column('code', function($row) {
                return view('account.customer_support.datatables.code', compact('row'))->render();
            })
            ->edit_column('subject', function($row) {
                return view('account.customer_support.datatables.subject', compact('row'))->render();
            })
            ->add_column('shipment', function($row) {
                return view('account.customer_support.datatables.tracking', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('account.customer_support.datatables.created_at', compact('row'))->render();
            })
            ->edit_column('status', function($row) {
                return view('account.customer_support.datatables.status', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('account.customer_support.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createMessage(Request $request, $ticketCode) {

        $customer = Auth::guard('customer')->user();

        $ticket = Ticket::filterSource()
            ->where('customer_id', $customer->id)
            ->where('code', $ticketCode)
            ->firstOrFail();

        return view('account.customer_support.modals.message', compact('message', 'action', 'formOptions', 'ticket', 'customer'))->render();
    }

    /**
     * Store new message
     * @param Request $request
     * @param $ticketCode
     * @return mixed
     */
    public function storeMessage(Request $request, $ticketCode) {
        $input = $request->all();
        $input['message'] = $input['message'];

        $customer = Auth::guard('customer')->user();

        $ticket = Ticket::filterSource()
            ->where('customer_id', $customer->id)
            ->where('code', $ticketCode)
            ->firstOrFail();

        if(empty($ticket->user_id)) { //auto assign ticket
            $input['user_id'] = Auth::user()->id;
        }

        $message = new Message();

        if ($message->validate($input)) {
            $message->fill($input);
            $message->ticket_id = $ticket->id;
            $message->subject   = $message->getSubject($ticket->subject, $ticket->code);
            $message->from      = $customer->email;
            $message->from_name = $customer->display_name;
            $message->to        = Setting::get('tickets_mail') ? Setting::get('tickets_mail') : env('MAIL_FROM');
            $message->to_name   = '';
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
            $lastMessage = Message::where('ticket_id', $ticket->id)
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

            $ticket->status  = Ticket::STATUS_WAINTING_CUSTOMER;
            $ticket->save();

            $ticket->setNotification(null,true, $message);

            try {
                $data = $message->toArray();

                $emails = validateNotificationEmails(@$ticket->user->email);
                $emails = $emails['valid'];

                if($emails) {

                    Mail::send('emails.customer_support.ticket', compact('data'), function ($message) use ($data, $emails, $customer) {
                        $message->to($emails)
                            ->from(env('MAIL_FROM'), $customer->display_name)
                            ->subject($data['subject']);
                    });
                }

            } catch (\Exception $e) {
                Message::where('ticket_id', $ticket->id)->whereId($message->id)->forceDelete();
                return Redirect::back()->with('error', 'Erro ao enviar e-mail. ' . $e->getMessage());
            }

            return Redirect::back()->with('success', 'Mensagem enviada com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $message->errors()->first());
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableMessages(Request $request, $ticketCode) {

        $customer = Auth::guard('customer')->user();

        $data = Message::whereHas('ticket', function($q) use($customer, $ticketCode) {
                $q->where('customer_id', $customer->id);
                $q->where('code', $ticketCode);
            })
            ->select();

        return Datatables::of($data)
            ->edit_column('created_at', function($row) {
                return view('account.customer_support.datatables.messages.from', compact('row'))->render();
            })
            ->edit_column('message', function($row) {
                return view('account.customer_support.datatables.messages.message', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Search providers on DB
     *
     * @return type
     */
    public function searchShipment(Request $request) {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'tracking_code',
            'recipient_name'
        ];

        try {
            $results = [];

            $shipments = Shipment::filterAgencies()
                ->filterCustomer()
                ->where(function($q) use($search){
                    $q->where('tracking_code', 'LIKE', $search)
                        ->orWhere('recipient_name', 'LIKE', $search);
                })
                ->get($fields);

            if($shipments) {
                $results = array();
                foreach($shipments as $shipment) {
                    $results[] = [
                        'id'           => $shipment->id,
                        'text'         => '#' . $shipment->tracking_code. ' - '.str_limit($shipment->recipient_name, 40),
                    ];
                }

            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum envio encontrado.'
                ];
            }

        } catch(\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return Response::json($results);
    }

    /**
     * Conclude ticket
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function conclude($id) {

        $customer = Auth::guard('customer')->user();

        $ticket = Ticket::filterSource()
            ->where('customer_id', $customer->id)
            ->where('code', $id)
            ->firstOrFail();


        $ticket->status = Ticket::STATUS_CONCLUDED;
        $result = $ticket->save();

        if ($result) {
            return Redirect::route('account.customer-support.index')->with('success', 'Pedido fechado com sucesso.');
        }

        return Redirect::back()->with('error', 'Ocorreu um erro ao tentar fechar o pedido.');
    }
}