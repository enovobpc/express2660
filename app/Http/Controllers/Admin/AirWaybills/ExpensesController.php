<?php

namespace App\Http\Controllers\Admin\AirWaybills;

use App\Models\AirWaybill\Expense;
use App\Models\AirWaybill\IataAirport;
use App\Models\AirWaybill\Provider;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Croppa;

class ExpensesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'air-waybills-expenses';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',air-waybills-expenses']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.awb.air_waybills_expenses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Encargo';
        
        $expense = new Expense();

        $formOptions = array('route' => array('admin.air-waybills.expenses.store'), 'method' => 'POST');

        $providers = Provider::filterSource()->pluck('name', 'id')->toArray();

        $airports = [];

        return view('admin.awb.air_waybills_expenses.edit', compact('expense', 'action', 'formOptions', 'providers', 'airports'))->render();
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
        
        $action = 'Editar Encargo';
        
        $expense = Expense::filterSource()->findOrfail($id);

        $formOptions = array('route' => array('admin.air-waybills.expenses.update', $expense->id), 'method' => 'PUT');

        $providers = Provider::filterSource()->pluck('name', 'id')->toArray();

        $airports = [];
        if(!empty($expense->prices)) {
            $codes = [];
            foreach ($expense->prices as $price) {
                $codes[] = $price->airport;
            }
            $airports = IataAirport::whereIn('code', $codes)->pluck('airport', 'code')->toArray();
        }

        return view('admin.awb.air_waybills_expenses.edit', compact('expense', 'action', 'formOptions', 'providers', 'airports'))->render();
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

        $prices = [];
        $i = 0;
        foreach ($input['prices'] as $price) {
            if(!empty($price['price']) || !empty($price['price_min'])) {
                $prices[$i] = [
                    'airport'   => $price['airport'],
                    'provider'  => $price['provider'],
                    'unity'     => $price['unity'],
                    'price'     => (float) $price['price'],
                    'price_min' => (float) $price['price_min'],
                ];

                $i++;
            }
        }

        $expense = Expense::filterSource()->findOrNew($id);

        if ($expense->validate($input)) {
            $expense->fill($input);
            $expense->prices = $prices;
            $expense->source = config('app.source');
            $expense->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $expense->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Expense::filterSource()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o encargo.');
        }

        return Redirect::route('admin.air-waybills.expenses.index')->with('success', 'Encargo removido com sucesso.');
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
        
        $result = Expense::filterSource()
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

        $data = Expense::filterSource()->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.awb.air_waybills_expenses.datatables.name', compact('row'))->render();
                })
                ->edit_column('type', function($row) {
                    return trans('admin/air_waybills.expenses.types.' . $row->type);
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.awb.air_waybills_expenses.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }

}
