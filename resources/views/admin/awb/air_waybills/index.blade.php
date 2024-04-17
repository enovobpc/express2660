@section('title')
    Cartas de Porte Aéreo
@stop

@section('content-header')
    Cartas de Porte Aéreo
@stop

@section('breadcrumb')
<li class="active">Cartas de Porte Aéreo</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.air-waybills.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-xl">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    <li>
                        <strong>Data</strong>
                        <div class="input-group input-group-sm w-220px">
                            {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início']) }}
                            <span class="input-group-addon">até</span>
                            {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim']) }}
                        </div>
                    </li>
                    <li>
                        <strong>Tipo</strong>
                        {{ Form::select('type', ['' => 'Todos'] + $goodsTypes, Request::has('type') ? Request::get('type') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    @include('admin.awb.air_waybills.partials.filters')
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-80px">AWB</th>
                                <th class="w-120px">Designação</th>
                                <th>Expedidor</th>
                                <th>Consignatário</th>
                                <th class="w-90px">Aeroporto</th>
                                <th class="w-70px">Vôos</th>
                                <th class="w-40px">Carga</th>
                                <th class="w-40px">Total</th>
                                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'air-waybills-invoices'))
                                <th class="w-40px">Fatura</th>
                                @endif
                                <th class="w-1">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.air-waybills.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                    {{ Form::close() }}
                    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'air-waybills-invoices'))
                    <a href="{{ route('admin.air-waybills.selected.billing') }}" class="btn btn-sm btn-default m-l-5 url-mass-billing" data-toggle="modal" data-target="#modal-remote-xl">
                        <i class="fas fa-file-alt"></i> Emitir Fatura
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable;

    $(document).ready(function () {

        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'date', name: 'date'},
                {data: 'title', name: 'title'},
                {data: 'sender_name', name: 'sender_name'},
                {data: 'consignee_name', name: 'consignee_name'},
                {data: 'airport', name: 'airport', orderable: false, searchable: false},
                {data: 'flight_no', name: 'flight_no', searchable: false},
                {data: 'volumes', name: 'volumes'},
                {data: 'total_price', name: 'total_price'},
                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'air-waybills-invoices'))
                {data: 'invoice_id', name: 'invoice_id'},
                @endif
                {data: 'actions', name: 'actions', orderable: false, searchable: false},

                {data: 'source_airport', name: 'source_airport', visible: false},
                {data: 'recipient_airport', name: 'recipient_airport', visible: false},
                {data: 'awb_no', name: 'awb_no', visible: false},

            ],
            ajax: {
                url: "{{ route('admin.air-waybills.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.type  = $('select[name=type]').val()
                    d.provider  = $('select[name=provider]').val();
                    d.operator  = $('select[name=operator]').val();
                    d.customer  = $('select[name=customer]').val();
                    d.agent     = $('select[name=agent]').val();
                    d.source_airport    = $('select[name=source_airport]').val();
                    d.recipient_airport = $('select[name=recipient_airport]').val();
                    d.date_min  = $('input[name=date_min]').val();
                    d.date_max  = $('input[name=date_max]').val();
                    d.has_hawb  = $('select[name=has_hawb]').val();
                    d.billed   = $('select[name=billed]').val();
                },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });

        $("select[name=customer]").select2({
            ajax: {
                url: "{{ route('admin.shipments.search.customer') }}",
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

        $(".datatable-filters-extended select[name=source_airport], .datatable-filters-extended select[name=recipient_airport]").select2({
            ajax: {
                url: "{{ route('admin.air-waybills.search.airport') }}",
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
    });

    //export selected
    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'air-waybills-invoices'))
    $(document).on('change', '.row-select',function(){
        var queryString = '';
        $('input[name=row-select]:checked').each(function(i, selected){
            queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
        });

        var exportUrl = Url.removeQueryString($('.url-mass-billing').attr('href'));
        $('.url-mass-billing').attr('href', exportUrl + '?' + queryString);
    });
    @endif
</script>
@stop