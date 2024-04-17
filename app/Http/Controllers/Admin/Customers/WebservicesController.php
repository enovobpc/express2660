<?php

namespace App\Http\Controllers\Admin\Customers;

use Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\CustomerWebservice;
use App\Models\Customer;
use App\Models\WebserviceMethod;
use App\Models\Provider;

class WebservicesController extends \App\Http\Controllers\Admin\Controller {

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
        
        $action = 'Adicionar Ligação a Webservice';
        
        $webservice = new CustomerWebservice;
                
        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $webserviceMethods = WebserviceMethod::remember(config('cache.query_ttl'))
                        ->cacheTags(WebserviceMethod::CACHE_TAG)
                        ->filterSources()
                        ->ordered()
                        ->pluck('name', 'method')
                        ->toArray();
        
        $formOptions = array('route' => array('admin.customers.webservices.store', $customerId), 'method' => 'POST');
        
        return view('admin.customers.customers.partials.webservices.edit', compact('webservice', 'providers', 'webserviceMethods', 'action', 'formOptions'))->render();
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($customerId, $id) {
        
        $action = 'Editar Ligação a Webservice';
        
        $webservice = CustomerWebservice::where('customer_id', $customerId)
                        ->findOrfail($id);

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();
                
        $webserviceMethods = WebserviceMethod::remember(config('cache.query_ttl'))
            ->cacheTags(WebserviceMethod::CACHE_TAG)
            ->filterSources()
            ->ordered()
            ->pluck('name', 'method')
            ->toArray();
        
        $formOptions = array('route' => array('admin.customers.webservices.update', $webservice->customer_id, $webservice->id), 'method' => 'PUT');

        return view('admin.customers.customers.partials.webservices.edit', compact('webservice', 'providers', 'webserviceMethods', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $customerId, $id) {

        CustomerWebservice::flushCache(CustomerWebservice::CACHE_TAG);

        $input = $request->all();
        $input['active']        = $request->get('active', false);
        $input['force_sender']  = $request->get('force_sender', false);
        
        $webservice = CustomerWebservice::where('customer_id', $customerId)
                                        ->findOrNew($id);
        
        if ($webservice->validate($input)) {
            $webservice->customer_id = $customerId;
            $webservice->fill($input);

            $webservice->save();

            $this->updateHasWebservice($customerId, true);
            
            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $webservice->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($customerId, $id) {

        CustomerWebservice::flushCache(CustomerWebservice::CACHE_TAG);

        $result = CustomerWebservice::where('customer_id', $customerId)
                                ->whereId($id)
                                ->delete();
        
        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o destinatário.');
        }

        $this->updateHasWebservice($customerId);
                
        return Redirect::back()->with('success', 'Destinatário removido com sucesso.');
    }

    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request, $customerId) {

        $data = CustomerWebservice::with(['provider' => function($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Provider::CACHE_TAG);
            }])
            ->with(['webservice_method' => function($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(WebserviceMethod::CACHE_TAG);
            }])
            ->where('customer_id', $customerId)
            ->select();

        return Datatables::of($data)
            ->edit_column('provider_id', function($row){
                return @$row->provider->name;
            })
            ->edit_column('method', function($row) {
                return view('admin.customers.customers.datatables.webservices.method', compact('row'))->render();
            })
            ->edit_column('session_id', function($row){
                return view('admin.webservices.global.datatables.session_id', compact('row'))->render();
            })
            ->edit_column('active', function($row){
                return view('admin.customers.customers.datatables.webservices.active', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.customers.customers.datatables.webservices.actions', compact('row'))->render();
            })
            ->make(true);
    }
    
    /**
     * Update has_webservice attribute on customer
     * 
     * @param type $customerId
     */
    public function updateHasWebservice($customerId, $count = false){

        if(!$count) {
            $count = CustomerWebservice::where('customer_id', $customerId)
                                        ->count();
            
            $count = $count ? true : false;
        }
        
        $customer = Customer::find($customerId);
        $customer->has_webservices = $count;
        return $customer->save();
    }


    /**
     * Show the form for import
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showImport($customerId) {

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $webserviceMethods = WebserviceMethod::remember(config('cache.query_ttl'))
            ->cacheTags(WebserviceMethod::CACHE_TAG)
            ->filterSources()
            ->orderBy('name', 'asc')
            ->pluck('name', 'method')
            ->toArray();

        return view('admin.customers.customers.partials.webservices.import', compact('providers', 'webserviceMethods', 'customerId'))->render();
    }


    /**
     * Show the form for import
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeImport(Request $request, $customerId) {

        CustomerWebservice::flushCache(CustomerWebservice::CACHE_TAG);

        $input = $request->all();

        $data = CustomerWebservice::where('customer_id', $input['import_customer_id'])
                        ->where('method', $input['method'])
                        ->first();

        $webservice = new CustomerWebservice;
        $webservice->customer_id = $customerId;
        $webservice->method      = $data->method;
        $webservice->provider_id = $data->provider_id;
        $webservice->agency      = $data->agency;
        $webservice->user        = $data->user;
        $webservice->password    = $data->password;
        $webservice->session_id  = $data->session_id;
        $result = $webservice->save();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar adicionar a ligação ou o cliente escolhido não possui a ligação pretendida.');
        }

        return Redirect::back()->with('success', 'Ligação importada com sucesso.');
    }

}
