{{ Form::open(['route' => ['admin.sepa-transfers.invoices.store', $payment->id], 'class' => 'autocreate-invoice']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Gerar faturas automáticamente</h4>
</div>
<div class="modal-body">
    <h4 class="m-t-0 bold">
        Confirma a geração automática das faturas?
        <br/>
        <small>{{ $transactions->filter(function($item){ return $item->status == \App\Models\SepaTransfer\PaymentTransaction::STATUS_ACCEPTED; })->count() }} transações bem sucedidas.</small>
    </h4>
    <div style="    border: 1px solid #ccc;
    height: 250px;
    overflow: scroll;">
        <table class="table table-condensed" style="margin-top: -1px; margin-bottom: 0">
            <tr>
                <th style="background: #ccc">Cliente</th>
                <th style="background: #ccc; width: 100px">Referência</th>
                <th style="background: #ccc; width: 90px">Doc. Gerar</th>
                <th style="background: #ccc; width: 80px; text-align: right">Valor</th>
            </tr>
            @foreach($transactions as $transaction)
                @if(@$transaction->invoice->assigned_invoice_id)
                <tr style="text-decoration: line-through">
                @else
                <tr style="{{ @$transaction->status != \App\Models\SepaTransfer\PaymentTransaction::STATUS_ACCEPTED ? 'background: #f7b1b1; color: red' : '' }}">
                @endif
                    <td>
                        {{ @$transaction->invoice->customer->name }}
                    </td>
                    <td>{{ @$transaction->invoice->reference }}</td>
                    <td>
                        @if(@$transaction->invoice->doc_type == 'invoice' && empty(@$transaction->invoice->doc_after_payment))
                        Recibo
                        @else
                        {{ trans('admin/billing.types_code.'.@$transaction->invoice->doc_after_payment) }}@if(@$transaction->invoice->assigned_invoice_id){{ @$transaction->invoice->invoice->doc_id }}@endif
                        @endif
                    </td>
                    <td class="text-right bold">{{ money(@$transaction->invoice->doc_total, '€') }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    <hr style="margin: 15px 0 15px;"/>
    <div class="row row-5">
        <div class="col-sm-6">
            {{ Form::label('payment_date', 'Data e Forma Pagamento') }}
            <div class="row row-0">
                <div class="col-sm-6 col-md-5">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        {{ Form::text('payment_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required', 'style' => 'padding: 0 5px;']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::select('payment_method', ['' => ''] + $paymentMethods, 'dd', ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required m-b-0 m-t-0">
                {{ Form::label('apiKey', 'Série') }}
                {{ Form::select('apiKey', $series, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required m-b-0 m-t-0">
                {{ Form::label('doc_type', 'Documento') }}
                {{ Form::select('doc_type', ['' => 'Automático'] + trans('admin/billing.types_code'), null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required m-b-0 m-t-0">
                {{ Form::label('doc_date', 'Data Doc.') }}
                {{ Form::text('doc_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required', 'style' => 'padding: 0 8px;']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options">
        <label style="font-weight: normal">
            {{ Form::checkbox('send_email', 1, true) }} Enviar fatura por e-mail
        </label>
    </div>
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit"
                class="btn btn-primary"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gerar...">
            Gerar Documentos
        </button>
    </div>
</div>
{{ Form::close() }}

<script>
    $('.autocreate-invoice input').iCheck(Init.iCheck());
    $('.autocreate-invoice .datepicker').datepicker(Init.datepicker());
    $('.autocreate-invoice .select2').select2(Init.select2());
</script>