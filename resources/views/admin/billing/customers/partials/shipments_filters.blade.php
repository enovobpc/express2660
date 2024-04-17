<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-shipments">
    @if(!$customer->billing_closed && hasModule('invoices'))
    <li>
        <a href="{{ route('admin.billing.customers.edit', [$customer->id, 'month' => $month, 'year' => $year, 'period' => $period] + Request::all()) }}"
           class="btn btn-sm btn-success"
            data-export-url="export"
            data-toggle="modal"
            data-target="#modal-remote-xl">
                <i class="fas fa-check"></i> Faturar Lista
        </a>
    </li>
    @endif
    <li>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-wrench"></i> Ferramentas <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.export.billing.customers.shipments', [$customer->id, 'month' => $month, 'year' => $year, 'period' => $period] + Request::all()) }}"
                       data-export-url="export"
                       target="_blank">
                       <i class="fas fa-fw fa-file-excel"></i> Exportar lista atual
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.export.shipments.dimensions', ['customer'=>$customer->id, 'month' => $month, 'year' => $year, 'billing_period' => $period] + Request::all()) }}" 
                        data-export-url="export"
                        target="_blank">
                        <i class="fas fa-fw fa-file-excel"></i> Exportar listagem mercadoria
                    </a>
                </li>

                @if(!$customer->billing_closed)
                    <li class="divider"></li>
                    <li>
                       <a href="{{ route('admin.billing.customers.shipments.update-prices', [$customer->id, 'month' => $month, 'year' => $year]) }}"
                          class="text-green"
                          data-method="post" data-confirm="Confirma a atualização de preços?<br/><br/><small class='text-info m-t-10px'><i class='fas fa-exclamation-circle'></i> Não serão calculados preços para envios com pagamento no destino.<br/><i class='fas fa-exclamation-circle'></i> Envios com preço bloqueado ou já faturados não serão considerados.</small>" data-confirm-title="Confirmar atualização de preços" data-confirm-label="Atualizar" data-confirm-class="btn-primary">
                           <i class="fas fa-sync-alt"></i> Atualizar todos preços
                       </a>
                    </li>
                @endif
            </ul>
        </div>
    </li>
    <li>
        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
        </button>
    </li>
    @if(count($billedItems['invoices']) > 1)
        <li class="fltr-primary w-170px">
            <strong>Fatura</strong><br class="visible-xs"/>
            <div class="w-100px pull-left form-group-sm">
                {{ Form::select('invoice', ['' => 'Todas'] + $billedItems['invoices'], Request::has('invoice') ? Request::get('invoice') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
    @endif
    @if(@$departments)
    <li class="fltr-primary w-180px">
        <strong>Dpto</strong><br class="visible-xs"/>
        <div class="w-140px pull-left form-group-sm">
            {{ Form::select('department', ['' => 'Todos'] + $departments + ['-1' => 'Sem departamento'], Request::has('department') ? Request::get('department') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    @endif
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-shipments">
    <ul class="list-inline pull-left">
        <li class="col-xs-12">
            <strong>Data</strong><br/>
            <div class="input-group input-group-sm w-220px">
                {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                <span class="input-group-addon">até</span>
                {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
            </div>
        </li>
       {{-- @if(@$departments)
            <li style="margin-bottom: 5px;" class="col-xs-6">
                <strong>Departamento</strong><br/>
                <div class="w-150px">
                    {{ Form::select('department', ['' => 'Todos'] + $departments + ['-1' => 'Sem departamento'], Request::has('department') ? Request::get('department') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
        @endif--}}
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Serviço</strong><br/>
            <div class="w-150px">
                {{ Form::selectMultiple('service', $services, fltr_val(Request::all(), 'service'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
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
                {{ Form::selectMultiple('status', $status, fltr_val(Request::all(), 'status'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Operador</strong><br/>
            <div class="w-160px">
                {{ Form::select('operator', array('' => 'Todos', 'not-assigned' => 'Sem operador') + $operators, Request::has('operator') ? Request::get('operator') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Encargos</strong><br/>
            <div class="w-80px">
                {{ Form::select('expenses', array('' => 'Todos', '1' => 'Sim', '0' => 'Não'), fltr_val(Request::all(), 'expenses'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Tipo Encargo</strong><br/>
            <div class="w-150px">
                {{ Form::selectMultiple('expense_type', $expensesTypes, fltr_val(Request::all(), 'expense_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple', 'data-placeholder' => 'Todos')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>Retorno</strong><br/>
            {{ Form::select('return', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('return') ? Request::get('return') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>Cobrança</strong><br/>
            {{ Form::select('charge_price', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('charge_price') ? Request::get('charge_price') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>Confirmado</strong><br/>
            {{ Form::select('conferred', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('conferred') ? Request::get('conferred') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>Faturado</strong><br/>
            {{ Form::select('billed', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('billed') ? Request::get('billed') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>Preço Bloq.</strong><br/>
            {{ Form::select('price_fixed', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('price_fixed') ? Request::get('price_fixed') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>Sem Preço</strong><br/>
            {{ Form::select('price', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('price') ? Request::get('price') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>Sem País</strong><br/>
            {{ Form::select('empty_country', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('empty_country') ? Request::get('empty_country') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </li>
    </ul>
</div>