<?php

namespace App\Http\Controllers\Admin\Website;

use App\Models\Website\PageSectionContent;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Html, Croppa, Response, File;

class PagesSectionsContentController extends \App\Http\Controllers\Admin\Controller {

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
//    public function index() {
//    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $pageId, $sectionId) {

        $block = $request->block;

        return view('admin.website.pages.contents.create', compact('content', 'block', 'pageId', 'sectionId'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $pageId, $sectionId) {
        $input = $request->all();
        $input['page_section_id'] = $sectionId;

        $content = new PageSectionContent();

        if ($content->validate($input)) {
            $content->fill($input);
            $content->save();

            return view('admin.website.pages.contents.edit', compact('content', 'block', 'pageId', 'sectionId'))->render();
        }

        return Redirect::back()->withInput()->with('error', $content->errors()->first());
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
    public function edit($pageId, $sectionId, $block) {

        $content = PageSectionContent::firstOrNew([
                        'page_section_id' => $sectionId,
                        'block'           => $block
                    ]);

        return view('admin.website.pages.contents.edit', compact('content', 'pageId'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $pageId, $sectionId, $block) {
        
        $input = $request->all();
        $input['target_blank'] = $request->get('target_blank', false);
        $input['autoplay']     = $request->get('autoplay', 0);
        $input['constrain_proportions'] = $request->get('constrain_proportions', 0);

        if(isset($input['embed']) && !empty($input['embed']) && !empty($input['autoplay'])) {
            $input['embed'] = $input['embed'] . '?autoplay=' . $input['autoplay'];
        }


        $content = PageSectionContent::where('page_section_id', $sectionId)
                                    ->firstOrNew(['block' => $block]);

        if ($content->validate($input)) {
            $content->fill($input);
            $content->page_section_id = $sectionId;

            //delete image
            if ($request->delete_photo && !empty($content->filepath)) {
                Croppa::delete($content->filepath);
                $content->filepath = null;
                $content->filename = null;
            }

            //upload image
            if($request->hasFile('image')) {

                if ($content->exists && !empty($content->filepath) && File::exists($content->filepath)) {
                    Croppa::delete($content->filepath);
                }

                if (!$content->upload($request->file('image'), 40)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem.');
                }

            } else {
                $content->save();
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $content->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($pageId, $sectionId, $block) {

        $result = PageSectionContent::where('page_section_id', $sectionId)
            ->where('block', $block)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Erro ao limpar conteúdos.');
        }

        return Redirect::back()->with('success', 'Conteúdos removidos com sucesso.');
    }

    /**
     * Load a single video by given video url
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getVideo(Request $request) {
        return loadYoutubeSingleVideo($request->url);
    }
}
