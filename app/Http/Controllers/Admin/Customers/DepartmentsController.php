<?php

namespace App\Http\Controllers\Admin\Customers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class DepartmentsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'customers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customers']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($customerId)
    {

        $action = 'Adicionar Departamento';

        $customer = Customer::filterSource()->find($customerId);

        $department = new Customer;
        $department->customer = $customer;

        $agencies = Auth::user()->listsAgencies();

        $formOptions = array('route' => array('admin.customers.departments.store', $customerId), 'method' => 'POST');

        return view('admin.customers.customers.partials.departments.edit', compact('department', 'action', 'formOptions'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $customerId)
    {
        return $this->update($request, $customerId, null);
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
    public function edit($customerId, $id)
    {

        $action = 'Editar Departamento';

        $department = Customer::where('customer_id', $customerId)
            ->findOrfail($id);

        $agencies = Auth::user()->listsAgencies();

        $formOptions = array('route' => array('admin.customers.departments.update', $department->customer_id, $department->id), 'method' => 'PUT');

        return view('admin.customers.customers.partials.departments.edit', compact('department', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $customerId, $id)
    {

        $input = $request->all();

        $customer = Customer::findOrFail($customerId);

        $department = Customer::where('customer_id', $customerId)
            ->findOrNew($id);


        if ($department->validate($input)) {
            $department->agency_id   = $customer->agency_id;
            $department->type_id     = $customer->type_id;
            $department->customer_id = $customer->id;
            $department->fill($input);
            $department->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $customer->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($customerId, $id)
    {

        $result = Customer::where('customer_id', $customerId)
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o departamento.');
        }

        return Redirect::back()->with('success', 'Departamento removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request, $customerId)
    {

        $ids = explode(',', $request->ids);

        $result = Customer::where('customer_id', $customerId)
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
    public function datatable(Request $request, $customerId)
    {

        $data = Customer::where('customer_id', $customerId)
            ->select();

        return Datatables::of($data)
            ->add_column('photo', function ($row) {
                return view('admin.partials.datatables.photo', compact('row'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.customers.customers.datatables.departments.name', compact('row'))->render();
            })
            ->add_column('contacts', function ($row) {
                return view('admin.customers.customers.datatables.departments.contacts', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.customers.customers.datatables.departments.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
