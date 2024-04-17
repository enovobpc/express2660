<?php

namespace App\Http\Controllers\Api\Partners;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Webservice\Base;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Date\Date;

use Auth, Validator, Setting, Log, DB;

class UsersController extends \App\Http\Controllers\Admin\Controller
{

      /**
     * Bindings
     *
     * @var array
     */
    protected $bindings = [
        'id',
        'code', 
        'code_abbrv', 
        'fullname',
        'name', 
        'email',
        'address',
        'city',
        'zip_code',
        'country',
        'phone', 
        'mobile', 
        'active',
        'obs',
        'last_login',
        'location_lat',
        'location_lng', 
        'location_last_update', 
        'is_operator',
        'login_app',
        'login_admin',
        'agency_id'
    ];


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}


    /**
     * Lista dos Colaboradores
     */

     public function lists(Request $request){

        $list = User::filterSource()
                     ->with(['agency' => function($q){
                         $q->select('name');
                     }])
                     ->with(['roles' => function($q){
                            $q->select('name');
                     }])
                     ->whereHas('roles', function($q){
                                    $q->whereIn('name', ['administrativo', 'agencia', 'operador']);
                      })
                     ->whereNotNull('source');
    
        //code
        if($request->has('code')) {
            $list = $list->where('code', $request->get('code'));
        }

        //agency
        if($request->has('agency')) {
            $list = $list->where('agency_id', $request->get('agency'));
        }

        //role
        $role = $request->role;
        if($request->has('role')){
            $list = $list->whereHas('roles', function($q) use($role){
                                    $q->where('name', $role);
            });
        }
        
        $list  = $list->orderBy('id', 'desc')->get($this->bindings);


        return response($list, 200)->header('Content-Type', 'application/json');
     }


     /**
     * Obtem o token de autenticação para um utilizador.
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request) {
        return $this->update($request);
    }

    public function update(Request $request){

        try{
            
            $activeRole = false;
            $input      = $request->all();     
            
            
            if($request->get('updated') == 1) {

                $feedback = 'User updated sucessfuly';

                $user     = User::filterSource()
                                 ->firstOrNew(['code' => $request->code]);

                if(!$user->exists) {
                    return $this->responseError('update', '-001', 'User not found');
                }
                
            }else{
                
                $feedback = 'User created sucessfuly';
               
                $role     = $request->get('role');
                
                if(empty($role)){
                    return $this->responseError('update', '-001', 'The role element not found');
                }
                
                if($role == 'administrativo'){
                    $roleId = 4;
                }else if($role == 'agencia'){
                    $roleId = 2;
                }else if ($role = 'operador'){
                    $roleId = 3;
                }
                
                $user       = new User();
                
                //atribuition role
                $activeRole = true;
                
                if($roleId == 3) {
                    $user->login_app      = 1;
                    $user->login_admin    = 0;
                    $user->is_operator    = 1;
                    
                } else {
                    $user->login_app      = 1;
                    $user->login_admin    = 1;
                    $user->is_operator    = 0;
             
                }
                
                //verification code
                $codeExist = User::where('code', @$input['code'])->pluck('id')->toArray();
                if(!empty($codeExist)){
                   return $this->responseError('update', '-001', 'The code already exists in another user');
                }
                
            }
            
            //agencia
            if(!empty($input['agency_id']))
            {
                $aux = strval($input['agency_id']);
                $user->agencies = ["$aux"];
            }
            
            //independentemente seja update ou criação tem se verificar este processo
            //verificion password
            if(empty($input['password'])){
                    unset($input['password']);
            }else{
                 $user->uncrypted_password = $input['password'];
                 $input['password']        = bcrypt($input['password']);
            }
            
            if(!empty($input['name'])){
                $user->fullname = $input['name'];
            }
            
            
            $validor = Validator::make($input, []);
            if($validor->passes()) {
                $user->fill($input);
                $user->source = config('app.source');
                $user->save();
                
                if($activeRole){
                    $user->roles()->sync([$roleId]);
                }
                
                $response = [
                        'error'   => null,
                        'message' => $feedback,
                        'code'    => $user->code
                ];
                return response($response, 200)->header('Content-Type', 'application/json');
            
            }

            return $this->responseError('destroy', '-002', $provider->errors()->first());

        
        }catch(\Exception $e){
            return $this->responseError('destroy', '-999', $e->getMessage());

        }

    }

    /**
     * Delete
     *
     * @param Request $request
     * @return mixed
     */
    public function destroy(Request $request, $code) {

        $result = User::filterSource()
            ->where('code', $code)
            ->delete();

        if(!$result) {
            return $this->responseError('destroy', '-001', 'User not found.');
        }

        $response = [
            'error'   => '',
            'message' => 'User deleted successfully.'
        ];
        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Store shipment custom attributes
     * @return array
     */
    public function responseError($method, $code, $message = null, $returnArr = false) {

        $errors = trans('api.users.errors');

        $data = [
            'error'   => $code,
            'message' => $message ? $message : $errors[$method][$code]
        ];

        if($returnArr) {
            return $data;
        }

        return response($data, 404)->header('Content-Type', 'application/json');
    }

}