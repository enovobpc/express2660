<?php

namespace App\Http\Controllers\Admin\Core;

use App\Models\Core\ProviderAgency;
use Response, DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Provider;
use App\Models\User;
use App\Models\Route;
use App\Models\Agency;

class ProviderAgenciesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'provider-agencies';

    protected $status = [
        'customer'    => 'Cliente',
        'prospect'    => 'Potencial',
        'no_interest' => 'Sem Interesse'
    ];

    protected $providers = [
        'envialia'  => 'Enviália',
        'tipsa'     => 'Tipsa',
        'gls'       => 'GLS',
        'estafetas' => 'Estafetas',
        'express'   => 'Furgões',
        'pesados'   => 'Pesados',
        'mudancas'  => 'Mudanças'
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',provider-agencies']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $providers = $this->providers;

        $status = $this->status;

        return $this->setContent('admin.core.provider_agencies.index', compact('providers', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $providerAgency = new ProviderAgency();

        $action = 'Adicionar Agência';

        $formOptions = array('route' => array('core.provider.agencies.store'), 'method' => 'POST');

        $providers = $this->providers;

        $status = $this->status;

        $data = compact(
            'route',
            'action',
            'formOptions',
            'providerAgency',
            'providers',
            'status'
        );

        return view('admin.core.provider_agencies.edit', $data)->render();
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $providerAgency = ProviderAgency::findOrfail($id);

        $action = 'Editar Agência';

        $formOptions = array('route' => array('core.provider.agencies.update', $providerAgency->id), 'method' => 'PUT');

        $providers = $this->providers;

        $status = $this->status;


        $data = compact(
            'route',
            'action',
            'formOptions',
            'providerAgency',
            'providers',
            'status'
        );

        return view('admin.core.provider_agencies.edit', $data)->render();
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

        $providerAgency = ProviderAgency::findOrNew($id);

        if ($providerAgency->validate($input)) {
            $providerAgency->fill($input);
            $providerAgency->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $providerAgency->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Route::flushCache(Route::CACHE_TAG);

        $route = Route::with('customers')
            ->whereSource(config('app.source'))
            ->filterAgencies()
            ->find($id);

        if($route->customers->count() > 0) {
            return Redirect::back()->with('error', 'Não é possível eliminar a rota porque existem clientes associados. Associe primeiro os clientes a outra rota antes de eliminar a rota.');
        }

        $result = $route->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a rota');
        }

        return Redirect::route('admin.core.provider_agencies.index')->with('success', 'Rota removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Route::flushCache(Route::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $routes = Route::filterAgencies()
            ->whereSource(config('app.source'))
            ->with('customers')
            ->whereIn('id', $ids)
            ->get();

        $result = true;
        foreach ($routes as $route) {
            if($route->customers->count()) {
                $result = false;
            } else {
                $route->delete();
            }
        }
        
        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover um ou mais registos porque porque existem clientes associados. Associe primeiro os clientes a outra rota antes de eliminar a rota.');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = ProviderAgency::select();

        //filter country
        $value = $request->country;
        if($request->has('country')) {
            $data = $data->where('country', $value);
        }

        //filter provider
        $value = $request->provider;
        if($request->has('provider')) {
            $data = $data->where('provider', $value);
        }

        //filter status
        $value = $request->status;
        if($request->has('status')) {
            if($value == 'prospect') {
                $data = $data->where(function($q){
                    $q->where('status', 'prospect');
                    $q->orWhereNull('status');
                });
            } else {
                $data = $data->where('status', $value);
            }

        }

        //filter active
        $value = $request->is_active;
        if($request->has('is_active')) {
            $data = $data->where('is_active', $value);
        }

        //filter hidden
        $value = $request->is_hidden;
        if($request->has('is_hidden')) {
            $data = $data->where('is_hidden', $value);
        }

        return Datatables::of($data)
            ->edit_column('provider', function($row) {
                return view('admin.core.provider_agencies.datatables.provider', compact('row'))->render();
            })
            ->edit_column('name', function($row) {
                return view('admin.core.provider_agencies.datatables.name', compact('row'))->render();
            })
            ->edit_column('company', function($row) {
                return view('admin.core.provider_agencies.datatables.company', compact('row'))->render();
            })
            ->edit_column('email', function($row) {
                return view('admin.core.provider_agencies.datatables.contacts', compact('row'))->render();
            })
            ->edit_column('status', function($row) {
                return view('admin.core.provider_agencies.datatables.status', compact('row'))->render();
            })
            ->edit_column('is_active', function($row) {
                return view('admin.core.provider_agencies.datatables.active', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.core.provider_agencies.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(Request $request) {

        $ids = explode(',', $request->ids);

        $updateData = [];

        if($request->has('hidden')) {
            $updateData['is_hidden'] = $request->hidden;
        }

        if($request->has('status')) {
            $updateData['status'] = $request->status;
        }

        if($request->has('active')) {
            $updateData['is_active'] = $request->active;
        }

        if($request->has('mass_country')) {
            $updateData['country'] = $request->mass_country;
        }

        $result = ProviderAgency::whereIn('id', $ids)
            ->update($updateData);

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível ocultar/desocultar um ou mais registos');
        }

        return Redirect::back()->with('success', 'Registos selecionados alterados com sucesso.');
    }

}
