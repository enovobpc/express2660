<?php

namespace App\Http\Controllers\Account\Logistic;

use App\Models\Logistic\ReceptionOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;
use App\Models\Logistic\ReceptionOrderStatus;
use Illuminate\Support\Facades\Response;

class ReceptionOrdersController extends \App\Http\Controllers\Controller
{
    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.account';

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'logistic-reception-orders';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Customer billing index controller
     *
     * @return type
     */
    public function index(Request $request) {

        //$customer = Auth::guard('customer')->user();

        return $this->setContent('account.logistic.reception_orders.index');
    }

    /**
     * Show the form for consult the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, ReceptionOrder $receptionOrder) {
        $data = compact(
            'receptionOrder'
        );

        return view('account.logistic.reception_orders.show', $data)->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $action = 'Criar Receção de Artigos';
        $receptionOrder = new ReceptionOrder();
        $formOptions = [
            'route'         => ['account.logistic.reception-orders.store'],
            'method'        => 'POST',
            'class'         => 'form-reception-order',
            'autocomplete'  => 'nofill'
        ];

        $data = compact(
            'receptionOrder',
            'action',
            'formOptions'
        );

        return view('account.logistic.reception_orders.edit', $data)->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, ReceptionOrder $receptionOrder) {
        $receptionOrder->load(['lines']);

        $action = 'Editar Receção de Artigos';
        $formOptions = [
            'route'         => ['account.logistic.reception-orders.update', $receptionOrder->id],
            'method'        => 'POST',
            'class'         => 'form-reception-order',
            'autocomplete'  => 'nofill'
        ];

        $data = compact(
            'receptionOrder',
            'action',
            'formOptions'
        );

        return view('account.logistic.reception_orders.edit', $data)->render();
    }

    /**
     * Store the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return $this->update($request, new ReceptionOrder());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReceptionOrder $receptionOrder) {
        if ($receptionOrder->exists && $receptionOrder->status_id != ReceptionOrderStatus::STATUS_REQUESTED) {
            return Response::json([
                'result' => false,
                'feedback' => 'O estado dessa ordem já não permite a sua edição!'
            ]);
        }

        $customer = Auth::guard('customer')->user();

        $input                = $request->all();
        $input['customer_id'] = $customer->id;
        $input['status_id']   = ReceptionOrderStatus::STATUS_REQUESTED;

        if ($receptionOrder->validate($input)) {
            $receptionOrder->fill($input);
            $receptionOrder->source = config('app.source');

            $foundAnyLine = false;
            for ($i = 0; $i < count($input['product_id']); $i++) {
                $productId = $input['product_id'][$i] ?? null;
                $qty       = $input['qty'][$i] ?? null;
                if (!$productId || !$qty) {
                    continue;
                }

                $foundAnyLine = true;
                if (!$receptionOrder->exists) {
                    $receptionOrder->save();
                } else {
                    $receptionOrder->lines()->delete();
                }

                $receptionOrder->lines()->create([
                    'product_id' => $productId,
                    'qty'        => $qty
                ]);
            }

            if (!$foundAnyLine) {
                return Response::json([
                    'result' => false,
                    'feedback' => 'Nenhum produto inserido!'
                ]);
            }

            $receptionOrder->setCode();
            $receptionOrder->total_items = @$receptionOrder->lines->count();
            $receptionOrder->total_qty   = @$receptionOrder->lines->sum('qty');
            $receptionOrder->save();

            return Response::json([
                'result' => true,
                'feedback' => 'Dados gravados com sucesso.'
            ]);
        }

        return Response::json([
            'result' => false,
            'feedback' => 'Não foi possível guardar a ordem de receção!'
        ]);
    }

    public function destroy(ReceptionOrder $receptionOrder) {
        if ($receptionOrder->status_id != ReceptionOrderStatus::STATUS_REQUESTED) {
            return Redirect::back()->with('success', 'O estado dessa ordem já não permite a sua eliminação!');
        }

        $receptionOrder->lines()->delete();
        $receptionOrder->delete();
        return Redirect::back()->with('success', 'Ordem de receção eliminada com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request) {

        $customer = Auth::guard('customer')->user();

        $data = ReceptionOrder::filterSource()
            ->where(function ($q) use($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->select();

        return Datatables::of($data)
            ->edit_column('code', function ($row) {
                return view('account.logistic.reception_orders.datatables.code', compact('row'))->render();
            })
            ->edit_column('status_id', function($row) {
                return view('account.logistic.reception_orders.datatables.status', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                $statusRequested = ReceptionOrderStatus::STATUS_REQUESTED;
                return view('account.logistic.reception_orders.datatables.actions', compact('row', 'statusRequested'))->render();
            })
            ->make(true);
    }
}