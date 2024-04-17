<?php

namespace App\Http\Controllers\Admin\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;
use App\Models\Website\Slider;
use Html, Response, Croppa;

class SlidersController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'sliders';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',sliders']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $sliders = Slider::select();

        $value = $request->locale;
        if($request->has('locale')) {
            $sliders = $sliders->where('locales', 'like', '%' . $value . '%');
        }

        //filter visible
        $value = $request->visible;
        if($request->has('visible')) {
            $sliders = $sliders->where('visible', $value);
        }

        $sliders = $sliders->ordered()->get();

        return $this->setContent('admin.website.sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $slider = new Slider;

        $action = 'Novo Slider';

        $formOptions = array('route' => array('admin.website.sliders.store'), 'files' => true);

        return view('admin.website.sliders.edit', compact('slider', 'action', 'formOptions'))->render();
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

        $action = 'Editar Slider';

        $slider = Slider::findOrfail($id);

        $formOptions = array('route' => ['admin.website.sliders.update', $slider->id], 'method' => 'PUT');

        return view('admin.website.sliders.edit', compact('slider', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     * PUT /admin/sliders/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id = null) {

        $slider = Slider::findOrNew($id);

        $input = $request->all();
        $input['id'] = $slider->id;
        $input['visible'] = $request->get('visible', false);
        $input['target_blank'] = $request->get('target_blank', false);

        if ($slider->validate($input)) {
            $slider->fill($input);

            if ($request->hasFile('imagem') || $request->hasFile('imagem_xs')) {

                if ($slider->exists) {
                    if(!empty($slider->filepath) && File::exists(public_path($slider->filepath))) {
                        Croppa::delete($slider->filepath);
                        File::delete(public_path($slider->filepath));
                    } elseif(!empty($slider->filepath_xs) && File::exists(public_path($slider->filepath_xs))) {
                        Croppa::delete($slider->filepath_xs);
                        File::delete(public_path($slider->filepath_xs));
                    }
                }

                $errors = [];
                //upload standard image
                if ($request->hasFile('imagem')) {
                    if (!$slider->upload($input['imagem'], true, 50)) {
                        $errors[] = 'Erro ao fazer upload da imagem principal do slider.';
                    }
                }

                //upload image xs
                if ($request->hasFile('imagem_xs')) {
                    if (!$slider->upload($input['imagem_xs'], true, 50, ['filepath' => 'filepath_xs', 'filename' => 'filename_xs'])) {
                        $errors[] = 'Erro ao fazer upload da imagem para telemóvel do slider.';
                    }
                }

                if($errors) {
                    return Redirect::back()->withInput()->with('error', 'Erro ao enviar a imagem do slider.');
                }

            } else {
                $slider->save();
            }

            return Redirect::route('admin.website.sliders.index')->with('success', 'Alterações gravadas com sucesso.');
        } else {
            return Redirect::back()->withInput()->with('error', $slider->errors()->first());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $slider = Slider::find($id);

        if(!empty($slider->filepath) && File::exists(public_path($slider->filepath))) {
            Croppa::delete(public_path($slider->filepath));
            File::delete(public_path($slider->filepath));
        }

        if(!empty($slider->filepath_xs) && File::exists(public_path($slider->filepath_xs))) {
            Croppa::delete(public_path($slider->filepath_xs));
            File::delete(public_path($slider->filepath_xs));
        }

        try {
            $slider->translations()->forceDelete();
            $slider->forceDelete();

            return Redirect::route('admin.website.sliders.index')->with('success', 'Slider removido com sucesso.');
        } catch (\Exception $e) {
            return Redirect::route('admin.website.sliders.index')->with('error', $e->getMessage());
        }

    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/sliders/order
     *
     * @return Response
     */
    public function sort(Request $request) {

        $ids = $request->get('ids');

        Slider::setNewOrder($ids);

        $response = array(
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        );

        return Response::json($response);
    }
}