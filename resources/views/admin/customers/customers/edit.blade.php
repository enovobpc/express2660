<?php
    $lastBalanceDate  = new \Carbon\Carbon($customer->balance_last_update);
    $balanceDiff      = \Carbon\Carbon::now()->diffInMinutes($lastBalanceDate);
    $balanceDiffHours = \Carbon\Carbon::now()->diffInHours($lastBalanceDate);
?>

@section('title')
    Clientes
@stop

@section('content-header')
    Clientes
    <small>
        @trans('Ficha de cliente')
    </small>
@stop

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.customers.index') }}">
            @trans('Clientes')
        </a>
    </li>
    <li class="active">
        @trans('Ficha de cliente')
    </li>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box no-border m-b-15">
            <div class="box-body p-5">
                <div class="row">
                    <div class="col-xs-12 col-md-5">
                        <div class="pull-left m-r-10">
                            @if($customer->filepath)
                                <img src="{{ asset($customer->getCroppa(200)) }}" onerror="this.src ='{{ img_broken(true) }}'" style="border:none" class="w-60px"/>
                            @else
                                <img src="{{ asset('assets/img/default/default.thumb.png') }}" style="border:none" class="w-60px"/>
                            @endif
                        </div>
                        <div class="pull-left w-85">
                            <h4 class="m-t-5 pull-left customer-name">
                                {{ $customer->name }}
                                @if(!$customer->active)
                                    <i class="fas fa-ban text-red" data-toggle="tooltip" title="Acesso bloqueado"></i>
                                @endif
                            </h4>
                            <div class="clearfix"></div>
                            <ul class="list-inline m-b-0">
                                <li><small>@trans('Código:')</small> <b>{{ $customer->code }}</b></li>
                                @if($customer->created_at)
                                <li><small>@trans('Registo:')</small> {{ @$customer->created_at->format('Y-m-d') }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-7">
                        <ul class="list-inline m-t-8 m-b-0 pull-right hidden-xs">
                            @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'billing,statistics'))
                            <li class="w-150px" style="position: relative">
                                <h4 class="m-t-0" data-popover-graph="#popover-services">
                                    <small>@trans('Serviços')</small><br/>
                                    <div class="progress xxs m-0">
                                        <div class="progress-bar bg-orange" role="progressbar" style="width:{{ $ranking['shipments']['pos_p'] }}%"></div>
                                    </div>
                                    <small><small>@trans('Posição') <b>{{ @$ranking['shipments']['pos_n'] }}</b> de {{ @$ranking['customers'] }}</small></small>
                                </h4>
                                <div class="popover-graph" id="popover-services">
                                    <div class="popover-title text-uppercase">@trans('Envios e Serviços') <small><i class="fas fa-times close"></i></small></div>
                                    <div class="p-15">
                                        <p>@trans('O cliente ocupa') {{ @$ranking['shipments']['pos_n'] }}ª @trans('posição em') {{ @$ranking['customers'] }} @trans('clientes'). {!! tip(__('Baseado no valor médio de envios/serviços, comparativamente com os restantes clientes.')) !!}</p>
                                        <h5 class="m-0 b-t-1 p-t-10">@trans('Histórico por mês')</h5>
                                        <canvas id="graphic-services" style='width: 500px; height: 250px'></canvas>
                                    </div>
                                </div>
                            </li>
                            <li class="w-150px" style="position: relative">
                                <h4 class="m-t-0" data-popover-graph="#popover-billing">
                                    <small>@trans('Faturação')</small><br/>
                                    <div class="progress xxs m-0">
                                        <div class="progress-bar bg-orange" role="progressbar" style="width:{{ $ranking['billing']['pos_p'] }}%"></div>
                                    </div>
                                    <small><small>@trans('Posição') <b>{{ $ranking['billing']['pos_n'] }}</b> @trans('de') {{ $ranking['customers'] }}</small></small>
                                </h4>
                                <div class="popover-graph" id="popover-billing">
                                    <div class="popover-title text-uppercase">Faturação <i class="fas fa-times close"></i></div>
                                    <div class="p-15">
                                        <p>
                                            @trans('O cliente ocupa') {{ @$ranking['billing']['pos_n'] }}ª @trans('posição em') {{ @$ranking['customers'] }} @trans('clientes.')' {!! tip(__('Baseado no valor médio de faturação, comparativamente com os restantes clientes.')) !!}
                                        </p>
                                        <h5 class="m-0 b-t-1 p-t-10">@trans('Histórico por mês')</h5>
                                        <canvas id="graphic-billing" style='width: 500px; height: 250px'></canvas>
                                    </div>
                                </div>
                            </li>
                            @endif
                            @if(hasModule('customers_balance') && hasPermission('customers_balance'))
                            <li class="w-105px">
                                <h4 class="m-0 pull-right" style="margin-top: -39px; position: absolute;">
                                    <small>@trans('Saldo Conta')</small><br/>
                                    <b class="balance-total">{{ money($customer->balance_total, Setting::get('app_currency')) }}</b>
                                </h4>
                            </li>
                            @endif
                            <li class="divider"></li>
                            @if(hasModule('account') && $customer->password)
                            <li>
                                <a href="{{ route('admin.customers.remote-login', $customer->id) }}"
                                   style="margin-top: -30px"
                                   class="btn btn-sm btn-warning"
                                   data-method="post"
                                   data-confirm-title="Iniciar Sessão Remota"
                                   data-confirm-class="btn-success"
                                   data-confirm-label="Iniciar Sessão"
                                   data-confirm="Pretende iniciar sessão como {{ $customer->display_name }}?"
                                   target="_blank">
                                    <i class="fas fa-user-circle"></i> @trans('Iniciar Sessão')
                                </a>
                            </li>
                            @endif
                            <li>
                                <div class="btn-group btn-group-sm" role="group" style="margin-top: -30px">

                                    @if($prevId = $customer->previousId())
                                        <a href="{{ route('admin.customers.edit', ['id' => $prevId, Request::getQueryString()]) }}" class="btn btn-default" data-toggle="tooltip" title="Anterior">
                                            <i class="fa fa-fw fa-angle-left"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-default" disabled>
                                            <i class="fa fa-fw fa-angle-left"></i>
                                        </button>
                                    @endif

                                        @if($nextId = $customer->nextId())
                                            <a href="{{ route('admin.customers.edit', [$nextId, Request::getQueryString()]) }}" class="btn btn-default" data-toggle="tooltip" title="Próximo">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-default" disabled>
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </button>
                                        @endif
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @if(!$customer->is_validated && $customer->is_active)
            <div class="alert alert-warning alert-dismissable hidden-print bigger-110">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong class="bigger-120">
                    <i class="fas fa-info-circle"></i>
                    @trans('Este cliente aguarda aprovação')
                </strong>

                <a href="{{ route('admin.customers.validate', ['customer' => $customer->id]) }}"
                   style="text-decoration: none; color: #333"
                   class="btn btn-xs btn-default"
                   data-toggle="modal"
                   data-target="#modal-remote-lg">
                   @trans('Aprovar ou Rejeitar')</a>

            </div>
        @endif
        <?php $warningLimit = null;?>
        @if(@$customer->unpaid_invoices_credit > 0.00 && @$customer->balance_total_unpaid > @$customer->unpaid_invoices_credit)
        <div class="alert alert-danger alert-dismissable hidden-print bigger-110">
            <h4 class="m-0">
                <i class="fas fa-exclamation-triangle"></i> <b>@trans('Envios Bloqueados devido a faturação em atraso.')</b><br/>
                <small style="color:white">@trans('Ultrapassou o limite de crédito de') {{ money(@$customer->unpaid_invoices_credit, Setting::get('app_currency')) }}. </small>
            </h4>
        </div>
        <div class="spacer-15"></div>
        @elseif(@$oldestUnpaidInvoice->due_date)
            @if(!empty($customer->unpaid_invoices_limit) && @$oldestUnpaidInvoice->days_late > $customer->unpaid_invoices_limit && @$oldestUnpaidInvoice->due_date->lte(Date::today()))
                <?php $warningLimit = $customer->unpaid_invoices_limit;?>
            @elseif((empty($customer->unpaid_invoices_limit) && @$oldestUnpaidInvoice->due_date->lte(Date::today()) && !empty(Setting::get('customers_unpaid_invoices_limit'))) && @$oldestUnpaidInvoice->days_late > Setting::get('customers_unpaid_invoices_limit'))
                <?php $warningLimit = Setting::get('customers_unpaid_invoices_limit');?>
            @endif

            @if($warningLimit)
            <div class="alert alert-danger alert-dismissable hidden-print bigger-110">
                    <h4 class="m-0">
                        <i class="fas fa-exclamation-triangle"></i> <b>@trans('Envios Bloqueados devido a faturação em atraso.')</b><br/>
                        <small  style="color:white">@trans('Possui faturas por liquidar há mais de') {{ $warningLimit }} @trans('dias.')</small>
                    </h4>
                </div>
                <div class="spacer-15"></div>
            @endif
        @endif
    </div>
</div>

<div class="row row-5">
    <div class="col-md-3 col-lg-2">
        <div class="box box-solid box-sidebar">
            <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                    <li class="active">
                        <a href="#tab-info" data-toggle="tab"><i class="fas fa-fw fa-id-card"></i> @trans('Dados Gerais')</a>
                    </li>
                    <li class="{{ $customer->exists ? '' : 'disabled' }}">
                        <a href="#tab-recipients" data-toggle="{{ $customer->exists ? 'tab' : '' }}"><i class="fas fa-fw fa-user-friends"></i>
                            @trans('Moradas Frequentes')
                            @if(@$duplicateRecipients->count())
                                <i class="fa fa-exclamation-triangle pull-right text-red m-t-3"></i>
                            @endif
                        </a>
                    </li>

                    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables,prices_tables_view'))
                        <li class="{{ $customer->exists ? '' : 'disabled' }}">
                            <a href="#tab-prices" data-toggle="{{ $customer->exists ? 'tab' : '' }}"><i class="fas fa-fw fa-euro-sign"></i>
                                {{--Tabela de Preços--}}
                                @trans('Preços e Taxas')

                                @if(!hasModule('prices_tables'))
                                    <i class="fa fa-lock pull-right text-muted m-t-3"></i>
                                @elseif(!$customer->has_prices)
                                    <i class="fa fa-exclamation-triangle pull-right text-red m-t-3"></i>
                                @endif
                            </a>
                        </li>
                    @endif

                    
                    @if(hasPermission('customers_balance'))
                    <li class="tab-balance {{ $customer->exists ? '' : 'disabled' }}">
                        <a href="#tab-balance" data-toggle="{{ $customer->exists ? 'tab' : '' }}">
                            <i class="fas fa-fw fa-file-invoice"></i> @trans('Conta Corrente')
                            @if(!hasModule('customers_balance'))
                                <i class="fa fa-lock pull-right text-muted m-t-3"></i>
                            @endif
                        </a>
                    </li>
                    @endif

                    @if(hasModule('prospection'))
                        <li class="{{ $customer->exists ? '' : 'disabled' }}">
                            <a href="#tab-tipology" data-toggle="{{ $customer->exists ? 'tab' : '' }}">
                                <i class="fas fa-fw fa-suitcase"></i> @trans('Tipologia Negócio')
                            </a>
                        </li>
                        @if(Auth::user()->perm('meetings'))
                        <li class="{{ $customer->exists ? '' : 'disabled' }}">
                            <a href="#tab-meetings" data-toggle="{{ $customer->exists ? 'tab' : '' }}">
                                <i class="fas fa-fw fa-calendar-alt"></i> @trans('Reuniões')
                            </a>
                        </li>
                        @endif
                    @endif

                    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'customer_covenants'))
                    <li class="{{ $customer->exists ? '' : 'disabled' }}">
                        <a href="#tab-covenants" data-toggle="{{ $customer->exists ? 'tab' : '' }}">
                            <i class="fas fa-fw fa-handshake"></i> @trans('Avenças Mensais')
                            @if(!hasModule('covenants'))
                                <i class="fa fa-lock pull-right text-muted m-t-3"></i>
                            @endif
                        </a>
                    </li>
                    @endif

                    @if(!$customer->final_consumer)
                        <li class="{{ $customer->exists ? '' : 'disabled' }}">
                            <a href="#tab-contacts" data-toggle="{{ $customer->exists ? 'tab' : '' }}">
                                <i class="fas fa-fw fa-phone"></i> @trans('Contactos')
                            </a>
                        </li>
                    @endif

                    <li class="{{ $customer->exists ? '' : 'disabled' }}">
                        <a href="#tab-attachments" data-toggle="{{ $customer->exists ? 'tab' : '' }}">
                            <i class="fas fa-fw fa-file"></i> @trans('Documentação')
                            @if(!hasModule('customers_attachments'))
                                <i class="fa fa-lock pull-right text-muted m-t-3"></i>
                            @endif
                        </a>
                    </li>

                    @if(!$customer->final_consumer)
                        <li class="{{ $customer->exists ? '' : 'disabled' }}">
                            <a href="#tab-departments" data-toggle="{{ $customer->exists ? 'tab' : '' }}" style="padding-right: 0">
                                <i class="fas fa-fw fa-users"></i> @trans('Departamentos/Subcontas')
                            </a>
                        </li>
                    @endif

                    <li class="{{ $customer->exists ? '' : 'disabled' }}">
                        <a href="#tab-login" data-toggle="{{ $customer->exists ? 'tab' : '' }}">
                            <i class="fas fa-fw fa-user-circle"></i> @trans('Acesso Área Cliente')
                            @if(!hasModule('account'))
                                <i class="fa fa-lock pull-right text-muted m-t-3"></i>
                            @else
                                @if(empty($customer->password))
                                    <i class="fa fa-exclamation-triangle pull-right text-yellow m-t-3"></i>
                                @elseif(!$customer->active)
                                    <i class="fa fa-ban pull-right text-red m-t-3"></i>
                                @endif
                            @endif
                        </a>
                    </li>

                    <li class="{{ $customer->exists ? '' : 'disabled' }}">
                        <a href="#tab-settings" data-toggle="{{ $customer->exists ? 'tab' : '' }}">
                            <i class="fas fa-fw fa-cog"></i> @trans('Definições')
                        </a>
                    </li>

                    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'webservices'))
                    <li class="{{ $customer->exists ? '' : 'disabled' }}">
                        <a href="#tab-webservices" data-toggle="{{ $customer->exists ? 'tab' : '' }}">
                            <i class="fas fa-fw fa-plug"></i> @trans('Webservices')
                            @if(!hasModule('webservices'))
                                <i class="fa fa-lock pull-right text-muted m-t-3"></i>
                            @endif
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->isAdmin())
                        <li class="tab-ballance-old {{ $customer->exists ? '' : 'disabled' }}">
                            <a href="#tab-balance-old" data-toggle="{{ $customer->exists ? 'tab' : '' }}">
                                <i class="fas fa-fw fa-file-invoice"></i> @trans('Conta Corrente (admins)')
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9 col-lg-10">
        <div class="tab-content">
            <div class="active tab-pane" id="tab-info">
                @include('admin.customers.customers.partials.info')
            </div>
            <div class="tab-pane" id="tab-recipients" data-empty="1">
                @include('admin.customers.customers.partials.recipients')
            </div>
            
            @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables,prices_tables_view'))
            <div class="tab-pane" id="tab-prices">
                @include('admin.customers.customers.partials.prices')
            </div>
            @endif


            @if(Auth::user()->isAdmin())
            <div class="tab-pane" id="tab-balance-old" data-empty="1">
                @include('admin.customers.customers.partials.balance_old')
            </div>
            @endif
            
            @if(hasModule('customers_balance') && hasPermission('invoices'))
            <div class="tab-pane" id="tab-balance" data-empty="1">
                @include('admin.customers.customers.partials.balance')
            </div>
            @endif

            @if(hasModule('prospection'))
                <div class="tab-pane" id="tab-tipology" data-empty="1">
                    <?php $prospect = $customer; ?>
                    @include('admin.customers.customers.partials.tipology')
                </div>

                @if(Auth::user()->perm('meetings'))
                <div class="tab-pane" id="tab-meetings" data-empty="1">
                    @include('admin.customers.customers.partials.meetings')
                </div>
                @endif
            @endif

            @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'webservices'))
                <div class="tab-pane" id="tab-webservices" data-empty="1">
                    @include('admin.customers.customers.partials.webservices')
                </div>
            @endif


            {{--<div class="tab-pane" id="tab-products">
                @include('admin.customers.customers.partials.products')
            </div>--}}

            <div class="tab-pane" id="tab-login">
                @include('admin.customers.customers.partials.login')
            </div>

            @if(!$customer->final_consumer)
                <div class="tab-pane" id="tab-departments" data-empty="1">
                    @include('admin.customers.customers.partials.departments')
                </div>

                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'customer_covenants'))
                <div class="tab-pane" id="tab-covenants" data-empty="1">
                    @include('admin.customers.customers.partials.covenants')
                </div>
                @endif
            
                <div class="tab-pane" id="tab-contacts" data-empty="1">
                    @include('admin.customers.customers.partials.contacts')
                </div>
                <div class="tab-pane" id="tab-attachments" data-empty="1">
                    @include('admin.customers.customers.partials.attachments')
                </div>
                <div class="tab-pane" id="tab-settings">
                    @include('admin.customers.customers.partials.settings')
                </div>
            @endif
        </div>
    </div>
</div>
@include('admin.customers.customers.modals.print_prices_table')
@include('admin.customers.customers.modals.import_services_table')
@include('admin.customers.customers.modals.import_global_services_table')
@include('admin.partials.modals.vat_validation')
@include('admin.partials.modals.map_preview')
@stop

@section('styles')
    {{ HTML::style('vendor/ios-checkbox/dist/css/iosCheckbox.min.css')}}
@stop

@section('scripts')
{{ Html::script('vendor/chart.js/dist/Chart.min.js') }}
{{ HTML::script('vendor/ios-checkbox/dist/js/iosCheckbox.min.js')}}
<script type="text/javascript">

    var EXISTING_VATS = {!! json_encode($existingVats) !!}
    $('[name="vat"]').on('change', function(){
        var value = $(this).val();
        if($.inArray(value, EXISTING_VATS) > 0) {
            $('.vat-alert').show();
        } else {
            $('.vat-alert').hide();
        }
    })

    $('[name="billing_country"]').on('change', function(){
        $('[name="vat"]').trigger('change');
    })

    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'billing,statistics'))
    var chartServices = {
        type: 'line',
        data: {
            labels: [{!! @$graphData['labels'] !!}],
            datasets: [{
                label: 'Envios e Serviços',
                data: [{{ @$graphData['shipments'] }}],
                backgroundColor: "#F57C00",
                borderWidth: 1
            },
            {
                label: 'Volumes',
                data: [{{ @$graphData['volumes'] }}],
                backgroundColor: "#FF9800",
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: { beginAtZero:true}
                }]
            },
            legend: { display: true }
        }
    };

    var chartBilling = {
        type: 'line',
        data: {
            labels: [{!! @$graphData['labels'] !!}],
            datasets: [{
                label: 'Total de Faturação',
                data: [{{ @$graphData['billing'] }}],
                backgroundColor: "#F57C00",
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: { beginAtZero:true}
                }]
            },
            legend: { display: true }
        }
    };

    var gServ = document.getElementById('graphic-services').getContext('2d');
    var gBill = document.getElementById('graphic-billing').getContext('2d');
    new Chart(gServ, chartServices);
    new Chart(gBill, chartBilling);
    @endif

    $(document).ready(function(){
        $('[data-toggle="popover"]').popover();
    });

    var oTableBalance;
    var oTableBalanceOld;
    var oTableMeetings;

    $(".ios").iosCheckbox();

    $('#tab-info [name="zip_code"], #tab-info [name="country"]').on('change', function() {
        var $form = $(this).closest('form');
        var zipCode = $form.find('[name="zip_code"]').val();
        var country = $form.find('[name="country"]').val();

        if(zipCode != '') {
            ZipCode.validateInput(country, zipCode);
        } else {
            ZipCode.validateInput('pt', '1000'); //força a manter codigo correto
        }
    })

    @if($balanceDiff >= 10) //diff 10 minutes
    $(document).ready(function(){
        $(document).find('.btn-sync-ballance-all').trigger('click');
    })
    @endif

    $('.select-all-services').on('click', function(e){
        e.preventDefault();

        if($('.row-service:checked').length) {
            $('.row-service').prop('checked', false)
        } else {
            $('.row-service').prop('checked', true)
        }
    })

    $(document).on('click', '[data-target="#modal-map-preview"]', function(){
        var url = $(this).data('map-url');
        $('#modal-map-preview iframe').attr('src', url)
    })

    $(document).on('click', '[data-toggle="marker-position"]', function(){
        $('#map').show();
        $('.customer-map-static').hide();
    })

    /**
     * Tab recipients
     * @returns {undefined}
     */
    $(document).on('click', 'a[href="#tab-recipients"]', function(){
        $tab = $('#tab-recipients');
        
        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);
            
            var oTable = $('#datatable-addresses').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'code', name: 'code'},
                    {data: 'name', name: 'name'},
                    {data: 'address', name: 'address'},
                    @if(!empty($departments))
                    {data: 'department', name: 'department', orderable: false, searchable: false},
                    @endif
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    {data: 'zip_code', name: 'zip_code', visible: false},
                    {data: 'email', name: 'email', visible: false},
                    {data: 'phone', name: 'phone', visible: false},
                ],
                ajax: {
                    type: "POST",
                    url: "{{ route('admin.customers.recipients.datatable', $customer->id) }}",
                    data: function (d) {
                        d.type_id = $('select[name=type]').val();
                        d.active  = $('select[name=active]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('.filter-datatable').on('change', function (e) {
                oTable.draw();
                e.preventDefault();

                var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('[data-toggle="export-url"]').attr('href', exportUrl);
            });
        }
    })
    
    /**
     * Tab departments
     * @returns {undefined}
     */
    $(document).on('click', 'a[href="#tab-departments"]', function(){
        $tab = $('#tab-departments');
        
        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);
            
            var oTable = $('#datatable-departments').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'photo', name: 'photo', orderable: false, searchable: false},
                    {data: 'code', name: 'code'},
                    {data: 'name', name: 'name'},
                    {data: 'contacts', name: 'contacts', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    {data: 'zip_code', name: 'zip_code', visible: false},
                    {data: 'email', name: 'email', visible: false},
                    {data: 'phone', name: 'phone', visible: false},
                ],
                ajax: {
                    type: "POST",
                    url: "{{ route('admin.customers.departments.datatable', $customer->id) }}",
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('.filter-datatable').on('change', function (e) {
                oTable.draw();
                e.preventDefault();
            });
        }
    })
    
    
    /**
     * Tab contacts
     * @returns {undefined}
     */
    $(document).on('click', 'a[href="#tab-contacts"]', function(){
        $tab = $('#tab-contacts');
        
        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);
            
            var oTable = $('#datatable-contacts').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'department', name: 'department'},
                    {data: 'name', name: 'name'},
                    {data: 'phone', name: 'phone'},
                    {data: 'mobile', name: 'mobile'},
                    {data: 'email', name: 'email'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    type: "POST",
                    url: "{{ route('admin.customers.contacts.datatable', $customer->id) }}",
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('.filter-datatable').on('change', function (e) {
                oTable.draw();
                e.preventDefault();
            });
        }
    })

    /**
     * Tab meetings
     * @returns {undefined}
     */
    $(document).on('click', 'a[href="#tab-meetings"]', function(){
        $tab = $('#tab-meetings');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableMeetings = $('#datatable-meetings').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'date', name: 'date'},
                    {data: 'seller_id', name: 'seller_id'},
                    {data: 'objectives', name: 'objectives', orderable: false, searchable: false},
                    {data: 'occurrences', name: 'occurrences', orderable: false, searchable: false},
                    {data: 'charges', name: 'charges', orderable: false, searchable: false},
                    {data: 'status', name: 'status'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    type: "POST",
                    url: "{{ route('admin.customers.meetings.datatable', $customer->id) }}",
                    data: function (d) {
                        d.status = $('select[name=status]').val();
                        d.date_min = $('input[name=date_min]').val();
                        d.date_max = $('input[name=date_max]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableMeetings) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('.filter-datatable').on('change', function (e) {
                oTableMeetings.draw();
                e.preventDefault();
            });
        }
    })
    
    /**
     * Tab covenants
     * @returns {undefined}
     */
    $(document).on('click', 'a[href="#tab-covenants"]', function(){
        $tab = $('#tab-covenants');
        
        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);
            
            var oTable = $('#datatable-covenants').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'type', name: 'type'},
                    {data: 'description', name: 'description'},
                    {data: 'max_shipments', name: 'max_shipments'},
                    {data: 'service', name: 'service', orderable: false, searchable: false},
                    {data: 'amount', name: 'amount'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'end_date', name: 'end_date'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    type: "POST",
                    url: "{{ route('admin.customers.covenants.datatable', $customer->id) }}",
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('.filter-datatable').on('change', function (e) {
                oTable.draw();
                e.preventDefault();
            });
        }
    })


    /**
     * Tab webservices
     * @returns {undefined}
     */
    $(document).on('click', 'a[href="#tab-webservices"]', function(){
        $tab = $('#tab-webservices');
        
        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);
            
            var oTable = $('#datatable-webservices').DataTable({
                columns: [
                    {data: 'id', name: 'id', visible: false},
                    {data: 'method', name: 'method'},
                    {data: 'provider_id', name: 'provider_id'},
                    {data: 'agency', name: 'agency'},
                    {data: 'user', name: 'user'},
                    {data: 'password', name: 'password'},
                    {data: 'session_id', name: 'session_id'},
                    {data: 'active', name: 'active'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    type: "POST",
                    url: "{{ route('admin.customers.webservices.datatable', $customer->id) }}",
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('.filter-datatable').on('change', function (e) {
                oTable.draw();
                e.preventDefault();
            });
        }
    })

    /**
     * Tab attachments
     */
    var oTableAttachments;
    $(document).on('click', 'a[href="#tab-attachments"]', function(){
        $tab = $('#tab-attachments');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableAttachments = $('#datatable-attachments').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'name', name: 'name'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'sort', name: 'sort'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[4, "desc"]],
                ajax: {
                    type: "POST",
                    url: "{{ route('admin.customers.attachments.datatable', $customer->id) }}",
                    data: function (d) {
                        d.type_id = $('select[name=type]').val();
                        d.active = $('select[name=active]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableAttachments) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('.filter-datatable').on('change', function (e) {
                oTableAttachments.draw();
                e.preventDefault();
            });
        }
    })

    $(document).on('hidden.bs.modal','#modal-remote', function(event, data){
        oTableAttachments.draw();
    })

    /**
     * Tab business history
     */
    @if(hasModule('prospection'))
    var oTableBusinessHistory;
    $(document).on('click', 'a[href="#tab-business-history"]', function() {
        $tab = $('#tab-business-history');

        if ($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableBusinessHistory = $('#datatable-business-history').DataTable({
                columns: [
                    {data:'created_at', name:'created_at'},
                    {data:'status', name:'status'},
                    {data:'message', name:'message'},
                    {data:'operator_id' ,name:'operator_id'},
                ],
                order: [ [0, "desc"]],
                ajax: {
                    url: "{{ route('admin.prospects.business.history.datatable', $customer->id) }}",
                    type: "POST",
                    data: function(d) {},
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableBusinessHistory) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('#tab-business-history .filter-datatable').on('change', function(e) {
                oTableBusinessHistory.draw();
                e.preventDefault();
            });
        }
    })
    @endif
            
    @if(hasModule('customers_balance') && hasPermission('customers_balance'))
    /**
     * Tab balance
     * @returns {undefined}
     */
    $(document).on('click', 'a[href="#tab-balance"]', function(){
        $tab = $('#tab-balance');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableBalance = $('#datatable-balance').DataTable({
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
                    type: "POST",
                    url: "{{ route('admin.invoices.datatable', ['customer' => $customer->id]) }}",
                    data: function (d) {
                        d.draft            = 0;
                        d.year             = $('#tab-balance select[name=year]').val();
                        d.month            = $('#tab-balance select[name=month]').val();
                        d.serie            = $('#tab-balance select[name=serie]').val();
                        d.doc_type         = $('#tab-balance select[name=doc_type]').val();
                        d.doc_id           = $('#tab-balance input[name=doc_id]').val();
                        d.date_min         = $('#tab-balance input[name=date_min]').val();
                        d.date_max         = $('#tab-balance input[name=date_max]').val();
                        d.settle           = $('#tab-balance select[name=settle]').val();
                        d.expired          = $('#tab-balance input[name=expired]').val();
                        d.payment_method   = $('#tab-balance select[name=payment_method]').val();
                        d.payment_condtion = $('#tab-balance select[name=payment_condition]').val();
                        d.deleted          = $('#tab-balance input[name=invoice_deleted]:checked').length;
                        d.hide_receipts    = $('#tab-balance input[name=hide_receipts]:checked').length;
                        
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableBalance) },
                    complete: function () { Datatables.complete(); }
                }
            });

            $('#tab-balance .filter-datatable').on('change', function (e) {
                oTableBalance.draw();
                e.preventDefault();
            });
        }
    })

    //show deleted invoices
    $(document).on('change', '[name="invoice_deleted"], [name="hide_receipts"]', function (e) {
        oTableBalance.draw();
        e.preventDefault();
    });
    @endif


    @if(Auth::user()->isAdmin())
    /**
     * Tab balance old
     * @returns {undefined}
     */
    $(document).on('click', 'a[href="#tab-balance-old"]', function(){

        @if($balanceDiff >= 10)
        if($('.balance-total-unpaid .fa-spin').length == 0) {
            $('.btn-sync-ballance-all').trigger('click');
        }
        @endif

        $tab = $('#tab-balance-old');

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);

            oTableBalanceOld = $('#datatable-balance-old').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'date', name: 'date'},

                    {data: 'doc_type', name: 'doc_type'},
                    {data: 'doc_serie', name: 'doc_serie'},
                    {data: 'reference', name: 'reference'},

                    @if(Setting::get('billing_show_cred_deb_column'))
                    {data: 'debit', name: 'total', 'class': 'text-right'},
                    {data: 'credit', name: 'credit', 'class': 'text-right', orderable: false, searchable: false},
                    @else
                    {data: 'total', name: 'total', 'class': 'text-right'},
                    @endif
                    {data: 'pending', name: 'pending', 'class': 'text-right', orderable: false, searchable: false},
                    {data: 'due_date', name: 'due_date'},
                    {data: 'is_paid', name: 'is_paid'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[2, "desc"]],
                ajax: {
                    type: "POST",
                    url: "{{ route('admin.customers.balance.datatable', $customer->id) }}",
                    data: function (d) {
                        d.sense     = $('#tab-balance-old select[name="sense"]').val(),
                        d.paid      = $('#tab-balance-old select[name="paid"]').val(),
                        d.hide_payments = $('#tab-balance-old input[name=hide_payments]:checked').length
                        d.serie     = $('#tab-balance-old select[name=serie]').val();
                        d.doc_type  = $('#tab-balance-old select[name=doc_type]').val();
                        d.doc_id    = $('#tab-balance-old input[name=doc_id]').val();
                        d.date_min  = $('#tab-balance-old input[name=date_min]').val();
                        d.date_max  = $('#tab-balance-old input[name=date_max]').val();
                        d.settle    = $('#tab-balance-old select[name=settle]').val();
                        d.expired   = $('#tab-balance-old input[name=expired]').val();
                        d.operator  = $('#tab-balance-old select[name=operator]').val();
                        d.deleted   = $('#tab-balance-old input[name=invoice_deleted]:checked').length;
                        d.payment_method = $('#tab-balance select[name=payment_method]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableBalanceOld) },
                    complete: function () { Datatables.complete(); }
                }
            });

            $('#tab-balance-old .filter-datatable').on('change', function (e) {
                oTableBalanceOld.draw();
                e.preventDefault();
            });
        }
    })

    //show deleted invoices
    $(document).on('change', '[name="invoice_deleted"]', function (e) {
        oTableBalanceOld.draw();
        e.preventDefault();
    });
    @endif

    $(document).on('click', 'a[href="#tab-login"]', function() {

        $('.ip-localtion-popup').on('show.bs.popover', function() {
            var ip    = $(this).data('ip')
            var url   = "{{ config('app.core') . '/helper/ip/location' }}" + "?ip=" + ip;
            var $this = $(this);
            if($this.data('loaded') == '0') {
                $.ajax({
                    url: url,
                    type: 'GET',
                    crossDomain: true,
                    success: function(data){
                        if(data.status) {
                            $this.data('loaded', '1');

                            html = "<div class='text-center'>" + $this.data('time') + "</div>";
                            html+= "<table class='table table-condensed m-0'>";
                            html+= "<tr>";
                            html+= "<td>País</td>";
                            html+= "<td>" + data.country_name + "</td>";
                            html+= "</tr><tr>";
                            html+= "<td>Localidade</td>";
                            html+= "<td>" + data.city + "</td>";
                            html+= "</tr><tr>";
                            html+= "<td style='width: 80px'>Cód. Postal</td>";
                            html+= "<td>" + data.postal_code + "</td>";
                            html+= "</tr><tr>";
                            html+= "<td>ISP</td>";
                            html+= "<td class='ip-isp'>" + data.isp + "</td>";
                            html+= "</tr>";
                            html+= "</table>";
                        }

                        $this.attr('data-content', html).data('bs.popover').setContent();
                        var popoverId = $this.attr('aria-describedby');
                        $('#' + popoverId).addClass($this.data('placement'));
                    },
                    fail: function (data) {
                        $.bootstrapGrowl("<i class='fas fa-exclamation-circle'></i> Erro ao carregar informação do IP.", {
                            type: 'error',
                            align: 'center',
                            width: 'auto',
                            delay: 8000
                        });
                    }
                })
            }
        })
    });

    $('[data-dismiss="fileinput"]').on('click', function () {
        $('[name=delete_photo]').val(1);
    })
    
    $(document).ready(function(){
        $('a[href="#tab-{{ Request::get("tab") }}"]').trigger('click');
    })
    
    var parentTab = $('a[href="#tab-{{ Request::get("tab") }}"]').data('parent-tab');
    $('a[href="' + parentTab + '"]').trigger('click');
    

    $("select[name=import_customer_id]").select2({
        ajax: {
            url: "{{ route('admin.customers.search') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    /**
     * Form login
     */
    $('.form-account-login').on('submit', function(e) {
         if(!$('input[name="enabled_services[]"]:checked').length) {
             e.preventDefault()  
              Growl.error('O cliente tem de ter pelo menos um serviço contratado.');
              return false;
        }
    })

    /**
     * Update prices
     */
    $('.form-update-prices').on('submit', function(e){
        e.preventDefault()
        
        var newVal, val, percent;
        var $form = $(this);
        var percent = $form.find('[name="update_percent"]').val() / 100
        var $target = $('#' + $form.find('[name="update_target"]').val() + '-services');

        if(percent != 0 && percent != "") {
        
            $target.find('input[name*="price"]').each(function(){
                val = $(this).val();

                if(val != "") {
                    if($form.find('[name="update_signal"]').val() == 'sub') {
                        newVal = parseFloat(val) - (parseFloat(val) * percent);
                    } else {
                        newVal = parseFloat(val) + (parseFloat(val) * percent);
                    }

                    $(this).val(newVal.toFixed(2));
                }
            })

            Growl.success('Atualizado com sucesso. Grave a tabela para gravar as alterações.');
        }
    })


/*     $('[name="bank_code"]').on('change', function(){
        var $this = $(this).find('option:selected');
        $('[name="bank_swift"]').val($this.data('swift'));
        $('[name="bank_name"]').val($this.data('name'));
    });
 */
    $("select[name=bank_code]").select2({
        minimumInputLength: 1,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.customers.search.banks') }}")
    });
    
    $('select[name=bank_code]').on('select2:select', function (e) {
        var data = e.params.data;
        $('[name="bank_swift"]').val(data.swift);
        $('[name="bank_name"]').val(data.name);
    });

    $(document).on('click', '.btn-toggle-mandate-date', function(){
        $('[name="bank_mandate_date"]').toggle()
    });

    $(document).on('click', '.btn-create-mandate', function(){
        var $this = $(this);

        $this.find('i').addClass('fa-spin');
        $.post("{{ route('admin.customers.get.mandate') }}", function(data){
            if(data.result) {
                $('[name=bank_mandate]').val(data.mandate_code)
                $('[name=bank_mandate_date]').val(data.mandate_date)
            } else {
                Growl.error(data.feedback)
            }

        }).fail(function() {
            Growl.error500()
        }).always(function() {
            $this.find('i').removeClass('fa-spin');
        })
    });

    @if(hasModule('customers_balance') && !in_array(Setting::get('invoice_software'), ['SageX3', 'EnovoTms']))
    /**
     * Update billing payment status
     */
    $('#modal-update-balance-status form').on('submit', function(e){
        e.preventDefault()

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        $submitBtn.button('loading');

        $('#modal-update-balance-status .loading-status').show();
        $('#modal-update-balance-status .loading-status').prev().hide();
        $('.balance-update-time').html('<i class="fas fa-spin fa-circle-notch"></i> A atualizar...')
        $.post($form.attr('action'), function(data){
            console.log(data);
            if(data.result) {
                Growl.success(data.feedback);
                oTableBalance.draw();
                $('.balance-total-expired').html(data.totalExpired);
                $('.balance-total-unpaid').html(data.totalUnpaid);
                $('.balance-update-time').html('<i class="far fa-clock"></i> Atualizado agora mesmo')
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $('#modal-update-balance-status .loading-status').hide();
            $('#modal-update-balance-status .loading-status').prev().show();
            $('#modal-update-balance-status').modal('hide');
            $submitBtn.button('reset');
        });
    });


    /**
     * Sync balance
     */
    $('.btn-sync-ballance-all, .btn-sync-balance-documents').on('click', function(e){
        e.preventDefault()

        var $form = $(this).closest('form');
        var $submitBtn = $(this);
        $submitBtn.button('loading')

        $('.balance-total-unpaid').prepend('<i class="fas fa-spin fa-circle-notch"></i> ');
        $('#modal-sync-balance-all .loading-status, #modal-sync-balance .loading-status').show();
        $('#modal-sync-balance-all .loading-status, #modal-sync-balance .loading-status').prev().hide();
        $('.balance-update-time').html('<i class="fas fa-spin fa-circle-notch"></i> A atualizar...')

        $.post($form.attr('action'), function(data){
            console.log(data);
            if(data.result) {
                $('.balance-total-expired').html(data.totalExpired);
                $('.balance-total-unpaid').html(data.totalUnpaid);
                $('.balance-update-time').html('<i class="far fa-clock"></i> Atualizado agora mesmo')

                if($('.tab-ballance').hasClass('active')) {
                    Growl.success(data.feedback);
                }

                if(typeof oTableBalance !== "undefined") {
                    oTableBalance.draw();
                }

            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error500();}).always(function () {

            $('#modal-sync-balance .loading-status, #modal-sync-balance-all .loading-status').hide();
            $('#modal-sync-balance .loading-status, #modal-sync-balance-all .loading-status').prev().show();
            $('#modal-sync-balance, #modal-sync-balance-all').modal('hide');
            $('.balance-total-unpaid').find('i').remove();
            $submitBtn.button('reset');
        }).always(function(){
            $submitBtn.button('reset')
        });
    })

    $(document).on('change', '[name="hide_payments"]', function (e) {
        oTableBalanceOld.draw();
    });
    @endif


    $(document).on('change', '[name="origin_zone"]', function(){
        var zone = $(this).val();
        var unity = $(this).data('unity');
        var url = Url.current();
        url = Url.updateParameter(url, 'origin_zone', zone);
        url = Url.updateParameter(url, 'unity', unity);
        window.location = url;
    })

    $(document).on('click', '.btn-pricetb-adv-opts', function(e){
        e.preventDefault();
        $target = $(this).closest('.panel-group')


        if(!$target.find('.panel-collapse').hasClass('in')) {
            $target.find('.pricetb-adv-opts').show();
            $target.find('.panel-heading').trigger('click');
        } else {
            $target.find('.pricetb-adv-opts').slideToggle();
        }
    })

    $(document).on('click', '.prices-tables-toggle', function(e){
        e.preventDefault();
        if($('.panel-collapse.in').length) {
            $('.panel-collapse').removeClass('in');
        } else {
            $('.panel-collapse').addClass('in');
        }
    })

    $(document).on('click', '.update-table-prices', function(e) {
        e.preventDefault();

        var $form = $(this).closest('form');

        bootbox.confirm({
            animate: false,
            title: 'Gravar tabela de preços',
            message: "<h4><b>Confirma a alteração da tabela de preços deste cliente?</b><br/>Ao confirmar vai perder todos os preços da tabela atual e serão substituidos pelos preços da nova tabela.</h4>",
            buttons: {
                confirm: {
                    label: 'Sim, Gravar',
                    className: 'btn-success'
                },
                cancel: {
                    label: "Cancelar",
                    className: "btn-default"
                }
            },
            callback: function(result) {
                if(result) {
                    $form.submit();
                }
            }
        });
    })

    $(document).on('click', '.increment-prices', function(e) {
        e.preventDefault();

        var $form = $(this).closest('form');

        bootbox.confirm({
            animate: false,
            title: 'Atualização de preços',
            message: "<h4><b>Confirma a atualização dos preços?</b></h4>" +
                "<p>Esta alteração apenas vai atualizar os preços da tabela." +
                "<br/>A alteração não vai gravar a tabela de forma definitiva, será preciso a sua confirmação antes de efetivar o processo.</p>",
            buttons: {
                confirm: {
                    label: 'Sim, Atualizar',
                    className: 'btn-success'
                },
                cancel: {
                    label: "Cancelar",
                    className: "btn-default"
                }
            },
            callback: function(result) {
                if(result) {
                    $form.submit();
                }
            }
        });
    })

    $('[name="password"], [name="password_confirmation"]').on('click', function(){
        $('.checkbox-send-password').show();
    })

    $('.business-status button').on('click', function() {
        var id = $(this).data('id');
        var color = $(this).data('color');

        $('[name=business_status]').val(id);
        $('.business-status button').css('background', '#f4f4f4').css('border-color', '#ddd').css('color',
            '#333')
        $(this).css('background', color).css('border-color', color).css('color', 'white')
    })
</script>
<link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.0/mapsjs-ui.css?dp-version=1549984893" />
<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-core.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-service.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-ui.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-mapevents.js"></script>
<script src="{{ asset('vendor/fuzzyset/fuzzyset.js') }}"></script>
<script>
    $(document).on('click', '[data-toggle="marker-position"], .mark-on-map',function(){
        locateAddress(true);
    })

    $('[name="address"], [name="zip_code"], [name="city"]').on('change', function(){
        locateAddress(false);
    });

    function locateAddress(ignoreSimilarity) {
        var address  = $('[name="address"]').val();
        var zip_code = $('[name="zip_code"]').val();
        var city     = $('[name="city"]').val();

        if(address != '' && zip_code != '' && city != '') {
            var address = address + ' ' + zip_code + ' ' + city;

            if (ignoreSimilarity) {
                findAddressLocation(address)
            } else {
                //check similiarity
                //only update marker location if similarity inferior to 90%
                similarity = FuzzySet(["{{ $customer->address . ' ' . $customer->zip_code . ' ' . $customer->city }}"]);
                similarity = similarity.get(address);

                if (similarity != null) {
                    similarity = similarity[0][0];
                }

                if (similarity < 0.90) { //similarity inferior to 88%
                    findAddressLocation(address)
                }
            }
        }
    }


    var MAP_LAT = '{{ $customer->map_lat }}';
    var MAP_LNG = '{{ $customer->map_lng }}';
    var platform;

    @if(env('APP_ENV') == 'local')
    platform = new H.service.Platform({
        'app_id': '{{ env('HERE_MAPS_ID') }}',
        'app_code': '{{ env('HERE_MAPS_CODE') }}'
    });
    @else
    platform = new H.service.Platform({
        'app_id': '{{ env('HERE_MAPS_ID') }}',
        'app_code': '{{ env('HERE_MAPS_CODE') }}',
        'useHTTPS': true
    });
    @endif

    var pixelRatio = window.devicePixelRatio || 1;
    var defaultLayers = platform.createDefaultLayers({
        tileSize: pixelRatio === 1 ? 256 : 512,
        ppi: pixelRatio === 1 ? undefined : 320
    });

    //initialize a map
    var map = new H.Map(document.getElementById('map'),defaultLayers.normal.map, {pixelRatio: pixelRatio});

    //dynamic map
    var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
    var ui = H.ui.UI.createDefault(map, defaultLayers);

    map.setCenter({lat: MAP_LAT, lng: MAP_LNG});
    map.setZoom(15);

    addDraggableMarker(map, behavior, MAP_LAT, MAP_LNG)

    /**
     * FUNCTIONS
     **/
    function addMarker(map, behavior) {
        var iconSVG = new H.map.Icon("{{ asset('/assets/img/default/marker.svg') }}", {size: {w: 80, h: 80}});
        var marker = new H.map.Marker({
            lat: MAP_LAT,
            lng: MAP_LNG
        }, {icon: iconSVG});
        map.addObject(marker);
    }

    function addDraggableMarker(map, behavior, lat, lng){

        var iconSVG = new H.map.Icon("{{ asset('/assets/img/default/marker.svg') }}", {size: {w: 80, h: 80}});
        var marker = new H.map.Marker({
            lat: lat,
            lng: lng
        }, {icon: iconSVG});

        // Ensure that the marker can receive drag events
        marker.draggable = true;
        map.addObject(marker);

        // disable the default draggability of the underlying map
        // when starting to drag a marker object:
        map.addEventListener('dragstart', function(ev) {
            var target = ev.target;
            if (target instanceof H.map.Marker) {
                behavior.disable();
            }
        }, false);


        // re-enable the default draggability of the underlying map
        // when dragging has completed
        map.addEventListener('dragend', function(ev) {
            var target = ev.target;
            if (target instanceof mapsjs.map.Marker) {
                behavior.enable();
            }
        }, false);

        // Listen to the drag event and move the position of the marker
        // as necessary
        map.addEventListener('drag', function(ev) {
            var target = ev.target,
                pointer = ev.currentPointer;
            if (target instanceof mapsjs.map.Marker) {
                target.setPosition(map.screenToGeo(pointer.viewportX, pointer.viewportY));

                var pos = map.screenToGeo(pointer.viewportX, pointer.viewportY);
                $('[name="map_lat"]').val(pos.lat)
                $('[name="map_lng"]').val(pos.lng)
            }
        }, false);
    }

    //check location from address
    function findAddressLocation(address) {

        //remove all markers and info bubbles
        map.removeObjects(map.getObjects())

        var geocoder = platform.getGeocodingService(),
            geocodingParameters = {
                searchText: address,
                jsonattributes : 1
            };

        geocoder.geocode(
            geocodingParameters,
            function(result) {

                if(typeof result.response.view[0] !== 'undefined') {
                    var locations = result.response.view[0].result;
                    lat = locations[0].location.displayPosition.latitude;
                    lng = locations[0].location.displayPosition.longitude;

                    addDraggableMarker(map, behavior, lat, lng);
                    map.setCenter({lat: lat, lng: lng});

                    $('[name="map_lat"]').val(lat)
                    $('[name="map_lng"]').val(lng)
                } else {
                    Growl.error('Não foi possível localizar a morada no mapa.')
                }
            },
            function(error) {
                Growl.error('Não foi possível localizar a morada no mapa.')
            }
        );
    }
</script>
{{-- GOOGLE MAPS API --}}
{{--@include('admin.customers.customers.partials.js_maps')--}}
@stop