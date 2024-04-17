{{ Form::open(['route' => 'admin.maps.load.operator.history']) }}
<div class="row row-5">
    <div class="col-sm-7" style="width: 65%">
        <div class="form-group m-b-5">
            {{ Form::label('operator', 'Histórico do Operador', ['class' => 'form-'] ) }}
            {{ Form::select('operator', ['' => ''] + $operatorsList, null, ['class' => 'form-control select2']) }}
        </div>
    </div>
    <div class="col-sm-5" style="width: 35%">
        <div class="form-group m-b-5">
            {{ Form::label('date', 'Data', ['class' => 'form-'] ) }}
            {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker', 'autocomplete' => 'off', 'style' => 'padding-left: 8px; padding-right: 0;']) }}
        </div>
    </div>
</div>
{{ Form::close() }}
<div class="history-list nicescroll" style="top: 120px"></div>