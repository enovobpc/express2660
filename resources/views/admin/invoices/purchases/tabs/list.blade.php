<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
    <li>
        <a href="{{ route('admin.invoices.purchase.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    {{--<li>
        <a href="{{ route('admin.invoices.purchase.create') }}"
           class="btn btn-default btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            <i class="fas fa-plus"></i> Registo Rápido
        </a>
    </li>--}}
    <li>
        <div class="btn-group btn-group-sm" role="group">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-wrench"></i> Ferramentas <i class="fas fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <li>
                            <a href="{{ route('admin.invoices.initial-balance.edit', ['entity' => 'providers']) }}" data-toggle="modal" data-target="#modal-remote-lg">
                                <i class="fas fa-fw fa-file-invoice"></i> Registo saldos iniciais
                            </a>
                        </li>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('admin.invoices.purchase.types.index') }}"
                           data-toggle="modal"
                           data-target="#modal-remote">
                            <i class="fas fa-fw fa-list"></i> Gerir tipos de despesa
                        </a>
                    </li>
                </ul>
            </div>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-print"></i> Relatórios <i class="fas fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu">
                    {{--<li>
                        <a href="#" data-toggle="modal" data-target="#modal-map-summary-type">
                            <i class="fas fa-fw fa-print"></i> Mapa Despesas por tipo
                        </a>
                    </li>--}}
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-map-summary-vehicle">
                            <i class="fas fa-fw fa-print"></i> Balancete Geral Despesas
                        </a>
                    </li>
                    <li class="divider"></li>
                    {{--<li>
                        <a href="{{ route('admin.printer.invoices.purchase.map', 'unpaid') }}" data-toggle="print-url" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Listagem pendentes por fornecedor
                        </a>
                    </li>--}}
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-map-providers-purchase">
                            <i class="fas fa-fw fa-print"></i> Listagem pendentes por fornecedor
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.printer.invoices.purchase.listing', [0] + Request::all()) }}" data-toggle="print-url" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir listagem atual
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.printer.invoices.purchase.listing', [1] + Request::all()) }}" data-toggle="print-url" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir listagem atual (agr. por tipo)
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('admin.export.invoices.purchase', Request::all()) }}" data-toggle="export-url">
                            <i class="fas fa-fw fa-file-excel"></i> Exportar Listagem Atual
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-export-yearly-grouped-type">
                            <i class="fas fa-fw fa-file-excel"></i> Exportar Listagem Anual (agr. por tipo)
                        </a>
                    </li>
                </ul>
            </div>
            <button type="button" class="btn btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> <i class="fas fa-angle-down"></i>
            </button>
        </div>
    </li>
    <li class="fltr-primary w-150px doc-paid-filter">
        <strong>Liquidado</strong><br class="visible-xs"/>
        <div class="w-75px pull-left form-group-sm">
            {{ Form::select('paid', ['' => 'Todos', '1' => 'Sim', '0' => 'Não', '2' => 'Parcial'], fltr_val(Request::all(), 'paid'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="fltr-primary w-135px">
        <strong>Vencido</strong><br class="visible-xs"/>
        <div class="w-75px pull-left form-group-sm">
            {{ Form::select('expired', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], fltr_val(Request::all(), 'expired'), array('class' => 'form-control input-sm filter-datatable select2')) }}
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
        <li style="margin-bottom: 5px;" class="form-group-sm hidden-xs col-xs-12">
            <strong>Data a filtrar</strong><br/>
            <div class="w-140px m-r-4" style="position: relative; z-index: 5;">
                {{ Form::select('date_unity', ['' => 'Data Documento', 'due' => 'Vencimento', 'pay' => 'Pagamento'], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li class="shp-date col-xs-12">
            <strong>Data</strong><br/>
            <div class="input-group input-group-sm w-220px">
                {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                <span class="input-group-addon">até</span>
                {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Tipo despesa</strong><br/>
            <div class="w-180px">
                {{ Form::selectMultiple('type', $purchasesTypes, fltr_val(Request::all(), 'type'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6 doc-type-filter">
            <strong>Tipo documento</strong><br/>
            <div class="w-140px">
                {{ Form::selectMultiple('doc_type', trans('admin/billing.types-list-purchase'), fltr_val(Request::all(), 'doc_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6 doc-type-filter">
            <strong>Agência</strong><br/>
            <div class="w-140px">
                {{ Form::select('agency', [''=>'Todas']+$agencies, fltr_val(Request::all(), 'agency'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Fornecedor</strong><br/>
            <div class="w-230px">
                {{ Form::select('provider',  Request::has('provider') ? [''=>'', Request::get('provider') => Request::get('provider-text')] : [''=>''], fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Imputado a</strong><br/>
            <div class="w-90px pull-left">
                {{ Form::select('target',  ['' => 'Todos', 'Invoice' => 'Nada', 'Vehicle' => 'Viatura', 'User' => 'Colaborador', 'Shipment' => 'Envio'], fltr_val(Request::all(), 'target'), array('class' => 'form-control input-sm w-100 filter-datatable select2')) }}
            </div>
            {{--<div class="w-250px pull-left">
                {{ Form::select('target_id',  [''=>''], fltr_val(Request::all(), 'target_id'), array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Procurar viatura, motorista ou envio')) }}
            </div>--}}
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Cond. Pgto</strong><br/>
            <div class="w-130px">
                {{ Form::selectMultiple('payment_conditions', $paymentConditions, fltr_val(Request::all(), 'payment_conditions'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Já em Sistema</strong><br/>
            <div class="w-100px">
                {{ Form::select('ignore_stats', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], fltr_val(Request::all(), 'ignore_stats'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="width: 100px">
            <div class="checkbox p-t-20">
                <label>
                    {{ Form::checkbox('deleted', 1, Setting::get('deleted')) }}
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
            <th class="w-45px">Nº Doc</th>
            <th class="w-80px">Referência</th>
            <th>Fornecedor</th>
            <th class="w-120px">Tipo Despesa</th>
            {{--<th class="w-80px">Documento</th>--}}
            <th class="w-70px">Total</th>
            {{--<th class="w-1">IVA</th>--}}
            <th class="w-60px">Pendente</th>
            <th class="w-80px">Vencimento</th>
            <th class="w-1">Estado</th>
            <th class="w-1"><i class="fas fa-link" data-toggle="tooltip" title="Fatura Imputada"></i></th>
            <th class="w-1"><i class="fas fa-copy" data-toggle="tooltip" title="Fatura já contemplada em sistema"></i></th>
            <th class="w-120px">Emitido por</th>
            <th class="w-65px">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.invoices.purchase.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Anular selecionados"><i class="fas fa-trash-alt"></i> Anular</button>
    {{ Form::close() }}

    <div class="pull-left">
        <a href="{{ route('admin.invoices.purchase.payment-notes.create') }}"
           class="btn btn-sm btn-default m-l-5"
           data-toggle="modal"
           data-target="#modal-remote-lg"
           data-action-url="datatable-action-url">
            <i class="fas fa-fw fa-check"></i> Liquidar Selecionados
        </a>

        <div class="btn-group btn-group-sm dropup m-l-5">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-print"></i> Imprimir <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.printer.invoices.purchase.listing') }}" data-toggle="datatable-action-url" target="_blank">
                        Listagem Selecionada
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.printer.invoices.purchase.listing', ['grouped' => 1]) }}" data-toggle="datatable-action-url" target="_blank">
                        Listagem (Agrupado por tipo)
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('admin.export.invoices.purchase') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
            <i class="fas fa-fw fa-file-excel"></i> Exportar
        </a>
    </div>
    <div class="pull-left">
        <h4 style="margin: -2px 0 -6px 10px;
                        padding: 1px 3px 3px 9px;
                        border-left: 1px solid #999;
                        line-height: 17px;">
            <small>Total Selecionado</small><br/>
            <span class="dt-sum-total bold"></span><b>€</b>
        </h4>
    </div>
</div>
<div class="clearfix"></div>