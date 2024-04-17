<?php

namespace App\Http\Controllers\Admin\Api\Docs;

use App\Models\Api\Docs\Category;
use App\Models\Api\Docs\Method;
use App\Models\Api\Docs\Section;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Redirect;
use Response;

class MethodsController extends \App\Http\Controllers\Admin\Controller {

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
    public function index() {

        $versions = Category::groupBy('api_version')
            ->pluck('api_version', 'api_version')
            ->toArray();

        $categories = Category::ordered()
            ->pluck('name', 'slug')
            ->toArray();

        $sections = Section::ordered()
            ->pluck('name', 'slug')
            ->toArray();

        $data = compact(
            'versions',
            'categories',
            'sections'
        );

        return $this->setContent('admin.api.docs.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Método API';
        
        $apiMethod = new Method();
                
        $formOptions = array('route' => array('admin.api.docs.methods.store'), 'method' => 'POST');

        $categories = Category::ordered()
            ->pluck('name', 'slug')
            ->toArray();

        $sections = Section::ordered()
            ->pluck('name', 'slug')
            ->toArray();

        $data = compact(
            'apiMethod',
            'categories',
            'sections',
            'action',
            'formOptions'
        );
        
        return view('admin.api.docs.edit_method', $data)->render();
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
        
        $action = 'Editar Método API';
        
        $apiMethod = Method::findOrfail($id);

        $formOptions = array('route' => array('admin.api.docs.methods.update', $apiMethod->id), 'method' => 'PUT');

        $categories = Category::ordered()
            ->pluck('name', 'slug')
            ->toArray();

        $sections = Section::ordered()
            ->pluck('name', 'slug')
            ->toArray();

        $data = compact(
            'apiMethod',
            'categories',
            'sections',
            'action',
            'formOptions'
        );

        return view('admin.api.docs.edit_method', $data)->render();
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

        $arr = [];
        foreach ($input['params'] as $param) {
            if(@$param['param']) {
                $arr[] = $param;
            }
        }
        $input['params'] = $arr;

        $arr = [];
        foreach ($input['fields1'] as $param) {
            if(@$param['field']) {
                $arr[] = $param;
            }
        }
        $input['fields1'] = $arr;

        $arr = [];
        foreach ($input['fields2'] as $param) {
            if(@$param['field']) {
                $arr[] = $param;
            }
        }
        $input['fields2'] = $arr;

        $arr = [];
        foreach ($input['fields3'] as $param) {
            if(@$param['field']) {
                $arr[] = $param;
            }
        }
        $input['fields3'] = $arr;

        $arr = [];
        foreach ($input['fields4'] as $param) {
            if(@$param['field']) {
                $arr[] = $param;
            }
        }
        $input['fields4'] = $arr;

        $method = Method::findOrNew($id);
        if ($method->validate($input)) {
            $method->fill($input);
            $method->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $method->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Method::whereId($id)->delete();

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
        
        $result = Method::whereIn('id', $ids)->delete();
        
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

        $data = Method::select();

        if($request->has('version')) {
            $data = $data->where('api_version', $request->get('version'));
        }

        if($request->has('category')) {
            $data = $data->where('category_id', $request->get('category'));
        }

        if($request->has('section')) {
            $data = $data->where('section_id', $request->get('section'));
        }

        if($request->has('level')) {
            $data = $data->where('levels', 'like', '%'.$request->get('level').'%');
        }


        return Datatables::of($data)
            ->editColumn('name', function($row) {
                return view('admin.api.docs.datatables.methods.name', compact('row'));
            })
            ->editColumn('url', function($row) {
                return view('admin.api.docs.datatables.methods.url', compact('row'));
            })
            ->editColumn('levels', function($row) {
                return view('admin.api.docs.datatables.methods.levels', compact('row'));
            })
            ->addColumn('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->addColumn('actions', function($row) {
                return view('admin.api.docs.datatables.methods.actions', compact('row'))->render();
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

        $items = Method::orderBy('sort')->get();

        $route = route('admin.api.docs.methods.sort.update');

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

        $result = Method::setNewOrder($request->ids);

        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }
}

