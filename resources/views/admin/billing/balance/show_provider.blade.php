<?php 
$hash = str_random(10);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">
        Detalhe de Conta Corrente - Fornecedor
    </h4>
</div>
<div class="modal-body {{ $hash }}">
    <ul class="list-inline pull-left" style="margin-top: -5px; margin-bottom: 0">
        <li>
            <h4 style="margin-top: -5px">
                <small>Fornecedor</small><br/>
                <b>{{ $provider->code }} - {{ $provider->name }}</b>
                <small>
                    <a href="{{ route('admin.providers.edit', $provider->id) }}" target="_blank"><i class="fas fa-external-link-alt"></i></a>
                </small>
            </h4>
        </li>
    </ul>
    <ul class="list-inline pull-right" style="margin-top: -5px; margin-bottom: 0">
        <li>
            <h4 style="margin-top: -5px">
                <small>{{ $totalUnpaid > 0.00 ? 'A Receber' : 'Por Liquidar' }}</small><br/>
                <b class="balance-total-unpaid {{ $totalUnpaid > 0.00 ? 'text-green' : 'text-red' }}">{{ money(@$totalUnpaid, Setting::get('app_currency')) }}</b>
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
        @if(@$totalExpired)
            <li>
                <h4 class="m-l-15" style="margin-top: -5px">
                    <small>Vencidos</small><br/>
                    <b class="balance-total-expired">{{ @$totalExpired }} Docs</b>
                </h4>
            </li>
        @endif
        @if($provider->payment_method)
            <li>
                <h4 class="m-l-15" style="margin-top: -5px">
                    <small>Pagamento</small><br/>
                    <b>{{ @$provider->paymentCondition->name }}</b>
                </h4>
            </li>
        @endif
        <li>
        <li>
            <div class="btn-group pull-right" style="margin: -5px -5px -5px;">
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-envelope"></i> Imprimir <i class="caret"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('admin.printer.invoices.purchase.balance', ['providerId' => $provider->id]) }}" data-toggle="export-url" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir Conta Corrente
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.printer.invoices.purchase.map', ['unpaid', 'provider' => $provider->id]) }}" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir Pendentes
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        </li>
    </ul>
    <div class="clearfix"></div>
    <hr class="m-t-5 m-b-10"/>
    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-balance-customer">
        <li>
            <a href="{{ route('admin.invoices.purchase.payment-notes.create', ['provider' => $provider->id]) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg"
               class="btn btn-sm btn-success">
                <i class="fas fa-check"></i> Novo Pagamento
            </a>
        </li>
        <li>
            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
            </button>
        </li>
        <li class="fltr-primary w-125px">
            <strong>Tipo</strong>
            <div class="pull-left form-group-sm w-90px">
                {{ Form::select('sense', ['' => 'Todos', 'debit' => 'Débitos', 'credit' => 'Créditos', 'hidden' => 'Ocultos'], Request::has('sense') ? Request::get('sense') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li class="fltr-primary w-170px">
            <strong>Liquidado</strong>
            <div class="pull-left form-group-sm w-90px">
                {{ Form::select('paid', ['' => 'Todos', '1' => 'Liquidado', '0' => 'Por Liquidar', '3' => 'Liquidado Parcial'], Request::has('paid') ? Request::get('paid') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li>
            <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                <label>
                    {{ Form::checkbox('hide_payments', '1', true) }}
                    Ocultar Pagamentos
                </label>
            </div>
        </li>
    </ul>
    <div class="datatable-filters-extended m-t-5 hide" data-target="#datatable-balance-customer">
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
            <li style="margin-bottom: 5px;"  class="col-xs-6">
                <strong>Tipo documento</strong><br/>
                <div class="w-140px">
                    {{ Form::selectMultiple('doc_type', trans('admin/billing.types-list-purchase'), fltr_val(Request::all(), 'doc_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                </div>
            </li>
            <li style="margin-bottom: 5px;" class="col-xs-6">
                <strong>Cond. Pgto</strong><br/>
                <div class="w-130px">
                    {{ Form::selectMultiple('payment_condition', $paymentConditions, fltr_val(Request::all(), 'payment_condition'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                </div>
            </li>
            <li style="margin-bottom: 5px;" class="col-xs-6">
                <strong>Registado por</strong><br/>
                <div class="w-150px">
                    {{ Form::selectMultiple('created_by', $operators, fltr_val(Request::all(), 'created_by'), array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple')) }}
                </div>
            </li>
            {{--<li style="width: 100px">
                <div class="checkbox p-t-22">
                    <label>
                        {{ Form::checkbox('deleted', 1, true) }}
                        Anulados
                    </label>
                </div>
            </li>--}}
        </ul>
    </div>
    <div class="table-responsive">
        <table id="datatable-balance-customer" class="table table-striped table-dashed table-hover table-condensed margin-0">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-65px">Data</th>
                    <th class="w-65px">Nº Doc</th>
                    <th>Referência</th>
                    <th class="w-80px">Tipo Doc.</th>
                    @if(Setting::get('billing_show_cred_deb_column'))
                    <th class="w-65px">Débito</th>
                    <th class="w-65px">Crédito</th>
                    @else
                    <th class="w-65px">Total</th>
                    @endif
                    <th class="w-70px">Pendente</th>
                    <th class="w-85px">Vencimento</th>
                    <th class="w-1">Pago</th>
                    <th class="w-1">Ações</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="selected-rows-action hide">
            <div class="pull-left">
                <a href="{{ route('admin.printer.invoices.purchase.listing') }}"
                   class="btn btn-sm btn-default m-l-5"
                   data-toggle="datatable-action-url"
                   target="_blank">
                    <i class="fas fa-print"></i> Imprimir
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
            {data: 'doc_date', name: 'doc_date'},
            {data: 'code', name: 'code'},
            {data: 'reference', name: 'reference'},
            {data: 'doc_type', name: 'doc_type'},
            @if(Setting::get('billing_show_cred_deb_column'))
            {data: 'debit', name: 'total'},
            {data: 'credit', name: 'credit', orderable: false, searchable: false},
            @else
            {data: 'total', name: 'total', class: 'text-right'},
            @endif
            {data: 'total_unpaid', name: 'total_unpaid', class:'text-right'},
            {data: 'due_date', name: 'due_date'},
            {data: 'payment_date', name: 'payment_date', class: 'text-center', searchable: false},
            {data: 'print_button', name: 'print_button', orderable: false, searchable: false},
            {data: 'billing_name', name: 'billing_name', visible: false},
            {data: 'vat', name: 'vat', visible: false},
            {data: 'total', name: 'total', visible: false},
        ],
        order: [[2, "desc"]],
        ajax: {
            url: "{{ route('admin.billing.balance.datatable.balance', [$provider->id, 'source' => 'providers']) }}",
            type: "POST",
            data: function (d) {
                d.sense             = $('.{{ $hash }} select[name="sense"]').val(),
                d.paid              = $('.{{ $hash }} select[name="paid"]').val(),
                d.hide_payments     = $('.{{ $hash }} input[name=hide_payments]:checked').length
                d.expired           = $('.{{ $hash }} select[name=expired]').val();
                d.type              = $('.{{ $hash }} select[name=type]').val();
                d.ignore_stats      = $('.{{ $hash }} select[name=ignore_stats]').val();
                d.doc_type          = $('.{{ $hash }} select[name=doc_type]').val();
                d.doc_id            = $('.{{ $hash }} input[name=doc_id]').val();
                d.provider          = $('.{{ $hash }} select[name=provider]').val();
                d.date_unity        = $('.{{ $hash }} select[name=date_unity]').val();
                d.date_min          = $('.{{ $hash }} input[name=date_min]').val();
                d.date_max          = $('.{{ $hash }} input[name=date_max]').val();
                d.payment_condition = $('.{{ $hash }} select[name=payment_condition]').val();
                d.created_by        = $('.{{ $hash }} select[name=created_by]').val();
                d.deleted           = 0;
            },
            beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
            complete: function () { Datatables.complete(); },
            error: function () { Datatables.error(); }
        }
    });

    $('.{{ $hash }} .filter-datatable').on('change', function (e) {
        oTableBalance.draw();
        e.preventDefault();
    });

    $(document).on('change', '.{{ $hash }} [name="hide_payments"]', function (e) {
        oTableBalance.draw();
    });

    /**
     * Datatable filters
     */
    $(document).on('click', '.{{ $hash }} .btn-filter-datatable', function(){
        var url = Url.current();
        if($('.{{ $hash }} .datatable-filters-extended').is(':visible')) {
            url = Url.updateParameter(url, 'filter', 1);
        } else {
            url = Url.removeParameter(url, 'filter');
        }
        Url.change(url);
    })

    $('.{{ $hash }} .btn-filter-datatable').on('click', function(){
        $('.{{ $hash }} .datatable-filters-extended').toggleClass('hide');
    })
</script>
