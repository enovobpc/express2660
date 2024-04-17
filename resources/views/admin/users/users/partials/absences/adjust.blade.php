{{ Form::model($absence, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('type_id', __('Tipo de Ajuste')) }}
                {!! Form::selectWithData('type_id', $types, null, ['class' => 'form-control select2', 'required']) !!}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('duration', __('Número de dias/horas')) }}
                {{ Form::text('duration', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('period', __('Período')) }}
                {{ Form::select('period', ['days' => 'dias','hours' => 'horas'], @$absence->period, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>
    <div class="form-group m-b-0">
        {{ Form::label('obs', __('Observações')) }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
    </div>
</div>
{{ Form::close() }}

<script>
   $('.select2').select2(Init.select2());
</script>