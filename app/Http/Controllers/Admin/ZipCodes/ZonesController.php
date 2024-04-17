<?php

namespace App\Http\Controllers\Admin\ZipCodes;

use App\Models\Service;
use App\Models\Provider;
use App\Models\ZipCode\ZipCodeZone;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class ZonesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'zip_codes';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',zip_codes']);
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
    public function create(Request $request) {
        
        $action = 'Adicionar Zona de Códigos Postais';
        
        $zipCodeZone = new ZipCodeZone();
        $zipCodeZone->type = $request->get('type', 'blocked');
        $zipCodeZone->zip_codes_str = '';

        $providers = Provider::remember(config('cache.query_ttl'))
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();
                
        $formOptions = array('route' => array('admin.zip-codes.zones.store'), 'method' => 'POST');
        
        $data = compact(
            'zipCodeZone', 
            'action', 
            'formOptions', 
            'providers', 
            'services'
        );
        
        return view('admin.zip_codes.zones.edit', $data)->render();
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
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $action = 'Editar Zona Remota';
        
        $zipCodeZone = ZipCodeZone::findOrfail($id);
        
        $zipCodeZone->zip_codes_str = !empty($zipCodeZone->zip_codes) ? implode(',', $zipCodeZone->zip_codes) : '';

        $providers = Provider::remember(config('cache.query_ttl'))
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        $formOptions = array('route' => array('admin.zip-codes.zones.update', $zipCodeZone->id), 'method' => 'PUT');

        $data = compact(
            'zipCodeZone', 
            'action', 
            'formOptions', 
            'providers', 
            'services'
        );

        return view('admin.zip_codes.zones.edit', $data)->render();
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
        $input['country']   = $request->get('zone_country');
        $input['zip_codes'] = empty($input['zip_codes']) ? null : explode(',', @$input['zip_codes']);

        $zipCodeZone = ZipCodeZone::findOrNew($id);

        if ($zipCodeZone->validate($input)) {
            $zipCodeZone->fill($input);
            $zipCodeZone->source = config('app.source');
            $zipCodeZone->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $zipCodeZone->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = ZipCodeZone::whereId($id)->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::back()->with('success', 'Registo removido com sucesso.');
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
        
        $result = ZipCodeZone::whereIn('id', $ids)->delete();
        
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

        $services = Service::pluck('name', 'id')->toArray();


        $data = ZipCodeZone::select();

        $value = $request->get('type');
        if($request->has('type')) {
            $data = $data->where('type', $value);
        }

        return Datatables::of($data)
            ->editColumn('code', function($row) {
                return view('admin.zip_codes.zones.datatables.code', compact('row'));
            })
            ->editColumn('name', function($row) {
                return view('admin.zip_codes.zones.datatables.name', compact('row'));
            })
            ->editColumn('zip_codes', function($row) {
                return view('admin.zip_codes.zones.datatables.mapping', compact('row'));
            })
            ->edit_column('country', function($row) {
                return view('admin.zip_codes.zip_codes.datatables.country', compact('row'))->render();
            })
            ->editColumn('provider', function($row) {
                return view('admin.zip_codes.agencies.datatables.provider', compact('row'));
            })
            ->editColumn('services', function($row) use($services) {
                return view('admin.zip_codes.agencies.datatables.services', compact('row', 'services'));
            })
            ->addColumn('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->addColumn('actions', function($row) {
                return view('admin.zip_codes.zones.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     * GET /admin/features/sort
     *
     * @return Response
     */
    public function sortEdit(Request $request) {

        $items = ZipCodeZone::orderBy('sort')
                    ->where('type', $request->type)
                    ->get();

        $route = route('admin.zip-codes.zones.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/features/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        $result = ZipCodeZone::setNewOrder($request->ids);

        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return response()->json($response);
    }
}
