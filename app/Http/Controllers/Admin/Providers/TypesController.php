<?php

namespace App\Http\Controllers\Admin\Providers;

use Html, Response, Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\ProviderCategory;

class TypesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'providers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',providers']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $providerCategory = new ProviderCategory;

        $formOptions = array('route' => array('admin.providers-types.store'), 'method' => 'POST', 'class' => 'modal-ajax-form');

        $colors = trans('admin/global.colors');

        return view('admin.providers.types.index', compact('providerCategory', 'formOptions', 'colors'))->render();
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        ProviderCategory::flushCache(ProviderCategory::CACHE_TAG);

        $input = $request->all();
        
        $providerCategory = ProviderCategory::filterSource()->findOrNew($id);

        if ($providerCategory->validate($input)) {
            $providerCategory->fill($input);
            $providerCategory->source = config('app.source');
            $providerCategory->save();

            $row = $providerCategory;
            return Response::json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.',
                'html'     => view('admin.providers.types.datatables.name', compact('row'))->render()
            ]);
        }

        return Response::json([
            'result'   => false,
            'feedback' => $providerCategory->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        ProviderCategory::flushCache(ProviderCategory::CACHE_TAG);

        $result = ProviderCategory::filterSource()
                                ->whereId($id)
                                ->delete();

        if (!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Ocorreu um erro ao tentar remover a categoria de fornecedor.'
            ]);
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'Categoria de cliente removido com sucesso.'
        ]);
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        ProviderCategory::flushCache(ProviderCategory::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = ProviderCategory::filterSource()
                            ->whereIn('id', $ids)
                            ->delete();
        
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

        $data = ProviderCategory::filterSource()->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.providers.types.datatables.name', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.providers.types.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }
}
