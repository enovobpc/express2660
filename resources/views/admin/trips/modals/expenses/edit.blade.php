{{ Form::model($expense, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('description', __('Descrição'), ['class' => 'control-label']) }}
                {{ Form::text('description', null, ['class' => 'form-control required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('total', __('Total'), ['class' => 'control-label']) }}
                {{ Form::text('total', null, ['class' => 'form-control decimal', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('operator_id', __('Motorista'), ['class' => 'control-label']) }}
                {{ Form::select('operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Guardar')</button>
</div>
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2())
</script>
