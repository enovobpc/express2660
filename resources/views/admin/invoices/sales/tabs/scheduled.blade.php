<p class="m-b-0 m-t-5"><i class="fas fa-info-circle"></i> As faturas programadas são emitidas e enviadas automáticamente ao cliente na períodicidade escolhida.</p>
<hr style="margin-top: 15px"/>
<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-scheduled">
    <li>
        <a href="{{ route('admin.invoices.create', ['scheduled' => true]) }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            <i class="fas fa-plus"></i> Fatura Programada
        </a>
    </li>
    <li>
        <div class="btn-group btn-group-sm" role="group">
          {{--  <div class="btn-group btn-group-sm" role="group">
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
            </div>--}}
            {{--<button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
            </button>--}}
        </div>
    </li>
   {{-- <li class="fltr-primary w-120px">
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
    </li>--}}
    <li class="fltr-primary w-140px">
        <strong>Série</strong><br class="visible-xs"/>
        <div class="w-100px pull-left form-group-sm" style="position: relative">
            {{ Form::selectMultiple('serie', $series, fltr_val(Request::all(), 'serie'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li class="fltr-primary w-180px">
        <strong>Documento</strong><br class="visible-xs"/>
        <div class="w-100px pull-left form-group-sm" style="position: relative">
            {{ Form::selectMultiple('type', trans('admin/billing.types-list') + ['proforma' => 'Proformas'], fltr_val(Request::all(), 'type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li class="fltr-primary w-220px">
        <strong>Cliente</strong><br class="visible-xs"/>
        <div class="w-140px pull-left form-group-sm" style="position: relative">
            {{ Form::select('customer',  Request::has('customer') ? [''=>'', Request::get('customer') => Request::get('customer-text')] : [''=>''], fltr_val(Request::all(), 'customer'), array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
        </div>
    </li>
</ul>
{{--<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-scheduled">
    <ul class="list-inline pull-left">
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Cliente</strong><br/>
            <div class="w-230px">
                {{ Form::select('customer',  Request::has('customer') ? [''=>'', Request::get('customer') => Request::get('customer-text')] : [''=>''], fltr_val(Request::all(), 'customer'), array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
            </div>
        </li>
    </ul>
</div>--}}
<div class="table-responsive">
    <table id="datatable-scheduled" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th>Cliente</th>
            <th class="w-1">Doc.</th>
            {{--<th class="w-50px">Subtotal</th>--}}
            <th class="w-50px">Valor</th>
            <th class="w-80px">Referência</th>
            <th class="w-65px">Vencimento</th>
            <th class="w-170px">Periodicidade</th>
            <th class="w-60px">Definições</th>
            <th class="w-120px">Últ. Emissão</th>
            <th class="w-50px">Estado</th>
            <th class="w-65px">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    <a href="{{ route('admin.export.invoices') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
        <i class="fas fa-fw fa-file-excel"></i> Exportar
    </a>
</div>