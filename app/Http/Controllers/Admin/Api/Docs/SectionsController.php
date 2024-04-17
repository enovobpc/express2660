<?php

namespace App\Http\Controllers\Admin\Api\Docs;

use App\Models\Api\Docs\Category;
use App\Models\Api\Docs\Method;
use App\Models\Api\Docs\Section;
use App\Models\Bank;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Redirect;
use Response;

class SectionsController extends \App\Http\Controllers\Admin\Controller {

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
        
        $action = 'Adicionar Secção API';
        
        $section = new Section();
                
        $formOptions = array('route' => array('admin.api.docs.sections.store'), 'method' => 'POST');

        $categories = Category::ordered()
            ->pluck('name', 'slug')
            ->toArray();

        $data = compact(
            'section',
            'categories',
            'action',
            'formOptions'
        );
        
        return view('admin.api.docs.edit_section', $data)->render();
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
        
        $action = 'Editar Secções API';
        
        $section = Section::findOrfail($id);

        $formOptions = array('route' => array('admin.api.docs.sections.update', $section->id), 'method' => 'PUT');

        $categories = Category::ordered()
            ->pluck('name', 'slug')
            ->toArray();

        $data = compact(
            'section',
            'categories',
            'action',
            'formOptions'
        );

        return view('admin.api.docs.edit_section', $data)->render();
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

        $section = Section::findOrNew($id);

        if ($section->validate($input)) {
            $section->fill($input);
            $section->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $section->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Section::whereId($id)->delete();

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
        
        $result = Section::whereIn('id', $ids)->delete();
        
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

        $data = Section::select();

        return Datatables::of($data)
            ->editColumn('name', function($row) {
                return view('admin.api.docs.datatables.sections.name', compact('row'));
            })
            ->editColumn('description', function($row) {
                return view('admin.api.docs.datatables.sections.description', compact('row'));
            })
            ->addColumn('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->addColumn('actions', function($row) {
                return view('admin.api.docs.datatables.sections.actions', compact('row'))->render();
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

        $items = Section::orderBy('sort')->get();

        $route = route('admin.api.docs.sections.sort.update');

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

        $result = Section::setNewOrder($request->ids);

        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }
}
