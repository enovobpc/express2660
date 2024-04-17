<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\Provider;
use App\Models\ShipmentExpense;
use App\Models\ShippingExpense;
use Html, Response, Cache, Setting, Date, Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use App\Models\Shipment;

class ShipmentsExpensesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'shipments';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',shipments']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $shipmentId = null) {

        $ids = $request->get('id');

        if(empty($ids)) {
            $action = 'Adicionar Encargos ao Envio';

            $shipment = Shipment::findOrFail($shipmentId);
            
            $shipmentExpense = new ShipmentExpense();
            $shipmentExpense->shipment_id = $shipmentId;

            $formOptions = array('route' => array('admin.shipments.expenses.store', $shipmentId), 'method' => 'POST', 'class' => 'form-expenses');

        } else {
            $action = 'Adicionar Encargo em Massa';

            $shipmentExpense = new ShipmentExpense();
            $shipmentExpense->ids = implode(',', $ids);

            $shipment = new Shipment();

            $formOptions = array('route' => array('admin.shipments.selected.assign-expenses.store'), 'method' => 'POST', 'class' => 'form-expenses');
        }

        $expenses = ShippingExpense::filterSource()
            ->pluck('name', 'id')
            ->toArray();


        return view('admin.shipments.shipments.edit.expense', compact('shipmentExpense', 'action', 'formOptions', 'expenses', 'shipment'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $shipmentId) {
        return $this->update($request, $shipmentId, null);
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
    public function edit($shipmentId, $id) {

        $action = 'Editar Encargos ao Envio';

        $shipment = Shipment::findOrFail($shipmentId);

        $shipmentExpense = ShipmentExpense::where('shipment_id', $shipmentId)
                                     ->findOrFail($id);

        $formOptions = array('route' => array('admin.shipments.expenses.update', $shipmentId, $id), 'method' => 'PUT', 'class' => 'form-expenses');

        $expenses = ShippingExpense::filterSource()
                                ->pluck('name', 'id')
                                ->toArray();

        return view('admin.shipments.shipments.edit.expense', compact('shipmentExpense', 'action', 'formOptions', 'expenses', 'shipment'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $shipmentId = null, $id = null) {

        $input  = $request->all();
        $errors = false;

        if($shipmentId) {
            $shipmentIds = [$shipmentId];
        } else {
            $shipmentIds = explode(',', $request->get('ids'));
        }

        foreach ($shipmentIds as $shipmentId) {

            if(!empty($input['expense_id'])) {
                foreach ($input['expense_id'] as $key => $expenseId) {

                    if(!empty(@$input['expense_id'][$key])) {

                        $data = [];
                        $data['id'] = $id; //assigned expense id. Check if exists
                        $data['expense_id'] = @$input['expense_id'][$key];
                        $data['cost_price'] = @$input['cost'][$key] ? @$input['cost'][$key] : 0;
                        $data['date']       = @$input['date'][$key];
                        $data['price']      = @$input['price'][$key];
                        $data['qty']        = @$input['qty'][$key] ? @$input['qty'][$key] : 1;

                        if(empty($input['unity'][$key]) || $input['unity'][$key] == 'euro') {
                            $data['subtotal'] = @$input['price'][$key] * @$input['qty'][$key];
                        } else {
                            $data['subtotal'] = (@$input['price'][$key] / 100) * @$input['shipment_price'];
                        }

                        $result = Shipment::storeExpenseByShipmentId($shipmentId, $data);

                        if($result) {
                            $shipment = Shipment::whereId($shipmentId)->first();
                        } else {
                            $errors = true;
                        }

                        /*
                        //CODIGO ANTIGO ANTES DA FUNÇÃO
                        $shipmentExpense = ShipmentExpense::firstOrNew([
                            'shipment_id' => $shipmentId,
                            'id'          => $expenseId
                        ]);

                        if($shipmentExpense->validate($input)) {
                            $shipmentExpense->fill($data);
                            $shipmentExpense->save();

                            //update shipment total_expenses field
                            ShipmentExpense::updateShipmentTotal($shipmentId);
                        } else {
                            $errors = true;
                        }*/
                    }
                }
            }
        }

        if($errors) {
            return [
                'result'   => false,
                'feedback' => 'Não foi possível gravar todos os encargos.',
            ];
        }

        return [
            'result'   => true,
            'feedback' => 'Encargo adicionado com sucesso.',
            'html'     => view('admin.shipments.shipments.partials.show.expenses', compact('shipment'))->render()
        ];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shipmentId, $id) {

        $result = ShipmentExpense::where('id', $id)
                                ->where('shipment_id', $shipmentId)
                                ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o encargo.');
        }

        //update shipment total_expenses field
        ShipmentExpense::updateShipmentTotal($shipmentId);

        return Redirect::back()->with('success', 'Encargo removido com sucesso.');
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

        $shipmentExpenses = ShipmentExpense::with('shipment')
                                ->whereIn('id', $ids)
                                ->get();

        foreach ($shipmentExpenses as $expense) {
            //update all shipment expenses
            $expense->delete();
            $total = ShipmentExpense::updateShipmentTotal($expense->shipment_id);

            $expense->shipment->total_expenses = $total['total'];
            $expense->shipment->total_expenses_cost = $total['cost'];
            $expense->shipment->save();
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Get price for expense advanced
     *
     * @return \Illuminate\Http\Response
     */
    public function getPrice(Request $request, $shipmentId = null) {

        $input = $request->all();

        $price     = empty(@$input['expense_price']) ? null : @$input['expense_price'];
        $costPrice = empty(@$input['expense_cost_price']) ? null : $input['expense_cost_price'];
        $qty       = $request->get('expense_qty');
        $qty       = $qty ? $qty : 1;

        $expense = ShippingExpense::filterSource()
            ->findOrFail($input['expense_id']);

        $shipment = new Shipment();
        $shipment->fill($input);

        $result = $expense->calcExpensePrice($shipment, $qty, $price, $costPrice);

        return Response::json($result);
    }
}
