@section('title')
    Serviços Expresso
@stop

@section('content-header')
    Serviços Expresso
@stop

@section('breadcrumb')
    <li class="active">Serviços Expresso</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.express-services.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
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
                        <div class="input-group input-group-sm w-200px">
                            {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início']) }}
                            <span class="input-group-addon">até</span>
                            {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim']) }}
                        </div>
                    </li>
                    <li style="margin-bottom: -14px;">
                        <strong class="w-10 pull-left p-t-5 p-r-5">Cliente</strong>
                        <div class="input-group">
                            <div class="w-200px pull-left">
                            {{ Form::select('customer', array('' => 'Todos'), Request::has('customer') ? Request::get('customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable')) }}
                            </div>
                            <span class="input-group-btn">
                                <button class="btn btn-default clean-select" type="button"><i class="fas fa-times"></i></button>
                            </span>
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        @if(count($agencies) > 1)
                            <li>
                                <strong>Agência</strong><br/>
                                {{ Form::select('agency', ['' => 'Todos'] + $agencies, Request::has('agency') ? Request::get('agency') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                            </li>
                        @endif
                        <li>
                            <strong>Operador</strong>
                            {{ Form::select('operator', array('' => 'Todos') + $operators, Request::has('operator') ? Request::get('operator') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>Estado</strong><br/>
                            {{ Form::select('status', array('' => 'Todos') + trans('admin/express_services.status'), Request::has('status') ? Request::get('status') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>Pago</strong><br/>
                            {{ Form::select('paid', array('' => 'Todos', '1' => 'Pago', '0' => 'Não Pago'), Request::has('paid') ? Request::get('paid') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>Faturado</strong><br/>
                            {{ Form::select('billed', array('' => 'Todos', '1' => 'Faturado', '0' => 'Não Faturado'), Request::has('billed') ? Request::get('billed') : '0', array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                    </ul>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-60px">Data</th>
                                <th>Cliente</th>
                                <th class="w-150px">Motorista</th>
                                <th class="w-70px">Km</th>
                                <th class="w-1">Valor</th>
                                <th class="w-1">Estado</th>
                                <th class="w-1">Fatura</th>
                                <th class="w-65px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    <p>Com os selecionados:</p>
                    {{ Form::open(array('route' => 'admin.express-services.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar</button>
                    {{ Form::close() }}
                    <a href="{{ route('admin.express-services.selected.billing') }}" class="btn btn-sm btn-default m-l-5 url-mass-billing" data-toggle="modal" data-target="#modal-remote-xl">
                        <i class="fas fa-file-alt"></i> Emitir Fatura
                    </a>
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
            stateSave: false,
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'date', name: 'date'},
                {data: 'title', name: 'title'},
                {data: 'operator.name', name: 'operator.name'},
                {data: 'km', name: 'km', searchable: false},
                {data: 'total_price', name: 'total_price', orderable: false, searchable: false},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'invoice_id', name: 'invoice_id'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},

            ],
            order: [[2, "desc"]],
            ajax: {
                url: "{{ route('admin.express-services.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.date_min = $('input[name=date_min]').val();
                    d.date_max = $('input[name=date_max]').val();
                    d.agency   = $('select[name=agency]').val()
                    d.customer = $('select[name=customer]').val();
                    d.operator = $('select[name=operator]').val();
                    d.status   = $('select[name=status]').val();
                    d.paid     = $('select[name=paid]').val();
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
                url: "{{ route('admin.express-services.search.customer') }}",
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


        //export selected
        $(document).on('change', '.row-select',function(){
            var queryString = '';
            $('input[name=row-select]:checked').each(function(i, selected){
                queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
            });

            var exportUrl = Url.removeQueryString($('.url-mass-billing').attr('href'));
            $('.url-mass-billing').attr('href', exportUrl + '?' + queryString);
        });
    });

</script>
@stop