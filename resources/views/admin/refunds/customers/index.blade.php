@section('title')
    Gestão de Reembolsos
@stop

@section('content-header')
    Gestão de Reembolsos
@stop

@section('breadcrumb')
    <li class="active">Gestão de Reembolsos</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs tabs-filter">
                    <li class="{{ Request::get('tab') == 'all' ? 'active' : '' }}">
                        <a href="all" data-type="" data-toggle="tab">
                            Todos
                        </a>
                    </li>
                    @if(Setting::get('refunds_request_mode'))
                    <li class="{{ Request::get('tab') == 'requested' || (Request::get('refund_status') == '7' || (!Request::get('tab')) && !Request::get('refund_status')) ? 'active' : '' }}">
                        <a href="requested" data-type="7" data-toggle="tab">
                            <i class="fas fa-user"></i> Solicitado
                        </a>
                    </li>
                    @endif
                    <li class="{{ (Request::get('tab') == 'pendings' ||  (!Request::has('tab') && !Request::get('refund_status') && !Setting::get('refunds_request_mode'))) ? 'active' : '' }}">
                        <a href="pendings" data-type="1" data-toggle="tab">
                            <i class="far fa-clock"></i> Por Receber
                        </a>
                    </li>
                    <li class="{{ Request::get('refund_status') == '2' ? 'active' : '' }}">
                        <a href="received" data-type="2" data-toggle="tab">
                            <i class="far fa-check-circle"></i> Por Devolver
                        </a>
                    </li>
                    <li class="{{ Request::get('refund_status') == '3' ? 'active' : '' }}">
                        <a href="concluded" data-type="3" data-toggle="tab">
                            <i class="fas fa-check-circle"></i> Finalizados
                        </a>
                    </li>
                    <li class="{{ Request::get('refund_status') == '4' ? 'active' : '' }}">
                        <a href="not-received" data-type="4" data-toggle="tab">
                            <i class="fas fa-exclamation-circle"></i> Devolvido e Não recebido
                        </a>
                    </li>
                    <li class="{{ Request::get('refund_status') == '5' ? 'active' : '' }}">
                        <a href="claimed" data-type="5" data-toggle="tab">
                            <i class="fas fa-gavel"></i> Reclamado
                        </a>
                    </li>
                    <li class="{{ Request::get('refund_status') == '6' ? 'active' : '' }}">
                        <a href="claimed" data-type="6" data-toggle="tab">
                            <i class="fas fa-times"></i> Cancelado
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane  {{ Request::get('grouped') == '1' ? '' : (Setting::get('refunds_request_mode') ? (Request::has('tab') && Request::get('tab') != 'requested' ? 'active' : '') : 'active') }}" id="tab-shipments">
                        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                            {{ Form::select('refund_status', ['' => '', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7'=>'7'], Request::has('refund_status') ? Request::get('refund_status') : (Setting::get('refunds_request_mode') ? 7 : 1), array('class' => 'filter-datatable hide')) }}
                            <li>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-success"
                                            data-toggle="modal"
                                            data-target="#modal-refund-import">
                                        <i class="fas fa-file-excel"></i> Importar
                                    </button>
                                </div>
                            </li>
                            <li>
                                <a href="{{ route('admin.operator-refunds.index') }}"
                                    class="btn btn-sm btn-default">
                                    <i class="fas fa-user"></i> Confer. Motorista
                                </a>
                            </li>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-print"></i> Imprimir/Exportar <i class="fas fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('admin.printer.refunds.customers.summary', !Request::has('refund_status') ? ['refund_status' => '1'] + Request::all() : Request::all()) }}" data-toggle="print-url" target="_blank">
                                            <i class="fas fa-fw fa-print"></i> Imprimir listagem atual
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.printer.refunds.customers.proof',['shipmentId' => 'selectedlist'] + (!Request::has('refund_status') ? ['refund_status' => '1'] + Request::all() : Request::all())) }}" data-toggle="print-url"  target="_blank">
                                            <i class="fas fa-fw fa-print"></i> Comprovativo lista atual
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.export.refunds.customers', !Request::has('refund_status') ? ['refund_status' => '1'] + Request::all() : Request::all()) }}" data-toggle="export-url">
                                            <i class="fas fa-fw fa-file-excel"></i> Exportar Listagem Atual
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <li>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-filter-datatable btn-default">
                                        <i class="fas fa-filter"></i> <i class="fas fa-angle-down"></i>
                                    </button>
                                </div>
                            </li>
                            <li class="fltr-primary w-290px">
                                <strong>Cliente</strong><br class="visible-xs"/>
                                <div class="w-230px pull-left form-group-sm">
                                    {{ Form::select('customer',  Request::has('customer') ? [''=>'', Request::get('customer') => Request::get('customer-text')] : [''=>''], Request::has('customer') ? Request::get('customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
                                </div>
                            </li>
                            <li class="fltr-primary w-190px">
                                <strong>Motorista</strong><br class="visible-xs"/>
                                <div class="w-120px pull-left form-group-sm">
                                    {{ Form::selectMultiple('operator', ['not-assigned' => 'Sem operador'] + $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                </div>
                            </li>
                            @if(Setting::get('shipments_limit_search'))
                            <li class="fltr-primary w-130px">
                                <div class="checkbox p-t-5">
                                    <label>
                                        {{ Form::checkbox('limit_search', 1, Request::has('limit_search') ? Request::get('limit_search') : true) }}
                                        Últimos {{ Setting::get('shipments_limit_search') }} meses
                                    </label>
                                </div>
                            </li>
                            @endif
                            <li class="fltr-primary w-150px filter-grouped">
                                <div class="checkbox p-t-5">
                                    <label>
                                        {{ Form::checkbox('grouped', 1, Request::has('grouped') ? Request::get('grouped') : null) }}
                                        Agrupar por cliente
                                    </label>
                                </div>
                            </li>
                        </ul>
                        <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                            @include('admin.refunds.customers.partials.filters')
                        </div>
                        <div class="table-responsive table-default">
                            <table id="datatable" class="table w-100 table-striped table-condensed table-dashed table-hover">
                                <thead>
                                <tr>
                                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                    <th></th>
                                    <th class="th-standard w-90px">TRK</th>
                                    @if(Setting::get('refunds_show_shipment_ref'))
                                    <th class="th-standard w-90px">Referência</th>
                                    @endif
                                    <th class="th-standard">Remetente</th>
                                    <th class="th-standard">Destinatário</th>
                                    <th class="th-standard w-75px">Data Envio</th>
                                    <th class="th-standard w-60px">Entrega</th>
                                    <th class="th-standard w-1">Valor</th>
                                    <th class="th-standard w-90px">Recebimento</th>
                                    <th class="th-standard w-90px">Devolução</th>
                                    <th class="th-standard w-1"><i class="fas fa-check" data-toggle="tooltip" title="Confirmado pelo Cliente"></i></th>
                                    <th class="th-standard w-170px">Observações</th>
                                    <th class="w-1"></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="selected-rows-action hide">
                            <div>
                                @if(Auth::user()->isAdmin() || Auth::user()->can('refunds_customers'))
                                    <button class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-refund-all">
                                        <i class="fas fa-pencil-alt"></i> Receber/Devolver Reembolsos
                                    </button>
                                    <button class="btn btn-sm btn-default m-l-5" data-toggle="modal" data-target="#modal-mass-refund-destroy">
                                        <i class="fas fa-times"></i> Cancelar Reembolsos
                                    </button>
                                    @include('admin.refunds.customers.modals.mass_edit')
                                    @include('admin.refunds.customers.modals.mass_delete')
                                @endif
                                <div class="pull-left">
                                <div class="btn-group btn-group-sm dropup m-l-5">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-print"></i> Imprimir <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('admin.printer.refunds.customers.proof') }}" data-toggle="datatable-action-url" target="_blank">
                                                Comprovativo Reembolso
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.printer.refunds.customers.summary') }}" data-toggle="datatable-action-url" target="_blank">
                                                Listagem de Resumo
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.export.refunds.customers') }}" data-toggle="datatable-action-url" target="_blank" class="btn btn-sm btn-default">
                                    <i class="fas fa-file-excel"></i> Exportar
                                </a>
                                </div>
                                <div class="pull-left">
                                    <h4 style="margin: -2px 0 -6px 10px;
                    padding: 1px 3px 3px 9px;
                    border-left: 1px solid #999;
                    line-height: 17px;">
                                        <small>Total Selecionado</small><br/>
                                        <span class="dt-sum-total bold"></span><b>{{ Setting::get('app_currency') }}</b>
                                    </h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>


                    <div class="tab-pane {{ Request::get('grouped') == '1' ? 'active' : '' }}" id="tab-shipments-grouped">
                        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-grouped">
                            {{ Form::select('refund_status', ['' => '', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7'=>'7'], Request::has('refund_status') ? Request::get('refund_status') : (Setting::get('refunds_request_mode') ? 7 : 1), array('class' => 'filter-datatable hide')) }}
                            
                            <li>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-success"
                                            data-toggle="modal"
                                            data-target="#modal-refund-import">
                                        <i class="fas fa-file-excel"></i> Importar
                                    </button>
                                </div>
                            </li>
                            <li>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-filter-datatable btn-default">
                                        <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                                    </button>
                                </div>
                            </li>
                            <li class="fltr-primary w-290px">
                                <strong>Cliente</strong><br class="visible-xs"/>
                                <div class="w-230px pull-left form-group-sm">
                                    {{ Form::select('customer',  Request::has('customer') ? [''=>'', Request::get('customer') => Request::get('customer-text')] : [''=>''], Request::has('customer') ? Request::get('customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
                                </div>
                            </li>
                            <li class="fltr-primary w-150px filter-grouped">
                                <div class="checkbox p-t-5">
                                    <label>
                                        {{ Form::checkbox('grouped', 1, Request::has('grouped') ? Request::get('grouped') : Setting::get('grouped')) }}
                                        Agrupar por cliente
                                    </label>
                                </div>
                            </li>
                        </ul>
                        <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-grouped">
                            @include('admin.refunds.customers.partials.filters')
                        </div>
                        <div class="table-responsive table-default">
                            <table id="datatable-grouped" class="table w-100 table-striped table-condensed table-dashed table-hover">
                                <thead>
                                <tr>
                                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                    <th></th>
                                    <th class="w-1">Nº</th>
                                    <th>Cliente</th>
                                    <th class="w-75px">Mais Antigo</th>
                                    <th class="w-1">Envios</th>
                                    <th class="w-90px">Valor Total</th>
                                    <th class="w-1"></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>



                    @if(Setting::get('refunds_request_mode'))
                    <div class="tab-pane {{ Request::get('tab') == 'requested' || (Request::get('refund_status') == '7' || (!Request::get('tab')) && !Request::get('refund_status')) ? 'active' : '' }}" id="tab-requested">
                        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-requested">
                            <li>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-filter-datatable btn-default">
                                        <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                                    </button>
                                </div>
                            </li>
                            <li class="fltr-primary w-300px">
                                <strong>Cliente</strong><br class="visible-xs"/>
                                <div class="w-230px pull-left form-group-sm">
                                    {{ Form::select('customer',  Request::has('customer') ? [''=>'', Request::get('customer') => Request::get('customer-text')] : [''=>''], Request::has('customer') ? Request::get('customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
                                </div>
                            </li>
                        </ul>
                        <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-requested">
                            <ul class="list-inline pull-left">
                                <li class="col-xs-12">
                                    <strong>Data</strong><br/>
                                    <div class="input-group input-group-sm w-240px">
                                        {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                                        <span class="input-group-addon">até</span>
                                        {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                                    </div>
                                </li>
                                <li style="margin-bottom: 5px;"  class="col-xs-6">
                                    <strong>Forma Solicitada</strong><br/>
                                    <div class="w-160px">
                                        {{ Form::selectMultiple('requested_method', trans('admin/refunds.payment-methods-list'), Request::has('requested_method') ? Request::get('requested_method') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                    </div>
                                </li>
                                <li style="margin-bottom: 5px;"  class="col-xs-6">
                                    <strong>Forma Reembolso</strong><br/>
                                    <div class="w-160px">
                                        {{ Form::selectMultiple('payment_method', trans('admin/refunds.payment-methods-list'), Request::has('payment_method') ? Request::get('payment_method') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                    </div>
                                </li>
                                <li style="margin-bottom: 5px;"  class="col-xs-12">
                                    <strong>Data Reembolso</strong><br/>
                                    <div class="w-130px">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            {{ Form::text('payment_date', Request::has('payment_date') ? Request::get('payment_date') : null, ['class' => 'form-control input-sm datepicker m-b-15 filter-datatable', 'autocomplete' => 'field-1']) }}
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="table-responsive">
                            <table id="datatable-requested" class="table w-100 table-striped table-condensed table-dashed table-hover">
                                <thead>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-95px">Data Pedido</th>
                                <th>Cliente</th>
                                <th class="w-1">Envios</th>
                                <th class="w-80px">Total</th>
                                <th class="w-160px">Reembolso</th>
                                <th class="w-160px">Devolução</th>
                                <th class="w-140px">Data Devolução</th>
                                <th class="w-75px">Estado</th>
                                <th class="w-1"></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="selected-rows-action hide">
                            <div>
                                <div class="btn-group btn-group-sm dropup m-l-5">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-print"></i> Imprimir <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('admin.printer.refunds.customers.proof') }}" data-toggle="datatable-action-url" target="_blank">
                                                Comprovativo Reembolso
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.printer.refunds.customers.summary') }}" data-toggle="datatable-action-url" target="_blank">
                                                Listagem de Resumo
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.export.refunds.customers') }}" data-toggle="datatable-action-url" target="_blank" class="btn btn-sm btn-default">
                                    <i class="fas fa-file-excel"></i> Exportar
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('admin.refunds.customers.modals.import')
@stop

@section('scripts')
<script type="text/javascript">

    @if(Session::has('printProof'))
    window.open('{!! Session::get('printProof') !!}', '_blank');
    @endif

    @if(Session::has('printSummary'))
    window.open('{!! Session::get('printSummary') !!}', '_blank');
    @endif

    $(document).ready(function(){
        $('.filter-grouped').hide();
        if($('.tabs-filter li.active a').data('type') == '2') {
            $('.filter-grouped').show();
        }
    })

    $('.tabs-filter a').on('click', function(e){
        e.preventDefault();
        var type = $(this).data('type');

        if(type == 7) {
            $('#tab-shipments').hide();
            $('#tab-requested').show();
        } else {
            $('#tab-shipments').show();
            $('#tab-requested').hide();

            $('.tabs-filter li').removeClass('active');
            $(this).closest('li').addClass('active');

            $('[name="refund_status"]').val(type).trigger('change');
        }

        $('.filter-grouped').hide();
        $('#tab-shipments-grouped').hide()
        if(type == 2) {
            $('.filter-grouped').show();
            if($('[name="grouped"]:checked').length > 0) {
                $('#tab-shipments').hide()
                $('#tab-shipments-grouped').show()
            } else {
                $('#tab-shipments').show()
                $('#tab-shipments-grouped').hide()
            }
        }
    })

    $('[data-target="#modal-refund-all"]').on('click', function(){
        var multipleCustomers = false;
        var lastId = '';
        var curId  = '';
        var email  = '';
        var rowsCount = 0;
        var rowsTotal = 0;
        $('.row-selected .lbl-customer').each(function(){
            curId = $(this).data('id');
            email = $(this).data('email');
            name  = $(this).data('name');
            iban  = $(this).data('iban');
            ibanLabel = (iban == '') ? 'Não definido na ficha de cliente.' : iban;
            total = $(this).data('total');
            rowsCount++;
            rowsTotal+= parseFloat(total);

            if(lastId != curId){
                if(lastId != '') {
                    multipleCustomers = true;
                }
                lastId = curId;
            }
        })

        if(multipleCustomers) {
            $('#modal-refund-all .extra-options .input-email input').prop('disabled', true).prop('disabled', true);
            $('#modal-refund-all [name="email"]').val('')
            $('#modal-refund-all .extra-options .input-group')
                .attr('title', 'Não pode enviar e-mail porque os reembolsos selecionados não pertencem todos ao mesmo cliente.')
                .attr('data-original-title', 'Não pode enviar e-mail porque os reembolsos selecionados não pertencem todos ao mesmo cliente.')

            $('#modal-refund-all .refund-name').html('Multiplos clientes')
        } else {
            $('#modal-refund-all .extra-options .input-email input').prop('disabled', false).prop('disabled', false);
            $('#modal-refund-all [name="email"]').val(email)
            $('#modal-refund-all .refund-name').html(name)
            $('#modal-refund-all .extra-options .input-group')
                .attr('title', '')
                .attr('data-original-title', '')
        }

        rowsTotal = rowsTotal.toFixed(2)
        $('#modal-refund-all .refund-counter').html(rowsCount)
        $('#modal-refund-all .refund-total').html(rowsTotal)

        $('#modal-refund-all [name="multiple_customers"]').val(multipleCustomers ? 1 : 0)
        $('#modal-refund-all .modal-alert .iban-lbl').html(ibanLabel);
        $('#modal-refund-all .modal-alert .iban-nospaces').html(Str.nospace(iban));
        $('#modal-refund-all .iban-input').val(iban);
    })

    //btn edit
    $('#modal-refund-all .btn-iban-edit').on('click', function () {
        $('#modal-refund-all .modal-alert .input-iban-edit').show();
        $('#modal-refund-all .modal-alert .iban-lbl, #modal-refund-all .btn-iban-copy, #modal-refund-all .btn-iban-edit').hide();
    })

    //btn save
    $('#modal-refund-all .btn-iban-save').on('click', function () {
        var newIban = $('#modal-refund-all .iban-input').val();
        var newIbanNospace = Str.nospace(newIban);
        var curIban = $('#modal-refund-all .modal-alert .iban-lbl').html();

        $('#modal-refund-all .modal-alert .input-iban-edit').hide();
        $('#modal-refund-all .modal-alert .iban-lbl, #modal-refund-all .btn-iban-copy, #modal-refund-all .btn-iban-edit').show();
        $('#modal-refund-all .modal-alert .iban-lbl').html(newIban);
        $('#modal-refund-all .modal-alert .iban-nospaces').html(newIbanNospace);
        $('.iban-nospaces').html(newIbanNospace);

        if(curIban != newIban) {
            $('#modal-refund-all [name="save_iban"]').val(1);
        }
    })

    //btn cancel
    $('#modal-refund-all .btn-iban-cancel').on('click', function () {
        $('#modal-refund-all .modal-alert .input-iban-edit').hide();
        $('#modal-refund-all .modal-alert .iban-lbl, #modal-refund-all .btn-iban-copy, #modal-refund-all  .btn-iban-edit').show();
    })

    $('.modal [name="payment_method"]').on('change', function(){
        if($(this).val() == 'transfer' && $('#modal-refund-all [name="multiple_customers"]').val() == '0') {
            $('.modal-alert').show();
        } else {
            $('.modal-alert').hide();
        }
    })

    $('#modal-refund-all [name="received_method"], #modal-refund-all [name="received_date"], #modal-refund-all [name="payment_method"],#modal-refund-all [name="payment_date"]').on('change', function(){
        $('#modal-refund-all .form-group').removeClass('has-error')
    })

    $(document).on('hidden.bs.modal', '#modal-refund-all', function(){
        $('#modal-refund-all select, #modal-refund-all input[type="text"], #modal-refund-all input[type="hidden"], #modal-refund-all textarea').val('')
        $('select').trigger('change.select2')
    });

    $('#modal-refund-all form').on('submit', function(e){
        var $receivedMethod = $('#modal-refund-all [name="received_method"]');
        var $receivedDate   = $('#modal-refund-all [name="received_date"]');
        var $paymentMethod  = $('#modal-refund-all [name="payment_method"]');
        var $paymentDate    = $('#modal-refund-all [name="payment_date"]');


        if(($receivedMethod.val() != '' && $receivedDate.val() == '')
            || ($receivedMethod.val() == '' && $receivedDate.val() != '')) {

            if($receivedMethod.val() == ''){
                $receivedMethod.closest('.form-group').addClass('has-error');
                Growl.error('É obrigatório indicar a forma de recebimento.')
            } else {
                $receivedDate.closest('.form-group').addClass('has-error');
                Growl.error('É obrigatório indicar a data de recebimento.')
            }
            return false;
        }

        if(($paymentMethod.val() != '' && $paymentDate.val() == '')
            || ($paymentMethod.val() == '' && $paymentDate.val() != '')) {

            if($paymentMethod.val() == ''){
                $paymentMethod.closest('.form-group').addClass('has-error');
                Growl.error('É obrigatório indicar a forma de reembolso.')
            } else {
                $paymentDate.closest('.form-group').addClass('has-error');
                Growl.error('É obrigatório indicar a data de reembolso.')
            }
            return false;
        }
    })

    var oTable, oTableRequests, oTableGrouped;
    $(document).ready(function () {

        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'tracking_code', name: 'tracking_code', visible: false},
                {data: 'shipments.id', name: 'shipments.id'},
                @if(Setting::get('refunds_show_shipment_ref'))
                {data: 'reference', name: 'reference'},
                @endif
                {data: 'sender_name', name: 'sender_name'},
                {data: 'recipient_name', name: 'recipient_name'},
                {data: 'date', name: 'date', searchable: false, class: 'text-center'},
                {data: 'delivery_date', name: 'delivery_date', orderable: false, searchable: false, class: 'text-center'},
                {data: 'charge_price', name: 'charge_price', class: 'text-center'},
                {data: 'refund_control.received_date', name: 'refund_control.received_date', searchable: false},
                {data: 'refund_control.payment_date', name: 'refund_control.payment_date', searchable: false},
                {data: 'confirmed', name: 'confirmed', class: 'text-center', orderable: false, searchable: false},
                {data: 'refund_control.obs', name: 'obs', orderable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'provider_tracking_code', name: 'provider_tracking_code', visible: false},
                {data: 'refund_control.customer_obs', name: 'refund_control.customer_obs', visible: false},
                {data: 'reference2', name: 'reference2', visible: false},
            ],
            order: [[5, "desc"]],
            ajax: {
                url: "{{ route('admin.refunds.customers.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.refund_status             = $('#tab-shipments select[name=refund_status]').val();
                    d.received_method           = $('#tab-shipments select[name=received_method]').val();
                    d.payment_method            = $('#tab-shipments select[name=payment_method]').val();
                    d.operator_received_method  = $('#tab-shipments select[name=operator_received_method]').val();
                    d.received_date_min         = $('#tab-shipments input[name=received_date_min]').val();
                    d.received_date_max         = $('#tab-shipments input[name=received_date_max]').val();
                    d.payment_date_min          = $('#tab-shipments input[name=payment_date_min]').val();
                    d.payment_date_max          = $('#tab-shipments input[name=payment_date_min]').val();
                    d.confirmed                 = $('#tab-shipments select[name=confirmed]').val();
                    d.customer                  = $('#tab-shipments select[name=customer]').val();
                    d.date_unity                = $('#tab-shipments select[name=date_unity]').val();
                    d.date_min                  = $('#tab-shipments input[name=date_min]').val();
                    d.date_max                  = $('#tab-shipments input[name=date_max]').val();
                    d.shipment_status           = $('#tab-shipments select[name=shipment_status]').val();
                    d.provider                  = $('#tab-shipments select[name=provider]').val();
                    d.operator                  = $('#tab-shipments select[name=operator]').val();
                    d.route                     = $('#tab-shipments select[name=route]').val();
                    d.agency                    = $('#tab-shipments select[name=agency]').val();
                    d.sender_agency             = $('#tab-shipments select[name=sender_agency]').val();
                    d.recipient_agency          = $('#tab-shipments select[name=recipient_agency]').val();
                    d.sender_country            = $('#tab-shipments select[name=sender_country]').val();
                    d.recipient_country         = $('#tab-shipments select[name=recipient_country]').val();
                    d.delayed_reception         = $('#tab-shipments select[name=delayed_reception]').val();
                    d.limit_search              = $('input[name=limit_search]:checked').length;
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); }
            }
        });

        $('#tab-shipments .filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();

            var refundStatus = $('#tab-shipments select[name=refund_status]').val();

            $('[data-toggle="export-url"]').each(function() {
                href = $(this).attr('href');
                exportUrl = Url.removeQueryString(href);
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                exportUrl = Url.updateParameter(exportUrl, 'refund_status', refundStatus);
                $(this).attr('href', exportUrl);
            });

            $('[data-toggle="print-url"]').each(function() {
                href = $(this).attr('href');
                printUrl = Url.removeQueryString(href);
                printUrl = printUrl + '?' + Url.getQueryString(Url.current())
                printUrl = Url.updateParameter(printUrl, 'refund_status', refundStatus);
                $(this).attr('href', printUrl);
            })
        });

        

        //show concluded shipments
        $(document).on('change', '[name="limit_search"]', function (e) {
            oTable.draw();
            e.preventDefault();

            var name = $(this).attr('name');
            var value = $(this).is(':checked');
            value = value == false ? 0 : 1;

            newUrl = Url.updateParameter(Url.current(), name, value)
            Url.change(newUrl);

            $('[data-toggle="export-url"]').each(function() {
                href = $(this).attr('href');
                exportUrl = Url.removeQueryString(href);
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $(this).attr('href', exportUrl);
            });

            $('[data-toggle="print-url"]').each(function() {
                href = $(this).attr('href');
                printUrl = Url.removeQueryString(href);
                printUrl = printUrl + '?' + Url.getQueryString(Url.current())
                $(this).attr('href', printUrl);
            })

        });


        oTableGrouped = $('#datatable-grouped').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'tracking_code', name: 'tracking_code', visible: false},
                {data: 'code', name: 'customer.code'},
                {data: 'customer', name: 'customer.name'},
                {data: 'oldest', name: 'oldest', searchable: false, orderable: false},
                {data: 'count', name: 'count', class:'text-center', searchable: false},
                {data: 'total', name: 'total', searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                /*{data: 'recipient_name', name: 'recipient_name', visible: false},*/
                {data: 'sender_name', name: 'sender_name', visible: false},
                {data: 'charge_price', name: 'charge_price', visible: false},
            ],
            order: [[6, "desc"]],
            ajax: {
                url: "{{ route('admin.refunds.customers.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.grouped           = 1;
                    d.refund_status     = $('#tab-shipments select[name=refund_status]').val();
                    d.received_method   = $('#tab-shipments select[name=received_method]').val();
                    d.payment_method    = $('#tab-shipments select[name=payment_method]').val();
                    d.received_date_min = $('#tab-shipments input[name=received_date_min]').val();
                    d.received_date_max = $('#tab-shipments input[name=received_date_max]').val();
                    d.payment_date_min  = $('#tab-shipments input[name=payment_date_min]').val();
                    d.payment_date_max  = $('#tab-shipments input[name=payment_date_min]').val();
                    d.confirmed         = $('#tab-shipments select[name=confirmed]').val();
                    d.customer          = $('#tab-shipments select[name=customer]').val();
                    d.date_unity        = $('#tab-shipments select[name=date_unity]').val();
                    d.date_min          = $('#tab-shipments input[name=date_min]').val();
                    d.date_max          = $('#tab-shipments input[name=date_max]').val();
                    d.shipment_status   = $('#tab-shipments select[name=shipment_status]').val();
                    d.provider          = $('#tab-shipments select[name=provider]').val();
                    d.operator          = $('#tab-shipments select[name=operator]').val();
                    d.agency            = $('#tab-shipments select[name=agency]').val();
                    d.sender_agency     = $('#tab-shipments select[name=sender_agency]').val();
                    d.recipient_agency  = $('#tab-shipments select[name=recipient_agency]').val();
                    d.sender_country    = $('#tab-shipments select[name=sender_country]').val();
                    d.recipient_country = $('#tab-shipments select[name=recipient_country]').val();
                    d.delayed_reception = $('#tab-shipments select[name=delayed_reception]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableGrouped) },
                complete: function () { Datatables.complete(); }
            }
        });

        $('#tab-shipments .filter-datatable').on('change', function (e) {
            oTableGrouped.draw();
            e.preventDefault();
        });

        //enable option to search with enter press
        Datatables.searchOnEnter(oTable);


        @if(Setting::get('refunds_request_mode'))
        oTableRequests = $('#datatable-requested').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'created_at', name: 'created_at'},
                {data: 'customer_id', name: 'customer_id', orderable: false, searchable: false},
                {data: 'count_shipments', name: 'count_shipments', class: 'text-center', orderable: false, searchable: false},
                {data: 'total', name: 'total'},
                {data: 'requested_method', name: 'requested_method'},
                {data: 'payment_method', name: 'payment_method'},
                {data: 'payment_date', name: 'payment_date'},
                {data: 'status', name: 'status', class: 'text-center', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[5, "desc"]],
            ajax: {
                url: "{{ route('admin.refunds.requests.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.requested_method  = $('#tab-requested select[name=requested_method]').val();
                    d.payment_method    = $('#tab-requested select[name=payment_method]').val();
                    d.payment_date      = $('#tab-requested input[name=payment_date]').val();
                    d.customer          = $('#tab-requested select[name=customer]').val();
                    d.date_min          = $('#tab-requested input[name=date_min]').val();
                    d.date_max          = $('#tab-requested input[name=date_max]').val();
                    d.status            = $('#tab-requested select[name=status]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); }
            }
        });

        $('#tab-requested .filter-datatable').on('change', function (e) {
            oTableRequests.draw();
            e.preventDefault();
        });
        @endif
    });

    $(document).on('change', '[name="grouped"]', function(){

        var name = $(this).attr('name');
        var value = $(this).is(':checked');
        value = value == false ? 0 : 1;

        newUrl = Url.updateParameter(Url.current(), name, value)
        Url.change(newUrl);

        if($(this).is(':checked')) {
            $('[name="grouped"]').prop('checked', true);
            $('#tab-shipments-grouped').show()
            $('#tab-shipments').hide()
        } else {
            $('[name="grouped"]').prop('checked', false);
            $('#tab-shipments-grouped').hide()
            $('#tab-shipments').show()
        }
    })

    $("select[name=customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
    });


    $('.import-form').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var url = $form.attr('action')

        var $submitBtn = $form.find('button[type=submit]');
        $submitBtn.button('loading');

        var form = $(this)[0];
        var formData = new FormData(form);

        $('.import-form-area').hide();
        $('.import-results-area').html('<div class="text-center"><i class="fas fa-spin fa-circle-notch fs-30 m-b-10"></i><br/>A importar ficheiro. Esta operação poderá demorar algum tempo...</div>');

        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            contentType: false,
            processData: false,
            success: function(data){

                if(data.close) {
                    $('#modal-refund-import .modal-dialog').removeClass('modal-lg');
                    $('#modal-refund-import').modal('hide');
                    $('#modal-refund-import .import-form-area').show();
                    $('#modal-refund-import .import-results-area').html('').hide();
                    if(data.result) {
                        Growl.success(data.feedback)
                    } else {
                        Growl.error(data.feedback)
                    }
                } else {
                    $submitBtn.button('reset');
                    $('.modal [type="submit"]').html('Importar');
                    $('.import-form').attr('action', "{{ route('admin.refunds.customers.import.confirm') }}")

                    $('#modal-refund-import .modal-dialog').addClass('modal-lg');
                    $('#modal-refund-import .import-results-area').html(data.html).show()
                    $('#modal-refund-import .import-form-area').hide();
                    $('#modal-refund-import .select2').select2(Init.select2())
                }

            }
        }).fail(function () {
            Growl.error500();
            $('#modal-refund-import').modal('hide');
        }).always(function () {
            $submitBtn.button('reset');
        });
    });



</script>
@stop