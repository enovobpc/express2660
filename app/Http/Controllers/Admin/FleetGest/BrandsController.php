<?php

namespace App\Http\Controllers\Admin\FleetGest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Croppa;
use App\Models\FleetGest\Brand;

class BrandsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_brands';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_brands']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $brands = Brand::pluck('name', 'id')->toArray();

        return $this->setContent('admin.fleet.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Marca';
        
        $brand = new Brand;
                
        $formOptions = array('route' => array('admin.fleet.brands.store'), 'method' => 'POST', 'files' => true);
        
        return view('admin.fleet.brands.edit', compact('brand', 'action', 'formOptions'))->render();
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

        $formOptions = array('route' => array('admin.fleet.brands.update', $brand->id), 'method' => 'PUT', 'files' => true);

        return view('admin.fleet.brands.edit', compact('brand', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Brand::flushCache(Brand::CACHE_TAG);

        $input = $request->all();
        
        $brand = Brand::findOrNew($id);

        if ($brand->validate($input)) {
            $brand->fill($input);
            $brand->save();

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

                if (!$brand->upload($request->file('image'))) {
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

        Brand::flushCache(Brand::CACHE_TAG);

        $result = Brand::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a marca.');
        }

        return Redirect::route('admin.feet.brands.index')->with('success', 'Marca removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Brand::flushCache(Brand::CACHE_TAG);

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
                        ->add_column('photo', function($row) {
                            return view('admin.partials.datatables.photo', compact('row'))->render();
                        })
                        ->edit_column('name', function($row) {
                            return view('admin.fleet.brands.datatables.name', compact('row'))->render();
                        })
                        ->add_column('select', function($row) {
                            return view('admin.partials.datatables.select', compact('row'))->render();
                        })
                        ->edit_column('created_at', function($row) {
                            return view('admin.partials.datatables.created_at', compact('row'))->render();
                        })
                        ->add_column('actions', function($row) {
                            return view('admin.fleet.brands.datatables.actions', compact('row'))->render();
                        })
                        ->make(true);
    }

}
