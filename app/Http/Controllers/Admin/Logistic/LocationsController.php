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

class LocationsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'logistic_locations';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',logistic_locations']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $types = LocationType::remember(config('cache.query_ttl'))
            ->cacheTags(LocationType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $warehouses = Warehouse::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        return $this->setContent('admin.logistic.locations.index', compact('warehouses', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
            ->orderBy('code', 'asc')
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
    public function edit($id)
    {

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
            ->orderBy('code', 'asc')
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
    public function update(Request $request, $id)
    {

        Location::flushCache(Location::CACHE_TAG);

        $input = $request->all();

        $warehouse = Warehouse::filterSource()->whereId($input['warehouse_id'])->firstOrFail();

        $location = Location::filterSource()->findOrNew($id);

        $barcode = $warehouse->code . str_replace('-', '', $input['code']);

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
    public function destroy($id)
    {

        Location::flushCache(Location::CACHE_TAG);

        $result = Location::filterSource()
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a localização.');
        }

        return Redirect::back()->with('success', 'Localização removida com sucesso.');
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
        Location::flushCache(Location::CACHE_TAG);

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
    public function datatable(Request $request)
    {

        $data = Location::with('warehouse', 'type')
            ->filterSource()
            ->select();

        //filter status
        $value = $request->status;
        if ($request->has('status')) {
            $data = $data->where('status', $value);
        }

        //filter warehouse
        $value = $request->warehouse;
        if ($request->has('warehouse')) {
            $data = $data->where('warehouse_id', $value);
        }

        //filter type
        $value = $request->type;
        if ($request->has('type')) {
            $data = $data->whereIn('type_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('warehouse.name', function ($row) {
                return view('admin.logistic.locations.datatables.locations.name', compact('row'))->render();
            })
            ->edit_column('code', function ($row) {
                return view('admin.logistic.locations.datatables.locations.code', compact('row'))->render();
            })
            ->edit_column('type_id', function ($row) {
                return view('admin.logistic.locations.datatables.locations.type', compact('row'))->render();
            })
            ->add_column('dimensions', function ($row) {
                return view('admin.logistic.locations.datatables.locations.dimensions', compact('row'))->render();
            })
            ->edit_column('status', function ($row) {
                return view('admin.logistic.locations.datatables.locations.status', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.logistic.locations.datatables.locations.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Print product labels
     *
     * @return \Illuminate\Http\Response
     */
    public function printLabels(Request $request, $locationId)
    {
        return Location::printLabels([$locationId]);
    }

    /**
     * Print locations labels mass
     *
     * @return \Illuminate\Http\Response
     */
    public function massPrintLabels(Request $request)
    {
        $request = $request->all();

        return Location::printLabels($request['id']);
    }



    /**
     * Return list of types with data attributes
     *
     * @param type $allServices
     * @return type
     */
    public function listTypes($allTypes)
    {

        $items[] = ['value' => '', 'display' => ''];
        foreach ($allTypes as $item) {

            $items[] = [
                'value'      => $item->id,
                'display'    => $item->name,
                'data-image' => asset($item->filepath)
            ];
        }
        return $items;
    }

    /**
     * Return list of types with data attributes
     *
     * @param type $allServices
     * @return type
     */
    public function print(Request $request)
    {
        return Location::printLocations($request->id);
    }
}
