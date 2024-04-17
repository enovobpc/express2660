<?php

namespace App\Http\Controllers\Admin\CustomerSupport;

use App\Models\Agency;
use App\Models\Customer;
use App\Models\CustomerSupport\Ticket;
use App\Models\CustomerSupport\Message;
use App\Models\CustomerSupport\TicketAttachment;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Auth, Response, File, Setting;

class TicketsController extends \App\Http\Controllers\Admin\Controller {

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isOperator(false)
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'operators'
        );

        return $this->setContent('admin.customer_support.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $action = 'Registar Pedido de Suporte';

        $ticket = new Ticket();

        $formOptions = array('route' => array('admin.customer-support.store'), 'method' => 'POST', 'files' => true);

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isOperator(false)
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.customer_support.edit', compact('ticket', 'action', 'formOptions', 'operators'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return $this->update($request, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        $ticket = Ticket::with('attachments')
                    ->filterAgencies()
                    ->findOrfail($id);

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isOperator(false)
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $shippingStatus = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();
        
        $data = compact(
            'ticket',
            'unreadProposes',
            'operators',
            'providers',
            'shippingStatus'
        );

        return $this->setContent('admin.customer_support.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $action = 'Editar Pedido de Suporte';

        $ticket = Ticket::with('customer', 'shipment')
                    ->filterAgencies()
                    ->findOrfail($id);

        $formOptions = array('route' => array('admin.customer-support.update', $ticket->id), 'method' => 'PUT', 'files' => true);

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isOperator(false)
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.customer_support.edit', compact('ticket', 'action', 'formOptions', 'operators'))->render();
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
        $input['customer_id'] = $request->get('customer_id');
        $input['shipment_id'] = $request->get('shipment_id');

        $customer = Customer::filterSource()->findOrFail($input['customer_id']);

        $ticket = Ticket::filterAgencies()
                    ->findOrNew($id);

        $exists = $ticket->exists;

        if(!empty($input['tracking_code'])) {
            $shipment = Shipment::where('tracking_code', $input['tracking_code'])->first();
            $input['shipment_id'] = $shipment->id;
        }

        if(empty($input['customer_id']) && !empty($input['shipment_id'])) {
            $shipment = Shipment::find($input['shipment_id']);
            $input['customer_id'] = $shipment->customer_id;

            if(!$ticket->email) {
                $input['name']  = @$shipment->customer->name;
                $input['email'] = @$shipment->customer->email;
            }
        }

        if ($ticket->validate($input)) {
            $ticket->fill($input);
            $ticket->source = config('app.source');
            $ticket->name   = $customer->name;
            $ticket->email  = $customer->email;

            if(!$exists) {
                $ticket->status  = Ticket::STATUS_ANALISYS;
                $ticket->user_id = Auth::user()->id;
                $ticket->setCode();
            } else {
                $ticket->save();
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

            if(!$exists) {
                return Redirect::route('admin.customer-support.show', $ticket->id)->with('success', 'Dados gravados com sucesso.');
            } else {
                return Redirect::back()->with('success', 'Dados gravados com sucesso.');
            }
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

        $ticket = Ticket::filterAgencies()
                    ->whereId($id)
                    ->first();

        $ticket->deleteNotification();
        $result = $ticket->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o pedido de suporte.');
        }

        return Redirect::back()->with('success', 'Pedido de suporte eliminado com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        $ids = explode(',', $request->ids);

        $result = Ticket::filterAgencies()
                    ->whereIn('id', $ids)
                    ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = Ticket::filterAgencies()
                    ->with('customer')
                    ->with('shipment.provider')
                    ->select();

        //filter category
        $value = $request->get('category');
        if($request->has('category')) {
            $data = $data->where('category', $value);
        }

        //filter hide concluded
        $value = $request->get('hide_concluded');
        if($request->has('hide_concluded')) {
            if($value) {
                $data = $data->where('status', '<>', Ticket::STATUS_CONCLUDED);
            }
        }

        //filter status
        $value = $request->get('status');
        if($request->has('status')) {
            $data = $data->where('status', $value);
        }

        //filter operator
        $value = $request->get('operator');
        if($request->has('operator')) {
            if($value == '-1') {
                $data = $data->whereNull('user_id');
            } else {
                $data = $data->where('user_id', $value);
            }

        }

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            if($request->date_unity == '2') { //ultimo registo
                $data = $data->whereBetween('updated_at', [$dtMin.' 00:00:00', $dtMax.' 23:59:59']);
            } else {
                $data = $data->whereBetween('date', [$dtMin, $dtMax]);
            }

        }

        return Datatables::of($data)
            ->edit_column('id', function($row) {
                return view('admin.customer_support.datatables.code', compact('row'))->render();
            })
            ->edit_column('subject', function($row) {
                return view('admin.customer_support.datatables.subject', compact('row'))->render();
            })
            ->edit_column('date', function($row) {
                return $row->date->format('Y-m-d');
            })
            ->edit_column('user_id', function($row) {
                return view('admin.customer_support.datatables.responsable', compact('row'))->render();
            })
            ->edit_column('shipment_id', function($row) {
                return view('admin.customer_support.datatables.tracking', compact('row'))->render();
            })
            ->edit_column('status', function($row) {
                return view('admin.customer_support.datatables.status', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.customer_support.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Sync ticket emails
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function syncEmails() {

        try {
            $message = new Message();
            $message->checkAnswers();
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Falha de sincronização. Motivo: '. $e->getMessage());
        }

        return Redirect::back()->with('success', 'E-mails sincronizados com sucesso.');
    }

    /**
     * Adjudicate ticket to current customer
     *
     * @return \Illuminate\Http\Response
     */
    public function adjudicate($id) {

        $ticket = Ticket::filterAgencies()->findOrNew($id);
        $ticket->user_id = Auth::user()->id;
        $ticket->save();

        return Redirect::back()->with('success', 'Pedido de suporte adjudicado com sucesso.');
    }

    /**
     * Conclude ticket to current customer
     *
     * @return \Illuminate\Http\Response
     */
    public function conclude($id) {

        $ticket = Ticket::filterAgencies()->findOrNew($id);
        $ticket->status  = Ticket::STATUS_CONCLUDED;
        $ticket->save();

        return Redirect::route('admin.customer-support.index')->with('success', 'Pedido de suporte fechado com sucesso.');
    }

    /**
     * Download attachment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attachment($id, $nameSlug) {

        $ticket = Ticket::filterAgencies()->findOrfail($id);

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

    /**
     * Show the form for merge two tickets.
     *
     * @return \Illuminate\Http\Response
     */
    public function mergeBudget(Request $request, $ticketId) {
        return view('admin.customer_support.modals.merge', compact('ticketId'))->render();
    }

    /**
     * Merge two tickets
     *
     * @return \Illuminate\Http\Response
     */
    public function mergeBudgetStore(Request $request, $ticketId) {

        $sourceTicket      = Ticket::filterAgencies()->findOrfail($ticketId);
        $destinationTicket = Ticket::filterAgencies()->findOrfail($request->assign_ticket_id);

        try {
            $message = new Message();
            $message->ticket_id   = $destinationTicket->id;
            $message->from        = $sourceTicket->email;
            $message->from_name   = $sourceTicket->name;
            $message->subject     = $sourceTicket->subject;
            $message->message     = $sourceTicket->message;
            $message->attachments = $sourceTicket->attachments;
            $message->created_at  = $sourceTicket->created_at;
            $message->save();

            $sourceTicket->merged = true;
            $sourceTicket->save();

            $sourceTicket->delete();

        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro ao convergir pedidos de suporte. '. $e->getMessage());
        }

        return Redirect::back()->with('success', 'Pedido de suporte convergido com sucesso.');
    }

    /**
     * Search ticket on DB
     *
     * @return type
     */
    public function searchTicket(Request $request) {

        $search = $request->get('q');
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $tickets = Ticket::filterSource()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('subject', 'LIKE', $search)
                        ->orWhere('email', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search);
                })
                ->get(['subject', 'code', 'id']);

            if($tickets) {

                $results = array();
                foreach($tickets as $ticket) {
                    $results[]=array('id'=> $ticket->id, 'text' => $ticket->code. ' - '.str_limit($ticket->subject, 40));
                }

            } else {
                $results = [['id' => '', 'text' => 'Nenhum pedido de suporte encontrado.']];
            }

        } catch(\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableShipments(Request $request) {

        //status
        $statusList = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->get(['id', 'name', 'color', 'is_final']);
        $statusList  = $statusList->groupBy('id')->toArray();

        //services
        $servicesList = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->get();
        $servicesList = $servicesList->groupBy('id')->toArray();

        //operator
        $operatorsList = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->get(['source', 'id', 'code', 'code_abbrv', 'name', 'vehicle', 'provider_id']);
        $operatorsList = $operatorsList->groupBy('id')->toArray();

        //providers
        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->get();
        $providersList = $providersList->groupBy('id')->toArray();

        //agencies
        $allAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->withTrashed()
            ->get(['name', 'code', 'id', 'color', 'source']);
        $agencies = $allAgencies->groupBy('id')->toArray();

        $bindings = [
            'id', 'tracking_code', 'type', 'parent_tracking_code', 'children_tracking_code', 'children_type',
            'agency_id', 'sender_agency_id', 'recipient_agency_id',
            'service_id', 'provider_id', 'status_id', 'operator_id', 'customer_id',
            'sender_name', 'sender_address','sender_zip_code', 'sender_city', 'sender_phone',
            'recipient_name', 'recipient_address','recipient_zip_code', 'recipient_city', 'recipient_phone', 'recipient_country',
            'obs', 'volumes', 'weight', 'total_price', 'date'
        ];

        $data = Shipment::filterAgencies()
            ->with('service', 'provider', 'status', 'operator', 'customer')
            ->select($bindings);

        /*$agencies = Agency::filterAgencies()->remember(5)->get(['name', 'code', 'id', 'color']);
        $agencies = $agencies->groupBy('id')->toArray();*/


        //filter provider
        $value = $request->provider;
        if($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter operator
        $value = $request->operator;
        if($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        //filter status
        $value = $request->status;
        if($request->has('status')) {
            $data = $data->where('status_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('service_id', function($row) use($agencies, $servicesList, $providersList) {
                return view('admin.shipments.shipments.datatables.service', compact('row', 'agencies', 'servicesList', 'providersList'))->render();
            })
            ->edit_column('id', function($row) use($agencies) {
                return view('admin.shipments.shipments.datatables.tracking', compact('row', 'agencies'))->render();
            })
            ->edit_column('sender_name', function($row) {
                return view('admin.shipments.shipments.datatables.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function($row) {
                return view('admin.shipments.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('status_id', function($row) use($statusList, $operatorsList) {
                return view('admin.shipments.shipments.datatables.status', compact('row', 'statusList', 'operatorsList'))->render();
            })
            ->edit_column('date', function($row) use($statusList) {
                return view('admin.shipments.shipments.datatables.date', compact('row', 'statusList'))->render();
            })
            ->edit_column('volumes', function($row) {
                return view('admin.shipments.shipments.datatables.volumes', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.customer_support.datatables.shipments.actions', compact('row'))->render();
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
}
