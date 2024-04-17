<?php

namespace App\Http\Controllers\Admin\Api\Docs;

use App\Models\Api\Docs\Category;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Redirect;
use Response;

class CategoriesController extends \App\Http\Controllers\Admin\Controller {

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Categoria API';
        
        $category = new Category();
                
        $formOptions = array('route' => array('admin.api.docs.categories.store'), 'method' => 'POST');

        $data = compact(
            'category',
            'action',
            'formOptions'
        );
        
        return view('admin.api.docs.edit_category', $data)->render();
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
        
        $action = 'Editar Categoria API';
        
        $category = Category::findOrfail($id);

        $formOptions = array('route' => array('admin.api.docs.categories.update', $category->id), 'method' => 'PUT');

        $data = compact(
            'category',
            'action',
            'formOptions'
        );

        return view('admin.api.docs.edit_category', $data)->render();
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

        $category = Category::findOrNew($id);

        if ($category->validate($input)) {
            $category->fill($input);
            $category->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $category->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Category::whereId($id)->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::back()->with('success', 'Registo removido com sucesso.');
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
        
        $result = Category::whereIn('id', $ids)->delete();
        
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

        $data = Category::select();

        return Datatables::of($data)
            ->editColumn('name', function($row) {
                return view('admin.api.docs.datatables.categories.name', compact('row'));
            })
            ->addColumn('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->addColumn('actions', function($row) {
                return view('admin.api.docs.datatables.categories.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     * GET /admin/features/sort
     *
     * @return Response
     */
    public function sortEdit() {

        $items = Category::orderBy('sort')->get();

        $route = route('admin.api.docs.categories.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/features/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        $result = Category::setNewOrder($request->ids);

        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }
}
