{{ Form::model($fuel, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('vehicle_id', __('Viatura')) }}
                {{ Form::select('vehicle_id', count($vehicles) > 1 ? ['' => ''] + $vehicles : $vehicles, null, ['class' => 'form-control select2','required']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('provider_id', __('Posto Abastecimento')) }}
                {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('product', __('Produto')) }}
                {{ Form::select('product', ['fuel' => __('Combustível'), 'adblue' => 'AdBlue'], null, ['class' => 'form-control select2','required']) }}
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('liters', __('Litros')) }}
                <div class="input-group">
                    {{ Form::text('liters', null, ['class' => 'form-control decimal', 'required']) }}
                    <span class="input-group-addon">L</span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('price_per_liter', __('Preço por Litro')) }}
                <div class="input-group">
                    {{ Form::text('price_per_liter', null, ['class' => 'form-control decimal', 'required']) }}
                    <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required" data-toggle="tooltip"
                title="@trans('Este preço é meramente indicativo. Só são contabilizados para análise de custos os valores inseridos no registo de compras.')">
                {{ Form::label('total', __('Total')) }}
                <div class="input-group">
                    {{ Form::text('total', null, ['class' => 'form-control decimal', 'required']) }}
                    <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('km', 'Km') }}
                <div class="input-group">
                    {{ Form::text('km', null, ['class' => 'form-control number', 'required']) }}
                    <span class="input-group-addon">km</span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('date', __('Data abast.')) }}
                <div class="input-group">
                    {{ Form::text('date', $fuel->exists ? $fuel->date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker','required']) }}
                    <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('operator_id', __('Operador')) }}
                {{ Form::select('operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-5">
            @if (hasModule('purchase_invoices') && hasPermission('purchase_invoices'))
                <div class="form-group">
                    {{ Form::label('assigned_invoice_id', __('Fatura Compra Associada')) }}
                    {{ Form::select('assigned_invoice_id',$fuel->exists ? [$fuel->assigned_invoice_id => @$fuel->invoice->reference] : [], null,['class' => 'form-control select2']) }}
                </div>
            @else
                <div data-toggle="tooltip"
                    title="{{ hasModule('purchase_invoices') ? '' : __('A sua licença não inclui o módulo de faturas de despesas.') }}">
                    <div class="form-group">
                        {{ Form::label('assigned', __('Fatura Compra Associada')) }}
                        {{ Form::select('assigned', [], null, ['class' => 'form-control select2', 'disabled']) }}
                    </div>
                </div>
            @endif
        </div>
        {{-- <div class="col-sm-12">
            <div class="form-group" style="display: {{ ($fuel->exists && $fuel->filepath) ?  'none' : 'block' }};" }}>
                {{ Form::label('name', 'Ficheiro a anexar') }}
                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                    <div class="form-control" data-trigger="fileinput">
                        <i class="fas fa-file fileinput-exists"></i>
                        <span class="fileinput-filename"></span>
                    </div>
                    <span class="input-group-addon btn btn-default btn-file">
                <span class="fileinput-new">Procurar...</span>
                <span class="fileinput-exists">Alterar</span>
                <input type="file" name="file">
            </span>
                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                </div>
            </div>
            <div class="form-group" style="display: {{ ($fuel->exists && $fuel->filepath) ?  'block' : 'none' }};" }}>
                {{ Form::label('name', 'Ficheiro a anexar') }}
                <div>
                    <a href="{{ asset($fuel->filepath) }}" target="_blank" class="">
                        <i class="fas fa-file"></i> {{ $fuel->filename }}
                    </a>
                </div>
                <button class="btn btn-danger btn-xs m-t-10 btn-delete">
                    <i class="fas fa-trash-alt"></i> Eliminar o anexo {{ $fuel->filename }}
                </button>
            </div>
        </div> --}}
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('obs', __('Observações'), ['data-content' => '']) }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 2, 'maxlength' => 150]) }}
            </div>
        </div>
    </div>
    {{-- <div class="alert alert-danger m-b-0">
        <i class="fas fa-info-circle"></i> O valor de km que inseriu é inferior ao último valor conhecido ({{ @$fuel->vehicle->km_counter }}km).
        <br/>Pretende gravar mesmo assim?
    </div> --}}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::hidden('delete_file') }}
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2());

    $("select[name=assigned_invoice_id]").select2({
        minimumInputLength: 2,
        ajax: Init.select2Ajax("{{ route('admin.invoices.purchase.search.invoice') }}")
    });

    $('[name="price_per_liter"]').on('change', function() {
        var pricePerLiter = $('[name="price_per_liter"]').val()
        var liters = $('[name="liters"]').val();

        total = liters * pricePerLiter;
        $('[name="total"]').val(total.toFixed(2));
    })

    $('[name="liters"]').on('change', function() {
        var pricePerLiter = $('[name="price_per_liter"]').val()
        var liters = $('[name="liters"]').val();

        total = liters * pricePerLiter;
        $('[name="total"]').val(total.toFixed(2));
    })

    $('[name="total"]').on('change', function() {
        var pricePerLiter = $('[name="price_per_liter"]').val()
        var liters = $('[name="liters"]').val();
        var total = $('[name="total"]').val();

        if (liters == '' && pricePerLiter != '') {
            liters = total * pricePerLiter;
            $('[name="liters"]').val(liters.toFixed(2));
        } else {
            pricePerLiter = total / liters;
            $('[name="price_per_liter"]').val(pricePerLiter.toFixed(2));
        }
    })

    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        $(this).closest('.form-group').hide();
        $(this).closest('.form-group').prev().show();
        $('[name="delete_file"]').val(1);
    })
</script>
