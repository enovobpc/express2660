<?php

namespace App\Http\Controllers\Admin\Brand;

use App\Models\Brand;
use App\Models\BrandModel;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Response;

class BrandModelController extends \App\Http\Controllers\Admin\Controller {

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
    public function index(Request $request, int $brandId) {
        $brand = Brand::filterSource()->findOrFail($brandId);
        $formOptions = [
            'route'  => ['admin.brands.models.store', $brandId],
            'method' => 'POST',
            'class'  => 'modal-ajax-form'
        ];

        return view('admin.brand.model.index', compact('brand', 'formOptions'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, int $brandId) {
        return $this->update($request, $brandId, null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $brandId, $id = null) {
        $brandModel = BrandModel::where('brand_id', $brandId)
            ->findOrNew($id);

        $input = $request->all();
        if ($brandModel->validate($input)) {
            $brandModel->fill($input);
            $brandModel->brand_id = $brandId;
            $brandModel->save();

            return Response::json([
                'result' => true,
                'feedback' => 'Modelo gravado com sucesso.'
            ]);
        }

        return Response::json([
            'result' => false,
            'feedback' => $brandModel->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $brandId, int $id) {
        $result = BrandModel::whereId($id)->delete();
        if (!$result) {
            return Response::json([
                'result' => false,
                'feedback' => 'Ocorreu um erro ao tentar remover o registo.'
            ]);
        }

        return Response::json([
            'result' => true,
            'feedback' => 'Registo removido com sucesso.'
        ]);
    }

    /**
     * Get models list
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request) {
        $brandModels = BrandModel::where('brand_id', $request->get('brand_id', -1))
            ->get();

        $data = [];
        foreach ($brandModels as $brandModel) {
            $data[] = [
                'id' => $brandModel->id,
                'text' => $brandModel->name
            ];
        }
        
        return Response::json([
            'result' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request, int $brandId) {
        $data = BrandModel::where('brand_id', $brandId)
            ->select();

        return Datatables::of($data)
            ->editColumn('name', function($row) {
                return view('admin.brand.model.datatables.name', compact('row'));
            })
            ->addColumn('actions', function($row) {
                return view('admin.brand.model.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
