<?php

namespace App\Http\Controllers\Admin\Website;

use App\Models\Website\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Croppa, Response;

class DocumentsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'documents';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',documents']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.website.documents.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar documento';
        
        $document = new Document();
                
        $formOptions = ['route' => ['admin.website.documents.store'], 'method' => 'POST', 'files' => true];
        
        return view('admin.website.documents.edit', compact('document', 'action', 'formOptions'))->render();
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
        
        $action = 'Editar documento';
        
        $document = Document::findOrfail($id);

        $formOptions = ['route' => ['admin.website.documents.update', $document->id], 'method' => 'PUT', 'files' => true];

        return view('admin.website.documents.edit', compact('document', 'action', 'formOptions'))->render();
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

        $document = Document::findOrNew($id);

        if ($document->validate($input)) {
            $document->fill($input);

            if($request->hasFile('file')) {
                if ($document->exists && !empty($document->filepath)) {
                    File::delete($document->filepath);
                }

                if (!$document->upload($request->file('file'), true, 40)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o documento.');
                }
            } else {
                $document->save();
            }

            if($request->ajax()) {
                return Response::json([
                    'result'    => true,
                    'type'      => 'success',
                    'feedback'  => 'Documento carregado com sucesso.'
                ]);
            } else {
                return Redirect::back()->with('success', 'Documento carregado com sucesso.');
            }
        }

        if($request->ajax()) {
            return Response::json([
                'result'    => false,
                'type'      => 'error',
                'feedback'  => $document->errors()->first()
            ]);
        } else {
            return Redirect::back()->withInput()->with('error', $document->errors()->first());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $document = Document::findOrFail($id);

        if(!empty($document->filepath) && File::exists(public_path($document->filepath))) {
            File::delete(public_path($document->filepath));
        }

        if (!$document->forceDelete()) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o documento.');
        }

        return Redirect::route('admin.website.documents.index')->with('success', 'Documento removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/documents/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        $ids = explode(',', $request->ids);

        $documents = Document::whereIn('id', $ids)->get();

        foreach ($documents as $document) {
            if (!empty($document->filepath) && File::exists(public_path($document->filepath))) {
                File::delete(public_path($document->filepath));
            }

            $document->forceDelete();
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = Document::select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.website.documents.datatables.name', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->edit_column('created_at', function($row) {
                    return view('admin.partials.datatables.created_at', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.website.documents.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }
    
    /**
     * Remove the specified resource from storage.
     * GET /admin/documents/sort
     *
     * @return Response
     */
    public function sortEdit() {
   
        $items = Document::orderBy('sort')
                        ->get(['id', 'name']);
        
        $route = route('admin.website.documents.sort.update');
        
        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }
    
    /**
     * Update the specified resource order in storage.
     * POST /admin/documents/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        $result = Document::setNewOrder($request->ids);
        
        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }


}
