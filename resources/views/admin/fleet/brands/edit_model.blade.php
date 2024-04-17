{{ Form::model($model, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('brand_id', __('Marca')) }}
                {{ Form::select('brand_id', ['' => ''] + $brands, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('name', __('Modelo')) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::hidden('delete_photo') }}
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
</script>

