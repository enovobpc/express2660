<?php

namespace App\Http\Controllers\Admin\Traceability;

use App\Models\Agency;
use App\Models\ShippingStatus;
use App\Models\Traceability\Event;
use App\Models\Traceability\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Response, Cache;

class EventsController extends \App\Http\Controllers\Admin\Controller {

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

        $event = new Event();

        $actions = trans('admin/traceability.read-points');

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->where('source', config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();
            
        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->where('is_traceability', 1)
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $locations = $this->listLocations(Location::with('agency')
            ->filterSource()
            ->ordered()
            ->get());
        
        $formOptions = array('route' => array('admin.traceability.events.store'), 'method' => 'POST', 'class' => 'modal-ajax-form');

        $data = compact(
            'event',
            'agencies',
            'actions',
            'status',
            'locations',
            'formOptions'
        );

        return view('admin.traceability.events.index', $data)->render();
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
        
        $event = Event::filterSource()->findOrNew($id);

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->where('source', config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();
            
        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->where('is_traceability', 1)
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $locations = $this->listLocations(Location::with('agency')
            ->filterSource()
            ->ordered()
            ->get());

        if ($event->validate($input)) {
            $event->fill($input);
            $event->source = config('app.source');
            $event->save();

            $row = $event;

            return Response::json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.',
                'html'     => view('admin.traceability.events.datatables.name', compact('row', 'agencies', 'status', 'locations'))->render()
            ]);
        }

        return Response::json([
            'result'   => false,
            'feedback' => $event->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $result = Event::filterSource()
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
        
        $result = Event::filterSource()
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

        $data = Event::with('agency', 'status', 'location')
            ->filterSource()
            ->select();

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->where('source', config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();
            
        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->where('is_traceability', 1)
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $locations = $this->listLocations(Location::with('agency')
            ->filterSource()
            ->ordered()
            ->get());

        return Datatables::of($data)
                ->edit_column('name', function($row) use($agencies, $status, $locations) {
                    return view('admin.traceability.events.datatables.name', compact('row', 'agencies', 'status', 'locations'))->render();
                })
                ->edit_column('action', function($row) {
                    return view('admin.traceability.events.datatables.action', compact('row'))->render();
                })
                ->edit_column('agency_id', function($row) {
                    return view('admin.traceability.events.datatables.agency', compact('row'))->render();
                })
                ->edit_column('status_id', function($row) {
                    return view('admin.traceability.events.datatables.status', compact('row'))->render();
                })
                ->edit_column('location_id', function($row) {
                    return view('admin.traceability.events.datatables.location', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.traceability.events.datatables.actions', compact('row'))->render();
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

        $items = Event::filterSource()
            ->orderBy('sort')
            ->get(['id', 'name']);

        $route = route('admin.services.groups.sort.update');

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

        Event::flushCache(Event::CACHE_TAG);

        try {
            Event::setNewOrder($request->ids);

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

    /**
     * List all locations array
     *
     * @param [type] $allLocations
     * @return void
     */
    public function listLocations($allLocations) {
        $allLocations = $allLocations->groupBy('agency.print_name');
        $arr = [];
        foreach($allLocations as $agencyName => $agencyLoactions) {
            $arr[$agencyName] = $agencyLoactions->pluck('name', 'id')->toArray();
        }
        return $arr;
    }
}
