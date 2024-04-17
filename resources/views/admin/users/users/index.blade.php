@section('title')
    Colaboradores
@stop

@section('content-header')
    Colaboradores
@stop

@section('breadcrumb')
    <li class="active">@trans('Colaboradores')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs tabs-filter">
                <li class="active"><a href="#tab-all" data-type="all" data-toggle="tab">@trans('Todos')</a></li>
                <li><a href="#tab-operador" data-type="3" data-toggle="tab">@trans('Motoristas')</a></li>
                <li><a href="#tab-agencia" data-type="2" data-toggle="tab">@trans('Gerência')</a></li>
                <li><a href="#tab-administrativo" data-type="24" data-toggle="tab">@trans('Administrativos')</a></li>
                <li><a href="#tab-financeiro" data-type="8" data-toggle="tab">@trans('Financeiros')</a></li>
                <li><a href="#tab-balcao" data-type="7" data-toggle="tab">@trans('Balcão')</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-all">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                        <li>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </a>
                        </li>
                        <li>
                            @if(hasModule('human_resources') && Auth::user()->ability(Config::get('permissions.role.admin'), 'users_absences,users_cards'))
                                <div class="btn-group btn-group-sm" role="group">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.users.absences.create.global') }}" class="btn btn-primary dropdown-toggle"
                                           data-toggle="modal"
                                           data-target="#modal-remote-xs">
                                            <i class="fas fa-calendar-alt"></i> @trans('Registar Férias/Faltas')
                                        </a>
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-print"></i> @trans('Relatórios') <i class="fas fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#modal-print-validities">
                                                    @trans('Resumo Documentos a Expirar')
                                                </a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#modal-print-absences">
                                                    @trans('Exportar Férias e Ausências')
                                                </a>
                                            </li>
                                            <li class="divider"></li>
                                            {{--<li>
                                                <a href="#" data-toggle="modal" data-target="#modal-print-holidays-balance">
                                                    Imprimir Balanço Anual Férias
                                                </a>
                                            </li>--}}
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#modal-print-holidays-balance">
                                                    @trans('Exportar Balanço Anual Férias')
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <div class="btn-group btn-group-sm" role="group">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <div data-toggle="tooltip" title="{{ !hasModule('human_resources') ? 'Módulo de recursos humanos não ativo.' : '' }}">
                                            <button class="btn btn-sm btn-primary dropdown-toggle disabled">
                                                <i class="fas fa-calendar-alt"></i> @trans('Registar Férias/Faltas')
                                            </button>
                                        </div>
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-print"></i> @trans('Relatórios') <i class="fas fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="#" style="cursor: not-allowed">
                                                    @trans('Resumo Documentos a Expirar')
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" style="cursor: not-allowed">
                                                    @trans('Exportar Férias e Ausências')
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" style="cursor: not-allowed">
                                                    @trans('Exportar Balanço Anual Férias')
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endif

                        </li>
                        <li>
                            <div class="btn-group btn-group-sm" role="group">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-wrench"></i> @trans('Ferramentas') <i class="fas fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('admin.export.operators', Request::all()) }}" data-toggle="export-url">
                                                <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar listagem atual')
                                            </a>
                                        </li>
                                        @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'users_profissional_info'))
                                            <li class="divider"></li>
                                            <li>
                                                <a href="{{ route('admin.users.workgroups.index') }}" data-toggle="modal" data-target="#modal-remote">
                                                    <i class="fas fa-fw fa-list"></i> @trans('Gerir grupos trabalho')
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.users.absences-types.index') }}" data-toggle="modal" data-target="#modal-remote">
                                                    <i class="fas fa-fw fa-list"></i> @trans('Gerir tipos ausência')
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                <button type="button" class="btn btn-filter-datatable btn-default">
                                    <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                                </button>
                            </div>
                        </li>
                        <li class="fltr-primary w-195px">
                            <strong>@trans('Perfil')</strong><br class="visible-xs"/>
                            <div class="w-140px pull-left form-group-sm">
                                {{ Form::select('role', ['' => 'Todos'] + $roles, Request::has('role') ? Request::get('role') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                    </ul>
                    <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                        <ul class="list-inline pull-left">
                            <li style="margin-bottom: 5px;" class="col-xs-6">
                                <strong>@trans('Agência')</strong><br/>
                                <div class="w-150px">
                                    {{ Form::select('agency', ['' => 'Todos'] + $agencies, Request::has('agency') ? Request::get('agency') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-6">
                                <strong>@trans('Grupo Trabalho')</strong><br/>
                                <div class="w-150px">
                                    {{ Form::selectMultiple('workgroup', $workgroups, Request::has('workgroup') ? Request::get('workgroup') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-6">
                                <strong>@trans('Estado')</strong><br/>
                                <div class="w-100px">
                                    {{ Form::select('active', ['' => 'Todos', '1'=>'Ativo', '0'=>'Inativo'] , Request::has('active') ? Request::get('active') : '1', array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-6">
                                <strong>@trans('Login Gestão')</strong><br/>
                                <div class="w-100px">
                                    {{ Form::select('login_admin', ['' => 'Todos', '1'=>'Sim', '0'=>'Não'] , Request::has('login_admin') ? Request::get('login_admin') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-6">
                                <strong>@trans('Login App')</strong><br/>
                                <div class="w-100px">
                                    {{ Form::select('login_app', ['' => 'Todos', '1'=>'Sim', '0'=>'Não'] , Request::has('login_app') ? Request::get('login_app') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
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
                                <th class="w-1">@trans('Código')</th>
                                <th>@trans('Nome')</th>
                                <th class="w-120px">@trans('NIF/CC')</th>
                                <th class="w-120px">@trans('Contactos')</th>
                                <th class="w-120px">@trans('Grupo Trabalho')</th>
                                <th class="w-1">@trans('Perfil')</th>
                                <th class="w-1"><i class="fas fa-fw fa-mobile-alt" data-toggle="tooltip" title="Acesso à aplicação motorista"></i></th>
                                <th class="w-1"><i class="fas fa-fw fa-laptop" data-toggle="tooltip" title="Acesso à área de gestão"></i></th>
                                <th class="w-1">@trans('Ativo')</th>
                                {{--<th class="w-120px">Último Acesso</th>--}}
                                {{--<th class="w-1">Localização</th>--}}
                                <th class="w-1">@trans('Ações')</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="selected-rows-action hide">
                        {{ Form::open(array('route' => 'admin.users.selected.destroy')) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                        {{ Form::close() }}
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('admin.users.users.modals.validities')
@include('admin.users.users.modals.absences')
@include('admin.users.users.modals.holidays_balance')
@stop

@section('styles')
    <style>
        .ip-isp {
            overflow: hidden;
            text-overflow: ellipsis;
            width: 165px;
            height: 65px;
        }
    </style>
@stop

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ getGoogleMapsApiKey() }}"></script>
<script type="text/javascript">

    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'code', name: 'code', visible: false},
                {data: 'photo', name: 'photo', orderable: false, searchable: false},
                {data: 'unsigned_code', name: 'unsigned_code', 'class': 'text-center', searchable: false},
                {data: 'name', name: 'name'},
                {data: 'vat', name: 'vat'},
                {data: 'email', name: 'email'},
                {data: 'workgroup', name: 'workgroup', orderable: false, searchable: false},
                {data: 'roles', name: 'roles', orderable: false, searchable: false},
                /*{data: 'agencies', name: 'agencies'},*/
                {data: 'login_app', name: 'login_app', orderable: false, searchable: false},
                {data: 'login_admin', name: 'login_admin', orderable: false, searchable: false},
                {data: 'active', name: 'active', orderable: false, searchable: false},
                /*{data: 'last_login', name: 'last_login'},*/
                /*{data: 'location_last_update', name: 'location_last_update'},*/
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'phone', name: 'phone', visible: false},
                {data: 'fullname', name: 'fullname', visible: false},
                {data: 'id_card', name: 'id_card', visible: false},
                {data: 'ss_card', name: 'ss_card', visible: false},
            ],
            order: [[3, "desc"]],
            ajax: {
                url: "{{ route('admin.users.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.role        = $('select[name=role]').val();
                    d.active      = $('select[name=active]').val();
                    d.agency      = $('select[name=agency]').val();
                    d.workgroup   = $('select[name=workgroup]').val();
                    d.login_app   = $('select[name=login_app]').val();
                    d.login_admin = $('select[name=login_admin]').val();
                },
                complete: function () {
                    $('[data-toggle="popover"]').on('show.bs.popover', function() {
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
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
            }
        });
        
        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });

    $('.tabs-filter a').on('click', function(e){
        e.preventDefault();
        var type = $(this).data('type');


        if(type == 'all') {
            $('.doc-type-filter').show();
            $('[name="role"]').val('').trigger('change');
        } else {
            $('.doc-type-filter').hide();
            $('[name="role"]').val(type).trigger('change');
        }

        $('.btn-add-invoice, .btn-saft').show();
        $('.btn-add-receipt').hide();
        if(type == 'receipt') {
            $('.btn-add-invoice, .btn-saft').hide();
            $('.btn-add-receipt').show();
        }
    })

    $('#modal-print-validities [type=submit], #modal-print-holidays-balance [type=submit], #modal-print-absences [type=submit]').on('click', function(e){
        e.preventDefault();
        $(this).closest('form').submit();
        $(this).closest('.modal').modal('hide');
        $(this).button('reset');
    })

</script>
@stop