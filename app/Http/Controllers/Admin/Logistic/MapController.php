<?php

namespace App\Http\Controllers\Admin\Logistic;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Logistic\Warehouse;
use App\Models\Logistic\LocationType;
use App\Models\Logistic\Location;
use Mpdf\Mpdf;
use Setting;

class MapController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'logistic_map';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',logistic_map']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.logistic.map.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
/*
        $action = 'Nova localização';

        $location = new Location();

        $formOptions = array('route' => array('admin.logistic.locations.store'), 'method' => 'POST', 'class' => 'form-location');

        $types = $this->listTypes(LocationType::remember(config('cache.query_ttl'))
            ->cacheTags(LocationType::CACHE_TAG)
            ->filterSource()
            ->get());

        $warehouses = Warehouse::remember(config('cache.query_ttl'))
            ->cacheTags(Warehouse::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'location',
            'action',
            'formOptions',
            'warehouses',
            'types'
        );

        return view('admin.logistic.locations.edit', $data)->render();*/
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

        $action = 'Editar localização';

        $location = Location::filterSource()->findOrfail($id);

        $formOptions = array('route' => array('admin.logistic.locations.update', $location->id), 'method' => 'PUT', 'class' => 'form-location');

        $types = $this->listTypes(LocationType::remember(config('cache.query_ttl'))
            ->cacheTags(LocationType::CACHE_TAG)
            ->filterSource()
            ->get());

        $warehouses = Warehouse::remember(config('cache.query_ttl'))
            ->cacheTags(Warehouse::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'location',
            'action',
            'formOptions',
            'warehouses',
            'types'
        );

        return view('admin.logistic.locations.edit', $data)->render();
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

        $warehouse = Warehouse::filterSource()->whereId($input['warehouse_id'])->firstOrFail();

        $location = Location::filterSource()->findOrNew($id);

        $barcode = $warehouse->code.str_replace('-','', $input['code']);

        if ($location->validate($input)) {
            $location->fill($input);
            $location->barcode = $barcode;
            $location->save();

            return Redirect::back()->with('success', 'Localização gravada com sucesso.');
        }

        return Redirect::back()->with('error', $location->errors()->first());
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
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a localização.');
        }

        return Redirect::back()->with('success', 'Localização removida com sucesso.');
    }

    /**
     * Save object info
     *
     * @param Request $request
     */
    public function saveObject(Request $request) {


    }
}
