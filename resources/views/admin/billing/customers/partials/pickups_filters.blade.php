<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-pickups">
    @if(!$customer->billing_closed)
        <li>
            <a href="{{ route('admin.billing.customers.shipments.update-prices', [$customer->id, 'month' => $month, 'year' => $year]) }}"
               class="btn btn-sm btn-success"
               data-method="post" data-confirm="Confirma a atualização de preços?<br/><br/><small class='text-red m-t-10px'><i class='fas fa-exclamation-triangle'></i> Envios com preços superiores ao calculado, não serão alterados.<br/><i class='fas fa-exclamation-triangle'></i> Não serão calculados preços para envios com pagamento no destino.</small>" data-confirm-title="Confirmar atualização de preços" data-confirm-label="Atualizar" data-confirm-class="btn-success">
                <i class="fas fa-sync-alt"></i> Atualizar Preços
            </a>
        </li>
    @endif
    <li>
        <a href="{{ route('admin.shipments.generate-shipments-from-pickups', ['year'=>$year, 'month' => $month, 'customer' => $customer->id]) }}"
           data-method="post"
           data-confirm="Pretende gerar ou associar automaticamente as recolhas ao respetivo envio?"
           data-confirm-title="Gerar envios automático"
           data-confirm-label="Gerar Envios"
           data-confirm-class="btn-success"
           class="btn btn-sm btn-default">
            <i class="fas fa-fw fa-sync-alt"></i> Gerar envios auto
        </a>
    </li>
    <li>
        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
        </button>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-pickups">
    <ul class="list-inline pull-left">
        <li class="col-xs-12">
            <strong>Data</strong><br/>
            <div class="input-group input-group-sm w-220px">
                {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                <span class="input-group-addon">até</span>
                {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
            </div>
        </li>
        @if(@$departments)
            <li style="margin-bottom: 5px;" class="col-xs-6">
                <strong>Departamento</strong><br/>
                <div class="w-150px">
                    {{ Form::select('department', ['' => 'Todos'] + $departments + ['-1' => 'Sem departamento'], Request::has('department') ? Request::get('department') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
        @endif
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Serviço</strong><br/>
            <div class="w-150px">
                {{ Form::select('service', ['' => 'Todos'] + $services + ['-1' => '- Sem serviço -'], Request::has('service') ? Request::get('service') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Fornecedor</strong><br/>
            <div class="w-150px">
                {{ Form::select('provider', ['' => 'Todos'] + $providers, Request::has('provider') ? Request::get('provider') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>País Origem</strong><br/>
            <div class="w-100px">
                {{ Form::select('sender_country', ['' => 'Todos'] + trans('country'), Request::has('sender_country') ? Request::get('sender_country') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>País Destino</strong><br/>
            <div class="w-100px">
                {{ Form::select('recipient_country', ['' => 'Todos'] + trans('country'), Request::has('recipient_country') ? Request::get('recipient_country') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>Zona</strong><br/>
            <div class="w-150px">
                {{ Form::select('ship_zone', ['' => 'Todos'] + $billingZones, Request::has('ship_zone') ? Request::get('ship_zone') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>A. Pagam.</strong><br/>
            <div class="w-200px">
                {{ Form::select('agency', ['' => 'Todos'] + $agencies, Request::has('agency') ? Request::get('agency') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>A. Destino</strong><br/>
            <div class="w-200px">
                {{ Form::select('recipient_agency', ['' => 'Todos'] + $agencies, Request::has('recipient_agency') ? Request::get('recipient_agency') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Estado</strong><br/>
            <div class="w-150px">
                {{ Form::select('status', ['' => 'Todos'] + $status, Request::has('status') ? Request::get('status') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Operador</strong><br/>
            <div class="w-160px">
                {{ Form::select('operator', array('' => 'Todos', 'not-assigned' => 'Sem operador') + $operators, Request::has('operator') ? Request::get('operator') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>Sem Preço</strong><br/>
            {{ Form::select('price', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('price') ? Request::get('price') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>Sem Envio Gerado</strong><br/>
            {{ Form::select('empty_children', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('empty_children') ? Request::get('empty_children') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </li>
    </ul>
</div>