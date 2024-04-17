<?php

namespace App\Http\Controllers\Admin\Customers;

use App\Models\Agency;
use App\Models\Customer;
use App\Models\CustomerMessage;
use App\Models\CustomerType;
use App\Models\PriceTable;
use App\Models\Route;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Auth, Cache, Response, DB, Mail;

class MessagesController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'customer-messages';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customer-messages']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $agencies = Auth::user()->listsAgencies();

        return $this->setContent('admin.customers.messages.index', compact('agencies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $action = 'Criar nova Mensagem';

        $customerMessage = new CustomerMessage();

        $formOptions = array('route' => array('admin.customers.messages.store'), 'method' => 'POST');

        $customers = Customer::where('source', config('app.source'))
            ->filterAgencies()
            //->where('contact_email', '<>', '')
            ->pluck('name', 'id')
            ->toArray();

        $selectedCustomers = null;

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('id')
            ->toArray();

        $agencies = Auth::user()->listsAgencies();

        $types = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $sellers = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->isSeller()
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $pricesTables = PriceTable::remember(config('cache.query_ttl'))
            ->cacheTags(PriceTable::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        $routes = Route::listsWithCode(Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->get());


        $data = compact(
            'customerMessage',
            'action',
            'formOptions',
            'customers',
            'selectedCustomers',
            'agencies',
            'types',
            'sellers',
            'pricesTables',
            'routes'
        );

        return view('admin.customers.messages.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if ($request->has('filter')) {

            $customers = Customer::whereSource(config('app.source'))
                ->filterAgencies();

            $hasFilters = false;
            if ($request->has('type') && !empty($request->get('type'))) {
                $customers = $customers->whereIn('type_id', $request->get('type'));
                $hasFilters = true;
            }

            if ($request->has('agency') && !empty($request->get('agency'))) {
                $customers = $customers->whereIn('agency_id', $request->get('agency'));
                $hasFilters = true;
            }

            if ($request->has('route') && !empty($request->get('route'))) {
                $customers = $customers->whereIn('route_id', $request->get('route'));
                $hasFilters = true;
            }

            if ($request->has('prices') && !empty($request->get('prices'))) {
                $customers = $customers->whereIn('price_table_id', $request->get('prices'));
                $hasFilters = true;
            }

            if (!$hasFilters) {
                $customers = $customers->where('source', 'xxxx');
            }

            $customers = $customers->pluck('id')->toArray();
            return response()->json($customers);
        } else {
            return $this->update($request, null);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        $customerMessage = CustomerMessage::filterSource()
            ->with('customers')
            ->whereId($id)
            ->firstOrFail();

        $showEmails = false;
        if ($request->has('list') && $request->get('list') == 'emails') {
            $showEmails = true;
        }

        return view('admin.customers.messages.show', compact('customerMessage', 'showEmails'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $action = 'Editar nova Mensagem';

        $customerMessage = CustomerMessage::filterSource()
            ->whereId($id)
            ->firstOrfail();

        $formOptions = array('route' => array('admin.customers.messages.update', $customerMessage->id), 'method' => 'PUT');

        $customers = Customer::where('source', config('app.source'))
            ->filterAgencies()
            //->where('contact_email', '<>', '')
            ->pluck('name', 'id')
            ->toArray();

        if ($customerMessage->is_static || $customerMessage->send_all) {
            $selectedCustomers = null;
        } else {
            $selectedCustomers = DB::table('customers_assigned_messages')->pluck('customer_id')->toArray();
        }

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('id')
            ->toArray();

        $agencies = Auth::user()->listsAgencies();

        $types = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $sellers = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->isSeller()
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $pricesTables = PriceTable::remember(config('cache.query_ttl'))
            ->cacheTags(PriceTable::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        $routes = Route::listsWithCode(Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->get());

        $data = compact(
            'customerMessage',
            'action',
            'formOptions',
            'customers',
            'selectedCustomers',
            'agencies',
            'types',
            'sellers',
            'pricesTables',
            'routes'
        );

        return view('admin.customers.messages.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $input = $request->all();

        $customers           = $request->get('selected_customers');
        $input['send_all']   = $request->get('send_all', false);
        $input['send_email'] = $request->get('send_email', false);
        $input['is_static']  = $request->get('is_static', false);

        if ($input['send_all']) {
            $customers = Customer::whereSource(config('app.source'))
                ->filterAgencies()
                ->isActive()
                ->pluck('id')
                ->toArray();
        }

        $customerMessage = CustomerMessage::filterAgencies()->findOrNew($id);

        $exists = $customerMessage->exists;
        if ($customerMessage->validate($input)) {
            $customerMessage->fill($input);
            $customerMessage->source = config('app.source');
            $customerMessage->save();

            if (!$input['is_static']) {
                $customerMessage->customers()->sync($customers);
            }

            if ($input['send_email']) {

                $emails = Customer::whereSource(config('app.source'))
                    ->filterAgencies()
                    ->where(function ($q) {
                        $q->where('contact_email', '<>', '')
                            ->whereNotNull('contact_email');
                    });

                if (!empty($customers)) {
                    $emails = $emails->whereIn('id', $customers);
                }

                $customerEmails = $emails->get(['contact_email', 'name']);

                $emails = $customerEmails->pluck('contact_email')->toArray();

                $customerEmailsArr = $customerEmails->pluck('name', 'contact_email')->toArray();

                $emails = implode(';', $emails);
                $emails = validateNotificationEmails($emails);
                $emails = $emails['valid'];

                //grava a lista de e-mails enviados
                $customerMessage->to_emails = $customerEmailsArr;
                $customerMessage->save();

                $emailParts = array_chunk($emails, 40);

                foreach ($emailParts as $emails) {

                    Mail::send('emails.customers.message', compact('customerMessage'), function ($message) use ($customerMessage, $emails) {
                        $message->bcc($emails)
                            ->from(config('mail.from.address'), config('mail.from.name'))
                            ->subject($customerMessage->subject);
                    });

                    sleep(2);
                }
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $customerMessage->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $result = CustomerMessage::filterSource()
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar eliminar a mensagem.');
        }

        return Redirect::route('admin.customers.messages.index')->with('success', 'Mensagem eliminada com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        $ids = explode(',', $request->ids);

        $result = CustomerMessage::filterSource()
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
    public function datatable(Request $request)
    {

        $data = CustomerMessage::filterSource()
            ->filterAgencies()
            ->select();

        return Datatables::of($data)
            ->edit_column('subject', function ($row) {
                return view('admin.customers.messages.datatables.subject', compact('row'))->render();
            })
            ->edit_column('send_all', function ($row) {
                return view('admin.customers.messages.datatables.send_all', compact('row'))->render();
            })
            ->edit_column('send_email', function ($row) {
                return view('admin.customers.messages.datatables.send_email', compact('row'))->render();
            })
            ->edit_column('is_static', function ($row) {
                return view('admin.customers.messages.datatables.is_static', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.customers.messages.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableRecipients(Request $request, $id)
    {

        $bindings = [
            'customers_assigned_messages.*',
            'customers.name',
            'customers.code',
            'customers.contact_email',
        ];

        $data = DB::table('customers_assigned_messages')
            ->join('customers', 'customers_assigned_messages.customer_id', '=', 'customers.id')
            ->where('message_id', $id)
            ->select($bindings);

        return Datatables::of($data)
            ->edit_column('customers.code', function ($row) {
                return $row->code;
            })
            ->edit_column('customers.name', function ($row) {
                return $row->name;
            })
            ->edit_column('customers.contact_email', function ($row) {
                return $row->contact_email;
            })
            ->edit_column('is_read', function ($row) {
                return view('admin.customers.messages.datatables.is_read', compact('row'))->render();
            })
            ->edit_column('deleted_at', function ($row) {
                return view('admin.customers.messages.datatables.deleted_at', compact('row'))->render();
            })
            ->make(true);
    }
}
