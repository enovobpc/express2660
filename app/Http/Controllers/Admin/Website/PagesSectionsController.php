<?php

namespace App\Http\Controllers\Admin\Website;

use App\Models\Website\PageSection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Html, Croppa, Response, File;

class PagesSectionsController extends \App\Http\Controllers\Admin\Controller {

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
    public function create($pageId) {

        $section = new PageSection();
        $section->page_id = $pageId;

        return view('admin.website.pages.sections.create', compact('section', 'formOptions'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $pageId) {
        return $this->update($request, $pageId, null);
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
    public function edit($pageId, $sectionId) {

        $section = PageSection::where('page_id', $pageId)
                            ->findOrfail($sectionId);

        return view('admin.website.pages.sections.edit', compact('section', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $pageId, $id) {
        
        $input = $request->all();
        $input['is_published'] = $request->get('is_published', false);
        
        $section = PageSection::where('page_id', $pageId)->findOrNew($id);

        if ($section->validate($input)) {
            $section->fill($input);
            $section->page_id = $pageId;
            $section->save();

            return Redirect::back()->with('success', 'Secção criada com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $section->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($pageId, $id) {

        $result = PageSection::where('page_id', $pageId)->where('id', $id)->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover esta secção da página.');
        }

        return Redirect::back()->with('success', 'Secção removida com sucesso.');
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/pages/{id}/sections/order
     *
     * @return Response
     */
    public function sort(Request $request, $pageId) {

        $ids = $request->get('ids');

        $result = PageSection::setNewOrder($ids);

        $response = array(
            'message' => 'Secções ordenadas com sucesso',
            'type'    => 'success'
        );

        return Response::json($response);
    }
    
}
