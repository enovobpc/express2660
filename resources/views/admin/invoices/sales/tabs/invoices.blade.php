<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
    <li class="btn-add-invoice" style="{{ in_array(Request::get('doc_type'), ['receipt', 'regularization'])  ? 'display: none' : '' }}">
        <a href="{{ route('admin.invoices.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li class="btn-add-receipt" style="{{ Request::get('doc_type') == 'receipt' ? : 'display: none' }}">
        <a href="{{ route('admin.invoices.receipt.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li class="btn-add-regularization" style="{{ Request::get('doc_type') == 'regularization' ? : 'display: none' }}">
        <a href="{{ route('admin.invoices.regularization.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    @if(in_array(Setting::get('app_country'), ['pt', 'ptmd', 'ptac']))
    <li class="btn-saft" style="{{ Request::get('doc_type') == 'receipt' ? 'display: none' : '' }}">
        <span data-toggle="tooltip" title="Consulte os ficheiros já emitidos e pode gerar a qualquer momento o ficheiro SAFT do mês atual.">
            <a href="{{ route('admin.invoices.saft') }}"
               class="btn btn-primary btn-sm"
               data-toggle="modal"
               data-target="#modal-remote">
                <i class="fas fa-file-archive"></i> SAF-T <i class="fas fa-angle-down"></i>
            </a>
        </span>
    </li>
    @endif
    <li>
        <div class="btn-group btn-group-sm" role="group">
            <div class="btn-group btn-group-sm" ro le="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-print"></i> Relatórios <i class="fas fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-print-mass-invoices">
                            <i class="fas fa-fw fa-print"></i> Download documentos em massa
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-map-customer-sales">
                            <i class="fas fa-fw fa-print"></i> Mapa faturação por cliente
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-map-annual-sales">
                            <i class="fas fa-fw fa-print"></i> Mapa faturação anual/mensal
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-map-vat-summary">
                            <i class="fas fa-fw fa-print"></i> Mapa resumo taxas IVA
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-map-unpaid-invoices">
                            <i class="fas fa-fw fa-print"></i> Mapa pendentes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.printer.invoices.customers.maps', 'unpaid') }}" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Resumo pendentes por cliente
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-print-operator-balance">
                            <i class="fas fa-fw fa-print"></i> Prestação de contas diária
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('admin.printer.invoices.summary', Request::all()) }}" data-toggle="print-url" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir listagem atual
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.export.invoices', Request::all()) }}" data-toggle="export-url">
                            <i class="fas fa-fw fa-file-excel"></i> Exportar Listagem atual
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('admin.invoices.initial-balance.edit') }}" data-toggle="modal" data-target="#modal-remote-lg">
                            <i class="fas fa-fw fa-file-invoice"></i> Registo saldos iniciais
                        </a>
                    </li>
                        
              
                    @if(Auth::user()->isAdmin())
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('admin.invoices.sales.divergences') }}"
                           data-method="post"
                           data-confirm="Confirma a verificação de divergencias entre o sistema e o KeyInvoice?"
                           data-confirm-label="Verificar Divergências"
                           data-confirm-class="btn-success">
                            <i class="fas fa-fw fa-exclamation-triangle"></i> Verificar divergencias
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> <i class="fas fa-angle-down"></i>
            </button>
        </div>
    </li>
    {{--<li>
        <div class="btn-group btn-group-sm" role="group">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-wrench"></i> Ferramentas <i class="fas fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('admin.export.invoices', Request::all()) }}" data-toggle="export-url">
                            <i class="fas fa-fw fa-file-excel"></i> Exportar Listagem Atual
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.printer.invoices.summary', Request::all()) }}" data-toggle="print-url" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir Listagem Atual
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-print-operator-balance">
                            <i class="fas fa-fw fa-print"></i> Prestação de contas
                        </a>
                    </li>
                </ul>
            </div>
            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> <i class="fas fa-angle-down"></i>
            </button>
        </div>
    </li>--}}
    <li class="fltr-primary w-120px">
        <strong>Ano</strong><br class="visible-xs"/>
        <div class="w-80px pull-left form-group-sm">
            {{ Form::select('year', ['' => 'Todos'] + $years, fltr_val(Request::all(), 'year'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="fltr-primary w-140px">
        <strong>Mês</strong><br class="visible-xs"/>
        <div class="w-100px pull-left form-group-sm" style="position: relative">
            {{ Form::select('month', ['' => 'Todos'] + $months, fltr_val(Request::all(), 'month'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="fltr-primary w-140px">
        <strong>Série</strong><br class="visible-xs"/>
        <div class="w-100px pull-left form-group-sm" style="position: relative">
            {{ Form::selectMultiple('serie', $series, fltr_val(Request::all(), 'serie'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li class="fltr-primary w-110px">
        <strong>Nº Doc.</strong><br class="visible-xs"/>
        <div class="w-50px pull-left form-group-sm" style="position: relative">
            {{ Form::text('doc_id', fltr_val(Request::all(), 'doc_id'), array('class' => 'form-control input-sm filter-datatable', 'style' => 'width: 100%;')) }}
        </div>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
    <ul class="list-inline pull-left">
        <li style="margin-bottom: 5px;"  class="col-xs-6 doc-type-filter">
            <strong>Documento</strong><br/>
            <div class="w-120px">
                {{ Form::selectMultiple('doc_type', trans('admin/billing.types-list') + ['debit-note' => 'Nota Débito','receipt' => 'Recibos', 'regularization' => 'Regularização', 'nodoc' => 'Sem Documento', 'proforma' => 'Proformas', 'scheduled' => 'Agendados'], fltr_val(Request::all(), 'doc_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6 doc-type-filter">
            <strong>Vencimento até</strong><br/>
            <div class="w-130px">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    {{ Form::text('expired', fltr_val(Request::all(), 'expired'), array('class' => 'form-control input-sm filter-datatable datepicker')) }}
                </div>
            </div>
        </li>
        <li class="col-xs-12">
            <strong>Data Documento</strong><br/>
            <div class="input-group input-group-sm w-240px">
                {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                <span class="input-group-addon">até</span>
                {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Cliente</strong><br/>
            <div class="w-250px">
                {{ Form::select('customer',  Request::has('customer') ? [''=>'', Request::get('customer') => Request::get('customer-text')] : [''=>''], fltr_val(Request::all(), 'customer'), array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Liquidado</strong><br/>
            <div class="w-80px pull-left form-group-sm" style="position: relative">
                {{ Form::select('settle', ['' => 'Todos', '1' => 'Sim', '0' => 'Não', '2' => 'Parcial', '3' => 'Não - Vencido', '4' => 'Não - Pendente'], fltr_val(Request::all(), 'settle'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Rascunho</strong><br/>
            <div class="w-80px">
                {{ Form::select('draft', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], fltr_val(Request::all(), 'draft'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Condição Pag.</strong><br/>
            <div class="w-120px">
                {{ Form::selectMultiple('payment_condition', $paymentConditions, fltr_val(Request::all(), 'payment_condition'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Forma Pagamento</strong><br/>
            <div class="w-120px">
                {{ Form::selectMultiple('payment_method', $paymentMethods, fltr_val(Request::all(), 'payment_method'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Registado por</strong><br/>
            <div class="w-160px">
                {{ Form::selectMultiple('operator', $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Tipo</strong><br/>
            <div class="w-100px">
                {{ Form::selectMultiple('target', trans('admin/billing.targets'), fltr_val(Request::all(), 'target'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
            </div>
        </li>
        @if($sellers)
            <li style="margin-bottom: 5px;" class="col-xs-6">
                <strong>Comercial</strong><br/>
                <div class="w-140px">
                    {{ Form::select('seller', ['' => 'Todos'] + $sellers, fltr_val(Request::all(), 'seller'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
        @endif
        @if(count($agencies) > 1)
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Agência</strong><br/>
            <div class="w-140px">
                {{ Form::select('agency', ['' => 'Todas'] + $agencies, fltr_val(Request::all(), 'agency'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        @endif
        @if(@$routes)
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Rota</strong><br/>
            <div class="w-100px">
                {{ Form::select('route', ['' => 'Todas'] + $routes, fltr_val(Request::all(), 'route'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        @endif
        <li style="width: 100px" class="col-xs-6">
            <div class="checkbox p-t-22">
                <label>
                    {{ Form::checkbox('deleted', 1, true) }}
                    Anulados
                </label>
            </div>
        </li>
    </ul>
</div>
<div class="table-responsive">
    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-65px">Data Doc</th>
            <th class="w-1">Fatura</th>
            <th>Cliente</th>
<!--            <th class="w-80px">Tipo Doc.</th>-->
            <th class="w-80px">Referência</th>
            <th class="w-50px">Subtotal</th>
            <th class="w-50px">Total</th>
            <th class="w-50px">Pendente</th>
            <th class="w-75px">Vencimento</th>
            <th class="w-1">Pago</th>
            <th class="w-65px">Pagamento</th>
            <th class="w-120px">Emitido por</th>
            <th class="w-60px">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    <div class="pull-left">
        <a href="{{ route('admin.printer.invoices.summary') }}"
           class="btn btn-sm btn-default m-l-5"
           data-action-url="datatable-action-url"
           target="_blank">
            <i class="fas fa-fw fa-print"></i> Imprimir
        </a>
        <a href="{{ route('admin.export.invoices') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
            <i class="fas fa-fw fa-file-excel"></i> Exportar
        </a>
        <a href="{{ route('admin.invoices.receipt.create', ['type' => 'receipt']) }}"
           class="btn btn-sm btn-default m-l-5"
           data-action-url="datatable-action-url"
           data-toggle="modal"
           data-target="#modal-remote-xl"
           target="_blank">
            <i class="fas fa-fw fa-receipt"></i> Criar Recibo
        </a>
        <a href="{{ route('admin.invoices.mass.edit') }}"
           style="{{ Request::get('doc_type') == 'nodoc' ? '' : 'display:none' }}"
           class="btn btn-sm btn-default btn-mass-edit m-l-5"
           data-action-url="datatable-action-url"
           data-toggle="modal"
           data-target="#modal-remote-xs"
           target="_blank">
            <i class="fas fa-fw fa-pencil-alt"></i> Editar Massivo
        </a>
        @if(hasModule('sepa_transfers'))
            @if(Auth::user()->perm('sepa_transfers'))
                <a href="{{ route('admin.sepa-transfers.import.invoices.edit') }}"
                   class="btn btn-sm btn-default m-l-5"
                   data-action-url="datatable-action-url"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-fw fa-file-export"></i> Criar Débito Direto
                </a>
            @else
                <a href="#" disabled class="btn btn-sm btn-default m-l-5">
                    <i class="fas fa-fw fa-file-export"></i> Criar Débito Direto
                </a>
            @endif
        @else
            <a href="#" disabled
               class="btn btn-sm btn-default m-l-5"
               data-toggle="tooltip"
               data-title="Efetue cobranças por débito direto. Módulo não incluido na sua licença.">
                <i class="fas fa-fw fa-file-export"></i> Criar Débito Direto
            </a>
        @endif
    </div>
    <div class="pull-left">
        <h4 class="pull-left" style="margin: -2px 0 -6px 10px;
                    padding: 1px 3px 3px 9px;
                    border-left: 1px solid #999;
                    line-height: 17px;">
            <small>Subtotal</small><br/>
            <span class="dt-sum-subtotal bold"></span><b>{{ Setting::get('app_currency') }}</b>
        </h4>
        <h4 class="pull-left" style="margin: -2px 0 -6px 10px;
                    padding: 1px 3px 3px 9px;
                    line-height: 17px;">
            <small>IVA</small><br/>
            <span class="dt-sum-vat bold"></span><b>{{ Setting::get('app_currency') }}</b>
        </h4>
        <h4 class="pull-left" style="margin: -2px 0 -6px 10px;
                    padding: 1px 3px 3px 9px;
                    line-height: 17px;">
            <small>Total</small><br/>
            <span class="dt-sum-total bold"></span><b>{{ Setting::get('app_currency') }}</b>
        </h4>
    </div>
    <div class="clearfix"></div>
</div>