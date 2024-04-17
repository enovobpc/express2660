<?php

namespace App\Http\Controllers\Admin\AirWaybills;

use App\Models\AirWaybill\type;
use App\Models\AirWaybill\GoodType;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Croppa;

class GoodsTypesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'air-waybills-goods-types';

    private $colors = [
        ''  => '',
        '#ffef7a' => 'Amarelo',
        '#F8B514' => 'Amarelo Escuro',
        '#F66013' => 'Laranja',
        '#f42434' => 'Vermelho',
        '#8CC63F' => 'Verde',
        '#57c1f2' => 'Azul Claro',
        '#27A9E1' => 'Azul Claro',
        '#337ab7' => 'Azul',
        '#1F2457' => 'Azul Escuro',
        '#622599' => 'Roxo',

        '#777777' => 'Cinza Escuro',
        //green
        '#1abc9c' => '#1abc9c',
        '#16a085' => '#16a085',
        '#2ecc71' => '#2ecc71',
        '#48AD01' => '#48AD01',

        //blue
        '#3498db' => '#3498db',
        '#2980b9' => '#2980b9',

        //purple
        '#9b59b6' => '#9b59b6',
        '#8e44ad' => '#8e44ad',

        //orange
        '#FEC606' => '#FEC606',
        '#f39c12' => '#f39c12',
        '#e67e22' => '#e67e22',
        '#d35400' => '#d35400',

        //red
        '#e74c3c' => '#e74c3c',
        '#c0392b' => '#c0392b',
        '#E3000E' => '#E3000E',

        //gray
        '#ecf0f1' => '#ecf0f1',
        '#bdc3c7' => '#bdc3c7',
        '#95a5a6' => '#95a5a6',
        '#7f8c8d' => '#7f8c8d',

        '#34495e' => '#34495e',
        '#2c3e50' => '#2c3e50',

    ];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',air-waybills-goods-types']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.awb.air_waybills_goods_types.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Tipo de Carga';
        
        $type = new GoodType();
                
        $formOptions = array('route' => array('admin.air-waybills.goods-types.store'), 'method' => 'POST');

        $colors = $this->colors;

        return view('admin.awb.air_waybills_goods_types.edit', compact('type', 'action', 'formOptions', 'colors'))->render();
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
//        
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $action = 'Editar Tipo de Carga';
        
        $type = GoodType::filterSource()->findOrfail($id);

        $formOptions = array('route' => array('admin.air-waybills.goods-types.update', $type->id), 'method' => 'PUT');

        $colors = $this->colors;

        return view('admin.awb.air_waybills_goods_types.edit', compact('type', 'action', 'formOptions', 'colors'))->render();
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
        
        $type = GoodType::filterSource()->findOrNew($id);

        if ($type->validate($input)) {
            $type->fill($input);
            $type->source = config('app.source');
            $type->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $type->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = GoodType::filterSource()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o tipo de carga.');
        }

        return Redirect::route('admin.air-waybills.goods-types.index')->with('success', 'Tipo de carga removido com sucesso.');
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
        
        $result = GoodType::filterSource()
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

        $data = GoodType::filterSource()->select();

        return Datatables::of($data)
                ->edit_column('color', function($row) {
                    return view('admin.awb.air_waybills_goods_types.datatables.color', compact('row'))->render();
                })
                ->edit_column('name', function($row) {
                    return view('admin.awb.air_waybills_goods_types.datatables.name', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.awb.air_waybills_goods_types.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }

}
