{{ Form::model($cost, $formOptions) }}
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
                {{ Form::select('vehicle_id', count($vehicles) > 1 ? ['' => ''] + $vehicles : $vehicles, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('type', __('Tipo Despesa')) }}
                {{ Form::select('type', ['' => ''] + trans('admin/fleet.fixed-costs.types'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('provider_id', __('Fornecedor')) }}                    
                {{ Form::select('provider_id', $provider->id ? [$provider->id => @$provider->code.' - '. str_limit(@$provider->name)] : [], null, ['class' => 'form-control search-customer', 'required']) }}
            </div>
        </div>
        

        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('description', __('Descrição')) }}
                {{ Form::text('description', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('total', __('Total/mês')) }}
                <div class="input-group">
                    {{ Form::text('total', null, ['class' => 'form-control decimal', 'required']) }}
                    <span class="input-group-addon">
                        {{ Setting::get('app_currency') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('start_date', __('Data Início')) }}
                <div class="input-group">
                    {{ Form::text('start_date', $cost->exists ? $cost->start_date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                    <span class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('end_date', __('Data Fim')) }}
                <div class="input-group">
                    {{ Form::text('end_date', $cost->exists ? $cost->end_date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                    <span class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('obs', __('Observações')) }}
                {{ Form::textarea('obs',null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>

{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker())

    $(".modal select[name=provider_id]").select2({
        ajax: {
            url: "{{ route('admin.invoices.sales.search.provider') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=provider_id] option').remove()

                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });
</script>

