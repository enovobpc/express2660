<?php

namespace App\Http\Controllers\Admin\Logistic;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Logistic\Warehouse;

class WarehousesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'logistic_warehouses';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',logistic_warehouses']);
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
    public function create() {

        $action = 'Novo armazém';
        
        $warehouse = new Warehouse();
                
        $formOptions = array('route' => array('admin.logistic.warehouses.store'), 'method' => 'POST');
        
        return view('admin.logistic.locations.edit_warehouse', compact('warehouse', 'action', 'formOptions'))->render();
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

        $action = 'Editar armazém';

        $warehouse = Warehouse::filterSource()
                            ->findOrfail($id);

        $formOptions = array('route' => array('admin.logistic.warehouses.update', $warehouse->id), 'method' => 'PUT');

        return view('admin.logistic.locations.edit_warehouse', compact('warehouse', 'action', 'formOptions'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Warehouse::flushCache(Warehouse::CACHE_TAG);

        $input = $request->all();

        $warehouse = Warehouse::filterSource()->findOrNew($id);

        if ($warehouse->validate($input)) {
            $warehouse->fill($input);
            $warehouse->source = config('app.source');
            $warehouse->save();

            return Redirect::back()->with('success', 'Armazém gravado com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $warehouse->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Warehouse::flushCache(Warehouse::CACHE_TAG);

        $result = Warehouse::filterSource()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o armazém.');
        }

        return Redirect::back()->with('success', 'Armazém removido com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Warehouse::flushCache(Warehouse::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = Warehouse::filterSource()
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
    public function datatable() {

        $data = Warehouse::filterSource()->select();

        return Datatables::of($data)
            ->edit_column('name', function($row) {
                return view('admin.logistic.locations.datatables.warehouses.name', compact('row'))->render();
            })
            ->edit_column('email', function($row) {
                return view('admin.logistic.locations.datatables.warehouses.email', compact('row'))->render();
            })
            ->edit_column('address', function($row) {
                return view('admin.logistic.locations.datatables.warehouses.address', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.logistic.locations.datatables.warehouses.actions', compact('row'))->render();
            })
            ->make(true);
    }

}
