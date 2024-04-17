<?php

namespace App\Http\Controllers\Admin\Budgets;

use App\Models\Budget\Ticket;
use App\Models\Budget\BudgetCourier;
use App\Models\Budget\BudgetCourierModel;
use App\Models\Budget\BudgetCourierService;
use App\Models\Budget\BudgetCourierHistory;
use App\Models\Budget\Message;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Auth, Response, File, Mail, Setting, Date;

class CourierController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'budgets_courier';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',budgets_courier|budgets_animals']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->orderBy('source', 'asc')
            ->orderBy('code', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name']), true);

        return $this->setContent('admin.budgets.budgets_courier.index', compact('operators'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $type = $request->get('type', 'courier');

        $action = 'Inserir Pedido de Orçamento';

        $budget = new BudgetCourier();
        $budget->type = $type;
        $budget->budget_date = date('Y-m-d');

        $formOptions = array('route' => array('admin.budgets.courier.store'), 'method' => 'POST', 'class' => 'form-courier-budget');

        $operators = User::remember(5)
            ->filterAgencies()
            ->where('id', '>', 1)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $services = BudgetCourierService::filterSource()
                        ->whereType($budget->type)
                        ->get()
                        ->pluck('name', 'id')
                        ->toArray();

        $models = BudgetCourierModel::filterSource()
                        ->whereType($budget->type)
                        ->get()
                        ->pluck('name', 'id')
                        ->toArray();

        $defaultModel = BudgetCourierModel::filterSource()->first();

        $courierServices = Service::remember(5)
            ->filterAgencies()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.budgets.budgets_courier.edit', compact('budget', 'action', 'formOptions', 'operators', 'services', 'models', 'defaultModel', 'courierServices'))->render();
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
//    public function show($id) {
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $action = 'Editar Pedido de Orçamento';

        $budget = BudgetCourier::filterAgencies()->findOrfail($id);

        $formOptions = array('route' => array('admin.budgets.courier.update', $budget->id), 'method' => 'PUT', 'class' => 'form-courier-budget');

        $operators = User::remember(5)
            ->filterAgencies()
            ->where('id', '>', 1)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        if($budget->locale != Setting::get('app_country')) {
            $services = BudgetCourierService::filterSource()
                ->whereType($budget->type)
                ->get()
                ->pluck('name_' . $budget->locale, 'id')
                ->toArray();
        } else {
            $services = BudgetCourierService::filterSource()
                ->whereType($budget->type)
                ->get()
                ->pluck('name', 'id')
                ->toArray();
        }

        $models = BudgetCourierModel::filterSource()
            ->whereType($budget->type)
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $defaultModel = BudgetCourierModel::filterSource()->first();

        $courierServices = Service::remember(5)
            ->filterAgencies()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'budget',
            'action',
            'formOptions',
            'operators',
            'services',
            'models',
            'defaultModel',
            'courierServices'
        );

        return view('admin.budgets.budgets_courier.edit', $data)->render();
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
        $input['status'] = empty($input['status']) ? 'pending' : $input['status'];
        $input['geral_conditions_separated'] = $request->get('geral_conditions_separated', false);

        $budget = BudgetCourier::filterAgencies()->findOrNew($id);

        if(!empty($input['customer_id'])) {
            $customer = Customer::filterAgencies()->find($input['customer_id']);
            $input['name'] = $customer->name;
        }

        $exists = $budget->exists ? true : false;

        $data = [];

        if(!empty($input['goods'])) {
            $i = 0;
            foreach ($input['goods'] as $good) {

                if(!empty($good['volumes'])) {
                    $data[] = $good;
                    $i++;
                }
            }
            $input['goods'] = $data;
        }

        if(!empty($input['animals'])) {
            foreach ($input['animals'] as $animal) {
                if(!empty($animal['type'])) {
                    $data[] = $animal;
                }
            }
            $input['animals'] = $data;
        }

        $data = [];
        foreach ($input['airports'] as $airport) {
            if(!empty($airport['source'])) {
                $data[] = $airport;
            }
        }
        $input['airports'] = $data;

        $data = [];
        foreach ($input['services'] as $service) {
            if(!empty($service['service_id'])) {
                $data[] = $service;
            }
        }
        $input['services'] = $data;

        if ($budget->validate($input)) {

            $originalStatus = @$budget->status;

            $budget->fill($input);
            $budget->operator_id = Auth::user()->id;
            $budget->source = config('app.source');

            $total = 0;
            $totalVat = 0;
            foreach ($input['services'] as $key => $service) {

                if (!empty(@$service['service_id'])) {
                    $total += @$service['subtotal'];
                    $service['vat'] = empty($service['vat']) ? 0 : $service['vat'];
                    $service['subtotal'] = empty($service['subtotal']) ? 0 : $service['subtotal'];
                    $totalVat += @$service['subtotal'] * (@$service['vat'] / 100);
                }
            }


            $validityDate = new Date($input['budget_date']);
            $validityDate = $validityDate->addDays($input['validity_days'])->format('Y-m-d');

            $budget->total = $total;
            $budget->total_vat = $totalVat;
            $budget->validity_date = $validityDate;

            if (!$exists) {
                $budget->setBudgetCode();
            }

            //events when change status
            if (!$exists || ($exists && isset($input['status']) && $originalStatus != $input['status'])) {
                $history = new BudgetCourierHistory();
                $history->budget_id = $budget->id;
                $history->status = $input['status'];
                $history->operator_id = Auth::user()->id;
                $history->save();

                $budget->status_date = date('Y-m-d H:i:s');
                $budget->save();
            }

            //update budget email report
            if(hasModule('budgets')) {
                $budgetEmail = Ticket::where('courier_budget_id', $budget->id)->first();
                if(!empty($budgetEmail)) {
                    $budgetEmail->status = $budget->status;
                    $budgetEmail->save();
                }
            }

            $budget->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $budget->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $budget = BudgetCourier::filterSource()->whereId($id)->first();

        $budget->deleteNotification();
        $result = $budget->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o orçamento.');
        }

        return Redirect::back()->with('success', 'Serviço removido com orçamento.');
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

        $result = BudgetCourier::filterSource()->whereIn('id', $ids)->delete();

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

        $type = $request->get('type', 'courier');

        $data = BudgetCourier::filterAgencies()
                        ->whereType($type)
                        ->select();

        //filter web
        $value = $request->get('web');
        if($request->has('web')) {
            $data = $data->where('is_web', $value);
        }

        //filter status
        $value = $request->get('status');
        if($request->has('status')) {
            $data = $data->where('status', $value);
        }

        //filter provider status
        $value = $request->get('provider_status');
        if($request->has('provider_status')) {
            $data = $data->where('provider_status', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {

            $dtMax = $dtMin;

            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            $data = $data->whereBetween('created_at', [$dtMin.' 00:00:00', $dtMax . ' 23:59:59']);
        }

        //filter accepted
        $value = $request->hide_concluded;
        if($request->has('hide_concluded') && !empty($value)) {
            $data = $data->where('status', '<>','accepted')
                         ->where('status', '<>','concluded');
        }

        //filter rejected
        $value = $request->hide_rejected;
        if($request->has('hide_rejected') && !empty($value)) {
            $data = $data->where('status', '<>','rejected');
        }

        $data = $data->orderByRaw('FIELD(status,"wainting","pending","wainting-customer","accepted","concluded","rejected","outdated","no-solution")');

        return Datatables::of($data)
            ->edit_column('budget_no', function($row) {
                return view('admin.budgets.budgets_courier.datatables.budget_no', compact('row'))->render();
            })
            ->edit_column('name', function($row) {
                return view('admin.budgets.budgets_courier.datatables.name', compact('row'))->render();
            })
            /*->edit_column('email', function($row) {
                return view('admin.budgets.budgets_courier.datatables.email', compact('row'))->render();
            })*/
            ->add_column('delivery_airport', function($row) {
                return view('admin.budgets.budgets_courier.datatables.delivery_airport', compact('row'))->render();
            })
            ->edit_column('operator_id', function($row) {
                return view('admin.budgets.budgets_courier.datatables.responsable', compact('row'))->render();
            })
            ->edit_column('status', function($row) {
                return view('admin.budgets.budgets_courier.datatables.status', compact('row'))->render();
            })
            ->edit_column('validity_date', function($row) {
                return view('admin.budgets.budgets_courier.datatables.validity_date', compact('row'))->render();
            })
            ->edit_column('status_date', function($row) {
                return view('admin.budgets.budgets_courier.datatables.status_date', compact('row'))->render();
            })
            ->edit_column('total', function($row) {
                return $row->total ? '<b>'. money($row->total + $row->total_vat, Setting::get('app_currency')) .'</b>' : null;
            })
            ->edit_column('budget_date', function($row) {
                return view('admin.budgets.budgets_courier.datatables.budget_date', compact('row'))->render();
            })
            ->edit_column('locale', function($row) {
                return view('admin.budgets.budgets_courier.datatables.locale', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.budgets.budgets_courier.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Print Budget document
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printBudget($id) {
        return BudgetCourier::printBudget([$id]);
    }

    /**
     * Show modal to edit billing emaill
     * @param Request $request
     * @param $id
     */
    public function editEmail(Request $request, $id) {

        $budget = BudgetCourier::filterAgencies()->findOrfail($id);

        if($budget->type == 'courier') {
            $defaultMessage = Setting::get('budgets_courier_mail_default_answer');
            if($budget->locale == 'en') {
                $defaultMessage = Setting::get('budgets_courier_mail_default_answer_en');
            }
            $subject = 'Cotação de Transporte de Carga Geral';
        } else {
            $defaultMessage = Setting::get('budgets_animals_mail_default_answer');
            if($budget->locale == 'en') {
                $defaultMessage = Setting::get('budgets_animals_mail_default_answer_en');
            }
            $subject = 'Cotação de Transporte de Animal(s)';
        }

        $defaultMessage = str_replace(':budgetNo', $budget->budget_no, $defaultMessage);
        $defaultMessage = str_replace(':nmCliente', $budget->name, $defaultMessage);

        return view('admin.budgets.budgets_courier.modals.email', compact('budget', 'defaultMessage', 'subject'))->render();
    }

    /**
     * Send billing info by e-mail
     * @param Request $request
     * @param $id
     */
    public function sendEmail(Request $request, $id) {

        $data = $request->all();
        $answered = $request->get('answered', false);

        $budget = BudgetCourier::filterAgencies()->findOrfail($id);

        $attachmentFile = BudgetCourier::printBudget([$id], 'S');

        $emails = validateNotificationEmails($data['email']);
        $emails = $emails['valid'];

        try {

            Mail::send('emails.budgets.'.$budget->type, compact('data'), function($message) use($attachmentFile, $data, $emails, $budget) {

                $message->to($emails);

                if($budget->type == 'courier' && Setting::get('budgets_courier_mail_cc')) {

                    $ccEmails = validateNotificationEmails(Setting::get('budgets_courier_mail_cc'));
                    if($ccEmails['valid']) {
                        $message->cc($ccEmails['valid']);
                    }

                } elseif($budget->type == 'animals' && Setting::get('budgets_animals_mail_cc')) {

                    $ccEmails = validateNotificationEmails(Setting::get('budgets_animals_mail_cc'));
                    if($ccEmails['valid']) {
                        $message->cc($ccEmails['valid']);
                    }
                }

                if(hasModule('budgets')) {
                    $fromAddress = trim(Setting::get('budgets_mail'));
                    $fromName    = trim(Auth::user()->name);
                } else {
                    $fromAddress = trim(Auth::user()->email);
                    $fromName    = trim(Auth::user()->name);
                }

                $subject = '[ORC-' . $budget->budget_no . '] ' . $data['subject'];

                $message = $message->from($fromAddress, $fromName)
                                   ->subject($subject);

                //attach file
                if($budget->type == 'animals') {
                    $filename =  'Transporte Animais - Ref_'.$budget->budget_no.'.pdf';
                } else {
                    $filename =  'Orçamento de Transporte - Ref_'.$budget->budget_no.'.pdf';
                }

                $message->attachData($attachmentFile, $filename, ['mime' => 'application/pdf']);

            });

            if($answered) {
                if(!in_array($budget->status, ['accepted', 'rejected'])) {
                    $budget->status = 'wainting-customer';
                    $budget->status_date = date('Y-m-d H:i:s');
                    $budget->save();

                    $history = new BudgetCourierHistory();
                    $history->budget_id = $budget->id;
                    $history->status = 'wainting-customer';
                    $history->operator_id = Auth::user()->id;
                    $history->save();
                }
            }


            if(hasModule('budgets')) {

                $budgetMsg = Ticket::firstOrNew([
                    'courier_budget_id' => $budget->id
                ]);

                $fromName = trim(Auth::user()->name);
                $fromEmail = Setting::get('budgets_mail');

                if($budgetMsg->exists) {
                    $msg = new Message();
                    $msg->budget_id = $budgetMsg->id;
                    $msg->from      = $fromEmail;
                    $msg->from_name = $fromName;
                    $msg->to        = $budget->email;
                    $msg->to_name   = $budget->name;
                    $msg->subject   = @$data['subject'];
                    $msg->message   = @$data['message'];
                    $msg->budget_id = $budget->id;
                    //$msg->attachments = $budgetMsg->id;
                    $msg->save();

                } else {
                    $budgetMsg->source  = config('app.source');
                    $budgetMsg->budget_no = $budget->budget_no;
                    $budgetMsg->subject = @$data['subject'];
                    $budgetMsg->name    = $budget->name;
                    $budgetMsg->email   = $budget->email;
                    $budgetMsg->message = @$data['message'];
                    $budgetMsg->status  = $budget->status;
                    $budgetMsg->total   = $budget->total;
                    $budgetMsg->user_id = $budget->operator_id;
                    $budgetMsg->date    = date('Y-m-d');
                    $budgetMsg->courier_budget_id = $budget->id;
                    $budgetMsg->save();
                }
            }


        } catch (\Exception $e) {

            return Response::json([
                'result'   => false,
                'feedback' => 'Não foi possível enviar o e-mail. ' . $e->getMessage()
            ]);
        }


        return Response::json([
            'result'   => true,
            'feedback' => 'E-mail enviado com sucesso.'
        ]);
    }

    /**
     * Get model details
     * @param Request $request
     * @param $id
     */
    public function getModel(Request $request) {

        $model = BudgetCourierModel::filterSource()->findOrfail($request->id);

        $result = $model->toArray();

        return Response::json($result);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request) {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $customers = Customer::filterAgencies()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->isDepartment(false)
                ->get(['name', 'code', 'id']);

            if($customers) {

                $results = array();
                foreach($customers as $customer) {
                    $results[]=array('id'=> $customer->id, 'text' => $customer->code. ' - '.str_limit($customer->name, 40));
                }

            } else {
                $results = [['id' => '', 'text' => 'Nenhum cliente encontrado.']];
            }

        } catch(\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }

    /**
     * Show modal to edit billing emaill
     * @param Request $request
     * @param $id
     */
    public function stats(Request $request) {

        $filter = $request->get('filter', false);
        $dtMin  = $request->get('date_min');
        $dtMax  = $request->get('date_max');
        $operatorId = $request->get('operator');
        $type   = $request->get('type');
        $web    = $request->get('web');

        $now   = new \Jenssegers\Date\Date();
        $start = !empty($dtMin) ? $dtMin : $now->firstOfMonth()->format('Y-m-d');
        $end   = !empty($dtMax) ? $dtMax : $now->lastOfMonth()->format('Y-m-d');

        $operators = User::remember(5)
            ->filterAgencies()
            ->where('id', '>', 1)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $status = BudgetCourierHistory::whereBetween('created_at', [$start, $end]);

        if(!empty($operatorId)) {
            $status = $status->where('operator_id', $operatorId);
        }

        if(!empty($type)) {
            $status = $status->whereHas('budget', function($q) use($type) {
                $q->where('type', $type);
            });
        }

        if(!empty($web)) {
            $status = $status->whereHas('budget', function($q) use($web) {
                $q->where('is_web', $web);
            });
        }

        $status = $status->get()->groupBy('status')->sortByDesc(function($item) {
            return count($item);
        });

        if($request->ajax() && $filter) {
            return Response::json([
                'html' => view('admin.budgets.budgets_courier.partials.stats_table', compact('status', 'operators', 'start', 'end'))->render()
            ]);
        }

        return view('admin.budgets.budgets_courier.modals.stats', compact('status', 'operators', 'start', 'end'))->render();
    }
}
