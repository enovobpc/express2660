<?php

namespace App\Http\Controllers\Admin\FilesImporter;

use App\Models\CustomerType;
use Html, Cache, Response;
use App\Models\Provider;
use App\Models\Service;
use App\Models\ImporterModel;
use App\Models\Agency;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Http\Request;


class ModelsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'importer';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',importer']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->setContent('admin.importer.models.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $type = $request->get('type', 'shipments');

        $importerModel = new ImporterModel;
        $importerModel->type = $type;

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $customerTypes = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $providersGasStations = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterSource()
            ->categoryGasStation()
            ->pluck('name', 'id')
            ->toArray();

        $fields = $this->getFields($importerModel->type);

        $action = 'Adicionar Modelo de Importação';
        $formOptions = array('route' => array('admin.importer.models.store'), 'method' => 'POST');

        $data = compact(
            'importerModel',
            'action',
            'formOptions',
            'agencies',
            'providers',
            'services',
            'customerTypes',
            'providersGasStations',
            'fields'
        );

        return view('admin.files_importer.models.edit', $data)->render();
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
    public function edit($id)
    {

        $importerModel = ImporterModel::filterSource()
            ->findOrfail($id);

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $customerTypes = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $providersGasStations = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterSource()
            ->categoryGasStation()
            ->pluck('name', 'id')
            ->toArray();

        $fields = $this->getFields($importerModel->type);

        $action = 'Editar Modelo de Importação';

        $formOptions = array('route' => array('admin.importer.models.update', $importerModel->id), 'method' => 'PUT');

        $data = compact(
            'importerModel',
            'action',
            'formOptions',
            'colors',
            'agencies',
            'providers',
            'services',
            'customerTypes',
            'providersGasStations',
            'fields'
        );

        return view('admin.files_importer.models.edit', $data)->render();
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

        ImporterModel::flushCache(ImporterModel::CACHE_TAG);

        $input = $request->all();
        $input['available_customers'] = $request->get('available_customers', false);

        $importerModel = ImporterModel::findOrNew($id);

        if ($importerModel->validate($input)) {
            $importerModel->fill($input);
            $importerModel->source = config('app.source');
            $importerModel->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $importerModel->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        ImporterModel::flushCache(ImporterModel::CACHE_TAG);

        $result = ImporterModel::filterSource()->whereId($id)->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o estado de envio.');
        }

        return Redirect::route('admin.status.index')->with('success', 'Estado de envio removido com sucesso.');
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

        ImporterModel::flushCache(ImporterModel::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = ImporterModel::filterSource()->whereIn('id', $ids)->delete();

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

        $data = ImporterModel::filterSource()->select();

        $value = $request->get('type');
        if ($request->has('type')) {
            $data = $data->where('type', $value);
        }

        return Datatables::of($data)
            ->edit_column('name', function ($row) {
                return view('admin.files_importer.models.datatables.name', compact('row'))->render();
            })
            ->edit_column('type', function ($row) {
                return trans('admin/importer.import_types.' . $row->type);
            })
            ->edit_column('available_customers', function ($row) {
                return view('admin.files_importer.models.datatables.available_customers', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.files_importer.models.datatables.actions', compact('row'))->render();
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

        $items = ImporterModel::filterSource()->orderBy('sort')
            ->get(['id', 'name']);

        $route = route('admin.shipping-status.sort.update');

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

        ImporterModel::flushCache(ImporterModel::CACHE_TAG);

        try {
            ImporterModel::setNewOrder($request->ids);
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
     * Return
     * @return array
     */
    public function getFields($modelType)
    {

        if (in_array($modelType, ['shipments_fast'])) {
            $modelType = 'shipments';
        }

        $allFields = trans('admin/importer.' . $modelType);
        // dd($allFields);
        //dd($allFields);
        $fields = [];
        foreach ($allFields as $key => $value) {
            if ($value['mapping']) {
                $fields[$key] = $value;
            }
        }

        return $fields;
    }
}
