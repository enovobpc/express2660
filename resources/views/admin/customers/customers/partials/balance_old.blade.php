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

        @if(!in_array(Setting::get('invoice_software'), ['SageX3', 'EnovoTms']))
        <ul class="list-inline pull-right">
            <li class="pull-right">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-sync-alt"></i> @trans('Sync') <i class="caret"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modal-sync-balance-all">
                                @trans('Sincronizar Tudo')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.customers.balance.reset', $customer->id) }}"
                               data-method="post"
                               data-confirm-title="Reset Conta Corrente"
                               data-confirm-class="btn-success"
                               data-confirm-label="Recarregar"
                               data-confirm="Confirma a eliminação da conta corrente e o seu carregamento de novo?">
                               @trans('Reset Conta Corrente')
                            </a>
                        </li>
                        {{--<li class="divider"></li>
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modal-sync-balance">
                                Sincronizar Documentos
                            </a>
                        </li>
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modal-update-balance-status">
                                Sincronizar Estados de Pagamento
                            </a>
                        </li>--}}
                    </ul>
                </div>
            </li>
            <li class="text-muted pull-right">
                <div class="text-yellow bold m-t-8" data-toggle="tooltip" title="{{ $lastBalanceDate->format('Y-m-d H:i:s') }}">
                    <span class="balance-update-time"><i class="far fa-clock"></i> @trans('Atualizado há') {{ $balanceDiff > 0 ? $balanceDiff . ' horas' : 'menos de 1 hora' }}</span>
                </div>
            </li>
        </ul>
        @endif

        <ul class="list-inline" style="margin-top: -5px; margin-bottom: 0">
            <li>
                <h3 style="margin-top: -5px">
                    <small>@trans('Por Liquidar')</small><br/>
                    <b class="balance-total-unpaid">{{ money($totalUnpaid, Setting::get('app_currency')) }}</b>
                </h3>
            </li>
            @if($totalExpired)
            <li>
                <h3 class="m-l-15" style="margin-top: -5px">
                    <small>@trans('Doc. Vencidos')</small><br/>
                    <b class="balance-total-expired">{{ $totalExpired }} @trans('Documentos')</b>
                </h3>
            </li>
            @endif
            @if($customer->payment_method)
            <li>
                <h3 class="m-l-15" style="margin-top: -5px">
                    <small>@trans('Pagamento')</small><br/>
                    <b>{{ @$customer->paymentCondition->name }}&nbsp;</b>
                </h3>
            </li>
            @endif
        </ul>
        <hr class="m-t-5 m-b-15"/>
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-balance-old">
            {{--<li>
                <a href="#" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-send-balance-email">
                    <i class="fas fa-envelope"></i> Imprimir ou Enviar <i class="caret"></i>
                </a>
            </li>--}}
            <li>
                {{--<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-envelope"></i> Imprimir ou Enviar <i class="caret"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modal-send-balance-email">
                                <i class="fas fa-fw fa-envelope"></i> Enviar por e-mail
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('admin.printer.invoices.balance', $customer->id) }}" target="_blank">
                                <i class="fas fa-fw fa-print"></i> Imprimir Conta Corrente
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.printer.invoices.customers.maps', ['unpaid', 'customer' => $customer->id]) }}" target="_blank">
                                <i class="fas fa-fw fa-print"></i> Imprimir Pendentes
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.printer.invoices.customers.maps', ['unpaid', 'customer' => $customer->id, 'expired' => 1]) }}" target="_blank">
                                <i class="fas fa-fw fa-print"></i> Imprimir Vencidos
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
                    <i class="fas fa-receipt"></i> Novo Recibo
                </a>
            </li>
            <li>
                <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                    <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                </button>
            </li>
            <li>
                <a href="#" class="btn btn-default btn-sm" target="_blank">
                    <i class="fas fa-print"></i> Imprimir
                </a>
            </li>--}}
            <li class="fltr-primary w-165px">
                <strong>@trans('Estado')</strong><br class="visible-xs"/>
                <div class="w-110px pull-left form-group-sm">
                    {{ Form::select('paid', ['' => 'Todos', '1' => 'Liquidado', '0' => 'Por Liquidar', '3' => 'Liquidado Parcial'], Request::has('paid') ? Request::get('paid') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
            <li>
                <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                    <label>
                        {{ Form::checkbox('hide_payments', '1', true) }}
                        @trans('Ocultar Recibos')
                    </label>
                </div>
            </li>
        </ul>
        <div class="datatable-filters-extended m-t-5 hide" data-target="#datatable-balance-old">
            <ul class="list-inline pull-left">
                <li class="col-xs-12">
                    <strong>@trans('Data Documento')</strong><br/>
                    <div class="input-group input-group-sm w-240px">
                        {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                        <span class="input-group-addon">@trans('até')</span>
                        {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-6 doc-type-filter">
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
                <li style="margin-bottom: 5px;" class="col-xs-6 doc-type-filter">
                    <strong>@trans('Sentido')</strong><br/>
                    <div class="w-120px">
                        {{ Form::select('sense', ['' => 'Todos', 'debit' => 'Débitos', 'credit' => 'Créditos', 'hidden' => 'Ocultos'], Request::has('sense') ? Request::get('sense') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-6 doc-type-filter">
                    <strong>@trans('Série')</strong><br/>
                    <div class="w-120px">
                        {{ Form::selectMultiple('serie', $series, fltr_val(Request::all(), 'serie'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-6 doc-type-filter">
                    <strong>@trans('Documento')</strong><br/>
                    <div class="w-120px">
                        {{ Form::selectMultiple('doc_type', trans('admin/billing.types-list') + ['receipt' => 'Recibos', 'nodoc' => 'Sem Documento', 'proforma' => 'Proformas', 'scheduled' => 'Agendados'], fltr_val(Request::all(), 'doc_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-6">
                    <strong>@trans('Pagamento')</strong><br/>
                    <div class="w-120px">
                        {{ Form::selectMultiple('payment_method', $paymentConditions, fltr_val(Request::all(), 'payment_method'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-6">
                    <strong>@trans('Registado por')</strong><br/>
                    <div class="w-150px">
                        {{ Form::selectMultiple('operator', $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
                    </div>
                </li>
                <li style="width: 100px">
                    <div class="checkbox p-t-20">
                        <label>
                            {{ Form::checkbox('invoice_deleted', 1, false) }}
                            @trans('Anulados')
                        </label>
                    </div>
                </li>
            </ul>
        </div>
        <table id="datatable-balance-old" class="table table-striped table-dashed table-hover table-condensed">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-100px">@trans('Data')</th>
                    <th class="w-90px">@trans('Tipo')</th>
                    <th>@trans('Documento')</th>
                    <th>@trans('Referência')</th>
                    @if(Setting::get('billing_show_cred_deb_column'))
                    <th class="w-60px">@trans('Débito')</th>
                    <th class="w-60px">@trans('Crédito')</th>
                    @else
                    <th class="w-60px">@trans('Total')</th>
                    @endif
                    <th class="w-60px">@trans('Pendente')</th>
                    <th class="w-100px">@trans('Vencimento')</th>
                    <th class="w-1">@trans('Estado')</th>
                    <th class="w-80px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="selected-rows-action hide">
            <div class="pull-left">
                <button class="btn btn-sm btn-default m-l-5" data-toggle="modal" data-target="#modal-mass-invoice-email">
                    <i class="fas fa-envelope"></i> @trans('Enviar por E-mail')
                </button>
                {{-- @include('admin.billing.balance.modals.send_mass_invoice_email') --}}
                <a href="{{ route('admin.printer.invoices.balance', $customer->id) }}"
                   class="btn btn-sm btn-default m-l-5"
                   data-toggle="datatable-action-url"
                   target="_blank">
                    <i class="fas fa-print"></i> @trans('Imprimir')
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
    </div>
</div>
@include('admin.customers.customers.modals.sync_balance')
@include('admin.customers.customers.modals.update_balance_status')
@include('admin.customers.customers.modals.sync_balance_all')
@endif