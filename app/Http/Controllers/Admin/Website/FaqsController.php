<?php

namespace App\Http\Controllers\Admin\Website;

use App\Models\Website\Faq;
use App\Models\Website\FaqCategory;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Http\Request;
use Html, Croppa, Response, DB;

class FaqsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'faqs';

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
    public function index() {

        $categories = FaqCategory::withTranslation()->ordered()->get();
        $categories = $categories->pluck('name', 'id')->toArray();

        return $this->setContent('admin.website.faqs.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Pergunta Frequente';
        
        $faq = new Faq();

        $categories = FaqCategory::withTranslation()
            ->where('is_visible', true)
            ->ordered()
            ->get();

        $categories = $categories->pluck('name', 'id')
            ->toArray();

        $formOptions = ['route' => ['admin.website.faqs.store'], 'method' => 'POST'];
        
        return view('admin.website.faqs.edit', compact('faq', 'action', 'formOptions', 'categories'))->render();
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
        
        $action = 'Editar Pergunta Frequente';

        $faq = Faq::with('translations')->findOrfail($id);

        $categories = FaqCategory::withTranslation()->ordered()->get();
        $categories = $categories->pluck('name', 'id')->toArray();

        $formOptions = ['route' => ['admin.website.faqs.update', $faq->id], 'method' => 'PUT'];

        return view('admin.website.faqs.edit', compact('faq', 'action', 'formOptions', 'categories'))->render();
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

        foreach(app_locales() as $locale => $country) {
            $input[$locale]['is_visible'] = $request->input($locale.'.is_visible', false);
        }
        
        $faq = Faq::findOrNew($id);

        if ($faq->validate($input)) {
            $faq->fill($input);
            $faq->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $faq->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Faq::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::route('admin.website.faqs.index')->with('success', 'Registo removido com sucesso.');
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
        
        $result = Faq::whereIn('id', $ids)->delete();
        
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

        $data = Faq::with('translations')
                    ->with('category')
                    ->select();

        //filter category
        if($request->has('category')) {
            $data = $data->where('faq_category_id', $request->category);
        }

        return Datatables::of($data)
                ->edit_column('translation.question', function($row) {
                    return view('admin.website.faqs.datatables.questions.question', compact('row'))->render();
                })
                ->add_column('category', function($row) {
                    return @$row->category->name;
                })
                ->edit_column('is_visible', function($row) {
                    return view('admin.website.faqs.datatables.questions.visible', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->edit_column('created_at', function($row) {
                    return view('admin.partials.datatables.created_at', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.website.faqs.datatables.questions.actions', compact('row'))->render();
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
   
        $items = Faq::listsTranslations('question')
                    ->orderBy('sort')
                    ->get();

        $route = route('admin.website.faqs.sort.update');
        
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

        $result = Faq::setNewOrder($request->ids);
        
        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }
}
