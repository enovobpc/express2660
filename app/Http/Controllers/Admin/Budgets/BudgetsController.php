<?php

namespace App\Http\Controllers\Admin\Budgets;

use App\Models\Agency;
use App\Models\Budget;
use App\Models\CacheSetting;
use App\Models\Provider;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Auth, Response, File, Setting;

class BudgetsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'budgets';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',budgets']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->setContent('admin.budgets.budgets_email.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $action = 'Inserir Pedido de Orçamento';

        $budget = new Budget\Budget();

        $formOptions = array('route' => array('admin.budgets.store'), 'method' => 'POST');

        return view('admin.budgets.budgets_email.edit', compact('budget', 'action', 'formOptions'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $budget = Budget\Budget::filterAgencies()->findOrfail($id);

        $providers = Provider::filterAgencies()
            ->remember(5)
            //->ordered()
            ->isCarrier()
            ->pluck('name', 'id')
            ->toArray();

        $shippingStatus = ShippingStatus::remember(5)
            ->isVisible()
            //->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $unreadProposes = Budget\Propose::where('budget_id', $budget->id)
            ->where('read', 0)
            ->count();

        $operators = User::remember(5)
            ->filterAgencies()
            ->where('id', '>', 1)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        return $this->setContent('admin.budgets.budgets_email.show', compact('budget', 'providers', 'shippingStatus', 'unreadProposes', 'operators'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $action = 'Editar Pedido de Orçamento';

        $budget = Budget\Budget::filterAgencies()->findOrfail($id);

        $budget->tracking_code = $budget->shipment_id ? @$budget->shipment->tracking_code : null;

        $formOptions = array('route' => array('admin.budgets.update', $budget->id), 'method' => 'PUT');

        return view('admin.budgets.budgets_email.edit', compact('budget', 'action', 'formOptions'))->render();
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

        $budget = Budget\Budget::filterAgencies()->findOrNew($id);

        $exists = $budget->exists;

        if (!empty($input['tracking_code'])) {
            $shipment = Shipment::where('tracking_code', $input['tracking_code'])->first();
            $input['shipment_id'] = $shipment->id;
        }

        if ($budget->validate($input)) {
            $budget->fill($input);
            $budget->source = config('app.source');

            if (!$exists) {
                $budget->setBudgetCode();
                return Redirect::route('admin.budgets.show', $budget->id)->with('success', 'Dados gravados com sucesso.');
            } else {
                $budget->save();

                return Redirect::back()->with('success', 'Dados gravados com sucesso.');
            }
        }

        return Redirect::back()->withInput()->with('error', $budget->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $budget = Budget\Budget::filterAgencies()->whereId($id)->first();

        $budget->deleteNotification();
        $result = $budget->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o orçamento.');
        }

        return Redirect::back()->with('success', 'Orçamento removido com orçamento.');
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

        $result = Budget\Budget::filterAgencies()->whereIn('id', $ids)->delete();

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

        $data = Budget\Budget::filterAgencies()
            ->with('shipment')
            ->select();

        //limit search
        $value = $request->limit_search;
        if ($request->has('limit_search') && !empty($value)) {
            $minId = (int) CacheSetting::get('budgets_limit_search');
            if ($minId) {
                $data = $data->where('id', '>=', $minId);
            }
        }

        //filter status
        $value = $request->get('status');
        if ($request->has('status')) {
            $data = $data->where('status', $value);
        } else {
            if ($value = $request->get('hide_final_status')) {
                $data = $data->whereNotIn('status', ['rejected', 'concluded', 'outdated', 'no-solution']);
            }
        }

        //filter provider status
        $value = $request->get('provider_status');
        if ($request->has('provider_status')) {
            $data = $data->where('provider_status', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if ($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {

            $dtMax = $dtMin;

            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        return Datatables::of($data)
            ->edit_column('id', function ($row) {
                return view('admin.budgets.budgets_email.datatables.budget_no', compact('row'))->render();
            })
            ->edit_column('subject', function ($row) {
                return view('admin.budgets.budgets_email.datatables.subject', compact('row'))->render();
            })
            ->edit_column('date', function ($row) {
                return $row->date->format('Y-m-d');
            })
            ->edit_column('user_id', function ($row) {
                return view('admin.budgets.budgets_email.datatables.responsable', compact('row'))->render();
            })
            ->edit_column('provider', function ($row) {
                return $row->provider;
            })
            ->edit_column('shipment_id', function ($row) {
                return view('admin.budgets.budgets_email.datatables.tracking', compact('row'))->render();
            })
            ->edit_column('courier_budget_id', function ($row) {
                return view('admin.budgets.budgets_email.datatables.budget_id', compact('row'))->render();
            })
            ->edit_column('status', function ($row) {
                return view('admin.budgets.budgets_email.datatables.status', compact('row'))->render();
            })
            ->edit_column('provider_status', function ($row) {
                return view('admin.budgets.budgets_email.datatables.provider_status', compact('row'))->render();
            })
            ->edit_column('total', function ($row) {
                return $row->total ? '<b>' . money($row->total, Setting::get('app_currency')) . '</b>' : null;
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.budgets.budgets_email.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Sync budget emails
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function syncEmails()
    {

        try {
            $message = new Budget\Message();
            $message->checkAnswers();
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Falha de sincronização. Motivo: ' . $e->getMessage() . ' linha ' . $e->getLine() . ' file ' . $e->getFile());
        }

        return Redirect::back()->with('success', 'E-mails sincronizados com sucesso.');
    }


    /**
     * Show modal of contacts list
     *
     * @return \Illuminate\Http\Response
     */
    public function showContactsList()
    {

        try {
            $filename = storage_path() . '/budgets_contacts_list.json';
            $contacts = json_decode(File::get($filename));
        } catch (\Exception $e) {
            $contacts = null;
        }

        return view('admin.budgets.modals.contacts_list', compact('contacts'))->render();
    }

    /**
     * Show modal of contacts list
     *
     * @return \Illuminate\Http\Response
     */
    public function updateContactsList(Request $request)
    {

        $input = $request->all();

        $data = null;
        foreach ($input['contacts'] as $key => $contacts) {
            if (!empty($contacts)) {
                $data[$input['group'][$key]] = $contacts;
            }
        }

        $data = json_encode($data);
        $filename = storage_path() . '/budgets_contacts_list.json';
        File::put($filename, $data);

        return Redirect::back()->with('success', 'Contactos gravados com sucesso.');
    }

    /**
     * Adjudicate budget to current customer
     *
     * @return \Illuminate\Http\Response
     */
    public function adjudicate($id)
    {

        $budget = Budget\Budget::filterAgencies()->findOrNew($id);
        $budget->user_id = Auth::user()->id;
        $budget->save();

        return Redirect::back()->with('success', 'Orçamento adjudicado com sucesso.');
    }

    /**
     * Download attachment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attachment($id, $nameSlug)
    {

        $budget = Budget\Budget::filterAgencies()->findOrfail($id);

        $data = null;
        $filename = null;
        foreach ($budget->attachments as $attachment) {
            $filename = $attachment->name;

            $filenameSlug = str_slug($attachment->name);
            if ($filenameSlug == $nameSlug) {
                $contentType = $attachment->content_type;
                $data = $attachment->content;
            }
        }

        header('content-type:' . $contentType);
        echo base64_decode($data);
    }

    /**
     * Show the form for merge two budgets.
     *
     * @return \Illuminate\Http\Response
     */
    public function mergeBudget(Request $request, $budgetId)
    {
        return view('admin.budgets.budgets_email.modals.merge', compact('budgetId'))->render();
    }

    /**
     * Merge two budgets
     *
     * @return \Illuminate\Http\Response
     */
    public function mergeBudgetStore(Request $request, $budgetId)
    {

        $sourceBudget = Budget\Budget::filterAgencies()->findOrfail($budgetId);
        $destinationBudget = Budget\Budget::filterAgencies()->findOrfail($request->assign_budget_id);

        try {
            $message = new Budget\Message();
            $message->budget_id   = $destinationBudget->id;
            $message->from        = $sourceBudget->email;
            $message->from_name   = $sourceBudget->name;
            $message->subject     = $sourceBudget->subject;
            $message->message     = $sourceBudget->message;
            $message->attachments = $sourceBudget->attachments;
            $message->created_at  = $sourceBudget->created_at;
            $message->save();

            $sourceBudget->merged = true;
            $sourceBudget->save();

            $sourceBudget->delete();
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro ao convergir orçamentos. ' . $e->getMessage());
        }

        return Redirect::back()->with('success', 'Orçamento convergido com sucesso.');
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchBudget(Request $request)
    {

        $search = $request->get('q');
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $budgets = Budget\Budget::filterSource()
                ->where(function ($q) use ($search) {
                    $q->where('budget_no', 'LIKE', $search)
                        ->orWhere('subject', 'LIKE', $search)
                        ->orWhere('email', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search);
                })
                ->get(['subject', 'budget_no', 'id']);

            if ($budgets) {

                $results = array();
                foreach ($budgets as $budget) {
                    $results[] = array('id' => $budget->id, 'text' => $budget->budget_no . ' - ' . str_limit($budget->subject, 40));
                }
            } else {
                $results = [['id' => '', 'text' => 'Nenhum orçamento encontrado.']];
            }
        } catch (\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableShipments(Request $request)
    {

        $bindings = [
            'id', 'tracking_code', 'type', 'parent_tracking_code', 'children_tracking_code', 'children_type',
            'agency_id', 'sender_agency_id', 'recipient_agency_id',
            'service_id', 'provider_id', 'status_id', 'operator_id', 'customer_id',
            'sender_name', 'sender_address', 'sender_zip_code', 'sender_city', 'sender_phone',
            'recipient_name', 'recipient_address', 'recipient_zip_code', 'recipient_city', 'recipient_phone', 'recipient_country',
            'obs', 'volumes', 'weight', 'total_price', 'date'
        ];

        $data = Shipment::filterAgencies()
            ->with('service', 'provider', 'status', 'operator', 'customer')
            ->select($bindings);

        $agencies = Agency::filterAgencies()->remember(5)->get(['name', 'code', 'id', 'color']);
        $agencies = $agencies->groupBy('id')->toArray();


        //filter provider
        $value = $request->provider;
        if ($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        //filter status
        $value = $request->status;
        if ($request->has('status')) {
            $data = $data->where('status_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('service_id', function ($row) use ($agencies) {
                return view('admin.shipments.shipments.datatables.service', compact('row', 'agencies'))->render();
            })
            ->edit_column('id', function ($row) use ($agencies) {
                return view('admin.shipments.shipments.datatables.tracking', compact('row', 'agencies'))->render();
            })
            ->edit_column('sender_name', function ($row) {
                return view('admin.shipments.shipments.datatables.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function ($row) {
                return view('admin.shipments.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('status_id', function ($row) {
                return view('admin.shipments.shipments.datatables.status', compact('row'))->render();
            })
            ->edit_column('volumes', function ($row) {
                return view('admin.shipments.shipments.datatables.volumes', compact('row'))->render();
            })
            ->edit_column('date', function ($row) {
                return view('admin.shipments.shipments.datatables.date', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.budgets.budgets_email.datatables.shipments.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
