@section('title')
    Faturação a Terceiros
@stop

@section('content-header')
    Faturação a Terceiros
    <small>Consultar</small>
@stop

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.billing.providers.index', ['month' => $month, 'year' => $year]) }}">
            Faturação a Terceiros
        </a>
    </li>
    <li class="active">
        Consultar
    </li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <div class="mailbox-read-info p-t-5 p-r-0 p-l-0 p-b-15">
                    <div class="pull-right" style="margin-top: -5px">
                        <a href="{{ route('admin.importer.index') }}" class="btn btn-sm btn-default" target="_blank">
                            <i class="fas fa-upload"></i> Conferir automático por ficheiro
                        </a>
                        <a href="{{ route('admin.printer.billing.providers.summary', [$provider->id, 'month' => $month, 'year' => $year]) }}" class="btn btn-sm btn-default" target="_blank">
                            <i class="fas fa-print"></i> Imprimir Resumo
                        </a>
                        <a href="{{ route('admin.export.billing.providers.shipments', [$provider->id, 'month' => $month, 'year' => $year]) }}" class="btn btn-sm btn-default" data-toggle="export-url">
                            <i class="fas fa-fw fa-file-excel"></i> Exportar
                        </a>
                    </div>
                    <h3>
                        <i class="fas fa-square" style="color: {{ $provider->color }}"></i>
                        Fornecedor {{ str_limit($provider->name, 50) }}
                        <small>// {{ trans('datetime.month.'.$month) }} de {{ $year }}</small>
                    </h3>
                </div>
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    <li class="fltr-primary w-210px">
                        <strong>Estado</strong>
                        <div class="w-150px pull-left form-group-sm">
                            {{ Form::selectMultiple('status',  $status, fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-180px">
                        <strong>Serviço</strong>
                        <div class="w-120px pull-left form-group-sm">
                            {{ Form::selectMultiple('service', $services, fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-200px">
                        <strong>Conferido</strong>
                        <div class="w-80px pull-left form-group-sm">
                            {{ Form::select('conferred', array('' => 'Todos', '1' => 'Sim', '0' => 'Não'), Request::has('conferred') ? Request::get('conferred') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li style="margin-bottom: 5px;">
                            <strong>Data</strong><br/>
                            <div class="input-group input-group-sm w-220px">
                                {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                                <span class="input-group-addon">até</span>
                                {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                            </div>
                        </li>
                        <li style="margin-bottom: -14px;">
                            <strong>Cliente</strong><br/>
                            <div class="input-group input-group-sm">
                                <div class="w-200px pull-left">
                                    {{ Form::select('customer', [], fltr_val(Request::all(), 'customer'), array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos')) }}
                                </div>
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;">
                            <strong>Operador</strong><br/>
                            <div class="w-160px">
                                {{ Form::selectMultiple('operator', $operators, fltr_val(Request::all(), 'operaor'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;">
                            <strong>Cobrança</strong><br/>
                            <div class="w-80px">
                                {{ Form::select('charge', array('' => 'Todos', '1' => 'Sim', '0' => 'Não'), fltr_val(Request::all(), 'charge'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;">
                            <strong>Portes</strong><br/>
                            <div class="w-80px">
                                {{ Form::select('payment_recipient', array('' => 'Todos', '1' => 'Sim', '0' => 'Não'), Request::has('payment_recipient') ? Request::get('payment_recipient') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="table-responsive m-t-10">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-90px">TRK</th>
                                <th>Remetente</th>
                                <th>Destinatário</th>
                                <th class="w-1">Serv.</th>
                                <th class="w-1">Remessa</th>
                                <th class="w-70px">Info</th>
                                <th>Estado</th>
                                <th class="w-60px">Faturado</th>
                                <th class="w-60px">Custo</th>
                                <th class="w-60px">Ganho</th>
                                <th class="w-1">
                                    <span data-toggle="tooltip" title="Envio Conferido"><i class="fas fa-check-circle"></i></span>
                                </th>
                                <th class="w-1">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    <div>
                        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal-confirm-selected-shipments">
                            <i class="fas fa-check-circle"></i> Marcar/Desmarcar Conferido
                        </button>
                        @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'billing'))
                            <div class="btn-group btn-group-sm dropup m-l-5">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Alterar... <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'billing'))
                                            <a href="#" data-toggle="modal" data-target="#modal-assign-customer">
                                                <i class="fas fa-user-plus"></i> Associar envios a outro Cliente
                                            </a>
                                        @endif
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="modal" data-target="#modal-mass-update">
                                            <i class="fas fa-fw fa-pencil-alt"></i> Editar/Corrigir Envios Selecionados
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <a href="{{ route('admin.export.billing.providers.shipments', [$provider->id, 'month' => $month, 'year' => $year]) }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
                                <i class="fas fa-fw fa-file-excel"></i> Exportar
                            </a>
                            @include('admin.billing.customers.modals.mass_update')
                            @include('admin.shipments.shipments.modals.assign.customer')
                        @endif
                        @include('admin.billing.providers.modals.mass_confirm')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <style>
        .brdlft {
            border-left: 2px solid #333 !important;
        }
    </style>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable;

    $(document).ready(function () {

        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'tracking_code', name: 'tracking_code'},
                {data: 'sender_name', name: 'sender_name'},
                {data: 'recipient_name', name: 'recipient_name'},
                {data: 'service_id', name: 'service_id', searchable: false},
                {data: 'volumes', name: 'volumes'},
                {data: 'date', name: 'date'},
                {data: 'status_id', name: 'status_id', class: 'text-center', searchable: false},
                {data: 'total_price', name: 'total_price', class: 'brdlft text-center'},
                {data: 'cost_price', name: 'cost_price', class: 'text-center'},
                {data: 'profit', name: 'profit', class: 'text-center', searchable: false, orderable: false},
                {data: 'provider_conferred', name: 'provider_conferred', class: 'text-center', searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
                {data: 'recipient_city', name: 'recipient_city', visible: false},
                {data: 'recipient_phone', name: 'recipient_phone', visible: false},
                {data: 'provider_tracking_code', name: 'provider_tracking_code', visible: false},
                {data: 'reference', name: 'reference', visible: false},
            ],
            ajax: {
                url: "{{ route('admin.billing.providers.shipments.datatable', $provider->id) }}",
                type: "POST",
                data: function (d) {
                    d.month = "{{ $month }}";
                    d.year  = "{{ $year }}";
                    d.conferred= $('select[name=conferred]').val();
                    d.status   = $('select[name=status]').val();
                    d.service  = $('select[name=service]').val();
                    d.charge   = $('select[name=charge]').val();
                    d.operator = $('select[name=operator]').val();
                    d.customer = $('select[name=customer]').val();
                    d.date_min = $('input[name=date_min]').val();
                    d.date_max = $('input[name=date_max]').val();
                    d.payment_recipient = $('select[name=payment_recipient]').val();
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
    });

    $(document).on('click', '.btn-confirm', function(){
        var $parent = $(this).parent();
        var id = $parent.data('id');
        var lastHtml = $parent.html();

        $parent.html('<i class="fas fa-spin fa-circle-notch"></i>');

        $.post("{{ route('admin.billing.providers.shipments.confirm') }}", {ids:id}, function(data){
            if(data.result) {
                Growl.success(data.feedback)
                $parent.replaceWith(data.html);
            } else {
                Growl.error(data.feedback)
                $parent.html(lastHtml);
            }
        }).fail(function() {
            Growl.error500()
            $parent.html(lastHtml);
        })
    })

    //select shipments
    $(document).on('ifChanged', '.row-select',function(){
            var queryString = '';
            $('input[name=row-select]:checked').each(function(i, selected){
                queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
            });

            var tab = $(this).closest('table').attr('id');
            tab = tab.replace('datatable-', '');

            var targetUrl = Url.removeQueryString($('[data-url-target="billing-provider-selected"]').attr('href'));
            $('[data-url-target="billing-provider-selected"]').attr('href', targetUrl + '?' + queryString+ '&month={{ $month }}&year={{ $year }}&tab=' + tab);
    });

    $("select[name=assign_customer_id], select[name=customer]").select2(Init.select2Ajax("{{ route('admin.shipments.search.customer') }}"));
</script>
@stop