<?php

namespace App\Http\Controllers\Admin\Logistic;

use App\Models\Logistic\Category;
use App\Models\Logistic\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Setting, Response;

class SubcategoriesController extends \App\Http\Controllers\Admin\Controller {

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
//    public function index() {
//    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $action = 'Nova subcategoria';
        
        $subcategory = new SubCategory();

        $formOptions = array('route' => array('admin.logistic.subcategories.store'), 'method' => 'POST', 'class' => 'form-subcategories');

        $categories = Category::filterSource()
            ->whereNull('customer_id')
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'subcategory',
            'action',
            'formOptions',
            'categories'
        );

        return view('admin.logistic.subcategories.edit', $data)->render();
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

        $action = 'Editar subcategoria';

        $subcategory = SubCategory::filterSource()->findOrfail($id);

        $formOptions = array('route' => array('admin.logistic.subcategories.update', $subcategory->id), 'method' => 'PUT', 'class' => 'form-subcategories');

        $categories = Category::filterSource()
            ->where('customer_id', $subcategory->customer_id)
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'subcategory',
            'action',
            'formOptions',
            'categories'
        );

        return view('admin.logistic.subcategories.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        SubCategory::flushCache(SubCategory::CACHE_TAG);

        $input = $request->all();
        $input['category_id'] = $request->get('category_id');
        $input['customer_id'] = $request->get('customer_id');

        $item = SubCategory::filterSource()->findOrNew($id);

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

        SubCategory::flushCache(SubCategory::CACHE_TAG);

        $result = SubCategory::filterSource()
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

        SubCategory::flushCache(SubCategory::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = SubCategory::filterSource()
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

        $data = SubCategory::with('customer', 'category')
                ->filterSource()
                ->select();

        //filter customer
        $value = $request->customer;
        if($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter category
        $value = $request->category;
        if($request->has('category')) {
            $data = $data->whereIn('category_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('name', function($row) {
                return view('admin.logistic.models.datatables.name', compact('row'))->render();
            })
            ->edit_column('category_id', function($row) {
                return @$row->category->name;
            })
            ->edit_column('customer_id', function($row) {
                return @$row->customer->name;
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.logistic.models.datatables.actions', compact('row'))->render();
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

        $items = SubCategory::filterSource()
            ->orderBy('sort')
            ->get(['id', 'name']);

        $route = route('admin.logistic.subcategories.sort.update');

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

        SubCategory::flushCache(SubCategory::CACHE_TAG);

        try {
            SubCategory::setNewOrder($request->ids);
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
}
