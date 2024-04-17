<?php

namespace App\Http\Controllers\Admin\Brand;

use App\Models\Brand;
use App\Models\BrandModel;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Redirect;
use Response;

class BrandController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'brands';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',brands']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index() {

        return $this->setContent('admin.brand.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create() {
        
        $action      = 'Adicionar Marca';
        $brand       = new Brand();
        $formOptions = array('route' => array('admin.brands.store'), 'method' => 'POST');
        
        $data = compact(
            'brand', 
            'action',
            'formOptions'
        );

        return view('admin.brand.edit', $data)->render();
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
     * @return string
     */
    public function edit($id) {
        $action      = 'Editar Marca';
        $brand       = Brand::filterSource()->findOrfail($id);
        $formOptions = array('route' => array('admin.brands.update', $brand->id), 'method' => 'PUT');

        $data = compact(
            'brand',
            'action',
            'formOptions'
        );

        return view('admin.brand.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null) {
        $brand = Brand::filterSource()->findOrNew($id);
        
        $input              = $request->all();
        $input['is_active'] = $request->get('is_active', false);

        if ($brand->validate($input)) {
            $brand->fill($input);
            $brand->source = config('app.source');
            $brand->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $brand->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $result = Brand::whereId($id)->delete();

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
        $ids    = explode(',', $request->ids);
        $result = Brand::whereIn('id', $ids)->delete();
        
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
        $data = Brand::with('models')
            ->select();

        return Datatables::of($data)
            ->addColumn('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->editColumn('name', function($row) {
                return view('admin.brand.datatables.name', compact('row'));
            })
            ->addColumn('models', function($row) {
                return view('admin.brand.datatables.models', compact('row'));
            })
            ->editColumn('is_active', function($row) {
                return view('admin.brand.datatables.active', compact('row'));
            })
            ->addColumn('actions', function($row) {
                return view('admin.brand.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     * GET /admin/features/sort
     *
     * @return string
     */
    public function sortEdit() {
        $items = Brand::orderBy('sort')->get();
        $route = route('admin.brands.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/features/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sortUpdate(Request $request) {
        Brand::setNewOrder($request->ids);
        $response = [
            'result'  => true,
            'message' => 'Ordenação gravada com sucesso.',
        ];

        return Response::json($response);
    }
}
