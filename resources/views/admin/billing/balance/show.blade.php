<?php $hash = str_random(10) ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Detalhe de Conta Corrente - Cliente</h4>
</div>
<div class="modal-body {{ $hash }}">
    <ul class="list-inline pull-left" style="margin-top: -5px; margin-bottom: 0">
        <li>
            <h4 style="margin-top: -5px">
                <small>Cliente</small>
                <br/>
                <b>{{ $customer->code }} - {{ $customer->name }}</b>
                <small>
                    <a href="{{ route('admin.customers.edit', $customer->id) }}" target="_blank"><i class="fas fa-external-link-alt"></i></a>
                </small>
            </h4>
        </li>
    </ul>
    <ul class="list-inline pull-right" style="margin-top: -5px; margin-bottom: 0">
        <li>
            <h4 style="margin-top: -5px">
                <small>{{ $customer->balance_total < 0.00 ? 'A Devolver' : 'Por Liquidar' }}</small><br/>
                <b class="balance-total-unpaid {{ $customer->balance_total < 0.00 ? 'text-green' : 'text-red' }}">{{ money($customer->balance_total, Setting::get('app_currency')) }}</b>
            </h4>
        </li>
        {{--<li>
            <h4 class="m-l-15" style="margin-top: -5px">
                <small>Débito</small><br/>
                <span class="balance-total-unpaid">{{ money(@$totalDebit, Setting::get('app_currency')) }}</span>
            </h4>
        </li>
        <li>
            <h4 class="m-l-15" style="margin-top: -5px">
                <small>Crédito</small><br/>
                <span class="balance-total-unpaid">{{ money(@$totalCredit, Setting::get('app_currency')) }}</span>
            </h4>
        </li>--}}
        @if($customer->balance_expired_count)
            <li>
                <h4 class="m-l-15" style="margin-top: -5px">
                    <small>Doc. Vencidos</small>
                    <br/>
                    <b class="balance-total-expired">{{ $customer->balance_expired_count }} Documentos</b>
                </h4>
            </li>
        @endif
        @if(@$customer->paymentCondition->name)
            <li>
                <h4 class="m-l-15" style="margin-top: -5px">
                    <small>Pagamento</small>
                    <br/>
                    <b>{{ @$customer->paymentCondition->name }}</b>
                </h4>
            </li>
        @endif
        <li>
        <li>
            <div class="btn-group pull-right" style="margin: -5px -5px -5px;">
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-envelope"></i> Imprimir ou Enviar <i class="caret"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('admin.billing.balance.email.balance.edit', $customer->id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote">
                            <i class="fas fa-fw fa-envelope"></i> Enviar por E-mail
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('admin.printer.invoices.balance', $customer->id) }}" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir Conta Corrente
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.printer.invoices.customers.maps', ['unpaid', 'customer' => $customer->id]) }}"
                           target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir Pendentes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.printer.invoices.customers.maps', ['unpaid', 'customer' => $customer->id, 'expired' => 1]) }}"
                           target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir Vencidos
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
    <div class="clearfix"></div>
    <hr class="m-t-5 m-b-10"/>
    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-balance-customer">
        <li>
            <a href="{{ route('admin.invoices.receipt.create', ['customer' => $customer->id]) }}"
               data-toggle="modal"
               data-target="#modal-remote-xlg"
               class="btn btn-sm btn-success">
                <i class="fas fa-fw fa-receipt"></i> Novo Recibo
            </a>
        </li>
        <li>
            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
            </button>
        </li>
        {{-- <li class="fltr-primary w-125px">
            <strong>Tipo</strong>
            <div class="pull-left form-group-sm w-90px">
                {{ Form::select('sense', ['' => 'Todos', 'debit' => 'Débitos', 'credit' => 'Créditos', 'hidden' => 'Ocultos'], Request::has('sense') ? Request::get('sense') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li> --}}
        <li class="fltr-primary w-180px">
            <strong>Liquidado</strong>
            <div class="w-105px pull-left form-group-sm">
                {{ Form::select('settle', ['' => 'Todos', '1' => 'Pago', '0' => 'Não pago', '2' => 'Pago Parcial', '3' => 'Não pago - Vencido', '4' => 'Não pago - Pendente'], fltr_val(Request::all(), 'settle'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li>
            <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                <label>
                    {{ Form::checkbox('hide_receipts', '1', true) }}
                    Ocultar Recibos
                </label>
            </div>
        </li>
    </ul>
    <div class="datatable-filters-extended m-t-5 hide" data-target="#datatable-balance-customer">
        <ul class="list-inline pull-left">
            <li style="margin-bottom: 5px;"  class="col-xs-6 doc-type-filter">
                <strong>Documento</strong><br/>
                <div class="w-120px">
                    {{ Form::selectMultiple('doc_type', trans('admin/billing.types-list') + ['debit-note' => 'Nota Débito','receipt' => 'Recibos', 'regularization' => 'Regularização', 'nodoc' => 'Sem Documento', 'proforma' => 'Proformas', 'scheduled' => 'Agendados'], fltr_val(Request::all(), 'doc_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                </div>
            </li>
            <li style="margin-bottom: 5px;" class="col-xs-6">
                <strong>Ano</strong><br/>
                <div class="w-80px pull-left form-group-sm" style="position: relative">
                    {{ Form::select('year', ['' => 'Todos'] + $years, fltr_val(Request::all(), 'year'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
            <li style="margin-bottom: 5px;" class="col-xs-6">
                <strong>Mês</strong><br/>
                <div class="w-80px pull-left form-group-sm" style="position: relative">
                    {{ Form::select('month', ['' => 'Todos'] + $months, fltr_val(Request::all(), 'month'), array('class' => 'form-control input-sm filter-datatable select2')) }}
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
            <li style="width: 100px" class="col-xs-6">
                <div class="checkbox p-t-22">
                    <label>
                        {{ Form::checkbox('invoice_deleted', 1, false) }}
                        Anulados
                    </label>
                </div>
            </li>
        </ul>
    </div>
    <div class="table-responsive">
        <table id="datatable-balance-customer"
               class="table table-striped table-dashed table-hover table-condensed margin-0">
            <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th></th>
                <th class="w-75px">Data</th>
                <th class="w-90px">Tipo</th>
                <th>Documento</th>
                <th>Referência</th>
                {{-- <th class="w-50px">Subtotal</th> --}}
                <th class="w-50px">Total</th>
                <th class="w-50px">Pendente</th>
                <th class="w-65px">Saldo</th>
                <th class="w-75px">Vencimento</th>
                <th class="w-1">Estado</th>
                <th class="w-80px">Ações</th>
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
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>

<script>
    $('.{{ $hash }} .select2').select2(Init.select2());
    $('.{{ $hash }} .select2-multiple').select2MultiCheckboxes(Init.select2Multiple());
    $('.{{ $hash }} .datepicker').datepicker(Init.datepicker());

    var oTableBalance;
    oTableBalance = $('.{{ $hash }} #datatable-balance-customer').DataTable({
        columns: [
            {data: 'select', name: 'select', orderable: false, searchable: false},
            {data: 'id', name: 'id', visible: false},
            {data: 'sort', name: 'sort'},
            {data: 'doc_type', name: 'doc_type'},
            {data: 'doc_name', name: 'doc_id'},
            {data: 'reference', name: 'reference'},
            /* {data: 'doc_subtotal', name: 'doc_subtotal', class:'text-right'}, */
            {data: 'doc_total', name: 'doc_total', class:'text-right'},
            {data: 'doc_total_pending', name: 'doc_total_pending', class:'text-right'},
            {data: 'customer_balance', name: 'customer_balance', class:'text-right'},
            {data: 'due_date', name: 'due_date'},
            {data: 'is_settle', name: 'is_settle', class: 'text-center'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false},
            {data: 'billing_name', name: 'billing_name', visible: false},
            {data: 'billing_code', name: 'billing_code', visible: false},
            {data: 'vat', name: 'vat', visible: false},
            {data: 'doc_subtotal', name: 'doc_subtotal', visible: false},
            {data: 'doc_total_pending', name: 'doc_total_pending', visible: false},
            {data: 'doc_date', name: 'doc_date', visible: false},
        ],
        order: [[2, "desc"]],
        ajax: {
            url: "{{ route('admin.invoices.datatable', ['customer' => $customer->id]) }}",
            type: "POST",
            data: function (d) {
                d.draft            = 0;
                d.year             = $('.{{ $hash }} select[name=year]').val();
                d.month            = $('.{{ $hash }} select[name=month]').val();
                d.serie            = $('.{{ $hash }} select[name=serie]').val();
                d.doc_type         = $('.{{ $hash }} select[name=doc_type]').val();
                d.doc_id           = $('.{{ $hash }} input[name=doc_id]').val();
                d.date_min         = $('.{{ $hash }} input[name=date_min]').val();
                d.date_max         = $('.{{ $hash }} input[name=date_max]').val();
                d.settle           = $('.{{ $hash }} select[name=settle]').val();
                d.expired          = $('.{{ $hash }} input[name=expired]').val();
                d.payment_method   = $('.{{ $hash }} select[name=payment_method]').val();
                d.payment_condtion = $('.{{ $hash }} select[name=payment_condition]').val();
                d.deleted          = $('.{{ $hash }} input[name=invoice_deleted]:checked').length;
                d.hide_receipts    = $('.{{ $hash }} input[name=hide_receipts]:checked').length;
            },
            beforeSend: function () {Datatables.cancelDatatableRequest(oTable)},
            complete: function () {Datatables.complete()}
        }
    });

    $('.{{ $hash }} .filter-datatable').on('change', function (e) {
        oTableBalance.draw();
        e.preventDefault();
    });

    $(document).on('change', '.{{ $hash }} [name="hide_receipts"], .{{ $hash }} [name="invoice_deleted"]', function (e) {
        oTableBalance.draw();
    });

    /**
     * Datatable filters
     */
    $(document).on('click', '.{{ $hash }} .btn-filter-datatable', function () {
        var url = Url.current();
        if ($('.{{ $hash }} .datatable-filters-extended').is(':visible')) {
            url = Url.updateParameter(url, 'filter', 1);
        } else {
            url = Url.removeParameter(url, 'filter');
        }
        Url.change(url);
    })

    $('.{{ $hash }} .btn-filter-datatable').on('click', function () {
        $('.{{ $hash }} .datatable-filters-extended').toggleClass('hide');
    })

    $('.{{ $hash }}').on('click', function () {
        $(this).closest('.modal').css('overflow-y', 'scroll')
    })
</script>
