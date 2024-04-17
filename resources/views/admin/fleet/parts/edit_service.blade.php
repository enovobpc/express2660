{{ Form::model($service, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group is-required">
        {{ Form::label('name', __('Designação'), ['data-content' => '']) }}
        {{ Form::text('name', null, ['class' => 'form-control', 'required', 'maxlenght' => 80]) }}
    </div>
    <div class="form-group is-required">
        {{ Form::label('type', __('Tipo')) }}
        {{ Form::select('type', ['' => '', 'maintenance' => __('Serviços Manutenção'), 'expenses' => __('Despesas gerais')], null, ['class' => 'form-control select2', 'required']) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}
<script>
    $('.select2').select2(Init.select2());
</script>