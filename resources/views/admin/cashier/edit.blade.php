{{ Form::model($movement, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('customer_id', 'Associar registo ao cliente...') }}
                {{ Form::select('customer_id', $movement->exists && $movement->customer_id ? [$movement->customer_id => @$movement->customer->code . ' - ' .@$movement->customer->name] : ['' => ''], null, ['class' => 'form-control select2', 'data-placeholder' => 'Nenhum']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('provider_id', 'ou associar ao fornecedor...') }}
                {{ Form::select('provider_id', $movement->exists && $movement->provider_id ? [$movement->provider_id => @$movement->provider->code . ' - ' .@$movement->provider->name] : ['' => ''], null, ['class' => 'form-control select2', 'data-placeholder' => 'Nenhum']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('operator_id', 'Associar a Colaborador') }}
                {{ Form::select('operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('description', 'Descrição Movimento') }}
                {{ Form::text('description', null, ['class' => 'form-control', 'required', 'maxlength' => 75]) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('type_id', 'Tipo Movimento') }}
                {{ Form::select('type_id', ['' => ''] + $purchasesTypes, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('obs', 'Observações') }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 2]) }}
    </div>
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('amount', 'Valor') }}
                <div class="input-group">
                    {{ Form::text('amount', null, ['class' => 'form-control decimal nospace', 'required']) }}
                    <div class="input-group-addon">€</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('date', 'Data') }}
                <div class="input-group">
                    {{ Form::text('date', $movement->exists ? $movement->date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                    <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('payment_method', 'Método') }}
                {{ Form::select('payment_method', ['' => ''] + $paymentMethods, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('sense', 'Tipo') }}
                {{ Form::select('sense', ['credit' => 'Crédito', 'debit' => 'Débito'], null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options">
        <div class="checkbox m-t-8">
            <label style="padding-left: 0">
                {{ Form::checkbox('is_paid', 1) }} Marcar movimento como pago
            </label>
        </div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2());
    $('.modal .datepicker').datepicker(Init.datepicker());

    $(".modal select[name=customer_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.cashier.search.customer') }}")
    });

    $(".modal select[name=provider_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.cashier.search.provider') }}")
    });
</script>
