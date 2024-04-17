{{ Form::open(['route' => 'admin.maps.load.operator.deliveries']) }}
<div class="row row-5">
    <div class="col-sm-7" style="width: 65%">
        <div class="form-group m-b-5">
            {{ Form::label('operator', 'Entregas do Operador', ['class' => 'form-'] ) }}
            {{ Form::select('operator', ['' => ''] + $operatorsList, null, ['class' => 'form-control select2']) }}
        </div>
    </div>
    <div class="col-sm-5" style="width: 35%">
        <div class="form-group m-b-5">
            {{ Form::label('date', 'Data Entrega', ['class' => 'form-'] ) }}
            {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker', 'autocomplete' => 'off', 'style' => 'padding-left: 8px; padding-right: 0;']) }}
        </div>
    </div>
</div>
{{--<button type="button" class="btn btn-sm btn-success search-deliveries">Consultar</button>--}}
{{--
<div class="btn-group btn-group-sm">
    <button type="button" class="btn btn-default trace-operator-directions"><i class="fas fa-location-arrow"></i> Traçar Percurso</button>
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
        <i class="fas fa-cog"></i> <span class="caret"></span>
    </button>
    <ul class="dropdown-menu deliveries-route-options" role="menu">
        <li>
            <a href="#">
                {{ Form::checkbox('avoid_highways', 1, null) }}
                Evitar Autoestradas
            </a>
        </li>
        <li>
            <a href="#">
                {{ Form::checkbox('avoid_tolls', 1, null) }} Evitar Portagens
            </a>
        </li>
    </ul>
</div>
--}}
{{ Form::close() }}
<div class="deliveries-list nicescroll" style="top: 120px"></div>