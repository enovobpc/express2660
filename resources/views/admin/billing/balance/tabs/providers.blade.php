<ul class="list-inline" style="margin-top: -5px">
    <li>
        <h3 style="margin-top: -5px">
            <small>Por Pagar</small><br/>
            <b class="balance-total-unpaid">{{ money($providersTotalUnpaid, Setting::get('app_currency')) }}</b>
        </h3>
    </li>
    <li>
        <h3 class="m-l-15" style="margin-top: -5px">
            <small>Doc. Vencidos</small><br/>
            <b class="balance-total-expired">{{ $providersTotalExpired }} Documentos</b>
        </h3>
    </li>
</ul>
<hr class="m-t-5 m-b-15"/>
<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-providers">
    <li>
        <div class="btn-group btn-group-sm" role="group">
            <div class="btn-group btn-group-sm" ro le="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-print"></i> Imprimir <i class="fas fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('admin.printer.invoices.balance', [0, 'source' => 'providers']) }}"
                           data-toggle="export-url">
                            <i class="fas fa-fw fa-print"></i> Listagem atual
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.printer.invoices.purchase.map', 'unpaid') }}" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Pendentes por fornecedor
                        </a>
                    </li>
                </ul>
            </div>
            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
            </button>
        </div>
    </li>
    {{--<li>
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
    <li class="fltr-primary w-145px">
        <strong>Vencido</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-85px">
            {{ Form::select('expired', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('expired') ? Request::get('expired') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="fltr-primary w-190px">
        <strong>Cond. Pgto</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-110px">
            {{ Form::select('payment_method', ['' => 'Todos'] + $paymentConditions, Request::has('payment_method') ? Request::get('payment_method') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="fltr-primary w-175px">
        <strong>Categoria</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-100px">
            {{ Form::select('category', ['' => 'Todos'] + $categories, Request::has('category') ? Request::get('category') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
{{--<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-providers">
    <ul class="list-inline pull-left">
        @if(count($agencies) > 1)
            <li style="margin-bottom: 5px;"  class="col-xs-6">
                <strong>Agência</strong><br/>
                <div class="w-160px">
                    {{ Form::select('agency', ['' => 'Todos'] + $agencies, Request::has('agency') ? Request::get('agency') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
        @endif
    </ul>
</div>--}}
<div class="table-responsive">
    <table id="datatable-providers" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-1">Código</th>
            <th>Fornecedor</th>
            <th class="w-1">Contribuinte</th>
            <th class="w-70px">Pagamento</th>
            <th class="w-40px">Docs</th>
            <th class="w-70px">Débito</th>
            <th class="w-70px">Crédito</th>
            <th class="w-70px">Saldo</th>

            <th class="w-75px">Último Doc</th>
            <th class="w-60px">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{--{{ Form::open(array('route' => 'admin.billing.balance.selected.email.balance')) }}
    <button class="btn btn-sm btn-default" data-action="confirm" title="Enviar Conta Corrente" data-confirm-class="btn-success" data-confirm-label="Enviar E-mail" data-confirm="Confirma o envio da conta corrente para os clientes selecionados?"><i class="fas fa-envelope"></i> Enviar Conta Corrente</button>
    {{ Form::close() }}--}}
</div>
<div class="clearfix"></div>