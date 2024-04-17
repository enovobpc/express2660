<?php

namespace App\Http\Controllers\Admin\Customers;

use Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\CustomerContact;

class ContactsController extends \App\Http\Controllers\Admin\Controller {

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
    public function create($customerId) {
        
        $action = 'Adicionar Contacto';
        
        $contact = new CustomerContact;
                
        $formOptions = array('route' => array('admin.customers.contacts.store', $customerId), 'method' => 'POST');
        
        return view('admin.customers.customers.partials.contacts.edit', compact('contact', 'action', 'formOptions'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $customerId) {
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
    public function edit($customerId, $id) {
        
        $action = 'Editar Contacto';
        
        $contact = CustomerContact::where('customer_id', $customerId)->findOrfail($id);

        $formOptions = array('route' => array('admin.customers.contacts.update', $contact->customer_id, $contact->id), 'method' => 'PUT');

        return view('admin.customers.customers.partials.contacts.edit', compact('contact', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $customerId, $id) {
        
        $input = $request->all();
        
        $contact = CustomerContact::whereHas('customer', function($q){
                            $q->filterAgencies();
                        })
                        ->where('customer_id', $customerId)
                        ->findOrNew($id);
        
        
        if ($contact->validate($input)) {
            $contact->fill($input);
            $contact->customer_id = $customerId;
            $contact->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $contact->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($customerId, $id) {
        
        $result = CustomerContact::whereHas('customer', function($q){
                            $q->filterAgencies();
                        })
                        ->where('customer_id', $customerId)
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o contacto.');
        }

        return Redirect::back()->with('success', 'Contacto removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request, $customerId) {
        
        $ids = explode(',', $request->ids);
        
        $result = CustomerContact::whereHas('customer', function($q){
                                $q->filterAgencies();
                            })
                            ->where('customer_id', $customerId)
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
    public function datatable(Request $request, $customerId) {

        $data = CustomerContact::whereHas('customer', function($q){
                                $q->filterAgencies();
                            })
                            ->where('customer_id', $customerId)
                            ->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.customers.customers.datatables.contacts.name', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->edit_column('created_at', function($row) {
                    return view('admin.partials.datatables.created_at', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.customers.customers.datatables.contacts.actions', compact('row'))->render();
                })
                ->make(true);
    }

}