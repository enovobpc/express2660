{{ Form::model($attachment, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    @if(in_array($attachment->parent_id, $guardedFolders))
        <div class="form-group is-required">
            {{ Form::label('source_id', __('Associar anexo ao registo...')) }}
            {{ Form::select('source_id', [], null, ['class' => 'form-control', 'required']) }}
        </div>
    @endif
    <div class="form-group is-required">
        {{ Form::label('name', $attachment->is_folder ? __('Nome da pasta') : __('Título do ficheiro')) }}
        {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
    </div>
    @if(!$attachment->exists && !$attachment->is_folder)
        <div class="form-group is-required">
            {{ Form::label('name', __('Ficheiro a carregar')) }}
            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                <div class="form-control" data-trigger="fileinput">
                    <i class="fas fa-file fileinput-exists"></i>
                    <span class="fileinput-filename"></span>
                </div>
                <span class="input-group-addon btn btn-default btn-file">
                <span class="fileinput-new">@trans('Procurar...')</span>
                <span class="fileinput-exists">@trans('Alterar')</span>
                <input type="file" name="file" required>
            </span>
                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
            </div>
        </div>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::hidden('is_folder') }}
{{ Form::hidden('target_id') }}
{{ Form::hidden('parent_id') }}
{{ Form::close() }}

<script>
    $("select[name=source_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.repository.search.source', ['parent' => $attachment->parent_id]) }}")
    });

</script>