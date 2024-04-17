<?php

namespace App\Http\Controllers\Admin\SepaTransfers;

use App\Models\Bank;
use App\Models\Invoice;
use App\Models\SepaTransfer\Payment;
use App\Models\SepaTransfer\PaymentGroup;
use App\Models\SepaTransfer\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Html, Auth, Response, File, Setting;

class GroupsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'sepa_transfers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',sepa_transfers']);
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
    public function create($paymentId) {

        $action = 'Adicionar Lote de Transferências';

        $payment = Payment::filterSource()->findOrFail($paymentId);
        $payment->edit_mode = true;

        $paymentGroup = new PaymentGroup();

        $banks = Bank::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $formOptions = array('route' => array('admin.sepa-transfers.groups.store', $paymentId), 'method' => 'POST', 'class'=>'form-sepa-group');

        $data = compact(
            'paymentGroup',
            'payment',
            'banks',
            'action',
            'formOptions'
        );

        return view('admin.sepa_transfers.edit_group', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $paymentId) {
        return $this->update($request, $paymentId, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($paymentId, $id) {

        $payment = Payment::filterSource()->findOrFail($paymentId);
        $payment->edit_mode = true;

        $group = PaymentGroup::where('payment_id', $paymentId)->findOrFail($id);

        $transactions = PaymentTransaction::where('payment_id', $paymentId)
            ->where('group_id', $id)
            ->get();

        return response()->json([
            'result'   => true,
            'html'     => view('admin.sepa_transfers.partials.transactions_list', compact('transactions', 'group', 'payment'))->render()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($paymentId, $id) {

        $action = 'Editar Lote de Transferências';

        $paymentGroup = PaymentGroup::where('payment_id', $paymentId)->findOrfail($id);
        $payment = $paymentGroup->payment;

        $banks = Bank::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $formOptions = array('route' => array('admin.sepa-transfers.groups.update', $paymentGroup->payment_id, $paymentGroup->id), 'method' => 'PUT', 'class'=>'form-sepa-group');

        $data = compact(
            'paymentGroup',
            'payment',
            'banks',
            'action',
            'formOptions'
        );

        return view('admin.sepa_transfers.edit_group', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $paymentId, $id = null) {

        $input = $request->all();

        $payment = Payment::filterSource()->findOrFail($paymentId);
        $payment->edit_mode = true;

        $paymentGroup = PaymentGroup::findOrNew($id);

        $transactions = PaymentTransaction::where('payment_id', $payment->id)
            ->where('group_id', $paymentGroup->id)
            ->get();

        $bank = Bank::filterSource()->findOrFail($input['bank_id']);

        if(!$paymentGroup->exists) {
            $maxPaymentGroup = PaymentGroup::where('payment_id', $paymentId)->max('code');
            $code = @$payment->code;
            $codeId = (int)substr($maxPaymentGroup, 9) + 1;
            $input['code']        = $code.'-'.str_pad($codeId, 2, '0', STR_PAD_LEFT);
        }

        $input['company']     = $bank->titular_name;
        $input['bank_name']   = $bank->bank_name;
        $input['bank_iban']   = $bank->bank_iban;
        $input['bank_swift']  = $bank->bank_swift;
        $input['credor_code'] = $payment->type == 'dd' ? $bank->credor_code : $bank->titular_vat;
        $input['transactions_count']  = $transactions->count();
        $input['transactions_total']  = $transactions->sum('amount');

        if ($paymentGroup->validate($input)) {
            $paymentGroup->fill($input);
            $paymentGroup->payment_id = $payment->id;
            $paymentGroup->save();

            $groups = PaymentGroup::where('payment_id', $payment->id)->get();

            $group        = $paymentGroup;
            $transactions = $paymentGroup->transactions;

            return response()->json([
                'result'   => true,
                'feedback' => 'Lote adicionado com sucesso.',
                'html'     => view('admin.sepa_transfers.partials.groups_list', compact('payment', 'groups'))->render(),
                'html_transactions' => view('admin.sepa_transfers.partials.transactions_list', compact('transactions', 'group', 'payment'))->render()
            ]);

        }

        return Redirect::back()->withInput()->with('error', $paymentGroup->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($paymentId, $id) {

        $result = PaymentGroup::where('payment_id', $paymentId)
            ->whereId($id)
            ->delete();

        if ($result) {

            $invoiceIds = PaymentTransaction::where('payment_id', $paymentId)
                ->where('group_id', $id)
                ->pluck('invoice_id')
                ->toArray();

            PaymentTransaction::where('payment_id', $paymentId)
                ->where('group_id', $id)
                ->delete();

            Invoice::whereIn('id', $invoiceIds)->update(['sepa_payment_id' => null]);

            $groups = PaymentGroup::where('payment_id', $paymentId)->get();

            $payment = Payment::filterSource()->find($paymentId);
            $payment->edit_mode = true;

            Payment::whereId($paymentId)->update([
                'transactions_count' => $groups->sum('transactions_count'),
                'transactions_total' => $groups->sum('transactions_total'),
            ]);

            $group = new PaymentGroup();

            $transactions = [];

            return response()->json([
                'result'   => true,
                'feedback' => 'Lote removido com sucesso.',
                'html_groups' => view('admin.sepa_transfers.partials.groups_list', compact('payment', 'groups'))->render(),
                'html' => view('admin.sepa_transfers.partials.transactions_list', compact('payment', 'groups', 'transactions', 'group'))->render(),
                'transactions_count' => $groups->sum('transactions_count'),
                'transactions_total' => money($groups->sum('transactions_total'), '€'),
            ]);

        }

        return response()->json([
            'result'   => false,
            'feedback' => 'Erro ao remover o lote.'
        ]);
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

        $result = PaymentGroup::filterSource()
            ->whereIn('id', $ids)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

}
