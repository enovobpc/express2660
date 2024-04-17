@if(!hasModule('customers_balance'))
    @include('admin.partials.denied_message')
@else
<?php
$total = App\Models\Customer::filterSource()->where('vat', $customer->vat)->count();
?>
<div class="box no-border">
    <div class="box-body">
       @if($total > 1)
            <div class="alert alert-info">
                <h4 style="margin: 0">
                    <i class="fas fa-info-circle"></i> @trans('Existem mais') {{ $total - 1}} @trans('clientes com o contribuinte') {{ $customer->vat }}.
                </h4>
                <p class="p-l-20 italic">@trans('Esta conta corrente apresenta apenas os documentos associados a este cliente.')</p>
            </div>
        @endif

        <ul class="list-inline" style="margin-top: -5px; margin-bottom: 0">
            <li>
                <h3 style="margin-top: -5px">
                    <small>@trans('Saldo Conta')</small><br/>
                    <b>{{ money($customer->balance_total, Setting::get('app_currency')) }}</b>
                </h3>
            </li>
            {{-- <li>
                <h5 class="m-0">
                    <small>Crédito</small>
                    {{ money($customer->balance_total_credit, Setting::get('app_currency')) }}
                </h5>
                <h5>
                    <small>Débito</small>
                    {{ money($customer->balance_total_debit, Setting::get('app_currency')) }}
                </h5>
            </li>  --}}
            @if($customer->balance_unpaid_count)
            <li>
                <h3 class="m-l-15" style="margin-top: -5px">
                    <small>@trans('Pendentes')</small><br/>
                    <b>{{ $customer->balance_unpaid_count }} @trans('Docs')</b>
                </h3>
            </li>
            @endif
            @if($customer->balance_expired_count)
            <li>
                <h3 class="m-l-15" style="margin-top: -5px">
                    <small>@trans('Vencidos')</small><br/>
                    <b>{{ $customer->balance_expired_count }} @trans('Docs')</b>
                </h3>
            </li>
            @endif
            @if(@$customer->paymentCondition->name)
            <li>
                <h3 class="m-l-15" style="margin-top: -5px">
                    <small>@trans('Pagamento')</small><br/>
                    <b>{{ @$customer->paymentCondition->name }}&nbsp;</b>
                </h3>
            </li>
            @endif
        </ul>
        <hr class="m-t-5 m-b-15"/>
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-balance">
            <li>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-envelope"></i> @trans('Imprimir ou Enviar') <i class="caret"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modal-send-balance-email">
                                <i class="fas fa-fw fa-envelope"></i> @trans('Enviar por e-mail')
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('admin.printer.invoices.balance', $customer->id) }}" target="_blank">
                                <i class="fas fa-fw fa-print"></i> @trans('Imprimir Conta Corrente')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.printer.invoices.customers.maps', ['unpaid', 'customer' => $customer->id]) }}" target="_blank">
                                <i class="fas fa-fw fa-print"></i> @trans('Imprimir Pendentes')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.printer.invoices.customers.maps', ['unpaid', 'customer' => $customer->id, 'expired' => 1]) }}" target="_blank">
                                <i class="fas fa-fw fa-print"></i> @trans('Imprimir Vencidos')
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <a href="{{ route('admin.invoices.receipt.create', ['customer' => $customer->id]) }}"
                   class="btn btn-default btn-sm"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-receipt"></i> @trans('Novo Recibo')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.invoices.regularization.create', ['customer' => $customer->id]) }}"
                   class="btn btn-default btn-sm"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-receipt"></i> @trans('Regularização')
                </a>
            </li>
            <li>
                <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                    <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                </button>
            </li>
            <li class="fltr-primary w-160px">
                <strong>@trans('Estado')</strong><br class="visible-xs"/>
                <div class="w-105px pull-left form-group-sm">
                    {{ Form::select('settle', ['' => __('Todos'), '1' => __('Pago'), '0' => __('Não pago'), '2' => __('Pago Parcial'), '3' => __('Não pago - Vencido'), '4' => __('Não pago - Pendente')], fltr_val(Request::all(), 'settle'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
            <li>
                <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                    <label>
                        {{ Form::checkbox('hide_receipts', '1', true) }}
                        @trans('Ocultar Recibos')
                    </label>
                </div>
            </li>
            {{-- <li class="fltr-primary w-110px">
                <strong>Nº Doc.</strong><br class="visible-xs"/>
                <div class="w-50px pull-left form-group-sm" style="position: relative">
                    {{ Form::text('doc_id', fltr_val(Request::all(), 'doc_id'), array('class' => 'form-control input-sm filter-datatable', 'style' => 'width: 100%;')) }}
                </div>
            </li> --}}
        </ul>
        <div class="datatable-filters-extended m-t-5 hide" data-target="#datatable-balance">
            <ul class="list-inline pull-left">
                <li style="margin-bottom: 5px;"  class="col-xs-6 doc-type-filter">
                    <strong>@trans('Documento')</strong><br/>
                    <div class="w-120px">
                        {{ Form::selectMultiple('doc_type', trans('admin/billing.types-list') + ['debit-note' => __('Nota Débito'),'receipt' => __('Recibos'), 'regularization' => __('Regularização'), 'nodoc' => __('Sem Documento'), 'proforma' => __('Proformas'), 'scheduled' => __('Agendados')], fltr_val(Request::all(), 'doc_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-6">
                    <strong>@trans('Ano')</strong><br/>
                    <div class="w-80px pull-left form-group-sm" style="position: relative">
                        {{ Form::select('year', ['' => __('Todos')] + $years, fltr_val(Request::all(), 'year'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-6">
                    <strong>@trans('Mês')</strong><br/>
                    <div class="w-80px pull-left form-group-sm" style="position: relative">
                        {{ Form::select('month', ['' => __('Todos')] + $months, fltr_val(Request::all(), 'month'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
                
                <li class="col-xs-12">
                    <strong>@trans('Data Documento')</strong><br/>
                    <div class="input-group input-group-sm w-240px">
                        {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                        <span class="input-group-addon">@trans('até')</span>
                        {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;"  class="col-xs-6 doc-type-filter">
                    <strong>@trans('Vencimento até')</strong><br/>
                    <div class="w-130px">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            {{ Form::text('expired', fltr_val(Request::all(), 'expired'), array('class' => 'form-control input-sm filter-datatable datepicker')) }}
                        </div>
                    </div>
                </li>
                <li style="margin-bottom: 5px;"  class="col-xs-6">
                    <strong>@trans('Condição Pag.')</strong><br/>
                    <div class="w-120px">
                        {{ Form::selectMultiple('payment_condition', $paymentConditions, fltr_val(Request::all(), 'payment_condition'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;"  class="col-xs-6">
                    <strong>@trans('Forma Pagamento')</strong><br/>
                    <div class="w-120px">
                        {{ Form::selectMultiple('payment_method', $paymentMethods, fltr_val(Request::all(), 'payment_method'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;"  class="col-xs-6">
                    <strong>@trans('Registado por')</strong><br/>
                    <div class="w-160px">
                        {{ Form::selectMultiple('operator', $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
                    </div>
                </li>
                <li style="width: 100px" class="col-xs-6">
                    <div class="checkbox p-t-22">
                        <label>
                            {{ Form::checkbox('deleted', 1, false) }}
                            @trans('Anulados')
                        </label>
                    </div>
                </li>
            </ul>
        </div>
        <table id="datatable-balance" class="table table-striped table-dashed table-hover table-condensed">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-75px">@trans('Data')</th>
                    <th class="w-90px">@trans('Tipo')</th>
                    <th>@trans('Documento')</th>
                    <th>@trans('Referência')</th>
                    {{-- <th class="w-50px">Subtotal</th> --}}
                    <th class="w-50px">@trans('Total')</th>
                    <th class="w-50px">@trans('Pendente')</th>
                    <th class="w-65px">@trans('Saldo')</th>
                    <th class="w-75px">@trans('Vencimento')</th>
                    <th class="w-1">@trans('Estado')</th>
                    <th class="w-80px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="selected-rows-action hide">
            <div class="pull-left">
                <a href="{{ route('admin.printer.invoices.summary') }}"
                   class="btn btn-sm btn-default m-l-5"
                   data-action-url="datatable-action-url"
                   target="_blank">
                    <i class="fas fa-fw fa-print"></i> @trans('Imprimir')
                </a>
                <a href="{{ route('admin.export.invoices') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
                    <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                </a>
                <a href="{{ route('admin.invoices.receipt.create', ['type' => 'receipt']) }}"
                   class="btn btn-sm btn-default m-l-5"
                   data-action-url="datatable-action-url"
                   data-toggle="modal"
                   data-target="#modal-remote-xl"
                   target="_blank">
                    <i class="fas fa-fw fa-receipt"></i> @trans('Criar Recibo')
                </a>
                
                @if(hasModule('sepa_transfers'))
                    @if(Auth::user()->perm('sepa_transfers'))
                        <a href="{{ route('admin.sepa-transfers.import.invoices.edit') }}"
                           class="btn btn-sm btn-default m-l-5"
                           data-action-url="datatable-action-url"
                           data-toggle="modal"
                           data-target="#modal-remote-xl">
                            <i class="fas fa-fw fa-file-export"></i> @trans('Criar Débito Direto')
                        </a>
                    @else
                        <a href="#" disabled class="btn btn-sm btn-default m-l-5">
                            <i class="fas fa-fw fa-file-export"></i> @trans('Criar Débito Direto')
                        </a>
                    @endif
                @else
                    <a href="#" disabled
                       class="btn btn-sm btn-default m-l-5"
                       data-toggle="tooltip"
                       data-title="Efetue cobranças por débito direto. Módulo não incluido na sua licença.">
                        <i class="fas fa-fw fa-file-export"></i> @trans('Criar Débito Direto')
                    </a>
                @endif
            </div>
            <div class="pull-left">
                <h4 class="pull-left" style="margin: -2px 0 -6px 10px;
                            padding: 1px 3px 3px 9px;
                            border-left: 1px solid #999;
                            line-height: 17px;">
                    <small>@trans('Subtotal')</small><br/>
                    <span class="dt-sum-subtotal bold"></span><b>{{ Setting::get('app_currency') }}</b>
                </h4>
                <h4 class="pull-left" style="margin: -2px 0 -6px 10px;
                            padding: 1px 3px 3px 9px;
                            line-height: 17px;">
                    <small>@trans('IVA')</small><br/>
                    <span class="dt-sum-vat bold"></span><b>{{ Setting::get('app_currency') }}</b>
                </h4>
                <h4 class="pull-left" style="margin: -2px 0 -6px 10px;
                            padding: 1px 3px 3px 9px;
                            line-height: 17px;">
                    <small>@trans('Total')</small><br/>
                    <span class="dt-sum-total bold"></span><b>{{ Setting::get('app_currency') }}</b>
                </h4>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

@include('admin.customers.customers.modals.send_balance_email')
@endif