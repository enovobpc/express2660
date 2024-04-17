@section('title')
    Emitir Venda de Artigos
@stop

@section('content-header')
    Emitir Venda de Artigos
@stop

@section('breadcrumb')
    <li class="active">Faturação</li>
    <li class="active">Emitir Venda de Artigos</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.products.sales.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.items.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-plus"></i> Gerir Produtos
                        </a>
                    </li>
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    <li>
                        <strong>Data</strong>
                        <div class="input-group input-group-sm w-250px">
                            {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início']) }}
                            <span class="input-group-addon">até</span>
                            {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim']) }}
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li class="w-250px">
                            <strong>Produto</strong>
                            {{ Form::select('product', array('' => 'Todos'), Request::has('product') ? Request::get('product') : null, array('class' => 'form-control input-sm w-100 filter-datatable')) }}
                        </li>
                        <li class="w-250px">
                            <strong>Cliente</strong>
                            {{ Form::select('customer', array('' => 'Todos'), Request::has('customer') ? Request::get('customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable')) }}
                        </li>
                    </ul>
                </div>

                <div class="table-responsive">
                    <table id="datatable" class="table table-condensed table-striped table-dashed table-hover">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-65px">Data</th>
                                <th>Designação</th>
                                <th class="w-40px">Preço</th>
                                <th class="w-20px">Qtd</th>
                                <th class="w-40px">Subtotal</th>
                                <th class="w-50px">Total</th>
                                <th class="w-20px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.products.sales.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                    {{ Form::close() }}
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
                {data: 'products.name', name: 'products.name'},
                {data: 'price', name: 'price'},
                {data: 'qty', name: 'qty'},
                {data: 'subtotal', name: 'subtotal'},
                {data: 'vat_rate', name: 'vat_rate'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.products.sales.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.product = $('select[name=product]').val();
                    d.customer = $('select[name=customer]').val();
                    d.date_min = $('input[name=date_min]').val();
                    d.date_max = $('input[name=date_max]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete() }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });

    $("select[name=customer]").select2({
        ajax: {
            url: "{{ route('admin.products.sales.search.customer') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=customer] option').remove()
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $("select[name=product]").select2({
        ajax: {
            url: "{{ route('admin.products.sales.search.products') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=product] option').remove()
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

</script>
@stop