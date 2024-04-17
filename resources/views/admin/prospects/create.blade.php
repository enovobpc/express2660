{{ Form::open(['route' => ['admin.prospects.store']]) }}
<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Novo Potencial Cliente')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('name', __('Designação Social')) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('agency_id', __('Agência')) }}
                {{ Form::select('agency_id', [''=>''] + $agencies, null, ['class' => 'form-control input-sm select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('type_id', __('Tipo de Cliente')) }}
                {{ Form::select('type_id', [''=>''] + $types, null, ['class' => 'form-control input-sm select2', 'required']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        {{ Form::hidden('business_status', 'pending') }}
        <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
        <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">@trans('Gravar')</button>
    </div>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2())
</script>