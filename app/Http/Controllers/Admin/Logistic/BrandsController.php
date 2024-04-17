<?php

namespace App\Http\Controllers\Admin\Logistic;

use App\Models\Logistic\Brand;
use App\Models\Logistic\Category;
use App\Models\Logistic\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Setting, Form, Response;

class BrandsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'logistic_brands';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',logistic_brands']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $brands = Brand::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $families = Family::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $categories = Category::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'brands',
            'families',
            'categories'
        );

        return $this->setContent('admin.logistic.brands.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $action = 'Nova marca';
        
        $brand = new Brand();

        $formOptions = array('route' => array('admin.logistic.brands.store'), 'method' => 'POST', 'class' => 'form-brand');

        $data = compact(
            'brand',
            'action',
            'formOptions'
        );

        return view('admin.logistic.brands.edit', $data)->render();
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
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $action = 'Editar Marca';

        $brand = Brand::filterSource()->findOrfail($id);

        $formOptions = array('route' => array('admin.logistic.brands.update', $brand->id), 'method' => 'PUT', 'class' => 'form-brand');

        $data = compact(
            'brand',
            'action',
            'formOptions'
        );

        return view('admin.logistic.brands.edit', $data)->render();
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
        $input['customer_id'] = $request->get('customer_id');

        $item = Brand::filterSource()->findOrNew($id);

        if ($item->validate($input)) {
            $item->fill($input);
            $item->source = config('app.source');
            $item->save();

            return response()->json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.'
            ]);
        }

        return response()->json([
            'result'   => false,
            'feedback' => $item->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Brand::flushCache(Brand::CACHE_TAG);

        $result = Brand::filterSource()
                    ->whereId($id)
                    ->delete();

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

        Brand::flushCache(Brand::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = Brand::filterSource()
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

        $data = Brand::with('customer')
                ->filterSource()
                ->select();

        //filter customer
        $value = $request->customer;
        if($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('name', function($row) {
                return view('admin.logistic.brands.datatables.name', compact('row'))->render();
            })
            ->edit_column('customer_id', function($row) {
                return @$row->customer->name;
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.logistic.brands.datatables.actions', compact('row'))->render();
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

        $items = Brand::filterSource()
            ->orderBy('sort')
            ->get(['id', 'name']);

        $route = route('admin.logistic.brands.sort.update');

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

        Brand::flushCache(Brand::CACHE_TAG);

        try {
            Brand::setNewOrder($request->ids);
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

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSelect2List(Request $request, $type) {

        if($type == 'brands') {
            $response = $this->getBrandsList($request);
        } elseif($type == 'families') {
            $response = $this->getFamiliesList($request);
        } elseif($type == 'categories') {
            $response = $this->getCategoriesList($request);
        }

        return Response::json($response);
    }

    /**
     * Return brands list
     *
     * @param Request $request
     */
    public function getBrandsList(Request $request) {

        $items = Brand::filterSource()
            ->ordered()
            ->where('customer_id', $request->customer)
            ->pluck('name', 'id')
            ->toArray();

        return [
            'html' => Form::select('brand_id', ['' => ''] + $items, null, ['class' => 'form-control select2', 'data-placeholder' => ''])->toHtml()
        ];
    }

    /**
     * Return brands list
     *
     * @param Request $request
     */
    public function getFamiliesList(Request $request) {

        $items = Family::filterSource()
            ->ordered()
            ->where('customer_id', $request->customer)
            ->pluck('name', 'id')
            ->toArray();

        return [
            'html' => Form::select('family_id', ['' => ''] + $items, null, ['class' => 'form-control select2', 'data-placeholder' => ''])->toHtml()
        ];
    }

    /**
     * Return brands list
     *
     * @param Request $request
     */
    public function getCategoriesList(Request $request) {

        $items = Category::filterSource()
            ->ordered()
            ->where('customer_id', $request->customer)
            ->pluck('name', 'id')
            ->toArray();

        return [
            'html' => Form::select('category_id', ['' => ''] + $items, null, ['class' => 'form-control select2', 'data-placeholder' => ''])->toHtml()
        ];
    }
}
