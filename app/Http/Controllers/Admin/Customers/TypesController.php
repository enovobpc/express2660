<?php

namespace App\Http\Controllers\Admin\Customers;

use Html, Response, Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\CustomerType;

class TypesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'customers_types';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customers_types']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $customerType = new CustomerType;

        $formOptions = array('route' => array('admin.customers-types.store'), 'method' => 'POST', 'class' => 'modal-ajax-form');

        return view('admin.customers.types.index', compact('customerType', 'formOptions'))->render();
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

        CustomerType::flushCache(CustomerType::CACHE_TAG);

        $input = $request->all();
        
        $customerType = CustomerType::filterSource()->findOrNew($id);

        if ($customerType->validate($input)) {
            $customerType->fill($input);
            $customerType->save();

            $row = $customerType;
            return Response::json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.',
                'html'     => view('admin.customers.types.datatables.name', compact('row'))->render()
            ]);
        }

        return Response::json([
            'result'   => false,
            'feedback' => $customerType->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        CustomerType::flushCache(CustomerType::CACHE_TAG);

        $result = CustomerType::filterSource()
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

        CustomerType::flushCache(CustomerType::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = CustomerType::filterSource()
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

        $data = CustomerType::filterSource()->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.customers.types.datatables.name', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.customers.types.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }
}
