<?php

namespace App\Http\Controllers\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Models\CustomerType;
use Croppa, Setting;

class DetailsController extends \App\Http\Controllers\Controller
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
    protected $sidebarActiveOption = 'details';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    
    /**
     * Customer details index controller
     * 
     * @return type
     */
    public function index() {

        $customer = Auth::guard('customer')->user();

        $editMode = Setting::get('account_edit_details');

        return $this->setContent('account.details.index', compact('customer', 'editMode'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customerUpdate(Request $request) {
        
        $input = $request->all();
        
        $customer = Auth::guard('customer')->user();

        if ($customer->validate($input)) {
            $customer->fill($input);
            $customer->save();

            return Redirect::back()->with('success', 'Dados alterados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $customer->errors()->first());
    }
    
    /**
     * Update customer login details
     * 
     * @param Request $request
     * @return type
     */
    public function loginUpdate(Request $request) {
        
        $input = $request->only('display_name', 'email', 'delete_photo');

        $customer = Auth::guard('customer')->user();

        $rules = [
            'email' => 'required|email|unique:customers,email,' . $customer->id
        ];
        
        $validator = Validator::make($input, $rules);
        
        if ($validator->passes()) {
            $customer->fill($input);
            
            if ($input['delete_photo'] && !empty($customer->filepath)) {
                Croppa::delete($customer->filepath);
                $customer->filepath = null;
                $customer->filename = null;
            }
            
            if($request->hasFile('image')) {
                if ($customer->exists && !empty($customer->filepath)) {
                    Croppa::delete($customer->filepath);
                }

                if (!$customer->upload($request->file('image'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem do perfil.');
                }
                
            } else {
                $customer->save();
            }
            
            return Redirect::back()->with('success', 'Dados de login atualizados com sucesso.');
        }
        
        return Redirect::back()->with('error', $validator->errors()->first());
    }

    
    /**
     * Update account password
     * 
     * @param Request $request
     * @return type
     */
    public function passwordUpdate(Request $request) 
    {  
        $input = $request->only('current_password', 'password', 'password_confirmation');

        $customer = Auth::guard('customer')->user();
        
        $rules = [
            'current_password' => 'required',
            'password'         => 'required|confirmed'
        ];
        
        $validator = Validator::make($input, $rules);
        
        if ($validator->passes()) {
            $customer->uncrypted_password = $input['password'];
            $customer->password = bcrypt($input['password']);
            $customer->save();
        
            return Redirect::back()->with('success', 'Palavra-passe alterada com sucesso.');
        }
        
        return Redirect::back()->with('error', $validator->errors()->first());
    }
}