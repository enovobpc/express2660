<?php

namespace App\Http\Controllers\Admin\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Html, Croppa, Response, File;

class PagesMultimediaController extends \App\Http\Controllers\Admin\Controller {

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
//    public function create() {
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $file = $request->file('file');

        $destinationPath = public_path() . '/uploads/pages/';

        if(!$file->move($destinationPath, $this->getRandomFileName($file))) {
            return Redirect::back()->with('error', 'Não foi possível carregar o ficheiro.');
        }

        return Redirect::back()->with('success', 'Ficheiro carregado com sucesso.');
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
//    public function edit($id) {
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, $id) {
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($basename) {

        $result = File::delete(public_path().'/uploads/pages/'.$basename);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a página.');
        }

        return Redirect::back()->with('success', 'Página eliminada com sucesso.');
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

        $filepaths = [];
        foreach ($ids as $filename) {
            $filepaths[] = public_path().'/uploads/pages/'.$filename;
        }

        $result = File::delete($filepaths);
        
        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Get random string to use as file name
     *
     * @param integer $id
     * @param array|object $file
     * @return string
     */
    public function getRandomFileName($file)
    {
        return  strtolower(str_random(10)) . '.' . $file->getClientOriginalExtension();
    }

}
