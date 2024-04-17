<?php

namespace App\Http\Controllers\Admin\Budgets;

use App\Models\Budget\BudgetCourierService;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Response;

class CourierServicesController extends \App\Http\Controllers\Admin\Controller {

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
    public function index() {

        $type = new BudgetCourierService();

        $formOptions = array('route' => array('admin.budgets.courier.services.store'), 'method' => 'POST', 'class' => 'modal-ajax-form');

        return view('admin.budgets.budgets_courier.services.index', compact('type', 'formOptions'))->render();
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        
        $input = $request->all();

        $type = BudgetCourierService::filterSource()->findOrNew($id);

        if ($type->validate($input)) {
            $type->fill($input);
            $type->source = config('app.source');
            $type->save();

            $row = $type;
            return Response::json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.',
                'html'     => view('admin.budgets.budgets_courier.services.datatables.name', compact('row'))->render()
            ]);
        }

        return Response::json([
            'result'   => false,
            'feedback' => $type->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = BudgetCourierService::filterSource()
                                ->whereId($id)
                                ->delete();

        if (!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Ocorreu um erro ao tentar remover o tipo de cliente.'
            ]);
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'Tipo de cliente removido com sucesso.'
        ]);
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
        
        $result = BudgetCourierService::filterSource()
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

        $data = BudgetCourierService::filterSource()
                            ->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.budgets.budgets_courier.services.datatables.name', compact('row'))->render();
                })
                ->edit_column('type', function($row) {
                    return trans('admin/budgets.types.' . $row->type);
                })
                ->add_column('actions', function($row) {
                    return view('admin.budgets.budgets_courier.services.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }

}
