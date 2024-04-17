<?php
    $ibanTitle = 'Devedor';
    if(@$payment->type == 'trf') {
        $ibanTitle = 'Beneficiário';
    }
?>
@if(@$payment->edit_mode)
    <a href="{{ route('admin.sepa-transfers.transactions.create', @$group->id) }}"
       data-toggle="modal"
       data-target="#modal-remote"
       class="btn btn-xs btn-success btn-add-transaction pull-right m-t-0">
        <i class="fas fa-plus"></i> Adicionar
    </a>
@endif
<h4 class="text-blue bold">Transações <small>({{ count(@$transactions) }})</small></h4>
<div class="clearfix"></div>
<div class="table-transactions m-b-0"  style="border: 1px solid #ccc;
    height: 200px;
    overflow: auto;
    border-radius: 5px;">
    <table class="table table-condensed">
        <tr style="">
            <th class="bg-gray-light w-100px">Referência</th>
            <th class="bg-gray-light w-1">Montante</th>
            @if(@$payment->type == 'dd')
            <th class="bg-gray-light">Mandato</th>
<!--            <th class="bg-gray-light">Assinatura</th>-->
            @else
            <th class="bg-gray-light w-100px">Cod. Transf.</th>
            @endif
            <th class="bg-gray-light">{{ $ibanTitle }}</th>
            <th class="bg-gray-light">IBAN {{ $ibanTitle }}</th>
            <th class="bg-gray-light">BIC {{ $ibanTitle }}</th>
            <!--<th class="bg-gray-light">Banco</th>-->
            <th class="bg-gray-light">Observações</th>
            @if(@$payment->edit_mode)
                <th class="bg-gray-light w-1">Ações</th>
            @else
                <th class="bg-gray-light w-1">Estado</th>
            @endif
        </tr>
        @if($transactions)
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->reference }}</td>
                    <td class="text-right bold">{{ money($transaction->amount, '€') }}</td>
                    @if($payment->type == 'dd')
                    <td>{{ $transaction->mandate_code }}</td>
                    @else
                        <td>{{ trans('admin/billing.sepa-transfers-types.'.$transaction->transaction_code) }}</td>
                    @endif
                    <td>{{ $transaction->company_name }}</td>
                    <td>
                        <span data-toggle="tooltip" title="{{ $transaction->bank_name }}">
                            {{ $transaction->bank_iban }}
                        </span>
                    </td>
                    <td>
                        <span data-toggle="tooltip" title="{{ $transaction->bank_name }}">
                            {{ $transaction->bank_swift }}
                        </span>
                    </td>
                    <!--<td>{{ $transaction->bank_name }}</td>-->
                    <td>{{ $transaction->obs }}</td>
                    @if($payment->edit_mode)
                        <td>
                            <a href="{{ route('admin.sepa-transfers.transactions.edit', [$transaction->group_id, $transaction->id]) }}"
                               data-toggle="modal"
                               data-target="#modal-remote"
                               class="text-green">
                                <i class="fas fa-fw fa-pencil-alt"></i>
                            </a>
                            <a href="{{ route('admin.sepa-transfers.transactions.destroy', [$transaction->group_id, $transaction->id]) }}"
                               class="text-red remove-transaction-line">
                                <i class="fas fa-fw fa-trash-alt"></i>
                            </a>
                        </td>
                    @else
                        <td class="text-center">
                            @if($transaction->status == \App\Models\SepaTransfer\PaymentTransaction::STATUS_ACCEPTED)
                                <span class="label label-success">Aceite</span>
                            @elseif($transaction->status == \App\Models\SepaTransfer\PaymentTransaction::STATUS_PENDING)
                                <span class="label label-warning">Pendente</span>
                            @elseif($transaction->status == \App\Models\SepaTransfer\PaymentTransaction::STATUS_REJECTED)
                                <span class="label label-danger"
                                    data-toggle="tooltip"
                                    title="{{ $transaction->error_code }} - {{ $transaction->error_msg }}">
                                    Rejeitado
                                </span>
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
        @endif
    </table>
</div>
