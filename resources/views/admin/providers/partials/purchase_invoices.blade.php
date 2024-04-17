@if(!hasModule('purchase_invoices'))
    @include('admin.partials.denied_message')
@else
<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-purchase-invoices">
            <li>
                <div class="btn-group btn-group-sm" role="group">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-envelope"></i> @trans('Imprimir') <i class="caret"></i>
                        </button>
                        <ul class="dropdown-menu">
                            {{--<li>
                                <a href="#" data-toggle="modal" data-target="#modal-send-balance-email">
                                    <i class="fas fa-fw fa-envelope"></i> Enviar por E-mail
                                </a>
                            </li>
                            <li class="divider"></li>--}}
                            <li>
                                <a href="{{ route('admin.printer.invoices.purchase.balance', ['providerId' => $provider->id]+Request::all()) }}" data-toggle="export-url" target="_blank">
                                    <i class="fas fa-fw fa-print"></i> @trans('Imprimir Conta Corrente')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.printer.invoices.purchase.map', ['unpaid', 'provider' => $provider->id]) }}" target="_blank">
                                    <i class="fas fa-fw fa-print"></i> @trans('Imprimir Listagem Pendentes')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.printer.invoices.purchase.listing', [0, 'provider' => $provider->id] + Request::all()) }}" data-toggle="print-url" target="_blank">
                                    <i class="fas fa-fw fa-print"></i> @trans('Imprimir Listagem Atual')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.printer.invoices.purchase.listing', [1, 'provider' => $provider->id] + Request::all()) }}" data-toggle="print-url" target="_blank">
                                    <i class="fas fa-fw fa-print"></i> @trans('Imprimir Listagem Atual (por tipo)')
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="{{ route('admin.export.invoices.purchase', Request::all()) }}" data-toggle="export-url">
                                    <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar Listagem Atual')
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            <li>
                <a href="{{ route('admin.invoices.purchase.payment-notes.create', ['provider' => $provider->id]) }}"
                   class="btn btn-default btn-sm"
                   data-toggle="modal"
                   data-target="#modal-remote-lg">
                    <i class="fas fa-receipt"></i> @trans('Novo Pagamento')
                </a>
            </li>
            <li>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-filter-datatable btn-default">
                        <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                    </button>
                </div>
            </li>
            {{--<li style="margin-bottom: 5px;" class="col-xs-6">
                <strong>Liquidado</strong><br/>
                <div class="w-100px">
                    {{ Form::select('paid', ['' => 'Todos', '1' => 'Sim', '0' => 'Não', '3' => 'Liquidado Parcial'], fltr_val(Request::all(), 'paid'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>--}}
            <li class="fltr-primary w-160px">
                <strong>@trans('Liquidado')</strong><br class="visible-xs"/>
                <div class="w-90px pull-left form-group-sm" style="position: relative">
                    {{ Form::select('paid', ['' => 'Todos', '1' => __('Sim'), '0' => __('Não'), '3' => __('Liquidado Parcial')], fltr_val(Request::all(), 'paid'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
            {{--<li class="fltr-primary w-110px">
                <strong>Nº Doc.</strong><br class="visible-xs"/>
                <div class="w-50px pull-left form-group-sm" style="position: relative">
                    {{ Form::text('doc_id', fltr_val(Request::all(), 'doc_id'), array('class' => 'form-control input-sm filter-datatable', 'style' => 'width: 100%;')) }}
                </div>
            </li>--}}
            <li>
                <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                    <label>
                        {{ Form::checkbox('hide_payments', '1', true) }}
                        @trans('Ocultar Pagamentos')
                    </label>
                </div>
            </li>
        </ul>
        <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-purchase-invoices">
            <ul class="list-inline pull-left">
                <li style="margin-bottom: 5px;" class="form-group-sm hidden-xs col-xs-12">
                    <strong>@trans('Data a filtrar')</strong><br/>
                    <div class="w-140px m-r-4" style="position: relative; z-index: 5;">
                        {{ Form::select('date_unity', ['' => __('Data Documento'), 'due' => __('Vencimento'), 'pay' => __('Pagamento')], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
                <li class="shp-date col-xs-12">
                    <strong>@trans('Data')</strong><br/>
                    <div class="input-group input-group-sm w-220px">
                        {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                        <span class="input-group-addon">@trans('até')</span>
                        {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-6">
                    <strong>@trans('Nº Doc.')</strong><br/>
                    <div class="w-90px">
                        {{ Form::text('doc_id', fltr_val(Request::all(), 'doc_id'), array('class' => 'form-control input-sm filter-datatable', 'style' => 'width: 100%;')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-6">
                    <strong>@trans('Sentido')</strong><br/>
                    <div class="w-90px">
                        {{ Form::select('sense', ['' => __('Todos'), 'credit' => __('Créditos'), 'debit' => __('Débitos')], fltr_val(Request::all(), 'sense'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-6">
                    <strong>@trans('Tipo Doc')</strong><br/>
                    <div class="w-130px">
                        {{ Form::selectMultiple('doc_type', trans('admin/billing.types-list-purchase'), fltr_val(Request::all(), 'doc_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                    </div>
                </li>

                <li style="margin-bottom: 5px;" class="col-xs-6">
                    <strong>@trans('Cond. Pgto')</strong><br/>
                    <div class="w-130px">
                        {{ Form::selectMultiple('payment_method', $paymentConditions, fltr_val(Request::all(), 'payment_method'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;"  class="col-xs-6">
                    <strong>@trans('Tipo despesa')</strong><br/>
                    <div class="w-180px">
                        {{ Form::selectMultiple('type', $purchasesTypes, fltr_val(Request::all(), 'type'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
                    </div>
                </li>
                {{--<li style="margin-bottom: 5px;"  class="col-xs-6">
                    <strong>Despesas imputadas a</strong><br/>
                    <div class="w-90px pull-left">
                        {{ Form::select('target',  ['' => 'Todos', 'Invoice' => 'Nada', 'Vehicle' => 'Viatura', 'User' => 'Colaborador', 'Shipment' => 'Envio'], fltr_val(Request::all(), 'target'), array('class' => 'form-control input-sm w-100 filter-datatable select2')) }}
                    </div>
                    <div class="w-250px pull-left">
                        {{ Form::select('target_id',  [''=>''], fltr_val(Request::all(), 'target_id'), array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Procurar viatura, motorista ou envio')) }}
                    </div>
                </li>--}}
                <li style="margin-bottom: 5px;" class="col-xs-6">
                    <strong>@trans('Já em Sistema')</strong><br/>
                    <div class="w-100px">
                        {{ Form::select('ignore_stats', ['' => __('Todos'), '1' => __('Sim'), '0' => __('Não')], fltr_val(Request::all(), 'ignore_stats'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
                <li style="width: 100px">
                    <div class="checkbox p-t-22">
                        <label>
                            {{ Form::checkbox('deleted', 1, Setting::get('deleted')) }}
                            @trans('Anulados')
                        </label>
                    </div>
                </li>
            </ul>
        </div>
        <div class="table-responsive">
            <table id="datatable-purchase-invoices" class="table table-striped table-dashed table-hover table-condensed">
                <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-65px">@trans('Data')</th>
                    <th class="w-65px">@trans('N.º Doc.')</th>
                    <th>@trans('Referência')</th>
                    <th class="w-80px">@trans('Tipo Doc.')</th>
                    @if(Setting::get('billing_show_cred_deb_column'))
                        <th class="w-60px">@trans('Débito')</th>
                        <th class="w-60px">@trans('Crédito')</th>
                    @else
                        <th class="w-60px">@trans('Total')</th>
                    @endif
                    <th class="w-70px">@trans('Por Pagar')</th>
                    <th class="w-80px">@trans('Vencimento')</th>
                    <th class="w-1">@trans('Pago')</th>
                    <th class="w-65px">@trans('Ações')</th>
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
                    <i class="fas fa-fw fa-check"></i> @trans('Liquidar Selecionados')
                </a>

                <div class="btn-group btn-group-sm dropup m-l-5">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-print"></i> @trans('Imprimir') <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ route('admin.printer.invoices.purchase.balance', $provider->id) }}" data-toggle="datatable-action-url" target="_blank">
                                @trans('Conta Corrente')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.printer.invoices.purchase.listing') }}" data-toggle="datatable-action-url" target="_blank">
                                @trans('Listagem Despesas')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.printer.invoices.purchase.listing', ['grouped' => 1]) }}" data-toggle="datatable-action-url" target="_blank">
                                @trans('Listagem Despesas (por tipo)')
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('admin.export.invoices.purchase') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
                    <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                </a>
            </div>
            <div class="pull-left">
                <h4 style="margin: -2px 0 -6px 10px;
                        padding: 1px 3px 3px 9px;
                        border-left: 1px solid #999;
                        line-height: 17px;">
                    <small>@trans('Total Selecionado')</small><br/>
                    <span class="dt-sum-total bold"></span><b>€</b>
                </h4>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
@endif