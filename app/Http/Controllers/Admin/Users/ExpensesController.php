<?php

namespace App\Http\Controllers\Admin\Users;

use App\Console\Commands\RunDailyTasks;
use App\Models\PurchaseInvoiceType;
use App\Models\UserExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Date\Date;
use Yajra\Datatables\Facades\Datatables;
use Html, Response, Cache, Auth;

class ExpensesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'users_expenses';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',purchase_invoices']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function index() {
//    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $userId) {

        $isFixed = $request->get('fixed', false);

        $action = 'Registar Despesa de Colaborador';

        $expense = new UserExpense();
        $expense->user_id  = $userId;
        $expense->is_fixed = $isFixed;

        $formOptions = array('route' => array('admin.users.expenses.store', $userId), 'method' => 'POST', 'class' => 'modal-ajax-form');

        $expensesTypes = PurchaseInvoiceType::remember(config('cache.query_ttl'))
            ->cacheTags(PurchaseInvoiceType::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'expense',
            'action',
            'formOptions',
            'expensesTypes'
        );

        return view('admin.users.users.expenses.edit', $data)->render();
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $userId) {
        return $this->update($request, $userId, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $userId, $id) {

        $action = 'Editar Despesa de Colaborador';

        $expense = UserExpense::filterSource()
                    ->where('user_id', $userId)
                    ->findOrNew($id);

        $formOptions = array('route' => array('admin.users.expenses.update', $expense->user_id, $expense->id), 'method' => 'PUT', 'class' => 'modal-ajax-form');

        $expensesTypes = PurchaseInvoiceType::remember(config('cache.query_ttl'))
            ->cacheTags(PurchaseInvoiceType::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'expense',
            'action',
            'formOptions',
            'expensesTypes'
        );

        return view('admin.users.users.expenses.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId, $id) {

        UserExpense::flushCache(UserExpense::CACHE_TAG);

        $input = $request->all();

        $expense = UserExpense::filterSource()
                    ->where('user_id', $userId)
                    ->findOrNew($id);

        $exists = $expense->exists;

        if ($expense->validate($input)) {
            $expense->fill($input);
            $expense->source     = config('app.source');
            $expense->created_by = Auth::user()->id;
            $expense->user_id    = $userId;
            $expense->save();

            if(!$exists && $expense->is_fixed) {

                $startDate = new Date($input['start_date']);

                if($startDate->month == date('m') && $startDate->year == date('Y')) {
                    $dailyTask = new RunDailyTasks();
                    $dailyTask->storeUsersFixedCosts($startDate, true);
                }
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->with('error', $expense->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId, $id) {

        UserExpense::flushCache(UserExpense::CACHE_TAG);

        $result = UserExpense::filterSource()
                    ->where('user_id', $userId)
                    ->whereId($id)
                    ->delete();

        if (!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Ocorreu um erro ao tentar remover a despesa.'
            ]);
        }

        return Redirect::back()->with('error', 'Despesa removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request, $userId) {

        UserExpense::flushCache(UserExpense::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = UserExpense::filterSource()
                    ->where('user_id', $userId)
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
    public function datatable(Request $request, $userId) {

        $data = UserExpense::filterSource()
                ->where('user_id', $userId)
                ->select();

        //filter fixed
        if(!empty($request->fixed)) {
            $data = $data->where('is_fixed', 1);
        } else {
            $data = $data->where('is_fixed', 0);
        }

        //filter type
        $value = $request->type;
        if($request->has('type')) {
            $data = $data->whereIn('type_id', $value);
        }

        //filter provider
        $value = $request->provider;
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
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        return Datatables::of($data)
            ->edit_column('description', function($row) {
                return view('admin.users.users.expenses.datatables.description', compact('row'))->render();
            })
            ->edit_column('provider_id', function($row) {
                return view('admin.users.users.expenses.datatables.provider', compact('row'))->render();
            })
            ->edit_column('type_id', function($row) {
                return @$row->type->name;
            })
            ->edit_column('total', function($row) {
                return view('admin.users.users.expenses.datatables.total', compact('row'))->render();
            })
            ->edit_column('assigned_invoice_id', function($row) {
                return view('admin.users.users.expenses.datatables.invoice', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.users.users.expenses.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
