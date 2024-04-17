<?php

namespace App\Http\Controllers\Admin\AirWaybills;

use App\Models\AirWaybill\Agent;
use App\Models\AirWaybill\Expense;
use App\Models\AirWaybill\GoodType;
use App\Models\AirWaybill\IataAirport;
use App\Models\AirWaybill\Model;
use App\Models\AirWaybill\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Croppa;

class ModelsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'air-waybills-models';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',air-waybills-models']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $providers = Provider::filterSource()->pluck('name', 'id')->toArray();

        $agents = Agent::filterSource()->pluck('name', 'id')->toArray();

        $goodsTypes = GoodType::filterSource()->pluck('name', 'id')->toArray();

        return $this->setContent('admin.awb.air_waybills_models.index', compact('providers', 'agents', 'goodsTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Modelo de Preenchimento';
        
        $model = new Model();
                
        $formOptions = array('route' => array('admin.air-waybills.models.store'), 'method' => 'POST', 'class' => 'form-horizontal');

        $airports = IataAirport::pluck('airport', 'code')->toArray();

        $providers = Provider::filterSource()->pluck('name', 'id')->toArray();

        $agents = Agent::filterSource()->pluck('name', 'id')->toArray();

        $expenses = Expense::filterSource()->pluck('name', 'id')->toArray();

        $scaleAirports = ['' => ''];

        $goodsTypes = GoodType::filterSource()->pluck('name', 'id')->toArray();

        return view('admin.awb.air_waybills_models.edit', compact('model', 'action', 'formOptions', 'airports', 'providers', 'agents', 'expenses', 'scaleAirports', 'goodsTypes'))->render();
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
        
        $action = 'Editar Modelo de Preenchimento';
        
        $model = Model::filterSource()->findOrfail($id);

        $formOptions = array('route' => array('admin.air-waybills.models.update', $model->id), 'method' => 'PUT', 'class' => 'form-horizontal');

        $airports = IataAirport::pluck('airport', 'code')->toArray();

        $providers = Provider::filterSource()->pluck('name', 'id')->toArray();

        $agents = Agent::filterSource()->pluck('name', 'id')->toArray();

        $expenses = Expense::filterSource()->pluck('name', 'id')->toArray();

        $modelExpenses = empty($model->expenses) ? [] : $model->expenses;
        $model->expenses = array_merge($model->expenses, $model->other_expenses);

        $scaleIds = [];
        if(!empty($model->flight_scales)) {
            foreach ($model->flight_scales as $flyscale) {
                $scaleIds[] = $flyscale->airport;
            }
        }

        $scaleAirports = IataAirport::whereIn('code', $scaleIds)->pluck('airport', 'code')->toArray();

        $goodsTypes = GoodType::filterSource()->pluck('name', 'id')->toArray();

        return view('admin.awb.air_waybills_models.edit', compact('model', 'action', 'formOptions', 'airports', 'providers', 'agents', 'expenses', 'scaleAirports', 'goodsTypes'))->render();
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

        $flightScales = [];
        $i = 0;
        foreach ($input['flight_scales'] as $scale) {
            if(!empty($scale['airport'])) {
                $flightScales[$i] = [
                    'airport'  => $scale['airport'],
                    'provider' => $scale['provider']
                ];

                $i++;
            }
        }

        $expenses = $otherExpenses = [];
        $otherExpensesList = Expense::filterSource()->whereType('other')->pluck('id')->toArray();
        $i = $j = 0;
        foreach ($input['expenses'] as $expense) {
            if(!empty($expense['expense'])) {

                $arr = [
                    'expense' => $expense['expense'],
                    'price'   => $expense['price']
                ];

                if(in_array($expense['expense'], $otherExpensesList)) {
                    $otherExpenses[$j] = $arr;
                    $j++;
                } else {
                    $expenses[$i] = $arr;
                    $i++;
                }
            }
        }

        $model = Model::filterSource()->findOrNew($id);

        if ($model->validate($input)) {
            $model->fill($input);
            $model->flight_scales = $flightScales;
            $model->expenses = $expenses;
            $model->other_expenses = $otherExpenses;
            $model->source = config('app.source');
            $model->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $model->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Model::filterSource()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o fornecedor.');
        }

        return Redirect::route('admin.air-waybills.models.index')->with('success', 'Fornecedor removido com sucesso.');
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
        
        $result = Model::filterSource()
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

        $data = Model::with('goodType')
                ->filterSource()
                ->select();

        //filter provider
        $value = $request->get('provider');
        if($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter customer
        $value = $request->get('customer');
        if($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter agent
        $value = $request->get('agent');
        if($request->has('agent')) {
            $data = $data->where('agent_id', $value);
        }

        //filter goods type
        $value = $request->get('type');
        if($request->has('type')) {
            $data = $data->where('goods_type_id', $value);
        }

        //filter source airport
        $value = $request->get('source_airport');
        if($request->has('source_airport')) {
            $data = $data->where('source_airport', $value);
        }

        //filter recipient airport
        $value = $request->get('recipient_airport');
        if($request->has('recipient_airport')) {
            $data = $data->where('recipient_airport', $value);
        }

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.awb.air_waybills_models.datatables.name', compact('row'))->render();
                })
                ->edit_column('provider_id', function($row) {
                    return @$row->provider->name;
                })
                ->edit_column('goods_type', function($row) {
                    return view('admin.awb.air_waybills_models.datatables.type', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.awb.air_waybills_models.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }

}
