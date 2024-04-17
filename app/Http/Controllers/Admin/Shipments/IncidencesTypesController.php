<?php

namespace App\Http\Controllers\Admin\Shipments;

use Response, Auth;
use App\Models\Core\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\IncidenceType;

class IncidencesTypesController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'incidences_types';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',incidences_types']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->setContent('admin.shipments.incidences_types.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $incidenceType = new IncidenceType;

        $action = 'Adicionar Tipo de Incidência';

        $formOptions = array('route' => array('admin.tracking.incidences.store'), 'method' => 'POST');

        return view('admin.shipments.incidences_types.edit', compact('incidenceType', 'sources', 'action', 'formOptions'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        $incidenceType = IncidenceType::filterSource()
            ->findOrfail($id);

        $action = 'Editar Tipo de Incidência';

        $formOptions = array('route' => array('admin.tracking.incidences.update', $incidenceType->id), 'method' => 'PUT');

        $data = compact(
            'incidenceType',
            'sources',
            'action',
            'formOptions'
        );

        return view('admin.shipments.incidences_types.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        IncidenceType::flushCache(IncidenceType::CACHE_TAG);

        $input = $request->all();

        $input['is_active']        = $request->get('is_active', false);
        $input['is_shipment']      = $request->get('is_shipment', false);
        $input['is_pickup']        = $request->get('is_pickup', false);
        $input['photo_required']   = $request->get('photo_required', false);
        $input['date_required']    = $request->get('date_required', false);
        $input['pudo_required']    = $request->get('pudo_required', false);
        $input['operator_visible'] = $request->get('operator_visible', false);

        $incidenceType = IncidenceType::filterSource()
            ->findOrNew($id);

        if ($incidenceType->validate($input)) {
            $incidenceType->fill($input);
            $incidenceType->source = config('app.source');
            $incidenceType->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $incidenceType->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        IncidenceType::flushCache(IncidenceType::CACHE_TAG);

        $result = IncidenceType::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o tipo de incidência.');
        }

        return Redirect::route('admin.tracking.incidences.index')->with('success', 'Tipo de incidência removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        IncidenceType::flushCache(IncidenceType::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = IncidenceType::filterSource()->whereIn('id', $ids)->delete();

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
    public function datatable(Request $request)
    {

        $data = IncidenceType::filterSource()
            ->select();

        $value = $request->get('active');
        if ($request->has('active')) {
            $data = $data->where('is_active', $value);
        }

        return Datatables::of($data)
            ->edit_column('name', function ($row) {
                return view('admin.shipments.incidences_types.datatables.name', compact('row'))->render();
            })
            ->edit_column('operator_visible', function ($row) {
                return view('admin.shipments.incidences_types.datatables.operator_visible', compact('row'))->render();
            })
            ->edit_column('photo_required', function ($row) {
                return view('admin.shipments.incidences_types.datatables.photo', compact('row'))->render();
            })
            ->edit_column('date_required', function ($row) {
                return view('admin.shipments.incidences_types.datatables.date', compact('row'))->render();
            })
            ->edit_column('pudo_required', function ($row) {
                return view('admin.shipments.incidences_types.datatables.pudo', compact('row'))->render();
            })
            ->edit_column('is_shipment', function ($row) {
                return view('admin.shipments.incidences_types.datatables.shipment', compact('row'))->render();
            })
            ->edit_column('is_pickup', function ($row) {
                return view('admin.shipments.incidences_types.datatables.pickup', compact('row'))->render();
            })
            ->edit_column('is_active', function ($row) {
                return view('admin.shipments.incidences_types.datatables.active', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.shipments.incidences_types.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     * GET /admin/services/sort
     *
     * @return Response
     */
    public function sortEdit()
    {

        $items = IncidenceType::orderBy('sort')->get(['id', 'name']);

        $route = route('admin.tracking.incidences.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request)
    {

        IncidenceType::flushCache(IncidenceType::CACHE_TAG);

        try {
            IncidenceType::setNewOrder($request->ids);
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
    public function massUpdate(Request $request)
    {

        try {
            IncidenceType::flushCache(IncidenceType::CACHE_TAG);

            $ids = explode(',', $request->ids);

            $updateArr = [];

            if ($request->get('is_active') != '') {
                $updateArr += ['is_active' => $request->get('is_active')];
            }

            if ($request->get('operator_visible') != '') {
                $updateArr += ['operator_visible' => $request->get('operator_visible')];
            }

            if ($request->get('is_shipment') != '') {
                $updateArr += ['is_shipment' => $request->get('is_shipment')];
            }

            if ($request->get('is_pickup') != '') {
                $updateArr += ['is_pickup' => $request->get('is_pickup')];
            }


            if (!empty($updateArr)) {
                IncidenceType::whereIn('id', $ids)->update($updateArr);
            }

            return Redirect::back()->with('success', 'Registos selecionados alterados com sucesso.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }
}
