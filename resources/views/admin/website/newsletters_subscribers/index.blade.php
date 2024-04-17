@section('title')
    Subscritores Newsletter
@stop

@section('content-header')
    Subscritores Newsletter
@stop

@section('breadcrumb')
<li class="active">Subscritores Newsletter</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable"> 
                    <li>
                        <a href="{{ route('admin.website.newsletters.subscribers.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.website.newsletters.subscribers.mail.list') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
                            <i class="fas fa-envelope"></i> Lista de E-mails
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.website.newsletters.subscribers.mail.csv') }}" class="btn btn-default btn-sm" target="_blank">
                            <i class="fas fa-file-excel"></i> Download CSV
                        </a>
                    </li>
                    <li class="fltr-primary w-150px">
                        <strong>Estado</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-80px">
                            {{ Form::select('status', ['' => 'Todos', '1' => 'Ativo', '0' => 'Inativo'], fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th>E-mail</th>
                                <th class="w-1">Ativo</th>
                                <th class="w-70px">Criado em</th>
                                <th class="w-80px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.website.newsletters.subscribers.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fa fa-trash"></i> Apagar Selecionados</button>
                    {{ Form::close() }}

                    <a href="{{ route('admin.website.newsletters.subscribers.mail.list') }}" class="btn btn-sm btn-default m-l-5" data-action-url="datatable-action-url" data-toggle="modal" data-target="#modal-remote">
                        <i class="fa fa-envelope"></i> Lista E-mails
                    </a>
                    <a href="{{ route('admin.website.newsletters.subscribers.mail.csv') }}" class="btn btn-sm btn-default m-l-5" data-action-url="datatable-action-url" target="_blank">
                        <i class="fa fa-file-excel-o"></i> Download CSV
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
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'email', name: 'email'},
                {data: 'active', name: 'active'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[4, "desc"]],
            ajax: {
                url: "{{ route('admin.website.newsletters.subscribers.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.status = $('select[name=status]').val();
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
    });

</script>
@stop