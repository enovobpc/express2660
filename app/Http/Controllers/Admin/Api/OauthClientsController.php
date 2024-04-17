<?php

namespace App\Http\Controllers\Admin\Api;

use Html, Croppa, Auth, App, Response, Mail, Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Api\OauthClient;
use App\Models\Customer;

class OauthClientsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'api';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',api']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        if($request->has('action') == 'reset') {

        }

        return $this->setContent('admin.api.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $action = 'Nova Chave da API';

        $oauthClient = new OauthClient();
                
        $formOptions = array('route' => array('admin.api.store'), 'method' => 'POST');
        
        return view('admin.api.edit', compact('oauthClient', 'action', 'formOptions'))->render();
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
    public function edit(Request $request, $id) {

        $action = 'Editar Chave da API';

        $oauthClient = OauthClient::findOrfail($id);

        $formOptions = array('route' => array('admin.api.update', $oauthClient->id), 'method' => 'PUT');

        if($oauthClient->password_client && !$oauthClient->personal_access_client) {
            $oauthClient->authentication = 'password';
        } elseif($oauthClient->personal_access_client && !$oauthClient->password_client) {
            $oauthClient->authentication = 'credentials';
        } else {
            $oauthClient->authentication = null;
        }

        if($request->action == 'email') {
            $formOptions = array('route' => array('admin.api.update', $oauthClient->id, 'action' => 'email'), 'method' => 'PUT');
            return view('admin.api.email', compact('oauthClient', 'action', 'formOptions'));
        }

        return view('admin.api.edit', compact('oauthClient', 'action', 'formOptions'))->render();
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

        if(@$input['action'] == 'email') {
            return $this->sendEmail($request, $id);
        }

        if($input['authentication'] == 'password') {
            $input['personal_access_client'] = 0;
            $input['password_client']        = 1;
        } elseif($input['authentication'] == 'credentials') {
            $input['personal_access_client'] = 1;
            $input['password_client']        = 0;
        } else {
            $input['personal_access_client'] = 0;
            $input['password_client']        = 0;
        }

        $oauthClient = OauthClient::findOrNew($id);

        if ($oauthClient->validate($input)) {
            $oauthClient->fill($input);
            $oauthClient->save();

            return Redirect::back()->with('success', 'Armazém gravado com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $oauthClient->errors()->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendEmail(Request $request, $id) {

        $input = $request->all();

        $oauthClient = OauthClient::findOrNew($id);


        $emails = validateNotificationEmails($input['email']);
        $emails = $emails['valid'];

        Mail::send('emails.customers.api', compact('oauthClient'), function($message) use($emails) {
            $message->to($emails)
                ->from(config('mail.from.address'), config('mail.from.name'))
                ->subject('API connection details');
        });


        return Redirect::back()->with('success', 'E-mail enviado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $result = OauthClient::whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a chave da API.');
        }

        return Redirect::back()->with('success', 'Chave da API removida com sucesso.');
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

        $result = OauthClient::whereIn('id', $ids)->delete();

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
    public function datatable() {

        $data = OauthClient::select();

        if(!Auth::user()->ability(Config::get('permissions.role.admin'), 'admin')) {
            $data = $data->where('id', '>', 1);
        }

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.api.datatables.name', compact('row'))->render();
                })
                ->add_column('details', function($row) {
                    return view('admin.api.datatables.details', compact('row'))->render();
                })
                ->edit_column('password_client', function($row) {
                    return view('admin.api.datatables.password', compact('row'))->render();
                })
                ->edit_column('revoked', function($row) {
                    return view('admin.api.datatables.revoked', compact('row'))->render();
                })
                ->edit_column('daily_counter', function($row) {
                    return view('admin.api.datatables.daily_counter', compact('row'))->render();
                })
                ->edit_column('last_call', function($row) {
                    return view('admin.api.datatables.last_call', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->edit_column('created_at', function($row) {
                    return view('admin.partials.datatables.created_at', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.api.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request) {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'agency_id',
            'code',
            'name',
            'address',
            'zip_code',
            'city',
            'country',
            'phone',
            'mobile',
            'email',
            'responsable',
            'obs_shipments'
        ];

        try {
            $results = [];

            $customers = Customer::filterAgencies()
                ->with(['departments' => function($q){
                    $q->select([
                        'name',
                        'id',
                        'code',
                        'address',
                        'zip_code',
                        'city',
                        'country',
                        'phone',
                        'email',
                        'agency_id',
                        'obs',
                        'customer_id'
                    ]);
                }])
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->isDepartment(false)
                ->get($fields);

            if($customers) {

                $results = array();
                foreach($customers as $customer) {

                    $departments = null;

                    if(!$customer->departments->isEmpty()) {

                        $departments[] = [
                            'id'   => '',
                            'text' => ''
                        ];

                        foreach ($customer->departments as $department) {
                            $departments[] = [
                                'id'           => $department->id,
                                'text'         => str_limit($customer->name, 40),
                                'code'         => $department->code,
                                'name'         => $department->name,
                                'address'      => $department->address,
                                'zip_code'     => $department->zip_code,
                                'city'         => $department->city,
                                'country'      => $department->country,
                                'phone'        => $department->mobile ? $department->mobile : $department->phone,
                                'email'        => $department->contact_email,
                                'agency'       => $department->agency_id,
                                'obs'          => $department->obs_shipments
                            ];
                        }
                    }

                    $results[] = [
                        'id'           => $customer->id,
                        'text'         => $customer->code. ' - '.str_limit($customer->name, 40),
                        'code'         => $customer->code,
                        'name'         => $customer->name,
                        'address'      => $customer->address,
                        'zip_code'     => $customer->zip_code,
                        'city'         => $customer->city,
                        'country'      => $customer->country,
                        'phone'        => $customer->mobile ? $customer->mobile : $customer->phone,
                        'email'        => $customer->contact_email,
                        'agency'       => $customer->agency_id,
                        'obs'          => $customer->obs_shipments,
                        'departments'  => $departments
                    ];
                }

            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum cliente encontrado.'
                ];
            }

        } catch(\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return Response::json($results);
    }

}
