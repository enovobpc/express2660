<?php

namespace App\Http\Controllers\Account;

use App\Models\Customer;
use App\Models\Custumer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;
use DB, View;

class DepartmentsController extends \App\Http\Controllers\Controller
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
    protected $sidebarActiveOption = 'departments';
    
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
        return $this->setContent('account.departments.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $action = 'Adicionar Departamento';

        $department = new Customer();

        $formOptions = array('route' => array('account.departments.store'), 'method' => 'POST');

        return view('account.departments.edit', compact('department', 'action', 'formOptions'))->render();
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
    public function edit($id) {
        
        $action = 'Editar Departamento';
        
        $customer = Auth::guard('customer')->user();

        $department = Customer::where('customer_id', $customer->id)
                                ->findOrfail($id);

        $formOptions = array('route' => array('account.departments.update', $department->id), 'method' => 'PUT');

        return view('account.departments.edit', compact('department', 'action', 'formOptions'))->render();
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

        $department = Customer::where('customer_id', $customer->id)
                               ->findOrNew($id);

        if ($department->validate($input)) {
            $department->fill($input);
            $department->source      = $customer->source;
            $department->customer_id = $customer->id;
            $department->agency_id   = $customer->agency_id;
            $department->view_parent_shipments = 0;
            $department->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $department->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $customer = Auth::guard('customer')->user();

        $result = Customer::where('customer_id', $customer->id)
                           ->whereId($id)
                           ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o departamento.');
        }

        return Redirect::back()->with('success', 'Departmento removido com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $customer = Auth::guard('customer')->user();
                
        $data = Customer::where('customer_id', $customer->id)
                                 ->select();

        return Datatables::of($data)
                        ->add_column('photo', function($row) {
                            return view('admin.partials.datatables.photo', compact('row'))->render();
                        })
                        ->edit_column('name', function($row) {
                            return view('account.departments.datatables.name', compact('row'))->render();
                        })
                        ->add_column('contacts', function($row) {
                            return view('account.departments.datatables.contacts', compact('row'))->render();
                        })
                        ->add_column('select', function($row) {
                            return view('admin.partials.datatables.select', compact('row'))->render();
                        })
                        ->add_column('actions', function($row) {
                            return view('account.departments.datatables.actions', compact('row'))->render();
                        })
                        ->make(true);
    }
   
}