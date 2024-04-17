<?php

namespace App\Http\Controllers\Admin;

use App\Models\IncidenceResolutionType;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html;

class IncidencesResolutionsTypesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'incidences_resolutions_types';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',incidences_types']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.incidences_resolutions_types.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Tipo de Resolução de Incidência';
        
        $incidenceType = new IncidenceResolutionType();
                
        $formOptions = array('route' => array('admin.incidences-resolutions.store'), 'method' => 'POST');
        
        return view('admin.incidences_resolutions_types.edit', compact('incidenceType', 'action', 'formOptions'))->render();
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

        $action = 'Editar Tipo de Resolução de Incidência';
        
        $incidenceType = IncidenceResolutionType::findOrfail($id);

        $formOptions = array('route' => array('admin.incidences-resolutions.update', $incidenceType->id), 'method' => 'PUT');

        return view('admin.incidences_resolutions_types.edit', compact('incidenceType', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        IncidenceResolutionType::flushCache(IncidenceResolutionType::CACHE_TAG);

        $input = $request->all();
        
        $incidenceType = IncidenceResolutionType::findOrNew($id);

        if ($incidenceType->validate($input)) {
            $incidenceType->fill($input);
            $incidenceType->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $incidenceType->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        IncidenceResolutionType::flushCache(IncidenceResolutionType::CACHE_TAG);

        $result = IncidenceResolutionType::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o tipo de resolução de incidência.');
        }

        return Redirect::route('admin.incidences-types.index')->with('success', 'Tipo de resolução de incidência removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        IncidenceResolutionType::flushCache(IncidenceResolutionType::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = IncidenceResolutionType::whereIn('id', $ids)->delete();
        
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

        $data = IncidenceResolutionType::select();

        return Datatables::of($data)
                        ->edit_column('name', function($row) {
                            return view('admin.incidences_types.datatables.name', compact('row'))->render();
                        })
                        ->edit_column('operator_visible', function($row) {
                            return view('admin.incidences_types.datatables.operator_visible', compact('row'))->render();
                        })
                        ->add_column('select', function($row) {
                            return view('admin.partials.datatables.select', compact('row'))->render();
                        })
                        ->edit_column('created_at', function($row) {
                            return view('admin.partials.datatables.created_at', compact('row'))->render();
                        })
                        ->add_column('actions', function($row) {
                            return view('admin.incidences_types.datatables.actions', compact('row'))->render();
                        })
                        ->make(true);
    }

}
