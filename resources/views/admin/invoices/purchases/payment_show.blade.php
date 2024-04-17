<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Nota Pagamento {{ $paymentNote->code }}</h4>
</div>
<div class="tabbable-line m-b-15">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab-info" data-toggle="tab">
                Detalhes Pagamento
            </a>
        </li>
        <li class="{{ @$tab == 'attachments' ? 'active' : '' }}">
            <a href="#tab-attachments" data-toggle="tab">
                Documentos Anexos
            </a>
        </li>
    </ul>
</div>
<div class="modal-body p-t-0 p-b-0 modal-shipment modal-shipment-detail">
    <div class="tab-content m-b-0" style="padding-bottom: 10px;">
        <div class="tab-pane active" id="tab-info">
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('provider_id', 'Fornecedor') }}
                        {{ Form::text('provider_id', @$paymentNote->provider->code . ' - ' .@$paymentNote->provider->company, ['class' => 'form-control', 'readonly']) }}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('reference', 'Nº Recibo Fornecedor') }}
                        {{ Form::text('reference', $paymentNote->reference, ['class' => 'form-control', 'readonly']) }}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('date', 'Data Documento') }}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            {{ Form::text('date', $paymentNote->doc_date, ['class' => 'form-control', 'readonly']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('discount', 'Desc. Financeiro') }}
                        <div class="input-group">
                            {{ Form::text('discount', $paymentNote->discount, ['class' => 'form-control', 'readonly']) }}
                            <div class="input-group-addon">{{ $paymentNote->discount_unity ?? '%' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert" style="background: #eee; display: none">
                <p><i class="fas fa-coins"></i> IBAN Pagamento: <b class="iban">{{ @$provider->iban ? $provider->iban : 'Não definido na ficha do fornecedor' }}</b></p>
            </div>
            <h4 class="bold">Documentos liquidados</h4>
            <table class="table table-condensed">
                <tr>
                    <th class="bold bg-gray-light vertical-align-middle w-150px">Tipo Doc.</th>
                    <th class="bold bg-gray-light vertical-align-middle">Referência</th>
                    <th class="bold bg-gray-light vertical-align-middle w-100px">Data</th>
                    <th class="bold bg-gray-light vertical-align-middle w-110px">Vencimento</th>
                    <th class="bold bg-gray-light vertical-align-middle w-90px">Total</th>
                    <th class="bold bg-gray-light vertical-align-middle w-120px text-right" style="border-left: 2px solid #333">Liquidado</th>
                </tr>
                @foreach($invoices as $invoice)
                    <tr>
                        <td class="vertical-align-middle">{{ trans('admin/billing.types.' . $invoice->invoice->doc_type) }}</td>
                        <td class="vertical-align-middle">{{ $invoice->invoice->reference }}</td>
                        <td class="vertical-align-middle">{{ $invoice->invoice->doc_date }}</td>
                        <td class="vertical-align-middle">{{ $invoice->invoice->due_date }}</td>
                        <td class="vertical-align-middle bold">{{ money($invoice->invoice->total, $invoice->currency) }}</td>
                        <td class="vertical-align-middle bold text-right" style="border-left: 2px solid #333">{{ $invoice->total }}</td>
                    </tr>
                @endforeach
            </table>
            <div style="padding: 0 7px 5px;
                        margin: -15px 0 -20px;
                        border: 1px solid #ddd;
                        float: right;
                        border-radius: 5px;
                        background: #eee;
                        text-align: right">
                <h4 style="
                    margin: 0;
                    float: left;
                    font-size: 15px;
                    width: 85px;
                ">
                    <small>Total</small><br/>
                    <span class="doc-subtotal">{{ money($paymentNote->total) }}</span>{{ Setting::get('app_currency') }}
                </h4>
                <h4 style="
                    margin: 0;
            float: left;
            font-size: 15px;
            width: 75px;
        ">
                    <small>Desconto</small><br/>
                    <span class="doc-discount">{{ money($paymentNote->discount) }}</span>{{ Setting::get('app_currency') }}
                </h4>
                <h4 style="
            margin: 0;
            float: left;
            font-size: 15px;
            font-weight: bold;
            width: 90px;
        ">
                    <small>Total Pago</small><br>
                    <span class="doc-total">{{ money($paymentNote->total - $paymentNote->discount) }}</span>{{ Setting::get('app_currency') }}
                </h4>
            </div>
            <div class="clearfix"></div>
            @if($payments)
            <h4 class="bold">Formas de pagamento</h4>
            <table class="table table-condensed m-0">
                <tr>
                    <th class="bold bg-gray-light w-140px">Data</th>
                    <th class="bold bg-gray-light w-150px">Meio Pagamento</th>
                    <th class="bold bg-gray-light w-120px bank-col">Banco</th>
                    <th class="bold bg-gray-light w-130px">Valor</th>
                    <th class="bold bg-gray-light">Observações</th>
                </tr>
                @foreach($payments as $i => $paymentMethod)
                    <tr class="payment-row">
                        <td class="vertical-align-middle">{{ $paymentMethod->date }}</td>
                        <td class="vertical-align-middle input-sm">{{ @$paymentMethod->payment_method->name }}</td>
                        <td class="vertical-align-middle input-sm bank-col">{{ @$paymentMethod->bankInfo->name }}</td>
                        <td class="vertical-align-middle">{{ money($paymentMethod->total, Setting::get('app_currency')) }}</td>
                        <td class="vertical-align-middle">{{ $paymentMethod->obs }}</td>
                    </tr>
                @endforeach
            </table>
            @endif
        </div>
        <div class="tab-pane" id="tab-attachments">
            <div class="attachments-content">
                @include('admin.invoices.purchases.partials.tabs.attachments_content_payments')
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>

<style>
    .modal [readonly],
    .modal [readonly]:hover,
    .modal [readonly]:focus {
        border-color: #d2d6de !important;
        background: #fff;
        cursor: default;
    }

    tr:hover .billing-remove-row {
        display: block;
    }

    tr .billing-remove-row {
        cursor: pointer;
        display: none;
        position: absolute;
        left: 4px;
        padding: 6px 0;
    }
</style>


