@section('title')
    Faturação a Terceiros
@stop

@section('content-header')
    Faturação a Terceiros
@stop

@section('breadcrumb')
    <li class="active">Faturação a Terceiros</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
    <div class="box no-border">
        <div class="box-body">
            <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                <li class="fltr-primary w-110px">
                    <strong>Ano</strong><br class="visible-xs"/>
                    <div class="w-70px pull-left form-group-sm">
                        {{ Form::select('year', $years, Request::has('year') ? Request::get('year') : date('Y'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
                <li class="fltr-primary w-145px">
                    <strong><i class="fas fa-spin fa-circle-notch filter-loading" style="display: none"></i> Mês</strong><br class="visible-xs"/>
                    <div class="w-100px pull-left form-group-sm">
                        {{ Form::select('month', trans('datetime.list-month'), Request::has('month') ? Request::get('month') : date('m'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
            </ul>
            <div class="table-responsive">
                <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                    <thead>
                    <tr>
                        <th></th>
                        <th class="w-50px">Mês</th>
                        <th>Fornecedor</th>
                        <th class="w-1"><span data-toggle="tooltip" title="Envios">Envios</span></th>
                        <th class="w-1"><span data-toggle="tooltip" title="Recolhas">Recolhas</span></th>
                        <th class="w-70px">Faturado</th>
                        <th class="w-70px">A pagar</th>
                        <th class="w-100px">Ganhos</th>
                        <th class="w-1">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
                {data: 'id', name: 'id', visible: false},
                {data: 'month', name: 'month', class: 'text-center', searchable: false},
                {data: 'name', name: 'customers.name', orderable: false, searchable: false},
                {data: 'shipments', name: 'shipments', class: 'text-center', searchable: false},
                {data: 'collections', name: 'collections', class: 'text-center', searchable: false},
                {data: 'total', name: 'total', class: 'text-center', searchable: false},
                {data: 'cost', name: 'cost', class: 'text-center', searchable: false},
                {data: 'profit', name: 'profit', class: 'text-center', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false}
            ],
            ajax: {
                url: "{{ route('admin.billing.providers.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.month = $('select[name=month]').val();
                    d.year  = $('select[name=year]').val();
                    d.agency = $('select[name=agency]').val();
                    d.billed = $('select[name=billed]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });

        //enable option to search with enter press
        Datatables.searchOnEnter(oTable);
    });
</script>
@stop