<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-balance">
    {{--<li>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-sync-alt"></i> Sincronizar <i class="caret"></i>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="#" data-toggle="modal" data-target="#modal-sync-balance">
                        Atualizar Todas as Contas Correntes
                    </a>
                </li>
                <li>
                    <a href="#" data-toggle="modal" data-target="#modal-update-balance-status">
                        Atualizar Todos os Estados de Pagamento
                    </a>
                </li>
            </ul>
        </div>
    </li>--}}
    <li class="btn-add-invoice">
        <a href="{{ route('admin.invoices.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            <i class="fas fa-plus"></i> Fatura
        </a>
    </li>
    <li>
        <a href="{{ route('admin.invoices.receipt.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            <i class="fas fa-plus"></i> Recibo
        </a>
    </li>
    <li>
        <div class="btn-group btn-group-sm" role="group">
            <div class="btn-group btn-group-sm" ro le="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-print"></i> Imprimir <i class="fas fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('admin.printer.invoices.balance') }}"
                           data-toggle="export-url">
                            <i class="fas fa-fw fa-print"></i> Listagem atual
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.printer.invoices.customers.maps', 'unpaid') }}" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Pendentes por cliente
                        </a>
                    </li>
                    @if(Auth::user()->isAdmin() && App\Models\Invoice::getInvoiceSoftware() != App\Models\Invoice::SOFTWARE_ENOVO)
                        <li>
                            <a href="#" data-toggle="modal"
                               data-target="#modal-import-divergences">
                                <i class="fas fa-upload"></i> Verificar Divergências
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
            </button>
        </div>
    </li>
    <li>

    </li>
    {{-- <li>
         <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
             <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
         </button>
     </li>--}}
    <li class="fltr-primary w-130px">
        <strong>Estado</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-80px">
            {{ Form::select('unpaid', ['' => 'Todos', '1' => 'Liquidado', '0' => 'Por Liquidar'], Request::has('unpaid') ? Request::get('unpaid') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="fltr-primary w-155px">
        <strong>Vencido</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-85px">
            {{ Form::select('expired', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('expired') ? Request::get('expired') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-balance">
    <ul class="list-inline pull-left">
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Distrito Expedição</strong><br/>
            <div class="w-140px">
                {{ Form::select('district', ['' => 'Todos'] + trans('districts_codes.districts.pt'), fltr_val(Request::all(), 'district'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Concelho Expedição <i class="fas fa-spin fa-circle-notch load-county" style="display: none"></i></strong><br/>
            <div class="w-140px">
                {{ Form::select('county', $recipientCounties ? ['' => 'Todos'] + $recipientCounties : ['' => 'Selec. Distrito'], fltr_val(Request::all(), 'county'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>País Expedição</strong><br/>
            <div class="w-110px">
                {{ Form::select('country', ['' => 'Todos'] + trans('country'), Request::has('country') ? Request::get('country') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>País Faturação</strong><br/>
            <div class="w-110px">
                {{ Form::select('country_billing', ['' => 'Todos'] + trans('country'), Request::has('country_billing') ? Request::get('country_billing') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        @if(!Auth::user()->hasRole([config('permissions.role.seller')]) && $sellers)
            <li style="margin-bottom: 5px;" class="col-xs-12">
                <strong>Comercial</strong><br/>
                <div class="w-140px">
                    {{ Form::select('seller', ['' => 'Todos'] + $sellers, Request::has('seller') ? Request::get('seller') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
        @endif
        @if($routes)
            <li style="margin-bottom: 5px;" class="col-xs-12">
                <strong>Rota</strong><br/>
                <div class="w-140px">
                    {{ Form::select('route', ['' => 'Todas', '0' => 'Sem rota associada'] + $routes, Request::has('route') ? Request::get('route') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
        @endif
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Tipo</strong><br/>
            <div class="w-80px">
                {{ Form::select('particular', ['' => 'Todos', '-1' => 'Empresa', '1' => 'Particular'], Request::has('particular') ? Request::get('particular') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Categoria</strong><br/>
            <div class="w-130px">
                {{ Form::select('type', ['' => 'Todos'] + $customerTypes, Request::has('type') ? Request::get('type') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Pagamento</strong><br/>
            <div class="w-100px">
                {{ Form::select('payment_method', ['' => 'Todos', 'wallet'=> 'Pré-pagamento'] + $paymentConditions, Request::has('payment_method') ? Request::get('payment_method') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Ult. Envio</strong><br/>
            <div class="w-100px">
                {{ Form::select('last_shipment', ['' => 'Todos', '1' => 'Menos ' . Setting::get('alert_max_days_without_shipments') . ' dias', '2' => 'Mais ' . Setting::get('alert_max_days_without_shipments') . ' dias', '3' => 'Sem envios'], Request::has('last_shipment') ? Request::get('last_shipment') : Setting::get('customers_list_only_active'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
    </ul>
</div>
<div class="table-responsive">
    <table id="datatable-balance" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-1">Código</th>
            <th>Cliente</th>
            <th class="w-1">Contribuinte</th>
            <th class="w-70px">Pagamento</th>
            <th class="w-40px">Docs</th>
            <th class="w-70px">Débito</th>
            <th class="w-70px">Crédito</th>
            <th class="w-70px">Saldo</th>
            <th class="w-65px">Últ. Envio</th>
            @if(App\Models\Invoice::getInvoiceSoftware() != App\Models\Invoice::SOFTWARE_ENOVO)
            <th class="w-60px">Últ. Sync.</th>
            @endif
            <th class="w-90px">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.billing.balance.selected.email.balance')) }}
    <button class="btn btn-sm btn-default" data-action="confirm" title="Enviar Conta Corrente" data-confirm-class="btn-success" data-confirm-label="Enviar E-mail" data-confirm="Confirma o envio da conta corrente para os clientes selecionados?"><i class="fas fa-envelope"></i> Enviar Conta Corrente</button>
    {{ Form::close() }}
</div>
<div class="clearfix"></div>