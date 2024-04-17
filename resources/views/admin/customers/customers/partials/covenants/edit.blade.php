{{ Form::model($covenant, $formOptions) }}
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
                {{ Form::label('type', __('Tipo')) }}
                {{ Form::select('type', ['' => ''] + trans('admin/global.covenants-types'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('start_date', __('Iníco Avença')) }}
                {{ Form::text('start_date', $covenant->exists ? $covenant->start_date->format('Y-m-d') : null , ['class' => 'form-control datepicker', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('end_date', __('Fim Avença')) }}
                {{ Form::text('end_date', $covenant->exists ? $covenant->end_date->format('Y-m-d') : null, ['class' => 'form-control datepicker', 'required']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('description', __('Descrição')) }}
                {{ Form::text('description', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-3" id="max_shipments" style="display: {{ $covenant->type == 'variable' ? 'block' : 'none' }}">
            <div class="form-group is-required">
                {{ Form::label('max_shipments', __('Máx. Envios')) }}
                {{ Form::text('max_shipments', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-6" id="service_id" style="display: {{ $covenant->type == 'variable' ? 'block' : 'none' }}">
            <div class="form-group is-required">
                {{ Form::label('service_id', __('Serviço')) }}
                {{ Form::select('service_id', ['' => ''] + $services, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('amount', __('Valor Mensal')) }}
                <div class="input-group">
                    {{ Form::text('amount', null, ['class' => 'form-control decimal', 'required']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
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

    $('.datepicker').datepicker(Init.datepicker());

    $(document).on('change', '[name="type"]', function(){
        var type = $(this).val();
        
        if(type == 'variable') {
            $('#max_shipments, #service_id').show();
            $('[name="max_shipments"]').prop('required', true);
            $('[name="service_id"]').prop('required', true);
        } else {
            $('#max_shipments, #service_id').hide();
            $('[name="max_shipments"]').prop('required', false);
            $('[name="service_id"]').prop('required', false);
        }
    })
</script>

