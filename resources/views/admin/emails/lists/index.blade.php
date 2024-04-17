@section('title')
    Listas de E-mail
@stop

@section('content-header')
    Listas de E-mail
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Listas de E-mail</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.emails.lists.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.emails.lists.sort') }}"
                           class="btn btn-default btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote">
                            <i class="fas fa-sort-amount-down"></i> Ordenar
                        </a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th>Lista</th>
                                <th class="w-1">Emails</th>
                                <th class="w-1">Pos.</th>
                                <th class="w-1">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.emails.lists.selected.destroy')) }}
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
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'count', name: 'count', class:'text-center'},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.emails.lists.datatable') }}",
                type: "POST",

                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete() }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });

</script>
@stop