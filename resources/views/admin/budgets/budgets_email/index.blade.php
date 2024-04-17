@section('title')
    Gestão Orçamental
@stop

@section('content-header')
    Gestão Orçamental
@stop

@section('breadcrumb')
    <li class="active">Gestão Orçamental</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <div class="w-25 pull-right">
                    <div class="input-group w-100">
                        <div class="input-group-addon datatable-search-loading" style="    border: none;
    position: absolute;
    top: 2px;
    left: 2px;">
                            <i class="fas fa-search" style="
                                margin-left: -5px;
                                margin-top: 1px;
                                margin-top: 0;
                                z-index: 10;
                                position: absolute;"></i>
                        </div>
                        {{ Form::text('data_search', Request::has('data_search') ? Request::get('data_search') : null, ['class' => 'form-control input-sm filter-datatable', 'style' => 'font-size: 14px; padding-left: 27px;']) }}
                    </div>
                </div>
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.budgets.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.budgets.sync.emails') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-sync-alt"></i> Sync. E-mails
                        </a>
                    </li>
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    <li>
                        <strong>Estado Cliente</strong>
                        {{ Form::select('status', ['' => 'Todos'] + trans('admin/budgets.status'), Request::has('status') ? Request::get('status') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li>
                            <strong>Estado Forn.</strong>
                            {{ Form::select('provider_status', ['' => 'Todos'] + trans('admin/budgets.provider-status'), Request::has('provider_status') ? Request::get('provider_status') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>Data</strong>
                            <div class="input-group input-group-sm w-200px">
                                {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início']) }}
                                <span class="input-group-addon">até</span>
                                {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim']) }}
                            </div>
                        </li>
                        <li style="width: 145px">
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('hide_final_status', 1, true) }}
                                    Ocultar Finalizados
                                </label>
                            </div>
                        </li>
                        @if(Setting::get('shipments_limit_search'))
                            <li style="width: 130px">
                                <div class="checkbox">
                                    <label>
                                        {{ Form::checkbox('limit_search', 1, Request::has('limit_search') ? Request::get('limit_search') : true) }}
                                        Últimos {{ Setting::get('shipments_limit_search') }} meses
                                    </label>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-1">Código</th>
                                <th>Assunto</th>
                                @if(config('app.source') != 'intercourier')
                                <th class="w-70px">Envio</th>
                                @endif
                                @if(hasModule('budgets_animals') || hasModule('budgets_courier'))
                                <th class="w-70px">Orçamento</th>
                                @endif
                                <th class="w-1">Preço</th>
                                <th class="w-70px">Dt. Pedido</th>
                                <th class="w-70px">Responsável</th>
                                <th class="w-90px">Resp. Cliente</th>
                                <th class="w-80px">Resp. Forn.</th>
                                <th class="w-55px">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.budgets.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('styles')
    <style>
        .MsoNormal {
            margin: 0 !important;
        }
        .popover {
            max-width: 600px !important;
        }

    </style>
@stop

@section('scripts')
<script type="text/javascript">
    var editor;
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'budget_no', name: 'budget_no', visible: false},
                {data: 'id', name: 'id'},
                {data: 'subject', name: 'subject'},
                @if(config('app.source') != 'intercourier')
                {data: 'shipment_id', name: 'shipment_id'},
                @endif
                @if(hasModule('budgets_animals') || hasModule('budgets_courier'))
                {data: 'courier_budget_id', name: 'courier_budget_id'},
                @endif
                {data: 'total', name: 'total'},
                {data: 'date', name: 'date'},
                {data: 'user_id', name: 'user_id'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'provider_status', name: 'provider_status', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'name', name: 'name', visible: false},
                {data: 'email', name: 'email', visible: false},
                {data: 'message', name: 'message', visible: false},
            ],
            dom: "<'row row-0'<'col-md-9 col-sm-8 datatable-filters-area'><'col-sm-4 col-md-3'><'col-sm-12 datatable-filters-area-extended'>>" +
            "<'row row-0'<'col-sm-12'tr>>" +
            "<'row row-0'<'col-sm-5'li><'col-sm-7'p>>",
            ajax: {
                url: "{{ route('admin.budgets.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.date_min = $('input[name=date_min]').val();
                    d.date_max = $('input[name=date_max]').val();
                    d.status = $('select[name=status]').val();
                    d.provider = $('select[name=provider]').val();
                    d.provider_status = $('select[name=provider_status]').val();
                    d.hide_final_status = $('input[name=hide_final_status]:checked').length;
                    d.limit_search = $('input[name=limit_search]:checked').length;
                },
                complete: function () {

                    $('[data-toggle="popover"]').popover({
                        'placement' : 'right',
                        'html' : true,
                        'trigger': 'click',
                        container: 'body',
                        'template' : '<div class="popover" role="tooltip">' +
                        '<div class="arrow"></div>' +
                        '<h3 class="popover-title"></h3>' +
                        '<div class="popover-content"></div>' +
                        '</div>'
                    })
                },
                error: function () {
                    $.bootstrapGrowl("<i class='fas fa-exclamation-circle'></i> Ocorreu um erro interno ao obter os dados da tabela.",
                            {type: 'error', align: 'center', width: 'auto', delay: 8000});
                }
            }
        });

        $('[name="date_max"]').on( 'keyup', function () {
            oTable.filter('pedido espanha', true, true, false ).draw();
        } );

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });

        $(document).on('keyup', '[name="data_search"]', function(){
            $('.datatable-search-loading').find('i').addClass('fa-circle-notch fa-spin');

            var searchValue = $(this).val();
            searchValue = searchValue.replace(' ', '%')
            oTable.search(searchValue).draw();
        })

        //show concluded shipments
        $(document).on('change', '[name="limit_search"], [name="hide_final_status"]', function (e) {
            oTable.draw();
            e.preventDefault();

            var name = $(this).attr('name');
            var value = $(this).is(':checked');
            value = value == false ? 0 : 1;

            newUrl = Url.updateParameter(Url.current(), name, value)
            Url.change(newUrl);

        });
    });

</script>
@stop