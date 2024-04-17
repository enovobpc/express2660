@section('title')
    Colaboradores
@stop

@section('content-header')
    Colaboradores
    <small>
        {{ $action }}
    </small>
@stop

@section('breadcrumb')
    <li>@trans('Entidades')</li>
    <li>
        <a href="{{ route('admin.users.index') }}">
            @trans('Colaboradores')
        </a>
    </li>
    <li class="active">
        {{ $action }}
    </li>
@stop

@section('content')
    @if($user->exists)
    <div class="row">
        <div class="col-md-12">
            <div class="box no-border m-b-15">
                <div class="box-body p-5">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="pull-left m-r-10">
                                @if($user->filepath)
                                    <img src="{{ asset($user->getCroppa(200, 200)) }}" id="" style="border:none" class="w-60px"/>
                                @else
                                    <img src="{{ asset('assets/img/default/avatar.png') }}" style="border:none" class="w-60px"/>
                                @endif
                            </div>
                            <div class="pull-left">
                                <h4 class="m-t-5 pull-left">
                                    {{ $user->name }}
                                    @if(!$user->active)
                                        <span class="label label-danger">@trans('Inativo')</span>
                                    @endif
                                    @if($user->password && ((!$user->is_operator && !$user->login_admin) || ($user->is_operator && !$user->login_app)))
                                        <i class="fas fa-ban text-red" data-toggle="tooltip" title="Utilizador bloqueado"></i>
                                    @endif
                                </h4>
                                <div class="clearfix"></div>
                                <ul class="list-inline m-b-0">
<!--                                    <li>
                                        @if($user->location_enabled)
                                        <span class="label label-success" data-toggle="tooltip" title="Os serviços de localização estão ativos na aplicação móvel.">
                                            <i class="fas fa-location-arrow"></i> Localização Ativa
                                        </span>
                                        @elseif($user->location_denied)
                                        <span class="label label-danger"  data-toggle="tooltip" title="Não é possível usar os serviços de localização porque estão definidos como bloqueados nas definições do telemóvel.">
                                            <i class="fas fa-location-arrow"></i> Localização Bloqueado
                                        </span>
                                        @else
                                        <span class="label label-default"  data-toggle="tooltip" title="O utilizador desligou a sua localização na aplicação móvel">
                                            <i class="fas fa-location-arrow"></i> Localização Inativa
                                        </span>
                                        @endif
                                    </li>-->
                                    <li><small>@trans('Registo:')</small> {{ $user->created_at->format('Y-m-d') }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <?php $today = Date::today(); ?>
                            <ul class="list-inline m-t-8 m-b-0 pull-right hidden-xs">
                                <li class="w-125px">
                                    <h4 class="m-0 pull-right" style="margin-top: -39px; position: absolute;">
                                        <small class="text-black">@trans('Ausências') {{ $today->year }}</small><br/>
                                        @if(!hasModule('human_resources'))
                                            <b data-toggle="tooltip" title="Módulo não ativo. Controle e faça a gestão de ausências e férias dos seus colaboradores.">N/A
                                                <small><i class="fas fa-info-circle text-black"></i></small>
                                            </b>
                                        @else
                                        <b>{{ @$absences['days'] ? $absences['days'] : 0 }}d | {{ @$absences['hours'] ? $absences['hours'] : 0 }}h <small>até hoje</small></b>
                                        @endif
                                    </h4>
                                </li>
                                <li class="w-105px">
                                    <h4 class="m-0 pull-right" style="margin-top: -39px; position: absolute;">
                                        <small class="text-black">@trans('Férias') {{ $today->year }}</small><br/>
                                        @if(!hasModule('human_resources'))
                                            <b data-toggle="tooltip" title="Módulo não ativo. Controle e faça a gestão de ausências e férias dos seus colaboradores.">N/A
                                                <small><i class="fas fa-info-circle text-black"></i></small>
                                            </b>
                                        @else
                                            <?php 
                                                $plusHolidays = Setting::get('rh_max_holidays') - @$holidaysLastYear['days'];
                                            ?>
                                            @if($absenceaAjusted)
                                                {{ Setting::get('rh_max_holidays') - @$holidays['days'] + $absenceaAjusted['days']}} <small>@trans('dias livres')</small>
                                            @elseif($user->created_at->format('Y') == date("Y"))
                                                    {{ Setting::get('rh_max_holidays') - @$holidays['days'] }} <small>@trans('dias livres')</small>
                                            @else
                                                <b data-toggle="tooltip" title="{{ $plusHolidays }} dias do ano passado">
                                                    {{ Setting::get('rh_max_holidays') - @$holidays['days'] + $plusHolidays }} <small>@trans('dias livres')</small><small> <i class="fas fa-info-circle text-black"></i></small>
                                                </b>
                                            @endif
                                        @endif

                                    </h4>
                                </li>
                                @if($holidaysNextYear)
                                    <li class="w-105px">
                                        <h4 class="m-0 pull-right" style="margin-top: -39px; position: absolute;">
                                            <small class="text-black">@trans('Férias') {{ $today->addYear()->year }}</small><br/>
                                            @if(!hasModule('human_resources'))
                                                <b data-toggle="tooltip" title="Módulo não ativo. Controle e faça a gestão de ausências e férias dos seus colaboradores.">N/A
                                                    <small><i class="fas fa-info-circle text-black"></i></small>
                                                </b>
                                            @else
                                                <b>{{ Setting::get('rh_max_holidays') - @$holidaysNextYear['days'] }} <small>dias livres</small></b>
                                            @endif

                                        </h4>
                                    </li>
                                @endif
                                <li class="divider"></li>
                                @if($user->password)
                                    <li>
                                        <a href="{{ route('admin.users.remote-login', $user->id) }}"
                                           style="margin-top: -30px"
                                           class="btn btn-sm btn-warning"
                                           data-method="post"
                                           data-confirm-title="Iniciar Sessão Remota"
                                           data-confirm-class="btn-success"
                                           data-confirm-label="Iniciar Sessão"
                                           data-confirm="Pretende iniciar sessão como {{ $user->display_name }}?"
                                           target="_blank">
                                            <i class="fas fa-user-circle"></i> @trans('Iniciar Sessão')
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <div class="btn-group btn-group-sm" role="group" style="margin-top: -30px">
                                        @if($nextId = $user->nextId())
                                            <a href="{{ route('admin.users.edit', [$nextId, Request::getQueryString()]) }}" class="btn btn-default" data-toggle="tooltip" title="Anterior">
                                                <i class="fa fa-fw fa-angle-left"></i>
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-default" disabled>
                                                <i class="fa fa-fw fa-angle-left"></i>
                                            </button>
                                        @endif

                                        @if($prevId = $user->previousId())
                                            <a href="{{ route('admin.users.edit', ['id' => $prevId, Request::getQueryString()]) }}" class="btn btn-default" data-toggle="tooltip" title="Próximo">
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
        </div>
    </div>
    @endif

    <div class="row row-5">
        @if($user->exists)
        <div class="col-md-3 col-lg-2">
            <div class="box box-solid">
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li class="{{ hasModule('human_resources') ? 'active' : '' }}">
                            <a href="#tab-personal-info" data-toggle="tab">
                                @if(!hasModule('human_resources'))
                                    <span class="text-muted pull-right"><i class="fas fa-lock"></i></span>
                                @endif
                                <i class="fas fa-fw fa-info-circle"></i> @trans('Dados Pessoais')
                            </a>
                        </li>

                        @if(hasModule('human_resources') && hasPermission('users_profissional_info'))
                            <li>
                                <a href="#tab-profissional-info" data-toggle="tab">
                                    <i class="fas fa-fw fa-briefcase"></i> @trans('Contrato e Salário')
                                </a>
                            </li>
                        @else
                            <li class="disabled">
                                <a href="#" data-toggle="tab">
                                    <span class="text-muted pull-right"><i class="fas fa-lock"></i></span>
                                    <i class="fas fa-fw fa-briefcase"></i> @trans('Contrato e Salário')
                                </a>
                            </li>
                        @endif


                        @if(hasModule('human_resources') && hasPermission('users_cards'))
                            <li>
                                <a href="#tab-cards" data-toggle="tab">
                                    <i class="far fa-fw fa-address-card"></i> @trans('Cartões e Certificados')
                                </a>
                            </li>
                        @else
                            <li class="disabled">
                                <a href="#" data-toggle="tab">
                                    <span class="text-muted pull-right"><i class="fas fa-lock"></i></span>
                                    <i class="far fa-fw fa-address-card"></i> @trans('Cartões e Certificados')
                                </a>
                            </li>
                        @endif

                        @if(hasModule('human_resources') && hasPermission('users_attendance'))
                            <li>
                                <a href="#tab-attendance" data-toggle="tab">
                                    <i class="fas fa-fw fa-clock"></i> @trans('Horários Trabalho')
                                </a>
                            </li>
                        @else
                            <li class="disabled">
                                <a href="#" data-toggle="tab">
                                    <span class="text-muted pull-right"><i class="fas fa-lock"></i></span>
                                    <i class="fas fa-fw fa-clock"></i> @trans('Horários Trabalho')
                                </a>
                            </li>
                        @endif


                        @if(hasModule('human_resources') && hasPermission('users_absences'))
                            <li>
                                <a href="#tab-absences" data-toggle="tab">
                                    <i class="fas fa-fw fa-calendar-alt"></i> @trans('Férias e Ausências')
                                </a>
                            </li>
                        @else
                            <li class="disabled">
                                <a href="#" data-toggle="tab">
                                    <span class="text-muted pull-right"><i class="fas fa-lock"></i></span>
                                    <i class="fas fa-fw fa-calendar-alt"></i> @trans('Férias e Ausências')
                                </a>
                            </li>
                        @endif

                        @if(hasModule('human_resources') && Auth::user()->ability(Config::get('permissions.role.admin'), 'users_cards'))
                            <li>
                                <a href="#tab-attachments" data-toggle="tab">
                                    <i class="fas fa-fw fa-file"></i> @trans('Documentos')
                                </a>
                            </li>
                        @else
                            <li class="disabled">
                                <a href="#" data-toggle="tab">
                                    <span class="text-muted pull-right"><i class="fas fa-lock"></i></span>
                                    <i class="fas fa-fw fa-file"></i> @trans('Documentos')
                                </a>
                            </li>
                        @endif

                        @if(hasModule('purchase_invoices') && hasPermission('purchase_invoices'))
                            <li>
                                <a href="#tab-expenses" data-toggle="tab" class="disabled">
                                    <i class="fas fa-fw fa-hand-holding-usd"></i> @trans('Despesas')
                                </a>
                            </li>
                        @else
                            <li class="disabled">
                                <a href="#" data-toggle="tab" class="disabled">
                                    <span class="text-muted pull-right"><i class="fas fa-lock"></i></span>
                                    <i class="fas fa-fw fa-hand-holding-usd"></i> @trans('Despesas')
                                </a>
                            </li>
                        @endif

                        <li class="{{ hasModule('human_resources') ? : 'active' }}">
                            <a href="#tab-login" data-toggle="tab">
                                <i class="fas fa-fw fa-lock"></i> @trans('Acesso à Aplicação')
                                @if(empty($user->password))
                                    <i class="fa fa-exclamation-triangle pull-right text-yellow m-t-3"></i>
                                @elseif(!$user->active)
                                    <i class="fa fa-ban pull-right text-red m-t-3"></i>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9 col-lg-10">
        @else
        <div class="col-md-12">
        @endif
            <div class="tab-content">
                <div class="{{ hasModule('human_resources') ? 'active' : '' }} tab-pane" id="tab-personal-info">
                    @include('admin.users.users.partials.personal_info')
                </div>

                @if(hasModule('human_resources') && Auth::user()->ability(Config::get('permissions.role.admin'), 'users_profissional_info'))
                <div class="tab-pane" id="tab-profissional-info">
                    @include('admin.users.users.partials.profissional_info')
                </div>
                @endif

                @if(hasModule('human_resources') && Auth::user()->ability(Config::get('permissions.role.admin'), 'users_cards'))
                <div class="tab-pane" id="tab-cards">
                    @include('admin.users.users.partials.cards')
                </div>
                <div class="tab-pane" id="tab-attachments" data-empty="1">
                    @include('admin.users.users.partials.attachments')
                </div>
                @endif

                @if(hasModule('human_resources') && Auth::user()->ability(Config::get('permissions.role.admin'), 'users_absences'))
                <div class="tab-pane" id="tab-attendance" data-empty="1">
                    @include('admin.users.users.partials.attendance')
                </div>
                <div class="tab-pane" id="tab-absences" data-empty="1">
                    @include('admin.users.users.partials.absences')
                </div>
                @endif

                @if(hasModule('purchase_invoices') && Auth::user()->ability(Config::get('permissions.role.admin'), 'purchase_invoices'))
                    <div class="tab-pane" id="tab-expenses" data-empty="1">
                        @include('admin.users.users.partials.expenses')
                    </div>
                @endif

                <div class="{{ hasModule('human_resources') ? : 'active' }} tab-pane" id="tab-login">
                    @include('admin.users.users.partials.login')
                </div>
            </div>
        </div>
    </div>
@stop

@section('styles')
    {{ HTML::style('vendor/ios-checkbox/dist/css/iosCheckbox.min.css')}}
    <style>
        .card-row {
            cursor: pointer;
        }

        .card-row .lbl-name i {
            opacity: 0;
        }

        .card-row:hover .lbl-name {
            cursor: pointer;
            color: #0f74a8;
        }

        .card-row:hover .lbl-name i {
            opacity: 1;
        }

        .check .icheckbox_minimal-blue {
            display: none;
        }
    </style>
@stop

@section('scripts')
    {{ HTML::script('vendor/ios-checkbox/dist/js/iosCheckbox.min.js')}}
    <script type="text/javascript">
        $(".ios").iosCheckbox();

        $('.select2country').select2(Init.select2Country());

        $('[name="password"], [name="password_confirmation"]').on('click', function(){
            $('.checkbox-send-password').show();
        })

        @if(Auth::user()->id != $user->id)
        $('[name="active"]').closest('.ios-checkbox-wrap').find('.ios-ui-select').on('click', function () {

            var checked = $(this).hasClass('checked');

            if(!checked) {
                $('[name="login_admin"], [name="login_app"]')
                    .prop('checked', false)
                    .prop('disabled', true)
                    .closest('.ios-checkbox-wrap')
                    .find('.ios-ui-select')
                    .removeClass('checked')
                    .css('opacity', 0.4)
            } else {
                changePermissions()
            }
        })

        $('[name="role_id"]').on('change', function () {
            changePermissions()
        })

        function changePermissions(){

            var permission = $('[name="role_id"]').val();

            if(permission == 3) { //MOTORISTA
                $('[name="login_admin"]')
                    .prop('checked', true)
                    .prop('disabled', true)
                    .closest('.ios-checkbox-wrap')
                    .find('.ios-ui-select')
                    .removeClass('checked')
                    .css('opacity', 0.4)

                $('[name="login_app"]')
                    .prop('checked', true)
                    .prop('disabled', false)
                    .closest('.ios-checkbox-wrap')
                    .find('.ios-ui-select')
                    .addClass('checked')
                    .css('opacity', 1)
            } else {
                $('[name="login_admin"]')
                    .prop('checked', true)
                    .prop('disabled', false)
                    .closest('.ios-checkbox-wrap')
                    .find('.ios-ui-select')
                    .addClass('checked')
                    .css('opacity', 1)

                $('[name="login_app"]')
                    .prop('checked', false)
                    .prop('disabled', false)
                    .closest('.ios-checkbox-wrap')
                    .find('.ios-ui-select')
                    .removeClass('checked')
                    .css('opacity', 1)

            }
        }
        @endif

        $('[data-toggle="popover"]').on('mouseout', function (e) {
            $('[data-toggle="popover"]').not(this).popover('hide');
        });

        $("[data-target='#datatable-expenses'] select[name=expenses_provider]").select2({
            minimumInputLength: 2,
            allowClear: true,
            ajax: Init.select2Ajax("{{ route('admin.shipments.search.provider') }}")
        });

        $('[data-dismiss="fileinput"]').on('click', function () {
            $('[name=delete_photo]').val(1);
        })

        @if($user->exists)
        var url = "{{ config('app.core') . '/helper/ip/location' }}" + "?ip={{ $user->ip }}";
        $.ajax({
            url: url,
            type: 'GET',
            crossDomain: true,
            success: function (data) {
                if (data.status) {
                    $('.ip-country').html(data.country_name)
                    $('.ip-city').html(data.city)
                    $('.ip-postal-code').html(data.postal_code)
                    $('.ip-isp').html(data.isp)
                } else {
                    $('.ip-country')
                        .closest('table')
                        .find('.fa-circle-notch')
                        .removeClass('fa-spin')
                        .removeClass('fa-circle-notch')
                        .addClass('fa-exclamation-triangle')
                        .addClass('text-red')
                }
            },
            fail: function (data) {
                $('.ip-country')
                    .closest('table')
                    .find('.fa-circle-notch')
                    .removeClass('fa-spin')
                    .removeClass('fa-circle-notch')
                    .addClass('fa-exclamation-triangle')
                    .addClass('text-red')
            }
        });
        @endif

        $(document).ready(function(){
            $('a[href="#tab-{{ Request::get("tab") }}"]').trigger('click');
        })

        var parentTab = $('a[href="#tab-{{ Request::get("tab") }}"]').data('parent-tab');
        $('a[href="' + parentTab + '"]').trigger('click');

        /**
         * Tab expenses
         */
        var oTableExpenses, oTableExpensesFixed;
        $(document).on('click', 'a[href="#tab-expenses"]', function(){
            $tab = $('#tab-expenses');

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTableExpenses = $('#datatable-expenses').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'date', name: 'date'},
                        {data: 'description', name: 'description'},
                        {data: 'provider_id', name: 'provider_id'},
                        {data: 'type_id', name: 'type_id'},
                        {data: 'total', name: 'total'},
                        {data: 'assigned_invoice_id', name: 'assigned_invoice_id'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    order: [[2, "desc"]],
                    ajax: {
                        type: "POST",
                        url: "{{ route('admin.users.expenses.datatable', $user->id) }}",
                        data: function (d) {
                            d.type     = $('#tab-costs-expenses select[name=expenses_type]').val();
                            d.provider = $('#tab-costs-expenses select[name=expenses_provider]').val();
                            d.date_min = $('#tab-costs-expenses input[name=expenses_date_min]').val();
                            d.date_max = $('#tab-costs-expenses input[name=expenses_date_max]').val();
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableExpenses) },
                        complete: function () { Datatables.complete(); },
                        error: function () {}
                    }
                });

                oTableExpensesFixed = $('#datatable-expenses-fixed').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'start_date', name: 'start_date'},
                        {data: 'end_date', name: 'end_date'},
                        {data: 'description', name: 'description'},
                        {data: 'provider_id', name: 'provider_id'},
                        {data: 'type_id', name: 'type_id'},
                        {data: 'total', name: 'total'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    order: [[3, "desc"]],
                    ajax: {
                        type: "POST",
                        url: "{{ route('admin.users.expenses.datatable', $user->id) }}",
                        data: function (d) {
                            d.fixed = 1;
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableExpensesFixed) },
                        complete: function () { Datatables.complete(); },
                        error: function () {}
                    }
                });

                $('#tab-costs-expenses .filter-datatable').on('change', function (e) {
                    oTableExpenses.draw();
                    e.preventDefault();
                });

                $('#tab-costs-fixed .filter-datatable').on('change', function (e) {
                    oTableExpensesFixed.draw();
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
                        {data: 'type_id', name: 'type_id'},
                        {data: 'sort', name: 'sort'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    order: [[4, "desc"]],
                    ajax: {
                        type: "POST",
                        url: "{{ route('admin.users.attachments.datatable', $user->id) }}",
                        data: function (d) {
                            d.type   = $('select[name=attachment_type]').val();
                            d.active = $('select[name=active]').val();
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableAttachments) },
                        complete: function () { Datatables.complete(); },
                        error: function () {}
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
         * Tab absences
         */
        var oTableAbsences;
        $(document).on('click', 'a[href="#tab-absences"]', function(){
            $tab = $('#tab-absences');

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTableAbsences = $('#datatable-absences').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'type_id', name: 'type_id'},
                        {data: 'status', name: 'status', class: 'text-center'},
                        {data: 'start_date', name: 'start_date'},
                        {data: 'end_date', name: 'end_date'},
                        {data: 'duration', name: 'duration'},
                        {data: 'obs', name: 'obs'},
                        {data: 'is_remunerated', name: 'is_remunerated', class:'text-center', orderable: false, searchable: false},
                        {data: 'is_meal_subsidy', name: 'is_meal_subsidy', class:'text-center', orderable: false, searchable: false},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    order: [[4, "desc"]],
                    ajax: {
                        type: "POST",
                        url: "{{ route('admin.users.absences.datatable', $user->id) }}",
                        data: function (d) {
                            d.type_id = $('select[name=type]').val();
                            d.active = $('select[name=active]').val();
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableAttachments) },
                        complete: function () { Datatables.complete(); },
                        error: function () {}
                    }
                });

                $('[data-target="#datatable-absences"] .filter-datatable').on('change', function (e) {
                    oTableAbsences.draw();
                    e.preventDefault();
                });
            }
        })

        /**
         * Tab absences
         */
         var oTableAttendance;
        $(document).on('click', 'a[href="#tab-attendance"]', function(){
            $tab = $('#tab-attendance');

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTableAttendance = $('#datatable-attendance').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},

                        {data: 'start_date', name: 'start_date'},
                        {data: 'start_hour', name: 'start_hour', searchable: false},
                        {data: 'end_date', name: 'end_date'},

                        {data: 'type', name: 'type', orderable: false, searchable: false},
                        {data: 'vehicle', name: 'vehicle', orderable: false, searchable: false},

                        {data: 'start_km', name: 'start_km', class: 'text-center'},
                        {data: 'end_km', name: 'end_km', class: 'text-center'},
                        {data: 'duration', name: 'duration', class: 'text-center bold', orderable: false, searchable: false},
                        {data: 'total_km', name: 'total_km', class: 'text-center bold'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    order: [[2, "desc"]],
                    ajax: {
                        url: "{{ route('admin.users.attendance.datatable', $user->id) }}",
                        type: "POST",
                        data: function (d) {
                            d.date_min = $('[data-target="#datatable-usage"] input[name=usage_date_min]').val();
                            d.date_max = $('[data-target="#datatable-usage"] input[name=usage_date_max]').val();
                            d.vehicle  = $('[data-target="#datatable-usage"] select[name=usage_vehicle]').val();
                            d.type     = $('[data-target="#datatable-usage"] select[name=type]').val();

                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableAttendance) },
                        complete: function () { Datatables.complete(); },
                        error: function () {}
                    }
                });

                $('[data-target="#datatable-attendance"] .filter-datatable').on('change', function (e) {
                    oTableAttendance.draw();
                    e.preventDefault();
                });
            }
        })

        //Add new card
        $('.btn-add-card').on('click', function(){
            var $clone = $('.table-cards tr:last').clone();

            $clone.find('input').val('');
            $clone.show();
            $clone.find('.rq-field').prop('required', true)
            $('.table-cards tr:last-child').prev().after($clone);
        })

        //Delete new card
        $(document).on('click', '.btn-delete-card', function(e){
            e.preventDefault();
            $(this).closest('tr').remove();
        })

        //edit name
        $(document).on('click', '.card-row', function(e){
            e.preventDefault();
            $(this).find('.lbl-name').hide()
            $(this).find('.inp-name').show()
        })

    </script>
@stop