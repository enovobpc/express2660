{{ Form::model($contract, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group is-required">
        {{ Form::label('contract_type', __('Tipo de Contrato')) }}
        {{ Form::select('contract_type', ['' => ''] + trans('admin/users.contract-types'), null, ['class' => 'form-control select2', 'required']) }}
    </div>
    <div class="row row-5">

        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('start_date', __('Data Início')) }}
                <div class="input-group">
                    {{ Form::text('start_date', $contract->exists ? $contract->start_date->format('Y-m-d') : null, ['class' => 'form-control datepicker nospace', 'required']) }}
                    <span class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('end_date', __('Data Fim')) }}
                <div class="input-group">
                    {{ Form::text('end_date', $contract->exists ? $contract->end_date->format('Y-m-d') : null, ['class' => 'form-control datepicker nospace']) }}
                    <span class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('notification_days', __('Avisar Fim')) }}
                <div class="input-group">
                    {{ Form::text('notification_days', null, ['class' => 'form-control number nospace', 'maxlength' => 3]) }}
                    <span class="input-group-addon" style="border: none">@trans('dias antes')</span>
                </div>
            </div>
        </div>
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
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2());
    $('.modal .datepicker').datepicker(Init.datepicker());
</script>