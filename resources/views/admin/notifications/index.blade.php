@section('title')
    Notificações
@stop

@section('content-header')
    Notificações
@stop

@section('breadcrumb')
<li class="active">Notificações</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.notifications.all-read') }}" class="btn btn-sm btn-default btn-read-all-notifications">Marcar tudo como lido</a>
                    </li>
                    <li>
                        @if(@$totalNotifications)
                            <h4 class="m-0 text-yellow total-notifications"><div class="badge bg-yellow" style="margin-top: -3px;">{{ @$totalNotifications }}</div> Notificações por ler</h4>
                        @endif
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-60px">Data</th>
                                <th class="w-1"></th>
                                <th>Notificação</th>
                                <th class="w-20px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.notifications.selected.destroy')) }}
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
                {data: 'alert_at', name: 'alert_at'},
                {data: 'icon', name: 'icon', orderable: false, searchable: false},
                {data: 'message', name: 'message'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[3, "desc"]],
            ajax: {
                type: "POST",
                url:  "{{ route('admin.notifications.datatable') }}",
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
    
    $(document).on('click', '.btn-read-all-notifications', function(e){
        e.preventDefault();
        $('.total-notifications').hide()
        $('.notifications-menu .footer a').trigger('click');
        oTable.draw();
    });
</script>
@stop