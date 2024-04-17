@section('title')
Clientes
@stop

@section('content-header')
Clientes
@stop

@section('breadcrumb')
<li class="active">@trans('Clientes')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                @if($unvalidated)
                    <div class="alert alert-warning alert-dismissable hidden-print bigger-110">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <strong class="bigger-120">
                            <i class="fas fa-info-circle"></i>
                            @trans('Existem') {{ $unvalidated }} @trans('novos clientes que aguardam aprovação')
                        </strong>

                        <a href="{{ route('admin.customers.validate') }}"
                           style="text-decoration: none; color: #333"
                           class="btn btn-xs btn-default"
                           data-toggle="modal"
                           data-target="#modal-remote-lg">
                           @trans('Aprovar ou Rejeitar')</a>

                    </div>
                @endif
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    @if(Auth::user()->isGuest())
                    <li>
                        <button type="button" class="btn btn-success btn-sm disabled" disabled>
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </button>
                    </li>
                    <li>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-default dropdown-toggle" disabled>
                                <i class="fas fa-wrench"></i> @trans('Ferramentas') <i class="fas fa-angle-down"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                                <i class="fas fa-fw fa-comment-alt"></i> @trans('Enviar Mensagem') <i class="fas fa-angle-down"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                            </button>
                        </div>
                    </li>
                    @else
                    <li>
                        <a href="{{ route('admin.customers.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </a>
                    </li>
                    <li>
                        <div class="btn-group btn-group-sm" role="group">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-wrench"></i> @trans('Ferramentas') <i class="fas fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('admin.export.customers', Request::all()) }}" data-toggle="export-url">
                                            <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar listagem atual')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.importer.index',['type' => 'customers']) }}" target="_blank">
                                            <i class="fas fa-fw fa-upload"></i> @trans('Importar Clientes')
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="{{ route('admin.customers-types.index') }}" data-toggle="modal" data-target="#modal-remote">
                                            <i class="fas fa-fw fa-list"></i> @trans('Gerir tipos de clientes')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="modal" data-target="#modal-mass-inactivate">
                                            <i class="fas fa-fw fa-user-times"></i> @trans('Inativar clientes sem atividade')
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            @if(hasModule('account'))
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-comment-alt"></i> @trans('Mensagens') <i class="fas fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('admin.customers.messages.index') }}">
                                            <i class="fas fa-fw fa-comment-alt"></i> @trans('Enviar mensagens em massa')
                                        </a>
                                    </li>
                                    @if(hasPermission('sms'))
                                    <li>
                                        <a href="{{ route('admin.sms.create') }}"
                                           data-toggle="modal"
                                            data-target="#modal-remote">
                                            <i class="fas fa-fw fa-mobile-alt"></i> @trans('Enviar mensagem SMS')
                                        </a>
                                    </li>
                                    @endif
                                    <li>
                                        <a href="#" data-toggle="modal" data-target="#modal-list-emails">
                                            <i class="fas fa-fw fa-envelope"></i> @trans('Obter lista de e-mails')
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            @endif
                            <button type="button" class="btn btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                            </button>
                        </div>
                    </li>
                    @endif

                    @if(count($agencies) > 1)
                        <li class="fltr-primary w-200px">
                            <strong>@trans('Agência')</strong><br class="visible-xs"/>
                            <div class="pull-left form-group-sm w-130px">
                                {{ Form::select('agency', ['' => 'Todos'] + $agencies, Request::has('agency') ? Request::get('agency') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                    @endif
                    <li class="fltr-primary w-110px">
                        <strong>@trans('Código')</strong><br class="visible-xs"/>
                        <div class="w-50px pull-left form-group-sm" style="position: relative">
                            {{ Form::text('code', fltr_val(Request::all(), 'code'), array('class' => 'form-control input-sm filter-datatable', 'style' => 'width: 100%;')) }}
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        @if($pricesTables)
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Tabela de Preços')</strong><br/>
                            <div class="w-110px">
                                {{ Form::select('prices', ['' => 'Todas', '-1' => 'Sem tabela', '0' => 'Personalizada'] + $pricesTables, Request::has('prices') ? Request::get('prices') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        @endif
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>@trans('Distrito Expedição')</strong><br/>
                            <div class="w-140px">
                                {{ Form::select('district', ['' => 'Todos'] + trans('districts_codes.districts.pt'), fltr_val(Request::all(), 'district'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>@trans('Concelho Expedição') <i class="fas fa-spin fa-circle-notch load-county" style="display: none"></i></strong><br/>
                            <div class="w-140px">
                                {{ Form::select('county', $recipientCounties ? ['' => 'Todos'] + $recipientCounties : ['' => 'Selec. Distrito'], fltr_val(Request::all(), 'county'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('País Expedição')</strong><br/>
                            <div class="w-110px">
                                {{ Form::select('country', ['' => 'Todos'] + trans('country'), Request::has('country') ? Request::get('country') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('País Faturação')</strong><br/>
                            <div class="w-110px">
                                {{ Form::select('country_billing', ['' => __('Todos')] + trans('country'), Request::has('country_billing') ? Request::get('country_billing') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        @if(!Auth::user()->hasRole([config('permissions.role.seller')]) && $sellers)
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>@trans('Comercial')</strong><br/>
                                <div class="w-140px">
                                    {{ Form::select('seller', ['' => 'Todos'] + $sellers, Request::has('seller') ? Request::get('seller') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                        @endif
                        @if($routes)
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>@trans('Rota')</strong><br/>
                                <div class="w-140px">
                                    {{ Form::select('route', ['' => 'Todas', '0' => 'Sem rota associada'] + $routes, Request::has('route') ? Request::get('route') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                        @endif
                        <li style="margin-bottom: 5px;" class="col-xs-12">
                            <strong>@trans('Motorista')</strong><br/>
                            <div class="w-140px">
                                {{ Form::select('operator', ['' => 'Todos'] + $operators, Request::has('operator') ? Request::get('operator') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Tipo')</strong><br/>
                            <div class="w-80px">
                                {{ Form::select('particular', ['' => 'Todos', '-1' => 'Empresa', '1' => 'Particular'], Request::has('particular') ? Request::get('particular') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Categoria')</strong><br/>
                            <div class="w-130px">
                                {{ Form::select('type', ['' => 'Todos'] + $types, Request::has('type') ? Request::get('type') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Pagamento')</strong><br/>
                            <div class="w-100px">
                                {{ Form::select('payment_method', ['' => 'Todos', 'wallet'=> 'Pré-pagamento'] + $paymentConditions, Request::has('payment_method') ? Request::get('payment_method') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Ult. Envio')</strong><br/>
                            <div class="w-100px">
                                {{ Form::select('last_shipment', ['' => 'Todos', '1' => 'Menos ' . Setting::get('alert_max_days_without_shipments') . ' dias', '2' => 'Mais ' . Setting::get('alert_max_days_without_shipments') . ' dias', '3' => 'Sem envios'], Request::has('last_shipment') ? Request::get('last_shipment') : Setting::get('customers_list_only_active'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Área Cliente')</strong><br/>
                            <div class="w-100px">
                                {{ Form::select('login', ['' => 'Todos', '1' => 'Com login', '0' => 'Sem login', '2' => 'Bloqueado'], Request::has('login') ? Request::get('login') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Webservices')</strong><br/>
                            <div class="w-120px">
                                {{ Form::select('webservices', ['' => 'Todos', '1' => 'Com webservices', '0' => 'Sem webservices'], Request::has('webservices') ? Request::get('webservices') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Aprovação')</strong><br/>
                            <div class="w-100px">
                                {{ Form::select('validated', ['' => 'Todos', '1' => 'Aprovado', '0' => 'Por aprovar'], Request::has('validated') ? Request::get('validated') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Estado')</strong><br/>
                            <div class="w-100px">
                                {{ Form::select('active', ['' => 'Todos', '1' => 'Ativo', '0' => 'Inativo'], Request::has('active') ? Request::get('active') : '1', array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-1"></th>
                                <th class="w-1">@trans('N.º')</th>
                                <th>@trans('Designação Social')</th>
                                <th class="w-150px">@trans('Contactos')</th>
                                <th>@trans('Localidade')</th>
                                @if($sellers)
                                <th class="w-120px">@trans('Comercial')</th>
                                @endif
                                <th class="w-85px">@trans('Ult. Env.')</th>
                                @if(Setting::get('customers_list_show_wallet'))
                                    <th class="w-1">@trans('Saldo')</th>
                                @endif
                                @if($routes)
                                <th class="w-1"><i class="fas fa-route" data-toggle="tooltip" title="Rota"></i></th>
                                @endif
                                <th class="w-1"><i class="fas fa-euro-sign" data-toggle="tooltip" title="Tabela Preços"></i></th>
                                <th class="w-1"><i class="fas fa-plug" data-toggle="tooltip" title="Webservices Ativos"></i></th>
                                <th class="w-1"><i class="fas fa-user-circle" data-toggle="tooltip" title="Acesso à Área Cliente"></i></th>
                                <th class="w-1"><i class="fas fa-flag" data-toggle="tooltip" title="País"></i></th>
                                <th class="w-1">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.customers.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-confirm-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                    {{ Form::close() }}
                    {{ Form::open(array('route' => 'admin.customers.selected.inactivate')) }}
                    <button class="btn btn-sm btn-default m-l-5"
                            data-action="confirm"
                            data-confirm-title="Inativar selecionados"
                            data-confirm-class="btn-success"
                            data-confirm-label="Inativar"
                            data-confirm="Pretende inativar os clientes selecionado e oculta-los da lista? Pode voltar a ativar o cliente a qualquer momento.">
                        <i class="fas fa-user-times"></i> @trans('Inativar')
                    </button>
                    {{ Form::close() }}
                    <button class="btn btn-sm btn-default m-l-5" data-toggle="modal" data-target="#modal-mass-update">
                        <i class="fas fa-pencil-alt"></i> @trans('Editar em Massa')
                    </button>
                    <a href="{{ route('admin.export.customers') }}" class="btn btn-sm btn-default m-l-5" data-action-url="datatable-action-url">
                        <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                    </a>
                    @include('admin.customers.customers.modals.mass_update')
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.customers.customers.modals.list_emails')
@include('admin.customers.customers.modals.mass_inactivate')
@stop

@section('scripts')
<script type="text/javascript">
    var oTable;
    $(document).ready(function () {

        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'photo', name: 'photo', orderable: false, searchable: false},
                {data: 'unsigned_code', name: 'unsigned_code', searchable: false},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'city', name: 'city'},
                @if($sellers)
                {data: 'seller_id', name: 'seller_id', searchable: false},
                @endif
                {data: 'last_shipment', name: 'last_shipment', searchable: false},
                @if(Setting::get('customers_list_show_wallet'))
                {data: 'wallet_balance', name: 'wallet_balance', searchable: false},
                @endif
                @if($routes)
                {data: 'route', name: 'route', orderable: false, searchable: false, class: 'text-center'},
                @endif
                {data: 'prices', name: 'prices', orderable: false, searchable: false, class: 'text-center'},
                {data: 'webservices', name: 'webservices', orderable: false, searchable: false, class: 'text-center'},
                {data: 'login', name: 'login', orderable: false, searchable: false, class: 'text-center'},
                {data: 'country', name: 'country', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'mobile', name: 'mobile', visible: false},
                {data: 'email', name: 'email', visible: false},
                {data: 'other_name', name: 'other_name', visible: false},
                {data: 'billing_name', name: 'billing_name', visible: false},
                {data: 'contact_email', name: 'contact_email', visible: false},
                {data: 'refunds_email', name: 'refunds_email', visible: false},
                {data: 'code_abbrv', name: 'code_abbrv', visible: false},
                {data: 'vat', name: 'vat',  visible: false},
                {data: 'bank_iban', name: 'bank_iban', orderable: false, searchable: true, visible: false},
                {data: 'code', name: 'code',  visible: false},
            ],
            order: [[3, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('admin.customers.datatable') }}",
                data: function (d) {
                    d.agency          = $('select[name=agency]').val();
                    d.code            = $('input[name=code]').val();
                    d.type_id         = $('select[name=type]').val();
                    d.seller          = $('select[name=seller]').val();
                    d.operator        = $('select[name=operator]').val();
                    d.route           = $('select[name=route]').val();
                    d.prices          = $('select[name=prices]').val();
                    d.login           = $('select[name=login]').val();
                    d.webservices     = $('select[name=webservices]').val();
                    d.billing_code    = $('select[name=billing_code]').val();
                    d.payment_method  = $('select[name=payment_method]').val();
                    d.district        = $('select[name=district]').val();
                    d.county          = $('select[name=county]').val();
                    d.country         = $('select[name=country]').val();
                    d.billing_country = $('select[name=billing_country]').val();
                    d.last_shipment   = $('select[name=last_shipment]').val();
                    d.active          = $('select[name=active]').val();
                    d.particular      = $('select[name=particular]').val();
                    d.validated       = $('select[name=validated]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                //error: function () { Datatables.error(); }
            },
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();

            var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);
        });

        //enable option to search with enter press
        Datatables.searchOnEnter(oTable);
    });

    $('#get-emails-list').on('click', function(){
        var agency         = $('#modal-list-emails [name="agency"]').val();
        var type           = $('#modal-list-emails [name="type"]').val();
        var payment_method = $('#modal-list-emails [name="payment_method"]').val();

        $.post('{{ route('admin.customers.list-emails') }}', {agency: agency, type:type, payment_method:payment_method}, function(data){
            $('#modal-list-emails [name="emails"]').val(data.emails);
            $('#modal-list-emails .total-helper').show();
            $('#modal-list-emails .total-helper b').html(data.total);
        }).fail(function(){
            Growl.error500();
        })
    });


    $(document).on('change', '[name=district]', function(){
        var district = $(this).val();
        $('.load-county').show();

        $.post('{{ route('admin.shipments.get.counties') }}', {district:district}, function (data) {
            $('[name=county]').empty().select2({data:data}).trigger('change');
        }).always(function () {
            $('.load-county').hide();
        })
    })
</script>
@stop