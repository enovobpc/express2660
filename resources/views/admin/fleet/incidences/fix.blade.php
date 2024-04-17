{{ Form::model($incidence, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Resolver Ocorrência ou Sinistro')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <h5 class="text-blue">@trans('Para resolver este problema, foi realizada alguma manutenção?')</h5>
            <div class="form-group">
                {{ Form::label('maintenance_id', __('Manutenção associada')) }}
                {{ Form::select('maintenance_id', ['' => ''] + $maintenances, null, ['class' => 'form-control select2', 'required']) }}
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
</script>

