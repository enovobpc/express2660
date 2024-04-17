<?php

namespace App\Http\Controllers\Admin\Website;

use App\Models\Website\FaqCategory;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Http\Request;
use Croppa, Response, DB;

class FaqsCategoriesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'faqs_categories';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',faqs']);
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
        
        $action = 'Adicionar Categoria';
        
        $category = new FaqCategory();
                
        $formOptions = ['route' => ['admin.website.faqs.categories.store'], 'method' => 'POST'];
        
        return view('admin.website.faqs.edit_category', compact('category', 'action', 'formOptions'))->render();
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
        
        $action = 'Editar Categoria';
        
        $category = FaqCategory::findOrfail($id);

        $formOptions = ['route' => ['admin.website.faqs.categories.update', $category->id], 'method' => 'PUT'];

        return view('admin.website.faqs.edit_category', compact('category', 'action', 'formOptions'))->render();
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
        $input['is_visible'] = $request->get('is_visible', false);

        $category = FaqCategory::findOrNew($id);

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
        
        $result = FaqCategory::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::back()->with('success', 'Registo removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/features/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {
        
        $ids = explode(',', $request->ids);
        
        $result = FaqCategory::whereIn('id', $ids)->delete();
        
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

        $bindings = [
            'faqs_categories.*',
            DB::raw('(select count(*) from faqs where faq_category_id = faqs_categories.id and faqs.deleted_at is NULL) as count_values'),
        ];
                
        $data = FaqCategory::with('translations')
                        ->select($bindings);
        
        return Datatables::of($data)
                        ->edit_column('translations.name', function($row) {
                            return view('admin.website.faqs.datatables.categories.name', compact('row'))->render();
                        })
                        ->edit_column('count_values', function($row) {
                            return '<div class="text-center">' . $row->count_values . '</div>';
                        })
                        ->edit_column('is_visible', function($row) {
                            return view('admin.website.faqs.datatables.categories.visible', compact('row'))->render();
                        })
                        ->add_column('select', function($row) {
                            return view('admin.partials.datatables.select', compact('row'))->render();
                        })
                        ->edit_column('created_at', function($row) {
                            return view('admin.partials.datatables.created_at', compact('row'))->render();
                        })
                        ->add_column('actions', function($row) {
                            return view('admin.website.faqs.datatables.categories.actions', compact('row'))->render();
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
   
        $items = FaqCategory::listsTranslations('name')
                    ->orderBy('sort')
                    ->get();

        $route = route('admin.faqs.categories.sort.update');
        
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

        $result = FaqCategory::setNewOrder($request->ids);
        
        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }
}
