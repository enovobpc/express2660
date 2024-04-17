<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-payment-notes">
    <li>
        <a href="{{ route('admin.invoices.purchase.payment-notes.create') }}"
           class="btn btn-success btn-sm btn-add-payment-note"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <div class="btn-group btn-group-sm" role="group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-print"></i> Relatórios <i class="fas fa-angle-down"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a href="{{ route('admin.printer.invoices.purchase.payment.note.listing', Request::all()) }}" data-toggle="print-url-payment-notes" target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir listagem atual
                </a>
            </li>
            <li>
                <a href="{{ route('admin.export.invoices.purchase.payment.note', Request::all()) }}" data-toggle="export-url-payment-notes">
                    <i class="fas fa-fw fa-file-excel"></i> Exportar Listagem Atual
                </a>
            </li>
        </ul>
    </div>
    <li>
        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
        </button>
    </li>
    <li class="fltr-primary w-140px">
        <strong>Nº Recb</strong><br class="visible-xs"/>
        <div class="w-80px pull-left form-group-sm" style="position: relative">
            {{ Form::select('has_ref', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], fltr_val(Request::all(), 'has_ref'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-payment-notes">
    <ul class="list-inline pull-left">
        <li class="col-xs-12">
            <strong>Data</strong><br/>
            <div class="input-group input-group-sm w-220px">
                {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                <span class="input-group-addon">até</span>
                {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Fornecedor</strong><br/>
            <div class="w-230px">
                {{ Form::select('provider',  Request::has('provider') ? [''=>'', Request::get('provider') => Request::get('provider-text')] : [''=>''], fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
            </div>
        </li>
        <li style="width: 100px">
            <div class="checkbox p-t-20">
                <label>
                    {{ Form::checkbox('payment_deleted', 1, Setting::get('deleted')) }}
                    Anulados
                </label>
            </div>
        </li>
    </ul>
</div>
<div class="table-responsive">
    <table id="datatable-payment-notes" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-65px">Data Doc</th>
            <th class="w-80px">Nº Pagam.</th>
            <th class="w-80px">Nº Recibo</th>
            <th>Fornecedor</th>
            <th class="w-70px">Total</th>
            <th class="w-60px">Faturas</th>
            <th class="w-120px">Criado por</th>
            <th class="w-65px">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    <div class="pull-left">
        <a href="{{ route('admin.printer.invoices.purchase.payment.note.listing') }}" data-toggle="datatable-action-url" class="btn btn-sm btn-default m-l-5" target="_blank">
            <i class="fas fa-print"></i> Listagem
        </a>
        <a href="{{ route('admin.export.invoices.purchase.payment.note') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected-payments">
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