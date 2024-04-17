<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\FleetGest\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Croppa;

class ServicesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_services';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_parts']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.fleet.services.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $action = 'Adicionar Serviço ou Despesa';
        
        $service = new Service;
                
        $formOptions = array('route' => array('admin.fleet.services.store'), 'method' => 'POST', 'files' => true);
        
        return view('admin.fleet.parts.edit_service', compact('service', 'action', 'formOptions'))->render();
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

        $service = Service::findOrfail($id);

        $action = 'Editar Serviço ou Despesa';

        $formOptions = array('route' => array('admin.fleet.services.update', $service->id), 'method' => 'PUT', 'files' => true);

        return view('admin.fleet.parts.edit_service', compact('service', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Service::flushCache(Service::CACHE_TAG);

        $input = $request->all();
        
        $service = Service::findOrNew($id);

        if ($service->validate($input)) {
            $service->fill($input);
            $service->source = config('app.source');
            $service->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $service->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Service::flushCache(Service::CACHE_TAG);

        $result = Service::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o serviço.');
        }

        return Redirect::back()->with('success', 'Serviço removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Service::flushCache(Service::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = Service::whereIn('id', $ids)->delete();
        
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

        $data = Service::filterSource()
            ->select();

        //filter type
        $value = $request->get('type');
        if($request->has('type')) {
            $data = $data->where('type', $value);
        }

        return Datatables::of($data)
            ->edit_column('name', function($row) {
                return view('admin.fleet.parts.datatables.services.name', compact('row'))->render();
            })
            ->edit_column('type', function($row) {
                if($row->type == 'maintenance') {
                    return 'Serviços Manutenção';
                } else {
                    return 'Despesas Gerais';
                }
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.fleet.parts.datatables.services.actions', compact('row'))->render();
            })
            ->make(true);
    }

}
