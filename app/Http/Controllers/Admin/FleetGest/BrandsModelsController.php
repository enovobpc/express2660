<?php

namespace App\Http\Controllers\Admin\FleetGest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\FleetGest\Brand;
use App\Models\FleetGest\BrandModel;
use Html, Croppa;

class BrandsModelsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_models';

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
//    public function index() {
//    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $action = 'Adicionar Modelo';
        
        $model = new BrandModel;
                
        $formOptions = array('route' => array('admin.fleet.brand-models.store'), 'method' => 'POST', 'files' => true);

        $brands = Brand::pluck('name', 'id')->toArray();
        
        return view('admin.fleet.brands.edit_model', compact('model', 'brands', 'action', 'formOptions'))->render();
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
        
        $action = 'Editar Modelo';
        
        $model = BrandModel::findOrfail($id);

        $formOptions = array('route' => array('admin.fleet.brand-models.update', $model->id), 'method' => 'PUT', 'files' => true);

        $brands = Brand::pluck('name', 'id')->toArray();

        return view('admin.fleet.brands.edit_model', compact('model', 'brands', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        BrandModel::flushCache(BrandModel::CACHE_TAG);

        $input = $request->all();
        
        $model = BrandModel::findOrNew($id);

        if ($model->validate($input)) {
            $model->fill($input);
            $model->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $model->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        BrandModel::flushCache(BrandModel::CACHE_TAG);

        $result = BrandModel::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a marca.');
        }

        return Redirect::route('admin.feet.models.index')->with('success', 'Marca removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        BrandModel::flushCache(BrandModel::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = BrandModel::whereIn('id', $ids)->delete();
        
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

        $data = BrandModel::with('brand')
                        ->select();

        //filter brand
        $value = $request->brand;
        if($request->has('brand')) {
            $data = $data->where('brand_id', $value);
        }

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.fleet.brands.datatables.models.name', compact('row'))->render();
                })
                ->edit_column('brand_id', function($row) {
                    return @$row->brand->name;
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->edit_column('created_at', function($row) {
                    return view('admin.partials.datatables.created_at', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.fleet.brands.datatables.models.actions', compact('row'))->render();
                })
                ->make(true);
    }

}
