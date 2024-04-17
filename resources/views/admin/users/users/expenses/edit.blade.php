{{ Form::model($expense, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('description', __('Descrição')) }}
                {{ Form::text('description', null, ['class' => 'form-control', 'required', 'placeholder' => 'Ex: Salário Março 2020']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('type_id', __('Tipo Despesa')) }}
                {{ Form::select('type_id', [''=>''] + $expensesTypes, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>

    <div class="row row-5">
        @if($expense->is_fixed)
            <div class="col-sm-3">
                <div class="form-group is-required">
                    {{ Form::label('provider_id', __('Fornecedor')) }}
                    {{ Form::select('provider_id', [$expense->provider_id => @$expense->provider->name], null, ['class' => 'form-control', 'required', 'data-placeholder' => '']) }}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group is-required">
                    {{ Form::label('start_date', __('Início')) }}
                    <div class="input-group">
                        {{ Form::text('start_date', null, ['class' => 'form-control datepicker', 'required']) }}
                        <div class="input-group-addon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group is-required">
                    {{ Form::label('end_date', __('Fim')) }}
                    <div class="input-group">
                        {{ Form::text('end_date', null, ['class' => 'form-control datepicker', 'required']) }}
                        <div class="input-group-addon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group is-required">
                    {{ Form::label('total', __('Total')) }}
                    <div class="input-group">
                        {{ Form::text('total', null, ['class' => 'form-control decimal', 'required']) }}
                        <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-sm-6">
                <div class="form-group is-required">
                    {{ Form::label('provider_id', __('Fornecedor')) }}
                    {{ Form::select('provider_id', [$expense->provider_id => @$expense->provider->name], null, ['class' => 'form-control', 'required', 'data-placeholder' => '']) }}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group is-required">
                    {{ Form::label('date', __('Data')) }}
                    <div class="input-group">
                        {{ Form::text('date', null, ['class' => 'form-control datepicker', 'required']) }}
                        <div class="input-group-addon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group is-required">
                    {{ Form::label('total', __('Total')) }}
                    <div class="input-group">
                        {{ Form::text('total', null, ['class' => 'form-control decimal', 'required']) }}
                        <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="form-group">
        {{ Form::label('obs', __('Observações')) }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::hidden('is_fixed', $expense->is_fixed) }}
{{ Form::close() }}
<script>
    $('#modal-remote .datepicker').datepicker(Init.datepicker());
    $('#modal-remote .select2').select2(Init.select2());

    $("select[name=provider_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.shipments.search.provider') }}")
    });
</script>
