<?php

namespace App\Http\Controllers\Admin\Budgets;

use App\Models\Budget\BudgetCourierModel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Croppa;

class CourierModelsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'budgets-courier';

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
    public function index() {
        return $this->setContent('admin.budgets.budgets_courier.models.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Modelo';
        
        $type = new BudgetCourierModel();
                
        $formOptions = array('route' => array('admin.budgets.courier.models.store'), 'method' => 'POST');
        

        return view('admin.budgets.budgets_courier.models.edit', compact('type', 'action', 'formOptions'))->render();
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
//        
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $action = 'Editar Modelo';
        
        $type = BudgetCourierModel::filterSource()->findOrfail($id);

        $formOptions = array('route' => array('admin.budgets.courier.models.update', $type->id), 'method' => 'PUT');

        return view('admin.budgets.budgets_courier.models.edit', compact('type', 'action', 'formOptions'))->render();
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

        $type = BudgetCourierModel::filterSource()->findOrNew($id);

        if ($type->validate($input)) {
            $type->fill($input);
            $type->source = config('app.source');
            $type->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $type->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = BudgetCourierModel::filterSource()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o modelo.');
        }

        return Redirect::route('admin.budgets.courier.models.index')->with('success', 'Modelo removido com sucesso.');
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
        
        $result = BudgetCourierModel::filterSource()
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

        $data = BudgetCourierModel::filterSource()->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.budgets.budgets_courier.models.datatables.name', compact('row'))->render();
                })
                ->edit_column('type', function($row) {
                    return trans('admin/budgets.types.' . $row->type);
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.budgets.budgets_courier.models.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }

}
