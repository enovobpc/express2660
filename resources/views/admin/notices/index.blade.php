@section('title')
    Emitir Avisos e Notificações
@stop

@section('content-header')
    Emitir Avisos e Notificações
@stop

@section('breadcrumb')
<li class="active">Emitir Avisos e Notificações</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.notices.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-1"></th>
                                <th>Título</th>
                                <th class="w-1">Publicado</th>
                                <th class="w-60px">Data</th>
                                <th class="w-75px">Criado em</th>
                                <th class="w-20px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.notices.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar"><i class="fas fa-trash-alt"></i> Apagar</button>
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
                {data: 'photo', name: 'photo', orderable: false, searchable: false},
                {data: 'title', name: 'title'},
                {data: 'published', name: 'published', class: 'text-center', orderable: false, searchable: false},
                {data: 'date', name: 'date'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[6, "desc"]],
            ajax: {
                url: "{{ route('admin.notices.datatable') }}",
                type: "POST",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
</script>
@stop