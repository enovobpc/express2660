<?php

namespace App\Http\Controllers\Admin\Webservices;

use Html, Cache, Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\WebserviceMethod;
use App\Models\Core\Source;

class MethodsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'webservice_methods';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',webservice_methods']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.webservices.methods.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $webserviceMethod = new WebserviceMethod;

        $sources = Source::orderBy('name')
                    ->pluck('name', 'source')
                    ->toArray();

        $action = 'Adicionar Método de Webservice';

        $formOptions = ['route' => ['admin.webservice-methods.store'], 'method' => 'POST'];

        $data = compact(
            'webserviceMethod',
            'action',
            'formOptions',
            'sources'
        );

        return view('admin.webservices.methods.edit', $data)->render();
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

        $webserviceMethod = WebserviceMethod::findOrfail($id);

        $sources = Source::orderBy('name')
                        ->pluck('name', 'source')
                        ->toArray();

        $action = 'Editar Método de Webservice';

        $formOptions = ['route' => ['admin.webservice-methods.update', $webserviceMethod->id], 'method' => 'PUT'];

        $data = compact(
            'webserviceMethod',
            'action',
            'formOptions',
            'sources'
        );

        return view('admin.webservices.methods.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        WebserviceMethod::flushCache(WebserviceMethod::CACHE_TAG);

        $input = $request->all();
        $input['enabled'] = $request->get('enabled', false);

        if($input['enabled']) {
            $input['sources'] = [config('app.source')];
        } else {
            $input['sources'] = null;
        }

        $webserviceMethod = WebserviceMethod::findOrNew($id);
        if ($webserviceMethod->validate($input)) {
            $webserviceMethod->fill($input);
            $webserviceMethod->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $webserviceMethod->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = WebserviceMethod::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o método de importação.');
        }

        return Redirect::back()->with('success', 'Método de importação removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {
        
        $ids = explode(',', $request->ids);
        
        $result = WebserviceMethod::whereIn('id', $ids)->delete();
        
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

        $data = WebserviceMethod::select();

        $sources = Source::remember(config('cache.query_ttl'))
                    ->cacheTags(Source::CACHE_TAG)
                    ->pluck('name', 'source')
                    ->toArray();
        
        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.webservices.methods.datatables.name', compact('row'))->render();
                })
                ->edit_column('enabled', function($row) {
                    return view('admin.webservices.methods.datatables.enabled', compact('row'))->render();
                })
                ->edit_column('sources', function($row) use ($sources) {
                    return view('admin.partials.datatables.sources', compact('row', 'sources'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->edit_column('created_at', function($row) {
                    return view('admin.partials.datatables.created_at', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.webservices.methods.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     * GET /admin/<module>/sort
     *
     * @return Response
     */
    public function sortEdit() {

        $items = WebserviceMethod::orderBy('sort')->get(['id', 'name']);

        $route = route('admin.webservice-methods.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/<module>/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        WebserviceMethod::flushCache(WebserviceMethod::CACHE_TAG);

        try {
            WebserviceMethod::setNewOrder($request->ids);
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
