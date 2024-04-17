<?php

namespace App\Http\Controllers\Admin\Website;

use App\Models\Website\Page;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Http\Request;
use Html, Croppa, Response, File;

class PagesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'pages';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',pages']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.website.pages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Criar nova Página';
        
        $page = new Page();
                
        $formOptions = ['route' => ['admin.website.pages.store'], 'method' => 'POST', 'files' => true];
        
        return view('admin.website.pages.create', compact('page', 'action', 'formOptions'))->render();
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
        
        $action = 'Editar Página';
        
        $page = Page::with(['sections' => function($q){
                            $q->ordered();
                        }])
                        ->findOrfail($id);

        $formOptions = ['route' => ['admin.website.pages.update', $page->id], 'method' => 'PUT', 'files' => true];

        $directory = public_path() . '/uploads/pages';

        if(!File::exists($directory)) {
            File::makeDirectory($directory);
        }

        $multimedia = collect(File::allFiles($directory))
            ->sortByDesc(function ($file) {
                return $file->getCTime();
            });

        return $this->setContent('admin.website.pages.edit', compact('page', 'action', 'formOptions', 'multimedia'));
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
        $input['published']  = $request->get('published', false);
        $input['show_title'] = $request->get('show_title', false);
        $input['show_breadcrumb'] = $request->get('show_breadcrumb', false);
        
        $page = Page::findOrNew($id);

        if ($page->validate($input)) {
            $page->fill($input);

            //delete image
            if ($request->delete_photo && !empty($page->filepath)) {
                Croppa::delete($page->filepath);
                $page->filepath = null;
                $page->filename = null;
            }
            
            //upload image
            if($request->hasFile('image')) {

                if ($page->exists && !empty($page->filepath) && File::exists($page->filepath)) {
                    Croppa::delete($page->filepath);
                }

                if (!$page->upload($request->file('image'), 40)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem.');
                }
                
            } else {
                $page->save();
            }

            $page->storeRoutes();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $page->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Page::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a página.');
        }

        return Redirect::route('admin.website.pages.index')->with('success', 'Página eliminada com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/Pages/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {
        
        $ids = explode(',', $request->ids);
        
        $result = Page::whereIn('id', $ids)->delete();
        
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

        $data = Page::withTranslation()
                        ->select();

        return Datatables::of($data)
                        ->edit_column('translations.title', function($row) {
                            return view('admin.website.pages.datatables.title', compact('row'))->render();
                        })
                        ->edit_column('translations.url', function($row) {
                            return view('admin.website.pages.datatables.url', compact('row'))->render();
                        })
                        ->edit_column('published', function($row) {
                            return view('admin.website.pages.datatables.published', compact('row'))->render();
                        })
                        ->add_column('select', function($row) {
                            return view('admin.partials.datatables.select', compact('row'))->render();
                        })
                        ->edit_column('created_at', function($row) {
                            return view('admin.partials.datatables.created_at', compact('row'))->render();
                        })
                        ->add_column('actions', function($row) {
                            return view('admin.website.pages.datatables.actions', compact('row'))->render();
                        })
                        ->make(true);
    }

}
