<?php

namespace App\Http\Controllers\Admin\Banks;

use App\Models\PaymentCondition;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Response;

class PaymentConditionsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'payment_conditions';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',payment_conditions']);
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
    public function create() {
        
        $action = 'Adicionar Condição de Pagamento';
        
        $condition = new PaymentCondition;
                
        $formOptions = array('route' => array('admin.payment-conditions.store'), 'method' => 'POST');
        
        return view('admin.banks.edit_payment_condition', compact('condition', 'action', 'formOptions'))->render();
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
        
        $action = 'Editar Condição de Pagamento';
        
        $condition = PaymentCondition::findOrfail($id);

        $formOptions = array('route' => array('admin.payment-conditions.update', $condition->id), 'method' => 'PUT');

        return view('admin.banks.edit_payment_condition', compact('condition', 'action', 'formOptions'))->render();
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
        $input['is_active']         = $request->get('is_active', false);
        $input['sales_visible']     = $request->get('sales_visible', false);
        $input['purchases_visible'] = $request->get('purchases_visible', false);
        $input['days']              = $request->get('days', '30');

        $condition = PaymentCondition::findOrNew($id);
        
        if ($condition->validate($input)) {
            $condition->fill($input);
            $condition->source = config('app.source');
            $condition->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $condition->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = PaymentCondition::whereId($id)->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::back()->with('success', 'Registo removido com sucesso.');
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
        
        $result = PaymentCondition::whereIn('id', $ids)->delete();
        
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

        $data = PaymentCondition::select();

        return Datatables::of($data)
            ->editColumn('name', function($row) {
                return view('admin.banks.datatables.payment_conditions.name', compact('row'));
            })
            ->editColumn('sales_visible', function($row) {
                return view('admin.banks.datatables.payment_conditions.sales_visible', compact('row'));
            })
            ->editColumn('purchases_visible', function($row) {
                return view('admin.banks.datatables.payment_conditions.purchases_visible', compact('row'));
            })
            ->editColumn('active', function($row) {
                return view('admin.banks.datatables.payment_conditions.active', compact('row'));
            })
            ->addColumn('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->addColumn('actions', function($row) {
                return view('admin.banks.datatables.payment_conditions.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     * GET /admin/features/sort
     *
     * @return Response
     */
    public function sortEdit() {

        $items = PaymentCondition::orderBy('sort')->get();

        $route = route('admin.payment-conditions.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/features/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        $result = PaymentCondition::setNewOrder($request->ids);

        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }
}
