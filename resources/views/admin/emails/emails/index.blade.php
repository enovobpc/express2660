@section('title')
    Histórico de Envio E-mails
@stop

@section('content-header')
    Histórico de Envio E-mails
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Histórico de Envio E-mails</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.emails.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
                            <i class="fas fa-plus"></i> Novo e-mail
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.emails.lists.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-list"></i> Gerir Listas de E-mail
                        </a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th>Assunto</th>
                                <th class="w-200px">De</th>
                                <th>Para</th>
                                <th class="w-75px">Enviado em</th>
                                <th class="w-150px">Dispultado por</th>
                                <th class="w-1">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.emails.selected.destroy')) }}
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
                {data: 'subject', name: 'subject'},
                {data: 'from', name: 'from'},
                {data: 'to', name: 'to'},
                {data: 'sended_at', name: 'sended_at'},
                {data: 'sended_by', name: 'sended_by'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.emails.datatable') }}",
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