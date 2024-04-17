<?php

namespace App\Http\Controllers\Admin\Equipments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Equipment\Warehouse;
use App\Models\Equipment\Location;
use App\Models\User;
use Setting, DB;

class LocationsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'equipments_locations';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',equipments_locations']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $warehouses = Warehouse::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        return $this->setContent('admin.equipments.locations.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $action = 'Nova localização';
        
        $location = new Location();

        $formOptions = array('route' => array('admin.equipments.locations.store'), 'method' => 'POST', 'class' => 'form-location');

        $warehouses = Warehouse::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();
            
        $drivers = User::filterSource()
            ->where(['active' => 1, 'login_app' => 1])
            ->pluck('name', 'id')
            ->toArray();
       
        $data = compact(
            'location',
            'action',
            'formOptions',
            'warehouses',
            'drivers'
        );

        return view('admin.equipments.locations.edit', $data)->render();
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
    public function show(Request $request, $id) {

        $location = Location::with(['equipments' => function($q) use($request) {
                if($request->has('category')) {
                    return $q->where('category_id', $request->get('category'));
                }
            }])
            ->filterSource()
            ->find($id);

        $data = compact(
            'location'
        );

        return view('admin.equipments.locations.show', $data)->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $action = 'Editar localização';

        $location = Location::filterSource()->findOrfail($id);

        $formOptions = array('route' => array('admin.equipments.locations.update', $location->id), 'method' => 'PUT', 'class' => 'form-location');

        $warehouses = Warehouse::filterSource()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

       $drivers = User::filterSource()
             ->where(['active' => 1, 'login_app' => 1])
             ->pluck('name', 'id')
             ->toArray();
           
        $data = compact(
            'location',
            'action',
            'formOptions',
            'warehouses',
            'drivers'
        );

        return view('admin.equipments.locations.edit', $data)->render();
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

        $warehouse = Warehouse::filterSource()
            ->whereId($input['warehouse_id'])
            ->firstOrFail();

        $location = Location::filterSource()
            ->findOrNew($id);

        $drivers = User::filterSource()
            ->whereId($input['operator_id'])
            ->firstOrFail();

        if ($location->validate($input)) {
            $location->fill($input);
            $location->source = config('app.source');
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

        $data = Location::with('warehouse')
                ->filterSource()
                ->select([
                    '*',
                    DB::raw('(select count(id) from equipments where location_id = equipments_locations.id and equipments.status <> "outstock") as total_equipments')
                ]);

        //filter status
        $value = $request->status;
        if($request->has('status')) {
            $data = $data->where('status', $value);
        }

        //filter warehouse
        $value = $request->warehouse;
        if($request->has('warehouse')) {
            $data = $data->where('warehouse_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('warehouse_id', function($row) {
                return view('admin.equipments.locations.datatables.locations.name', compact('row'))->render();
            })
            ->edit_column('code', function($row) {
                return view('admin.equipments.locations.datatables.locations.code', compact('row'))->render();
            })
            ->edit_column('operator_id', function($row) {
                return view('admin.equipments.locations.datatables.locations.operator', compact('row'))->render();
            })
            ->edit_column('total_equipments', function($row) {
                return $row->total_equipments;
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.equipments.locations.datatables.locations.actions', compact('row'))->render();
            })
            ->make(true);
    }

}
