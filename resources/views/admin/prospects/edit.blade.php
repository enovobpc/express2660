@section('title')
    Potenciais Clientes
@stop

@section('content-header')
    @trans('Potenciais Clientes')
    <small>
        @trans('Ficha de Potencial Cliente')
    </small>
@stop

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.prospects.index') }}">
            @trans('Potenciais Clientes')
        </a>
    </li>
    <li class="active">
        @trans('Ficha de Potencial Cliente')
    </li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box no-border m-b-15">
                <div class="box-body p-5">
                    <div class="row">
                        <div class="col-sm-8">
                            {{-- <div class="pull-left m-r-10">
                            @if ($prospect->filepath)
                                <img src="{{ asset($prospect->getCroppa(200)) }}" id="" style="border:none" class="w-60px"/>
                            @else
                                <img src="{{ asset('assets/img/default/avatar.png') }}" style="border:none" class="w-60px"/>
                            @endif
                        </div> --}}
                            <div class="pull-left">
                                <h4 class="m-t-0 m-b-5 pull-left uppercase">{{ $prospect->name }}</h4>
                                <div class="clearfix"></div>
                                <ul class="list-inline m-b-0">
                                    @if ($prospect->business_status)
                                        <li>
                                            <span class="label"
                                                style="background: {{ trans('admin/prospects.status-label.' . $prospect->business_status) }}">{{ trans('admin/prospects.status.' . $prospect->business_status) }}</span>
                                        </li>
                                    @endif

                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <a href="{{ route('admin.prospects.convert', $prospect->id) }}"
                                class="btn btn-sm btn-primary pull-right m-t-3 m-r-5" data-method="post"
                                data-confirm-title="@trans('Converter potencial cliente em cliente')"
                                data-confirm-class="btn-success" data-confirm-label="@trans('Converter')"
                                data-confirm="@trans('Pretende converter este potencial cliente em cliente?')">
                                <i class="fas fa-user-plus"></i> @trans('Converter em Cliente')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-md-3 col-lg-2">
            <div class="box box-solid">
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li class="active">
                            <a href="#tab-tipology" data-toggle="tab"><i class="fas fa-fw fa-suitcase"></i> @trans('Tipologia de
                                Negócio')</a>
                        </li>

                        <li>
                            <a href="#tab-info" data-toggle="tab"><i class="fas fa-fw fa-info-circle"></i> @trans('Ficha da
                                Empresa')</a>
                        </li>

                        <li class="{{ $prospect->exists ? '' : 'disabled' }}">
                            <a href="#tab-contacts" data-toggle="{{ $prospect->exists ? 'tab' : '' }}"><i
                                    class="fas fa-fw fa-phone"></i> @trans('Contactos')</a>
                        </li>

                        @if (Auth::user()->ability(Config::get('permissions.role.admin'), 'meetings'))
                            <li class="{{ $prospect->exists ? '' : 'disabled' }}">
                                <a href="#tab-meetings" data-toggle="{{ $prospect->exists ? 'tab' : '' }}"><i
                                        class="fas fa-fw fa-calendar-alt"></i> @trans('Reuniões')</a>
                            </li>
                        @endif


                        @if (Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables,prices_tables_view'))
                            <li class="{{ $prospect->exists ? '' : 'disabled' }}">
                                <a href="#tab-prices" data-toggle="{{ $prospect->exists ? 'tab' : '' }}"><i
                                        class="fas fa-fw fa-euro-sign"></i> @trans('Tabela de Preços')</a>
                            </li>
                        @endif

                        <li class="{{ $prospect->exists ? '' : 'disabled' }}">
                            <a href="#tab-attachments" data-toggle="{{ $prospect->exists ? 'tab' : '' }}"><i
                                    class="fas fa-fw fa-file"></i> @trans('Documentação')</a>
                        </li>

                        <li class="{{ $prospect->exists ? '' : 'disabled' }}">
                            <a href="#tab-history" data-toggle="{{ $prospect->exists ? 'tab' : '' }}"><i
                                    class="fas fa-fw fa-list-ul"></i> @trans('Histórico')</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="tab-content">
                <div class="active tab-pane" id="tab-tipology">
                    @include('admin.prospects.partials.tipology')
                </div>

                <div class="tab-pane" id="tab-info">
                    @include('admin.prospects.partials.info')
                </div>

                @if (Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables,prices_tables_view'))
                    <div class="tab-pane" id="tab-prices">
                        <?php $customer = $prospect; $source='prospects' ?>
                        @include('admin.customers.customers.partials.prices')
                    </div>
                @endif

                @if (Auth::user()->ability(Config::get('permissions.role.admin'), 'meetings'))
                    <div class="tab-pane" id="tab-meetings" data-empty="1">
                        @include('admin.prospects.partials.meetings')
                    </div>
                @endif

                @if (!$prospect->final_consumer)
                    <div class="tab-pane" id="tab-contacts" data-empty="1">
                        @include('admin.prospects.partials.contacts')
                    </div>
                    <div class="tab-pane" id="tab-attachments" data-empty="1">
                        @include('admin.prospects.partials.attachments')
                    </div>
                @endif

                <div class="tab-pane" id="tab-history">
                    @include('admin.prospects.partials.history')
                </div>
            </div>
        </div>
    </div>

    @include('admin.partials.modals.vat_validation')
    <?php $customer = $prospect; ?>
    @include('admin.customers.customers.modals.import_services_table')
    @include('admin.customers.customers.modals.import_global_services_table')
    @include('admin.customers.customers.modals.print_prices_table')
@stop

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.btn-sync-ballance-all').trigger('click');
        })

        $('.select-all-services').on('click', function(e) {
            e.preventDefault();
            $('.selectbox-services option').prop('selected', true);
            $('.selectbox-services').trigger('change')
        })

        var oTableMeetings;

        /**
         * Tab contacts
         * @returns {undefined}
         */
        $(document).on('click', 'a[href="#tab-contacts"]', function() {
            $tab = $('#tab-contacts');

            if ($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                var oTable = $('#datatable-contacts').DataTable({
                    columns: [{
                            data: 'select',
                            name: 'select',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                        {
                            data: 'department',
                            name: 'department'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'phone',
                            name: 'phone'
                        },
                        {
                            data: 'mobile',
                            name: 'mobile'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    ajax: {
                        url: "{{ route('admin.customers.contacts.datatable', $prospect->id) }}",
                        type: "POST",
                        data: function(d) {},
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('.filter-datatable').on('change', function(e) {
                    oTable.draw();
                    e.preventDefault();
                });
            }
        })

        /**
         * Tab meetings
         * @returns {undefined}
         */
        $(document).on('click', 'a[href="#tab-meetings"]', function() {
            $tab = $('#tab-meetings');

            if ($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTableMeetings = $('#datatable-meetings').DataTable({
                    columns: [{
                            data: 'select',
                            name: 'select',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'seller_id',
                            name: 'seller_id'
                        },
                        {
                            data: 'objectives',
                            name: 'objectives',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'occurrences',
                            name: 'occurrences',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'charges',
                            name: 'charges',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    ajax: {
                        url: "{{ route('admin.customers.meetings.datatable', $prospect->id) }}",
                        type: "POST",
                        data: function(d) {
                            d.status = $('select[name=status]').val();
                            d.date_min = $('input[name=date_min]').val();
                            d.date_max = $('input[name=date_max]').val();
                        },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('.filter-datatable').on('change', function(e) {
                    oTableMeetings.draw();
                    e.preventDefault();
                });
            }
        })

        /**
         * Tab attachments
         */
        var oTableAttachments;
        $(document).on('click', 'a[href="#tab-attachments"]', function() {
            $tab = $('#tab-attachments');

            if ($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTableAttachments = $('#datatable-attachments').DataTable({
                    columns: [{
                            data: 'select',
                            name: 'select',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'sort',
                            name: 'sort'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    order: [
                        [3, "desc"]
                    ],
                    ajax: {
                        url: "{{ route('admin.customers.attachments.datatable', $prospect->id) }}",
                        type: "POST",
                        data: function(d) {
                            d.type_id = $('select[name=type]').val();
                            d.active = $('select[name=active]').val();
                        },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('.filter-datatable').on('change', function(e) {
                    oTableAttachments.draw();
                    e.preventDefault();
                });
            }
        })

        $(document).on('hidden.bs.modal', '#modal-remote', function(event, data) {
            oTableAttachments.draw();
        })

        /**
         * Tab business history
         */
        var oTableHistory;
        $(document).on('click', 'a[href="#tab-history"]', function() {
            $tab = $('#tab-history');

            if ($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTableHistory = $('#datatable-history').DataTable({
                    columns: [
                        {data:'created_at', name:'created_at'},
                        {data:'status', name:'status'},
                        {data:'message', name:'message'},
                        {data:'operator_id' ,name:'operator_id'},
                    ],
                    order: [[0, "desc"]],
                    ajax: {
                        url: "{{ route('admin.prospects.business.history.datatable', $prospect->id) }}",
                        type: "POST",
                        data: function(d) {},
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('.filter-datatable').on('change', function(e) {
                    oTableHistory.draw();
                    e.preventDefault();
                });
            }
        })

        $('[data-dismiss="fileinput"]').on('click', function() {
            $('[name=delete_photo]').val(1);
        })

        $(document).ready(function() {
            $('a[href="#tab-{{ Request::get('tab') }}"]').trigger('click');
        })

        var parentTab = $('a[href="#tab-{{ Request::get('tab') }}"]').data('parent-tab');
        $('a[href="' + parentTab + '"]').trigger('click');

        $("select[name=import_customer_id]").select2({
            ajax: {
                url: "{{ route('admin.customers.search') }}",
                dataType: 'json',
                method: 'post',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        /**
         * Update prices
         */
        $('.form-update-prices').on('submit', function(e) {
            e.preventDefault()

            var newVal, val, percent;
            var $form = $(this);
            var percent = $form.find('[name="update_percent"]').val() / 100
            var $target = $('#' + $form.find('[name="update_target"]').val() + '-services');

            if (percent != 0 && percent != "") {

                $target.find('input[name*="price"]').each(function() {
                    val = $(this).val();

                    if (val != "") {
                        if ($form.find('[name="update_signal"]').val() == 'sub') {
                            newVal = parseFloat(val) - (parseFloat(val) * percent);
                        } else {
                            newVal = parseFloat(val) + (parseFloat(val) * percent);
                        }

                        $(this).val(newVal.toFixed(2));
                    }
                })

                Growl.success('Atualizado com sucesso. Grave a tabela para gravar as alterações.');
            }
        });

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
                    if (result) {
                        $form.submit();
                    }
                }
            });
        })

        $(document).on('click', '.customers-prices-tables [type=submit]', function(e) {
            e.preventDefault();

            var priceTable = $('[name="price_table_id"]').val();
            var priceTableLabel = $('[name="price_table_id"]').find(':selected').text();
            var $form = $(this).closest('form');

            if (priceTable == "") {
                $form.submit();
            } else {

                bootbox.confirm({
                    title: 'Gravar tabela de preços',
                    message: "<h4>Se gravar a tabela vai criar uma tabela personalizada para o cliente e este deixará de estar vinculado à tabela '<b>" +
                        priceTableLabel +
                        "</b>'.<br/>Pretende gravar e criar uma tabela personalizada?</h4>",
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
                        if (result) {
                            $form.submit();
                        }
                    }
                });
            }
        })

        $('[name="password"], [name="password_confirmation"]').on('click', function() {
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
    @include('admin.prospects.partials.js_maps')
@stop
