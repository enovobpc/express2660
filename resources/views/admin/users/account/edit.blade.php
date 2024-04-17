{{ Form::open(array('route' => array('admin.account.update'))) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Editar as minhas definições')</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('locale', __('Idioma')) }}
        {{ Form::select('locale', trans('locales'), Auth::user()->locale, ['class' => 'form-control select2country']) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}
<script>
    $('.select2country').select2(Init.select2Country());
</script>