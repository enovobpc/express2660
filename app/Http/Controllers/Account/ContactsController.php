<?php

namespace App\Http\Controllers\Account;

use App\Models\CustomerContact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;
use DB, View;

class ContactsController extends \App\Http\Controllers\Controller
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
    protected $sidebarActiveOption = 'customer-contacts';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Contacto';

        $contact = new CustomerContact();

        $formOptions = array('route' => array('account.contacts.store'), 'method' => 'POST');

        return view('account.details.edit_contact', compact('contact', 'action', 'formOptions'))->render();
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
        
        $action = 'Editar Contacto';
        
        $customer = Auth::guard('customer')->user();

        $contact = CustomerContact::where('customer_id', $customer->id)
                                        ->findOrfail($id);

        $formOptions = array('route' => array('account.contacts.update', $contact->id), 'method' => 'PUT');

        return view('account.details.edit_contact', compact('contact', 'action', 'formOptions'))->render();
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

        $contact = CustomerContact::where('customer_id', $customer->id)
                                        ->findOrNew($id);

        if ($contact->validate($input)) {
            $contact->customer_id = $customer->id;
            $contact->fill($input);
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
    public function destroy($id) {
        
        $customer = Auth::guard('customer')->user();

        $result = CustomerContact::where('customer_id', $customer->id)
                                ->whereId($id)
                                ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o contacto.');
        }

        return Redirect::back()->with('success', 'Contacto removido com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $customer = Auth::guard('customer')->user();
                
        $data = CustomerContact::where('customer_id', $customer->id)
                                 ->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('account.details.datatables.contacts.name', compact('row'))->render();
                })
                ->edit_column('phone', function($row) {
                    return view('account.details.datatables.contacts.phone', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('account.details.datatables.contacts.actions', compact('row'))->render();
                })
                ->make(true);
    }
   
}