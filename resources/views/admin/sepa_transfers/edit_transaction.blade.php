<?php $hash = str_random(5) ?>
{{ Form::model($paymentTransaction, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body {{ $hash }}">
    <div class="row row-5">
        @if(@$group->payment->type == 'dd')
            <div class="col-sm-8">
                <div class="form-group">
                    {{ Form::label('customer_id', 'Associar ao cliente...') }}
                    {{ Form::select('customer_id', $paymentTransaction->customer_id ? [$paymentTransaction->customer_id => @$paymentTransaction->customer->name] : [], null, ['class' => 'form-control', 'data-placeholder' => 'Nenhum cliente associado']) }}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('invoice_id', 'Associar à fatura venda...') }}
                    {{ Form::select('invoice_id', $paymentTransaction->invoice_id ? [$paymentTransaction->invoice_id => @$paymentTransaction->invoice->internal_code] : [], null, ['class' => 'form-control', 'data-placeholder' => 'Nenhuma fatura associada']) }}
                </div>
            </div>
        @else
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('customer_id', 'Associar ao cliente...') }}
                    {{ Form::select('customer_id', $paymentTransaction->customer_id ? [$paymentTransaction->customer_id => @$paymentTransaction->customer->name] : [], null, ['class' => 'form-control', 'data-placeholder' => 'Nenhum cliente associado']) }}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('provider_id', 'Associar ao fornecedor...') }}
                    {{ Form::select('provider_id', $paymentTransaction->provider_id ? [$paymentTransaction->provider_id => @$paymentTransaction->provider->name] : [], null, ['class' => 'form-control', 'data-placeholder' => 'Nenhum fornecedor associado']) }}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('purchase_invoice_id', 'Associar à fatura compra...') }}
                    {{ Form::select('purchase_invoice_id', $paymentTransaction->purchase_invoice_id ? [$paymentTransaction->purchase_invoice_id => @$paymentTransaction->purchase_invoice->internal_code] : [], null, ['class' => 'form-control', 'data-placeholder' => 'Nenhuma fatura associada']) }}
                </div>
            </div>
        @endif
    </div>
    <div class="row row-5" style="background: #ddd;
    border-radius: 3px;
    padding: 10px 0;
    margin-bottom: 15px;">
        <div class="col-sm-3">
            <div class="form-group is-required m-0">
                {{ Form::label('reference', 'Referência') }}
                {{ Form::text('reference', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required m-0">
                {{ Form::label('amount', 'Valor') }}
                <div class="input-group">
                    {{ Form::text('amount', null, ['class' => 'form-control decimal', 'required', 'style' => 'border-width:2px; border-color: #999']) }}
                    <div class="input-group-addon">€</div>
                </div>
            </div>
        </div>
        @if(@$group->payment->type == 'dd')
            <div class="col-sm-3">
                <div class="form-group is-required m-0">
                    {{ Form::label('mandate_code', 'Nº Mandato') }}
                    {{ Form::text('mandate_code', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group is-required m-0">
                    {{ Form::label('mandate_date', 'Data Mandato') }}
                    <div class="input-group">
                        {{ Form::text('mandate_date', null, ['class' => 'form-control datepicker', 'required']) }}
                        <div class="input-group-addon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-sm-6">
                <div class="form-group is-required m-0">
                    {{ Form::label('transaction_code', 'Cód. Transferência') }}
                    {{ Form::select('transaction_code', ['' => ''] + trans('admin/billing.sepa-transfers-types'), null, ['class' => 'form-control select2', 'required']) }}
                </div>
            </div>
        @endif
    </div>
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('company_code', 'Cod. Titular') }}
                {{ Form::text('company_code', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-10">
            <div class="form-group is-required">
                {{ Form::label('company_name', 'Nome Titular') }}
                {{ Form::text('company_name', null, ['class' => 'form-control uppercae', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('bank_name', 'Banco Cliente') }}
                {{ Form::text('bank_name', null, ['class' => 'form-control uppercae', 'required']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('bank_iban', 'IBAN') }}
                {{ Form::text('bank_iban', null, ['class' => 'form-control iban uppercase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('bank_swift', 'SWIFT') }}
                {{ Form::text('bank_swift', null, ['class' => 'form-control uppercase nospace', 'required']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group m-0">
                {{ Form::label('obs', 'Descrição') }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2())
    $('.modal .datepicker').datepicker(Init.datepicker())
    $('.modal input').iCheck(Init.iCheck())

    $(".{{ $hash }} select[name=customer_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.sepa-transfers.select.search', 'customer') }}")
    });

    $(".{{ $hash }} select[name=provider_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.sepa-transfers.select.search', 'provider') }}")
    });

    $(".{{ $hash }} select[name=invoice_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.sepa-transfers.select.search', 'invoice') }}")
    });

    $(".{{ $hash }} select[name=purchase_invoice_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.sepa-transfers.select.search', 'purchase-invoice') }}")
    });

    $('.{{ $hash }} select[name=customer_id]').on('select2:select', function (e) {
        var data = e.params.data;
        console.log(data);
        $('.{{ $hash }} [name=company_code]').val(data.code);
        $('.{{ $hash }} [name=company_name]').val(data.name);
        $('.{{ $hash }} [name=bank_name]').val(data.bank_name);
        $('.{{ $hash }} [name=bank_iban]').val(data.bank_iban);
        $('.{{ $hash }} [name=bank_swift]').val(data.bank_swift);
        $('.{{ $hash }} [name=mandate_code]').val(data.mandate_code);
        $('.{{ $hash }} [name=mandate_date]').val(data.mandate_date);
    })

    $('.{{ $hash }} select[name=provider_id]').on('select2:select', function (e) {
        var data = e.params.data;
        console.log(data);
        $('.{{ $hash }} [name=company_code]').val(data.code);
        $('.{{ $hash }} [name=company_name]').val(data.name);
        $('.{{ $hash }} [name=bank_name]').val(data.bank_name);
        $('.{{ $hash }} [name=bank_iban]').val(data.bank_iban);
        $('.{{ $hash }} [name=bank_swift]').val(data.bank_swift);
        $('.{{ $hash }} [name=mandate_code]').val(data.mandate_code);
        $('.{{ $hash }} [name=mandate_date]').val(data.mandate_date);
    })

    $('.{{ $hash }} select[name=invoice_id], .{{ $hash }} select[name=purchase_invoice_id]').on('select2:select', function (e) {
        var data = e.params.data;
        $('.{{ $hash }} [name=reference]').val(data.code);
        $('.{{ $hash }} [name=amount]').val(data.total);
    })

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-sepa-transaction').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $form = $(this);
        var $btn = $form.find('button[type=button]');

        $btn.button('loading');

        $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            type: $form.attr('method'),
            success: function(data) {
                if(data.result) {
                    Growl.success(data.feedback);
                    $('.modal .sepa-groups-list').html(data.html_groups)
                    $('.modal .sepa-transactions-list').html(data.html)
                    $('.modal .sepa-transactions-count').html(data.transactions_count)
                    $('.modal .sepa-transactions-total').html(data.transactions_total)
                    $('#modal-remote').modal('hide');
                } else {
                    Growl.error(data.feedback);
                }
            }
        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $btn.button('reset');
        });

    });
</script>
