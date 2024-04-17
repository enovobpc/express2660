<ul class="list-inline">
    <li>
        <strong>Operador</strong><br/>
        {{ Form::select('operator', array('' => 'Todos') + $operators, Request::has('operator') ? Request::get('operator') : null, array('class' => 'form-control input-sm filter-datatable')) }}
    </li>
    <li>
        <strong>Agente</strong><br/>
        {{ Form::select('agent', array('' => 'Todos') + $agents, Request::has('agent') ? Request::get('agent') : null, array('class' => 'form-control input-sm filter-datatable')) }}
    </li>
    <li>
        <strong>HAWB</strong><br/>
        {{ Form::select('has_hawb', array('' => 'Todos', '1' => 'Com HAWB', '0' => 'Sem HAWB'), Request::has('has_hawb') ? Request::get('has_hawb') : null, array('class' => 'form-control input-sm filter-datatable')) }}
    </li>
    <li style="margin-bottom: -14px;">
        <strong>Transportador</strong><br/>
        <div class="input-group">
            <div class="w-150px pull-left">
                {{ Form::select('provider', ['' => 'Todos'] + $providers, Request::has('provider') ? Request::get('provider') : null, array('class' => 'form-control input-sm filter-datatable select2 w-100')) }}
            </div>
        </div>
    </li>
    <li style="margin-bottom: -14px;">
        <strong>Aeroporto Origem</strong><br/>
        <div class="input-group">
            <div class="w-150px pull-left">
                {{ Form::select('source_airport', array('' => 'Todos'), Request::has('source_airport') ? Request::get('source_airport') : null, array('class' => 'form-control input-sm w-100 filter-datatable')) }}
            </div>
            <span class="input-group-btn">
                <button class="btn btn-sm btn-default clean-select" type="button"><i class="fas fa-times"></i></button>
            </span>
        </div>
    </li>
    <li style="margin-bottom: -14px;">
        <strong>Aeroporto Destino</strong><br/>
        <div class="input-group">
            <div class="w-150px pull-left">
                {{ Form::select('recipient_airport', array('' => 'Todos'), Request::has('recipient_airport') ? Request::get('recipient_airport') : null, array('class' => 'form-control input-sm w-100 filter-datatable')) }}
            </div>
            <span class="input-group-btn">
                <button class="btn btn-sm btn-default clean-select" type="button"><i class="fas fa-times"></i></button>
            </span>
        </div>
    </li>
    <li style="margin-bottom: -14px;">
        <strong>Expedidor</strong><br/>
        <div class="input-group">
            <div class="w-150px pull-left">
                {{ Form::select('customer', array('' => 'Todos'), Request::has('customer') ? Request::get('customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable')) }}
            </div>
            <span class="input-group-btn">
                <button class="btn btn-sm btn-default clean-select" type="button"><i class="fas fa-times"></i></button>
            </span>
        </div>
    </li>
    <li>
        <strong>Faturado</strong><br/>
        {{ Form::select('billed', array('' => 'Todos', '1' => 'Faturado', '0' => 'Não Faturado'), Request::has('billed') ? Request::get('billed') : null, array('class' => 'form-control input-sm filter-datatable')) }}
    </li>
    {{--<li>
        <div class="checkbox">
            <label>
                {{ Form::checkbox('hide_final_status', 1, Setting::get('shipments_hide_final_status')) }}
                {{ Route::currentRouteName() == 'admin.shipments.index' ? 'Ocultar Entregues' : 'Ocultar Finalizados' }}
            </label>
        </div>
    </li>--}}
</ul>