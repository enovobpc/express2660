<ul class="list-inline pull-left">
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>Estado Envio</strong><br/>
        <div class="w-120px">
            {{ Form::selectMultiple('shipment_status', $status, Request::has('shipment_status') ? Request::get('shipment_status') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="form-group-sm hidden-xs col-xs-12">
        <strong>Tipo Data</strong><br/>
        <div class="w-100px m-r-4" style="position: relative; z-index: 5;">
            {{ Form::select('date_unity', ['' => 'Data Envio', '5' => 'Entregue', '3' => 'Transporte', '4' => 'Distribuição'], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="shp-date col-xs-12">
        <strong>Data Envio</strong><br/>
        <div class="input-group input-group-sm w-220px">
            {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
            <span class="input-group-addon">até</span>
            {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
        </div>
    </li>
    <li class="col-xs-12">
        <strong>Data Recebimento</strong><br/>
        <div class="input-group input-group-sm w-220px">
            {{ Form::text('received_date_min', fltr_val(Request::all(), 'received_date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
            <span class="input-group-addon">até</span>
            {{ Form::text('received_date_max', fltr_val(Request::all(), 'received_date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
        </div>
    </li>
    <li class="col-xs-12">
        <strong>Data Devolução</strong><br/>
        <div class="input-group input-group-sm w-220px">
            {{ Form::text('payment_date_min', fltr_val(Request::all(), 'payment_date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
            <span class="input-group-addon">até</span>
            {{ Form::text('payment_date_max', fltr_val(Request::all(), 'payment_date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>Forma Recebimento</strong><br/>
        <div class="w-160px">
            {{ Form::selectMultiple('received_method', trans('admin/refunds.payment-methods-list'), Request::has('received_method') ? Request::get('received_method') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>Forma Devolução</strong><br/>
        <div class="w-160px">
            {{ Form::selectMultiple('payment_method', trans('admin/refunds.payment-methods-list'), Request::has('payment_method') ? Request::get('payment_method') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>Forma Receb. Operador</strong><br/>
        <div class="w-160px">
            {{ Form::selectMultiple('operator_received_method', trans('admin/refunds.payment-methods-list'), Request::has('operator_received_method') ? Request::get('operator_received_method') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    @if(config('app.source') == 'asfaltolargo' && in_array(Auth::user()->id, [2,695,6,667]))
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>Recebimento</strong><br/>
        <div class="w-160px">
            {{ Form::selectMultiple('received_user', $users, Request::has('received_user') ? Request::get('received_user') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>Pago por</strong><br/>
        <div class="w-160px">
            {{ Form::selectMultiple('payment_user', $users, Request::has('payment_user') ? Request::get('payment_user') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    @endif

    @if(config('app.source') == 'rlrexpress')
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Atraso</strong><br/>
            <div class="w-100px">
                {{ Form::select('delayed_reception', ['' => 'Todos', '1' => 'Recepção em atraso'], Request::has('delayed_reception') ? Request::get('delayed_reception') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
    @endif
    
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>Fornecedor</strong><br/>
        <div class="w-120px">
            {{ Form::selectMultiple('provider', $providers, Request::has('provider') ? Request::get('provider') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
   
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>Rota</strong><br/>
        <div class="w-120px">
            {{ Form::selectMultiple('route', ['not-assigned' => 'Sem rota'] + $routes, fltr_val(Request::all(), 'route'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    {{-- <li style="margin-bottom: 5px;" class="col-xs-12">
        <strong>Agência Responsável</strong><br/>
        <div class="w-150px">
            {{ Form::selectMultiple('agency',$agencies, fltr_val(Request::all(), 'agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li> --}}
    <li style="margin-bottom: 5px;" class="col-xs-12">
        <strong>Agência Origem</strong><br/>
        <div class="w-150px">
            {{ Form::selectMultiple('sender_agency', $agencies, fltr_val(Request::all(), 'sender_agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-12">
        <strong>Agência Destino</strong><br/>
        <div class="w-150px">
            {{ Form::selectMultiple('recipient_agency',$agencies, fltr_val(Request::all(), 'recipient_agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>País Origem</strong><br/>
        <div class="w-100px">
            {{ Form::select('fltr_sender_country', ['' => 'Todos'] + trans('country'), fltr_val(Request::all(), 'fltr_sender_country'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>País Destino</strong><br/>
        <div class="w-100px">
            {{ Form::select('fltr_recipient_country', ['' => 'Todos'] + trans('country'), fltr_val(Request::all(), 'fltr_recipient_country'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>Confirmado</strong><br/>
        <div class="w-100px">
            {{ Form::select('confirmed', ['' => 'Todos', '1' => 'Confirmado', '0' => 'Por confirmar'], Request::has('confirmed') ? Request::get('confirmed') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li>
        <a href="{{ route('admin.refunds.customers.index') }}" class="cleanflr m-t-20" style="display: inline-block">
            <i class="fas fa-eraser"></i> Limpar Filtros
        </a>
    </li>
</ul>