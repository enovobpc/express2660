<?php

namespace App\Http\Controllers\Admin\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Website\Brand;
use Croppa, Response;

class BrandsController extends \App\Http\Controllers\Admin\Controller {

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
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.website.brands.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Marca';
        
        $brand = new Brand;
                
        $formOptions = ['route' => ['admin.website.brands.store'], 'method' => 'POST', 'files' => true];
        
        return view('admin.website.brands.edit', compact('brand', 'action', 'formOptions'))->render();
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
        
        $action = 'Editar Marca';
        
        $brand = Brand::findOrfail($id);

        $formOptions = ['route' => ['admin.website.brands.update', $brand->id], 'method' => 'PUT', 'files' => true];

        return view('admin.website.brands.edit', compact('brand', 'action', 'formOptions'))->render();
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
        $input['is_visible']   = $request->get('is_visible', false);
        $input['target_blank'] = $request->get('target_blank', false);
        
        $brand = Brand::findOrNew($id);

        if ($brand->validate($input)) {
            $brand->fill($input);
            
            //delete image
            if ($request->delete_photo && !empty($brand->filepath)) {
                Croppa::delete($brand->filepath);
                $brand->filepath = null;
                $brand->filename = null;
            }
            
            //upload image
            if($request->hasFile('image')) {

                if ($brand->exists && !empty($brand->filepath)) {
                    Croppa::delete($brand->filepath);
                }

                if (!$brand->upload($request->file('image'), 40)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem.');
                }
                
            } else {
                $brand->save();
            }

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
        
        $result = Brand::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a marca.');
        }

        return Redirect::route('admin.website.brands.index')->with('success', 'Marca removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/brands/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {
        
        $ids = explode(',', $request->ids);
        
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

        $data = Brand::select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.website.brands.datatables.name', compact('row'))->render();
                })
                ->edit_column('is_visible', function($row) {
                    return $row->is_visible ? '<div class="text-center"><i class="fa fa-check-circle text-green"></i></div>' : '<div class="text-center"><i class="fa fa-times-circle text-muted"></i></div>';
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('photo', function($row) {
                    return view('admin.partials.datatables.photo', compact('row'))->render();
                })
                ->edit_column('created_at', function($row) {
                    return view('admin.partials.datatables.created_at', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.website.brands.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }
    
    /**
     * Remove the specified resource from storage.
     * GET /admin/brands/sort
     *
     * @return Response
     */
    public function sortEdit() {
   
        $items = Brand::orderBy('sort')
                        ->get(['id', 'name']);
        
        $route = route('admin.website.brands.sort.update');
        
        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }
    
    /**
     * Update the specified resource order in storage.
     * POST /admin/brands/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        $result = Brand::setNewOrder($request->ids);
        
        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }


}
