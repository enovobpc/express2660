<?php

namespace App\Http\Controllers\Admin\SepaTransfers;

use App\Models\BankInstitution;
use App\Models\Invoice;
use App\Models\SepaTransfer\PaymentGroup;
use App\Models\SepaTransfer\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TransactionsController extends \App\Http\Controllers\Admin\Controller {

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
    public function create($groupId) {

        $action = 'Adicionar Transação';

        $paymentTransaction = new PaymentTransaction();

        $group = PaymentGroup::find($groupId);

        $banks = BankInstitution::listBanks();

        $formOptions = array('route' => array('admin.sepa-transfers.transactions.store', $groupId), 'method' => 'POST', 'class'=>'form-sepa-transaction');

        $data = compact(
            'paymentTransaction',
            'group',
            'banks',
            'action',
            'formOptions'
        );

        return view('admin.sepa_transfers.edit_transaction', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $groupId) {
        return $this->update($request, $groupId, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($groupId, $id) {
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($groupId, $id) {

        $action = 'Editar Transação';

        $paymentTransaction = PaymentTransaction::where('group_id', $groupId)
            ->findOrfail($id);

        $group = PaymentGroup::find($groupId);

        $banks = BankInstitution::listBanks();

        $formOptions = array('route' => array('admin.sepa-transfers.transactions.update', $paymentTransaction->group_id, $paymentTransaction->id), 'method' => 'PUT', 'class'=>'form-sepa-transaction');

        $data = compact(
            'paymentTransaction',
            'group',
            'banks',
            'action',
            'formOptions'
        );

        return view('admin.sepa_transfers.edit_transaction', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $groupId, $id = null) {

        $input = $request->all();

        $group = PaymentGroup::whereHas('payment', function ($q){
                $q->filterSource();
            })
            ->findOrFail($groupId);

        $payment = $group->payment;

        $paymentTransaction = PaymentTransaction::where('group_id', $groupId)->findOrNew($id);

        if ($paymentTransaction->validate($input)) {
            $paymentTransaction->fill($input);
            $paymentTransaction->group_id   = $group->id;
            $paymentTransaction->payment_id = $group->payment_id;
            $paymentTransaction->status     = $payment->type == 'dd' ? 'pending' : 'accepted';
            $paymentTransaction->save();

            $transactions = PaymentTransaction::where('group_id', $group->id)
                ->where('payment_id', $group->payment_id)
                ->get();

            $group->update([
                'transactions_count' => $transactions->count(),
                'transactions_total' => $transactions->sum('amount'),
            ]);

            $groups = PaymentGroup::where('payment_id', $payment->id)->get();

            $payment->update([
                'transactions_count' => $groups->sum('transactions_count'),
                'transactions_total' => $groups->sum('transactions_total'),
            ]);

            return response()->json([
                'result'      => true,
                'feedback'    => 'Transação adicionada com sucesso.',
                'html_groups' => view('admin.sepa_transfers.partials.groups_list', compact('payment', 'groups'))->render(),
                'html'        => view('admin.sepa_transfers.partials.transactions_list', compact('payment', 'groups', 'transactions', 'group'))->render(),
                'transactions_count' => $groups->sum('transactions_count'),
                'transactions_total' => money($groups->sum('transactions_total'), '€'),
            ]);

        }

        return Redirect::back()->withInput()->with('error', $paymentTransaction->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($groupId, $id) {


        $transaction = PaymentTransaction::where('group_id', $groupId)
            ->whereId($id)
            ->first();

        if($transaction->invoice_id) {
            Invoice::where('id', $transaction->invoice_id)->update(['sepa_payment_id' => null]);
        }

        $result = $transaction->delete();

        $result = true;
        if ($result) {

            $group = PaymentGroup::whereHas('payment', function ($q){
                    $q->filterSource();
                })
                ->findOrFail($groupId);

            $groups = PaymentGroup::where('payment_id', $group->payment_id)->get();

            $payment = $group->payment;

            $transactions = PaymentTransaction::where('group_id', $group->id)
                ->where('payment_id', $group->payment_id)
                ->get();

            $payment->update([
                'transactions_count' => $groups->sum('transactions_count'),
                'transactions_total' => $groups->sum('transactions_total'),
            ]);

            $group->update([
                'transactions_count' => $transactions->count(),
                'transactions_total' => $transactions->sum('amount'),
            ]);

            return response()->json([
                'result'      => true,
                'feedback'    => 'Transação removida com sucesso.',
                'html_groups' => view('admin.sepa_transfers.partials.groups_list', compact('payment', 'groups'))->render(),
                'html'        => view('admin.sepa_transfers.partials.transactions_list', compact('payment', 'groups', 'transactions', 'group'))->render(),
                'transactions_count' => $groups->sum('transactions_count'),
                'transactions_total' => money($groups->sum('transactions_total'), '€'),
            ]);
        }

        return response()->json([
            'result'   => false,
            'feedback' => 'Erro ao remover a transação.',
        ]);
    }
}
