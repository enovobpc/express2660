<?php

namespace App\Http\Controllers\Admin\Services;

use App\Models\ServiceGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Response, Cache;

class GroupsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'services';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',services']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $serviceGroup = new ServiceGroup();

        $formOptions = array('route' => array('admin.services.groups.store'), 'method' => 'POST', 'class' => 'modal-ajax-form');

        return view('admin.services.groups.index', compact('serviceGroup', 'formOptions'))->render();
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        ServiceGroup::flushCache(ServiceGroup::CACHE_TAG);

        $input = $request->all();
        
        $serviceGroup = ServiceGroup::filterSource()->findOrNew($id);

        if ($serviceGroup->validate($input)) {
            $serviceGroup->fill($input);
            $serviceGroup->source = config('app.source');
            $serviceGroup->save();

            $row = $serviceGroup;
            return Response::json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.',
                'html'     => view('admin.services.groups.datatables.name', compact('row'))->render()
            ]);
        }

        return Response::json([
            'result'   => false,
            'feedback' => $serviceGroup->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        ServiceGroup::flushCache(ServiceGroup::CACHE_TAG);

        $result = ServiceGroup::filterSource()
                    ->whereId($id)
                    ->delete();

        if (!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Ocorreu um erro ao tentar remover o grupo.'
            ]);
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'Grupo removido com sucesso.'
        ]);
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        ServiceGroup::flushCache(ServiceGroup::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = ServiceGroup::filterSource()
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

        $data = ServiceGroup::filterSource()->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.services.groups.datatables.name', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.services.groups.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }


    /**
     * Remove the specified resource from storage.
     * GET /admin/services/sort
     *
     * @return Response
     */
    public function sortEdit() {

        $items = ServiceGroup::filterSource()
            ->orderBy('sort')
            ->get(['id', 'name']);

        $route = route('admin.services.groups.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        ServiceGroup::flushCache(ServiceGroup::CACHE_TAG);

        try {
            ServiceGroup::setNewOrder($request->ids);

            $response = [
                'result'  => true,
                'message' => 'Ordenação gravada com sucesso.',
            ];
        } catch (\Exception $e) {
            $response = [
                'result'  => false,
                'message' => 'Erro ao gravar ordenação. ' . $e->getMessage(),
            ];
        }

        return Response::json($response);
    }
}
