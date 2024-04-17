@section('title')
    Mensagens a Clientes
@stop

@section('content-header')
    Mensagens a Clientes
@stop

@section('breadcrumb')
@section('breadcrumb')
    <li class="active">@trans('Entidades')</li>
    <li>
        <a href="{{ route('admin.customers.index') }}">
            @trans('Clientes')
        </a>
    </li>
    <li class="active">@trans('Mensagens a Clientes')</li>
@stop
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.customers.messages.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-65px">@trans('Data')</th>
                                <th>@trans('Mensagem')</th>
                                <th class="w-1">@trans('Todos')</th>
                                <th class="w-50px">@trans('E-mail')</th>
                                <th class="w-1">@trans('Fixo')</th>
                                <th class="w-1">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.customers.messages.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
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
                {data: 'created_at', name: 'created_at'},
                {data: 'subject', name: 'subject'},
                {data: 'send_all', name: 'send_all', class:'text-center'},
                {data: 'send_email', name: 'send_email', class:'text-center'},
                {data: 'is_static', name: 'is_static', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.customers.messages.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.customer  = $('select[name=customer]').val()
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