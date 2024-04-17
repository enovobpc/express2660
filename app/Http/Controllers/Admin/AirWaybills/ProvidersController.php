<?php

namespace App\Http\Controllers\Admin\AirWaybills;

use Illuminate\Http\Request;
use App\Models\Provider;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
class ProvidersController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'air-waybills-providers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',air-waybills-providers']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.awb.air_waybills_providers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Fornecedor';
        
        $provider = new Provider();
                
        $formOptions = array('route' => array('admin.air-waybills.providers.store'), 'method' => 'POST');
        
        return view('admin.awb.air_waybills_providers.edit', compact('provider', 'action', 'formOptions'))->render();
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
//        
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $action = 'Editar Fornecedor';
        
        $provider = Provider::filterSource()->findOrfail($id);

        $formOptions = array('route' => array('admin.air-waybills.providers.update', $provider->id), 'method' => 'PUT');

        return view('admin.awb.air_waybills_providers.edit', compact('provider', 'action', 'formOptions'))->render();
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
        
        $provider = Provider::filterSource()->findOrNew($id);

        if ($provider->validate($input)) {
            $provider->fill($input);
            $provider->source = config('app.source');
            $provider->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $provider->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Provider::filterSource()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o fornecedor.');
        }

        return Redirect::route('admin.air-waybills.providers.index')->with('success', 'Fornecedor removido com sucesso.');
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
        
        $result = Provider::filterSource()
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

        $data = Provider::filterSource()->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.awb.air_waybills_providers.datatables.name', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.awb.air_waybills_providers.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }
}
