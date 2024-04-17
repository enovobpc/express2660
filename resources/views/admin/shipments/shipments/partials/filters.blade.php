<ul class="list-inline pull-left m-b-5">
    <li style="margin-bottom: 5px;" class="form-group-sm hidden-xs col-xs-12">
        <strong>@trans('Filtar Data')</strong><br/>
        <div class="w-100px m-r-4" style="position: relative; z-index: 5;">
            @if(in_array(Setting::get('app_mode'),['move']))
                {{ Form::select('date_unity', ['' => 'Previsão Recolha', 'delivery' => 'Previsão Entrega', '3' => 'Transporte', '4' => 'Distribuição', '35' => 'Recolhido', '5' => 'Entregue', '9' => 'Incidência', 'billing' => 'Faturação', 'creation' => 'Data Registo'], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            @else
                {{ Form::select('date_unity', ['' => 'Data Envio', '3' => 'Transporte', '4' => 'Distribuição', '35' => 'Recolhido', '5' => 'Entregue', '9' => 'Incidência', 'delivery' => 'Previsão Entrega', 'billing' => 'Faturação', 'creation' => 'Data Registo'], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            @endif
        </div>
    </li>
    <li class="shp-date col-xs-12">
        <strong>@trans('Data')</strong><br/>
        <div class="input-group input-group-sm w-220px">
            {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
            <span class="input-group-addon">@trans('até')</span>
            {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-12">
        <strong>@trans('Serviço')</strong><br/>
        <div class="w-140px">
            {{ Form::selectMultiple('service', $services, fltr_val(Request::all(), 'service'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-12">
        <strong>@trans('Fornecedor')</strong><br/>
        <div class="w-120px">
            {{ Form::selectMultiple('provider', $providers, fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <small style="position: absolute; right: 5px"><a href="#" class="fltr-sall">@trans('Todos')</a></small>
        <strong>@trans('Motorista')</strong><br/>
        <div class="w-120px">
            {{ Form::selectMultiple('operator', array('not-assigned' => __('Sem operador')) + $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <small style="position: absolute; right: 5px"><a href="#" class="fltr-sall">@trans('Todos')</a></small>
        <strong>@trans('Motorista Rec.')</strong><br/>
        <div class="w-120px">
            {{ Form::selectMultiple('operator_pickup', array('not-assigned' => 'Sem operador') + $operators, fltr_val(Request::all(), 'operator_pickup'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    @if($cargoMode)
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <small style="position: absolute; right: 5px"><a href="#" class="fltr-sall">@trans('Todos')</a></small>
        <strong>@trans('Gestor Tráfego')</strong><br/>
        <div class="w-120px">
            {{ Form::selectMultiple('dispatcher', array('not-assigned' => 'Sem Gestor') + $users, fltr_val(Request::all(), 'dispatcher'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    @endif
    @if(@$workgroups)
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('Grupo Trabalho')</strong><br/>
            <div class="w-120px">
                {{ Form::selectMultiple('workgroups', $workgroups, fltr_val(Request::all(), 'workgroups',  Auth::user()->workgroups_arr ? implode(',', Auth::user()->workgroups_arr) : null), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
    @endif
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>@trans('Viatura')</strong><br/>
        <div class="w-100px">
            {{ Form::select('vehicle', array('' => __('Todos'), '-1' => 'Sem viatura') + $vehicles, fltr_val(Request::all(), 'vehicle'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    @if(@$trailers)
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>@trans('Reboque')</strong><br/>
        <div class="w-100px">
            {{ Form::select('trailer', array('' => __('Todos'), '-1' => 'Sem reboque') + $trailers, fltr_val(Request::all(), 'trailer'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    @endif
    @if(@$routes)
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>@trans('Rota Recolha')</strong><br/>
            <div class="w-100px">
                {{ Form::select('pickup_route', array('' => __('Todos'), '-1' => 'Sem rota') + $routes, fltr_val(Request::all(), 'pickup_route'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>@trans('Rota Entrega')</strong><br/>
            <div class="w-100px">
                {{ Form::select('route', array('' => __('Todos'), '-1' => 'Sem rota') + $routes, fltr_val(Request::all(), 'routes'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
    @endif
    {{--<li style="margin-bottom: 5px;" class="col-xs-12">
        <strong>Agência Responsável</strong><br/>
        <div class="w-150px">
            {{ Form::selectMultiple('agency',$agencies, fltr_val(Request::all(), 'agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>--}}
    @if(count($agencies) > 1)
    <li style="margin-bottom: 5px;" class="col-xs-12">
        <strong>@trans('Agência Origem')</strong><br/>
        <div class="w-150px">
            {{ Form::selectMultiple('sender_agency', $agencies, fltr_val(Request::all(), 'sender_agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-12">
        <strong>@trans('Agência Destino')</strong><br/>
        <div class="w-150px">
            {{ Form::selectMultiple('recipient_agency',$agencies, fltr_val(Request::all(), 'recipient_agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    @endif
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('País Origem')</strong><br/>
        <div class="w-100px">
            {{ Form::select('fltr_sender_country', ['' => __('Todos')] + trans('country'), fltr_val(Request::all(), 'fltr_sender_country'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('País Destino')</strong><br/>
        <div class="w-100px">
            {{ Form::select('fltr_recipient_country', ['' => __('Todos')] + trans('country'), fltr_val(Request::all(), 'fltr_recipient_country'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('Distrito Destino')</strong><br/>
        <div class="w-120px">
            {!! Form::selectMultiple('fltr_recipient_district', array('not-assigned' => 'Todos') + trans('districts_codes.districts.pt'), fltr_val(Request::all(), 'fltr_recipient_district'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) !!}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('Concelho Destino') <i class="fas fa-spin fa-circle-notch load-county" style="display: none"></i></strong><br/>
        <div class="w-120px">
            {!! Form::selectMultiple('fltr_recipient_county', array('not-assigned' => 'Todos') + @$recipientCounties, fltr_val(Request::all(), 'fltr_recipient_county'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) !!}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('CP Destino')</strong><br/>
        <div class="w-80px" data-toggle="tooltip" title="Separe vários codigos postais por vírgula.">
            {{ Form::text('fltr_recipient_zp', fltr_val(Request::all(), 'fltr_recipient_zp'), array('class' => 'form-control input-sm filter-datatable', 'style' => 'width:100%')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('Cobrança')</strong><br/>
        <div class="w-80px">
            {{ Form::select('charge', array('' => __('Todos'), '1' => 'Sim', '0' => 'Não'), fltr_val(Request::all(), 'charge'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('Portes')</strong><br/>
        <div class="w-80px">
            {{ Form::select('cod', ['' => __('Todos'), 'C' => 'Cliente', 'D' => 'Destino', 'S' => 'Remetente', 'P' => 'Pagos (excluir faturação)'], fltr_val(Request::all(), 'cod'), ['class' => 'form-control input-sm filter-datatable select2']) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('Pago')</strong><br/>
        <div class="w-80px">
            {{ Form::select('ignore_billing', array('' => __('Todos'), '1' => 'Sim', '0' => 'Não'), fltr_val(Request::all(), 'ignore_billing'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    @if(hasModule('invoices'))
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('Fatura')</strong><br/>
        <div class="w-80px">
        {{ Form::select('invoice', array('' => __('Todos'), '1' => 'Sim', '0' => 'Não'), fltr_val(Request::all(), 'invoice'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('Encargos')</strong><br/>
        <div class="w-80px">
            {{ Form::select('expenses', array('' => __('Todos'), '1' => 'Sim', '0' => 'Não'), fltr_val(Request::all(), 'expenses'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('Tipo Encargo')</strong><br/>
        <div class="w-150px">
            {{ Form::selectMultiple('expense_type', ['rpack'=>'Retorno Encomenda']+$expenses, fltr_val(Request::all(), 'expense_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple', 'data-placeholder' => 'Todos')) }}
        </div>
    </li>
    @endif
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('Outros Filtros')</strong><br/>
        <div class="w-100px">
            {{ Form::select('shp_type', ['' => __('Todos'), 'sync-error' => 'Webservice - Erro', 'sync-no' => 'Webservice - Não Submetido', 'sync-yes' => 'Webservice - Submetido', 'pod_signature'=>'POD com Assinatura', 'pod_file'=>'POD com anexo', 'readed' => 'Com Leitura Chegada/Saida', 'unreaded' => 'Sem Leitura Chegada/Saida', 'api' => 'Criado via API', 'closed'=> 'Por Fechar', 'pudo'=> 'Entrega Ponto Pickup', 'noprice'=> 'Sem preço', 'S' => 'Envios', 'M' => 'Agrupados/Grupagens', 'R' => 'Retornos', 'P' => 'Recolhas', 'D' => 'Devoluções'], fltr_val(Request::all(), 'shp_type'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('Bloqueado')</strong><br/>
        <div class="w-80px">
            {{ Form::select('blocked', array('' => __('Todos'), '1' => 'Sim', '0' => 'Não'), fltr_val(Request::all(), 'blocked'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    @if(Setting::get('shipment_list_show_conferred'))
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('Conferido')</strong><br/>
        <div class="w-80px">
            {{ Form::select('customer_conferred', ['' => __('Todos'), '1' => 'Sim', '0' => 'Não'], Request::has('customer_conferred') ? Request::get('customer_conferred') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    @endif
    <li style="margin-bottom: 5px;" class="col-xs-6">
        <strong>@trans('Impresso')</strong><br/>
        <div class="w-80px">
            {{ Form::select('printed', array('' => __('Todos'), '1' => 'Sim', '0' => 'Não'), fltr_val(Request::all(), 'printed'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>@trans('Cliente')</strong><br/>
        <div class="w-250px">
            {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>@trans('Tipo Cliente')</strong><br/>
        <div class="w-130px">
            {{ Form::select('customer_type', ['' => __('Todos')] + $customerTypes, Request::has('customer_type') ? Request::get('customer_type') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>Vendedor</strong><br/>
        <div class="w-130px">
            {{ Form::select('seller', ['' => 'Todos'] + $sellers, Request::has('seller') ? Request::get('seller') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="col-xs-12">
        <strong>@trans('Volumes')</strong><br/>
        <div class="input-group input-group-sm w-130px">
            {{ Form::text('volumes_min', fltr_val(Request::all(), 'volumes_min'), ['class' => 'form-control filter-datatable w-20px', 'placeholder' => 'Min', 'autocomplete' => 'field-1']) }}
            <span class="input-group-addon">@trans('até')</span>
            {{ Form::text('volumes_max', fltr_val(Request::all(), 'volumes_max'), ['class' => 'form-control filter-datatable w-20px', 'placeholder' => 'Max', 'autocomplete' => 'field-1']) }}
        </div>
    </li>
    <li class="col-xs-12">
        <strong>@trans('Peso')</strong><br/>
        <div class="input-group input-group-sm w-160px">
            {{ Form::text('weight_min', fltr_val(Request::all(), 'weight_min'), ['class' => 'form-control filter-datatable w-20px', 'placeholder' => 'Min', 'autocomplete' => 'field-1']) }}
            <span class="input-group-addon">@trans('até')</span>
            {{ Form::text('weight_max', fltr_val(Request::all(), 'weight_max'), ['class' => 'form-control filter-datatable w-20px', 'placeholder' => 'Max', 'autocomplete' => 'field-1']) }}
        </div>
    </li>
    @if(@$trips)
    <li style="margin-bottom: 5px;"  class="col-xs-6">
        <strong>{{ Setting::get('app_mode') == 'cargo' ? __('Mapa Viagem') : __('Mapa Distribuição') }}</strong><br/>
        <div class="w-130px">
            {{ Form::select('trips', ['' => __('Todos'), '-1' => 'Sem mapa'] + $trips, Request::has('trips') ? Request::get('trips') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}

        </div>
    </li>
    @endif
   @if(Auth::user()->isAdmin())
        <li style="margin-bottom: 5px; display: none;" class="col-xs-12">
            <strong>Plataforma</strong><br/>
            <div class="w-100px">
                {{ Form::select('source', ['' => ''] + $sources, fltr_val(Request::all(), 'source', config('app.source')), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
    @endif
</ul>
<ul class="list-inline pull-left m-b-5">
    <li style="width: 145px">
        <div class="checkbox">
            <label>
                {{ Form::checkbox('hide_final_status', 1, Request::has('hide_final_status') ? Request::get('hide_final_status') : Setting::get('shipments_hide_final_status')) }}
                @trans('Ocultar Finalizados')
            </label>
        </div>
    </li>
    <li style="width: 145px">
        <div class="checkbox">
            <label>
                {{ Form::checkbox('hide_scheduled', 1, Request::has('hide_scheduled') ? Request::get('hide_scheduled') : Setting::get('shipments_hide_scheduled')) }}
                @trans('Ocultar Agendados')
            </label>
        </div>
    </li>
    @if(Setting::get('shipments_limit_search'))
        <li style="width: 130px">
            <div class="checkbox">
                <label>
                    {{ Form::checkbox('limit_search', 1, Request::has('limit_search') ? Request::get('limit_search') : true) }}
                    @trans('Últimos :months meses', ['months' => Setting::get('shipments_limit_search')])
                </label>
            </div>
        </li>
    @endif
    <li style="width: 95px">
        <div class="checkbox">
            <label>
                {{ Form::checkbox('deleted', 1, Request::has('deleted') ? Request::get('deleted') : Setting::get('deleted')) }}
                @trans('Apagados')
            </label>
        </div>
    </li>
    <li>
        <a href="{{ route('admin.shipments.index') }}" class="cleanflr">
            <i class="fas fa-eraser"></i> @trans('Limpar Filtros')
        </a>
    </li>
</ul>