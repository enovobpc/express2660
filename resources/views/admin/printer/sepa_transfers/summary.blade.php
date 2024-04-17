<div style="font-size: 12px">
    <div style="background: #eee; border: 1px solid #ccc; border-radius: 3px; padding: 10px; margin-top: 25px; margin-bottom: 15px">
        <div style="float: left; width: 64%">
            <h4 style="margin: 0">
                <small>Descrição</small><br/>
                {{ $payment->code }} - {{ $payment->name }}
            </h4>
        </div>
        <div style="float: left; width: 15%; text-align: right">
            <h4 style="margin: 0">
                <small>Transações</small><br/>
                {{ $payment->transactions_count }}
            </h4>
        </div>
        <div style="float: left; width: 20%; text-align: right">
            <h4 style="margin: 0">
                <small>Total</small><br/>
                <span style="font-weight: bold">{{ money($payment->transactions_total, '€') }}</span>
            </h4>
        </div>
    </div>

    @foreach($payment->groups as $group)
        <h4 style="font-weight: bold">Lote {{ $group->code }}
            @if($payment->type == 'dd')
            <small>{{ $group->service_type }} / {{ $group->sequence_type }}</small>
            @endif
        </h4>
        <p style="float: left; width: 59%">
        Criação: {{ $group->created_at->format('Y-m-d') }}<br/>
        Conta: {{ $group->bank_name }} ({{ $group->company }})<br/>
        IBAN: {{ $group->bank_iban }} / {{ $group->bank_swift }}<br/>
        Código Credor: {{ $group->credor_code }}
    </p>
    <p style="float: left; width: 40%; text-align: right">
        @if($payment->type == 'trf')
            Categoria: {{ trans('admin/billing.sepa-transfers-types.'.$group->category) }}<br/>
        @endif
        Processamento: {{ $group->processing_date->format('Y-m-d') }}<br/>
        Transações: {{ $group->transactions_count }}<br/>
        Montante Total: <span style="font-weight: bold">{{ money($group->transactions_total, '€') }}</span>
    </p>
    <table class="table table-condensed table-bordered">
        <tr>
            <th style="background: #ccc">Referência</th>
            <th style="background: #ccc">{{ $payment->type == 'dd' ? 'Mandato' : 'Tipo' }}</th>
            <th style="background: #ccc">{{ $payment->type == 'dd' ? 'Devedor' : 'Beneficiário' }}</th>
            <th style="background: #ccc">IBAN</th>
            <th style="background: #ccc">BIC/Swift</th>
            <th style="background: #ccc">Obs</th>
            <th style="background: #ccc">Estado</th>
            <th style="background: #ccc; text-align:right">Montante</th>
        </tr>
        @foreach($group->transactions as $transaction)
            <tr>
                <td>{{ $transaction->reference }}</td>
                <td>
                    @if($payment->type == 'dd')
                    {{ $transaction->mandate_code }}
                    @else
                    {{ trans('admin/billing.sepa-transfers-types.'.$transaction->transaction_code) }}
                    @endif
                </td>
                <td>{{ $transaction->company_name }}</td>
                <td>{{ $transaction->bank_iban }}</td>
                <td>{{ $transaction->bank_swift }}</td>
                <td>{{ $transaction->obs }}</td>
                <td>
                    @if($transaction->status == \App\Models\SepaTransfer\PaymentTransaction::STATUS_PENDING)
                        Aguarda
                    @elseif($transaction->status == \App\Models\SepaTransfer\PaymentTransaction::STATUS_ACCEPTED)
                        Aceite
                    @elseif($transaction->status == \App\Models\SepaTransfer\PaymentTransaction::STATUS_REJECTED)
                        Rejeitado
                    @endif
                </td>
                <td style="font-weight: bold; text-align:right">{{ money($transaction->amount, '€') }}</td>
            </tr>
        @endforeach
    </table>
@endforeach
</div>