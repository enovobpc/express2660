<?php

namespace App\Http\Controllers\Admin\Website;

use App\Models\Website\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Croppa, Response, File;

class TestimonialsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'testimonials';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',testimonials']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.website.testimonials.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $testimonial = new Testimonial();

        $action = 'Adicionar Testemunho';

        $formOptions = ['route' => ['admin.website.testimonials.store'], 'method' => 'POST', 'files' => true];
        
        return view('admin.website.testimonials.edit', compact('testimonial', 'action', 'formOptions'))->render();
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
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $testimonial = Testimonial::findOrfail($id);

        $action = 'Editar Testemunho';

        $formOptions = ['route' => ['admin.website.testimonials.update', $testimonial->id], 'method' => 'PUT', 'files' => true];

        return view('admin.website.testimonials.edit', compact('testimonial', 'action', 'formOptions'))->render();
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
        
        $testimonial = Testimonial::findOrNew($id);

        if ($testimonial->validate($input)) {
            $testimonial->fill($input);
            
            //delete image
            if ($request->delete_photo && !empty($testimonial->filepath)) {
                if(File::exists($testimonial->filepath)) {
                    Croppa::delete($testimonial->filepath);
                    File::delete($testimonial->filepath);
                }
                $testimonial->filepath = null;
                $testimonial->filename = null;
            }
            
            //upload image
            if($request->hasFile('image')) {

                if ($testimonial->exists && !empty($testimonial->filepath) && File::exists($testimonial->filepath)) {
                    Croppa::delete($testimonial->filepath);
                    File::delete($testimonial->filepath);
                }

                if (!$testimonial->upload($request->file('image'), 40)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem.');
                }
                
            } else {
                $testimonial->save();
            }



            //delete brand image
            if ($request->delete_photo && !empty($testimonial->brand_filepath)) {
                if(File::exists($testimonial->brand_filepath)) {
                    Croppa::delete($testimonial->brand_filepath);
                    File::delete($testimonial->brand_filepath);
                }
                $testimonial->brand_filepath = null;
                $testimonial->brand_filename = null;
            }

            //upload image
            if($request->hasFile('brand_image')) {

                if ($testimonial->exists && !empty($testimonial->brand_filepath) && File::exists($testimonial->brand_filepath)) {
                    Croppa::delete($testimonial->brand_filepath);
                    File::delete($testimonial->brand_filepath);
                }

                $overrideColumns = [
                    'filename' => 'brand_filename',
                    'filepath' => 'brand_filepath'
                ];

                if (!$testimonial->upload($request->file('brand_image'), true, -1, $overrideColumns)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem.');
                }

            } else {
                $testimonial->save();
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $testimonial->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Testimonial::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a marca.');
        }

        return Redirect::route('admin.website.testimonials.index')->with('success', 'Marca removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/testimonials/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {
        
        $ids = explode(',', $request->ids);
        
        $result = Testimonial::whereIn('id', $ids)->delete();
        
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

        $data = Testimonial::withTranslation()
                        ->select();

        return Datatables::of($data)
                ->edit_column('translations.message', function($row) {
                    return view('admin.website.testimonials.datatables.message', compact('row'))->render();
                })
                ->edit_column('author', function($row) {
                    return view('admin.website.testimonials.datatables.author', compact('row'))->render();
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
                    return view('admin.website.testimonials.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }
    
    /**
     * Remove the specified resource from storage.
     * GET /admin/testimonials/sort
     *
     * @return Response
     */
    public function sortEdit() {
   
        $items = Testimonial::listsTranslations('message')
                    ->orderBy('sort')
                    ->get();
        
        $route = route('admin.website.testimonials.sort.update');
        
        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }
    
    /**
     * Update the specified resource order in storage.
     * POST /admin/testimonials/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        Testimonial::setNewOrder($request->ids);
        
        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }


}
