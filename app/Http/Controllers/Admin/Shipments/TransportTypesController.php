<?php

namespace App\Http\Controllers\Admin\Shipments;

use App\Models\TransportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Response, Auth;

class TransportTypesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'pack_types';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',transport_types']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.shipments.transport_types.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $transportType = new TransportType();

        $action = 'Adicionar Tipo de Transporte';

        $formOptions = array('route' => array('admin.transport-types.store'), 'method' => 'POST');

        return view('admin.shipments.transport_types.edit', compact('transportType', 'action', 'formOptions'))->render();
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

        $transportType = TransportType::findOrfail($id);

        $action = 'Editar Tipo de Transporte';

        $formOptions = array('route' => array('admin.transport-types.update', $transportType->id), 'method' => 'PUT');

        $data = compact(
            'transportType',
            'action',
            'formOptions'
        );
        return view('admin.shipments.transport_types.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        TransportType::flushCache(TransportType::CACHE_TAG);

        $input = $request->all();
        $input['is_active'] = $request->get('is_active', false);

        $exists = TransportType::where('code', @$input['code'])
            ->where('id', '<>', $id)
            ->first();

        if($exists) {
            return Redirect::back()->with('errpr', 'Já existe outro tipo com o código indicado.');
        }

        $transportType = TransportType::findOrNew($id);

        if ($transportType->validate($input)) {
            $transportType->fill($input);
            $transportType->source = config('app.source');
            $transportType->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $transportType->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        TransportType::flushCache(TransportType::CACHE_TAG);

        $result = TransportType::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::route('admin.transport-types.index')->with('success', 'Registo removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        TransportType::flushCache(TransportType::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = TransportType::whereIn('id', $ids)->delete();

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

        $data = TransportType::filterSource()->select();

        return Datatables::of($data)
            ->edit_column('name', function($row) {
                return view('admin.shipments.transport_types.datatables.name', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.shipments.transport_types.datatables.actions', compact('row'))->render();
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

        $items = TransportType::orderBy('sort')->get(['id', 'name']);

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
    public function sortUpdate(Request $request) {

        TransportType::flushCache(TransportType::CACHE_TAG);

        try {
            TransportType::setNewOrder($request->ids);
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
