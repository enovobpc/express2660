<?php

namespace App\Http\Controllers\Admin\Shipments;

use Html, Cache, Response, Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

use App\Models\ShippingStatus;
use App\Models\Core\Source;

class StatusController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'tracking_status';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',tracking_status']);
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.shipments.status.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $status = new ShippingStatus;

        $action = 'Adicionar Estado de Envio';
                
        $formOptions = array('route' => ['admin.tracking.status.store'], 'method' => 'POST');

        $data = compact(
            'status',
            'action',
            'formOptions'
        );

        return view('admin.shipments.status.edit', $data)->render();
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
    public function edit(Request $request, $id) {

        $status = ShippingStatus::findOrfail($id);

        $action = 'Editar Estado de Envio';

        $formOptions = array('route' => ['admin.tracking.status.update', $status->id], 'method' => 'PUT');

        $data = compact(
            'status',
            'action',
            'formOptions'
        );

        return view('admin.shipments.status.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        ShippingStatus::flushCache(ShippingStatus::CACHE_TAG);

        $input = $request->all();
        $input['is_shipment']       = $request->get('is_shipment', false);
        $input['is_collection']     = $request->get('is_collection', false);
        $input['is_final']          = $request->get('is_final', false);
        $input['is_visible']        = $request->get('is_visible', false);
        $input['is_traceability']   = $request->get('is_traceability', false);
        $input['is_public']         = $request->get('is_public', false);
        $input['is_static']         = $request->get('is_static', false);
        $input['sources']           = [config('app.source')];

        $status = ShippingStatus::findOrNew($id);

        if ($status->validate($input)) {
            $status->fill($input);
            $status->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $status->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        ShippingStatus::flushCache(ShippingStatus::CACHE_TAG);

        $result = ShippingStatus::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o estado de envio.');
        }

        return Redirect::route('admin.tracking.status.index')->with('success', 'Estado de envio removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        ShippingStatus::flushCache(ShippingStatus::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = ShippingStatus::whereIn('id', $ids)->delete();
        
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

        $data = ShippingStatus::where('sources', 'like', '%"'.config('app.source').'"%')
            ->select();

        //filter is_shipment
        $value = $request->is_shipment;
        if($request->has('is_shipment')) {
            $data = $data->where('is_shipment', $value);
        }

        //filter is_collection
        $value = $request->is_collection;
        if($request->has('is_collection')) {
            $data = $data->where('is_collection', $value);
        }

        //filter is_final
        $value = $request->is_final;
        if($request->has('is_final')) {
            $data = $data->where('is_final', $value);
        }

        //filter is_visible
        $value = $request->is_visible;
        if($request->has('is_visible')) {
            $data = $data->where('is_visible', $value);
        }

        //filter is_public
        $value = $request->is_public;
        if($request->has('is_public')) {
            $data = $data->where('is_public', $value);
        }

        //filter platforms
        $value = $request->sources;
        if($request->has('sources')) {
            $data = $data->where(function($q) use($value) {
                foreach ($value as $item) {
                    $q->orWhere('sources', 'like', '%"'.$item.'"%');
                }
            });
        }

        $sources = Source::remember(config('cache.query_ttl'))
                ->cacheTags(Source::CACHE_TAG)
                ->pluck('name', 'source')
                ->toArray();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.shipments.status.datatables.name', compact('row'))->render();
                })
                ->add_column('custom_name', function($row) {
                    return view('admin.shipments.status.datatables.custom_name', compact('row'))->render();
                })
                ->edit_column('sources', function($row) use ($sources) {
                    return view('admin.partials.datatables.sources', compact('row', 'sources'))->render();
                })
                ->edit_column('is_shipment', function($row) {
                    return view('admin.shipments.status.datatables.is_shipment', compact('row'))->render();
                })
                ->edit_column('is_collection', function($row) {
                    return view('admin.shipments.status.datatables.is_collection', compact('row'))->render();
                })
                ->edit_column('is_final', function($row) {
                    return view('admin.shipments.status.datatables.is_final', compact('row'))->render();
                })
                ->edit_column('is_visible', function($row) {
                    return view('admin.shipments.status.datatables.is_visible', compact('row'))->render();
                })
                ->edit_column('is_public', function($row) {
                    return view('admin.shipments.status.datatables.is_public', compact('row'))->render();
                })
                ->edit_column('is_traceability', function($row) {
                    return view('admin.shipments.status.datatables.is_traceability', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.shipments.status.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }


    /**
     * Remove the specified resource from storage.
     * GET /admin/services/sort
     *
     * @return Response
     */
    public function sortEdit() {

        $items = ShippingStatus::where('sources', 'like', '%"'.config('app.source').'"%')
            ->orderBy('sort')
            ->get(['id', 'name']);

        $route = route('admin.tracking.status.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        ShippingStatus::flushCache(ShippingStatus::CACHE_TAG);

        try {
            ShippingStatus::setNewOrder($request->ids);
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

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(Request $request) {

        try {
            ShippingStatus::flushCache(ShippingStatus::CACHE_TAG);

            $ids            = explode(',', $request->ids);
            $isVisible      = $request->get('is_visible', false);
            $shippingStatus = ShippingStatus::whereIn('id', $ids)->get();

            foreach ($shippingStatus as $status) {
                $status->is_visible = $isVisible;
                $status->save();
            }

            return Redirect::back()->with('success', 'Registos selecionados alterados com sucesso.');

        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }
}
