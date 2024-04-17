<?php

namespace App\Http\Controllers\Admin\Routes;

use App\Models\RouteGroup;
use App\Models\ServiceGroup;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Response;

class GroupsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'routes';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',routes']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index() {
        $routeGroup = new RouteGroup();
        $formOptions = array('route' => array('admin.routes.groups.store'), 'method' => 'POST', 'class' => 'modal-ajax-form');

        return view('admin.routes.groups.index', compact('routeGroup', 'formOptions'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        return $this->update($request, null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id = null) {
        RouteGroup::flushCache(RouteGroup::CACHE_TAG);

        $input = $request->all();
        
        $routeGroup = RouteGroup::findOrNew($id);

        if ($routeGroup->validate($input)) {
            $routeGroup->fill($input);
            $routeGroup->save();

            $row = $routeGroup;
            return Response::json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.',
                'html'     => view('admin.routes.groups.datatables.name', compact('row'))->render()
            ]);
        }

        return Response::json([
            'result'   => false,
            'feedback' => $routeGroup->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        RouteGroup::flushCache(ServiceGroup::CACHE_TAG);

        $result = RouteGroup::whereId($id)
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
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {
        $data = RouteGroup::select();

        return Datatables::of($data)
            ->edit_column('name', function($row) {
                return view('admin.routes.groups.datatables.name', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.routes.groups.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
