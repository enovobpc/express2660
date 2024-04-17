<?php

namespace App\Http\Controllers\Admin\Traceability;

use App\Models\Agency;
use App\Models\Traceability\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Response, Cache;

class LocationsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'traceability';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',traceability']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $location = new Location();

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->where('source', config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $formOptions = array('route' => array('admin.traceability.locations.store'), 'method' => 'POST', 'class' => 'modal-ajax-form');

        return view('admin.traceability.locations.index', compact('location', 'agencies', 'formOptions'))->render();
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

        $input = $request->all();
        
        $location = Location::filterSource()->findOrNew($id);

        $agencies = Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->where('source', config('app.source'))
                ->filterAgencies()
                ->orderBy('code', 'asc')
                ->pluck('name', 'id')
                ->toArray();

        if ($location->validate($input)) {
            $location->fill($input);
            $location->source = config('app.source');
            $location->save();

            $row = $location;
            
            return Response::json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.',
                'html'     => view('admin.traceability.locations.datatables.name', compact('row', 'agencies'))->render()
            ]);
        }

        return Response::json([
            'result'   => false,
            'feedback' => $location->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $result = Location::filterSource()
                    ->whereId($id)
                    ->delete();

        if (!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Ocorreu um erro ao tentar remover o registo.'
            ]);
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'Registo removido com sucesso.'
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

        $ids = explode(',', $request->ids);
        
        $result = Location::filterSource()
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

        $data = Location::filterSource()
                ->with('agency')
                ->filterSource()
                ->select();

        $agencies = Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->where('source', config('app.source'))
                ->filterAgencies()
                ->orderBy('code', 'asc')
                ->pluck('name', 'id')
                ->toArray();

        return Datatables::of($data)
            ->edit_column('name', function($row) use($agencies) {
                return view('admin.traceability.locations.datatables.name', compact('row', 'agencies'))->render();
            })
            ->edit_column('agency_id', function($row) {
                return view('admin.traceability.locations.datatables.agency', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.traceability.locations.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Remove the specified resource from storage.
     * GET /admin/services/sort
     *
     * @return Response
     */
    /* public function sortEdit() {

        $items = Location::filterSource()
            ->orderBy('sort')
            ->get(['id', 'name']);

        $route = route('admin.traceability.locations.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    } */

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /* public function sortUpdate(Request $request) {

        try {
            Location::setNewOrder($request->ids);

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
    } */
}
