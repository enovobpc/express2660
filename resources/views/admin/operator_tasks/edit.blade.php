{{ Form::model($task, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('name', 'Tarefa') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('status', 'Estado') }}
                {{ Form::select('status', ['pending' => 'Pendente', 'accepted' => 'Aceite', 'concluded' => 'Concluído'], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('date', 'Data') }}
                {{ Form::text('date', null, ['class' => 'form-control datepicker', 'autocomplete' => 'off']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('start_hour', 'Hora Início') }}
                {{ Form::select('start_hour', ['' => '--:--'] + listHours(5), null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('end_hour', 'Hora Fim') }}
                {{ Form::select('end_hour', ['' => '--:--'] + listHours(5), null, ['class' => 'form-control select2']) }}
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('operator_id', 'Operador') }}
                {{ Form::select('operator_id', ['' => '-- Nenhum --'] + $operators, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('description', 'Detalhes') }}
        {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}
<script>
    $('#modal-edit-operator-task .select2').select2(Init.select2());
    $('#modal-edit-operator-task .datepicker').datepicker(Init.datepicker());
</script>
