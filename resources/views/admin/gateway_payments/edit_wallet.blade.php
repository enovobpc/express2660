{{ Form::open(['route' => 'admin.gateway.payments.wallet.update', 'method' => 'POST']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Gerir Saldo Conta Corrente</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-8">
            <div class="form-group">
                {{ Form::label('customer_id', 'Cliente') }}
                {{ Form::select('customer_id', [@$customer->id => @$customer->code. ' - ' . @$customer->name], null, ['class' => 'form-control', 'data-placeholder' => '']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <h3 class="m-0 m-l-10">
                <small>Saldo</small><br/>
                <span class="wallet-total">{{ money(@$customer->wallet_balance) }}</span>{{ Setting::get('app_currency') }}
            </h3>
        </div>
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('description', 'Descrição do movimento') }}
                {{ Form::text('description', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('sense', 'Ação') }}
                <ul class="list-inline m-t-10">
                    <li>
                        <label style="font-weight: normal">
                            {{ Form::radio('sense', 'debit', true) }} Débito (-)
                        </label>
                    </li>
                    <li>
                        <label style="font-weight: normal">
                            {{ Form::radio('sense', 'credit', false) }} Crédito (+)
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('amount', 'Valor') }}
                <div class="input-group">
                    {{ Form::text('amount', null, ['class' => 'form-control decimal', 'required']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2());

    $(".modal select[name=customer_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
    });

    $(".modal select[name=customer_id]").on('select2:select', function (e) {
        data  = e.params.data;
        var wallet = data.wallet.toFixed(2);
        $('.wallet-total').html(wallet)
    })

    $('.modal [name="sense"]').on('change', function () {
        $('.sense-signal').html('+')
        if($(this).val() == 'debit') {
            $('.sense-signal').html('-')
        }
    })
</script>
