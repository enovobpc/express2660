<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Histórico Mensal</h4>
</div>
<div class="modal-body">
    {{ Form::open(array('route' => array('admin.budgets.courier.stats'), 'method' => 'get', 'class' => 'form-history')) }}
    <div class="row row-5">
        <div class="col-sm-2">
            {{ Form::label('date_min', 'Data Inicio') }}
            {{ Form::text('date_min', $start, ['class' => 'form-control input-sm datepicker']) }}
        </div>
        <div class="col-sm-2">
            {{ Form::label('date_max', 'Data Fim') }}
            {{ Form::text('date_max', $end, ['class' => 'form-control input-sm datepicker']) }}
        </div>
        <div class="col-sm-3">
            {{ Form::label('operator', 'Operador') }}
            <span class="input-sm" style=" margin-left: -10px;">
            {{ Form::select('operator', ['' => 'Todos'] + $operators, null, ['class' => 'form-control select2']) }}
            </span>
        </div>
        <div class="col-sm-2">
            {{ Form::label('web', 'Web') }}
            <span class="input-sm" style=" margin-left: -10px;">
            {{ Form::select('web', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], null, ['class' => 'form-control select2']) }}
            </span>
        </div>
        <div class="col-sm-2">
            {{ Form::label('type', 'Tipo') }}
            <span class="input-sm" style=" margin-left: -10px;">
            @if(hasModule('budgets_animals'))
            {{ Form::select('type', ['' => 'Todos', 'courier' => 'Carga Geral', 'animals' => 'Animais'], null, ['class' => 'form-control select2']) }}
            @else
            {{ Form::select('type', ['' => 'Todos', 'courier' => 'Carga Geral'], null, ['class' => 'form-control select2']) }}
            @endif
            </span>
        </div>
        <div class="col-sm-1">
            <div style="height: 20px;"></div>
            {{ Form::hidden('filter', 1) }}
            <button type="submit" class="btn btn-sm btn-block btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A filtrar..."><i class="fas fa-search"></i></button>
        </div>
    </div>
    {{ Form::close() }}
    <div class="sp-15"></div>
    <div class="stats-table">
        @include('admin.budgets.budgets_courier.partials.stats_table')
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    </div>
</div>

<script>
    $('.select2').select2(Init.select2());

    $('.form-history').on('submit', function (e) {
        e.preventDefault();

        var $form = $('.form-history');
        var btn = $form.find('button')

        $.get($form.attr('action'), $form.serialize(), function (data) {
            $('.stats-table').html(data.html)
        }).always(function(){
            btn.button('reset');
        });
    })
</script>