@section('title')
    Transferências e Débitos Diretos
@stop

@section('content-header')
    Transferências e Débitos Diretos
@stop

@section('breadcrumb')
<li class="active">Transferências e Débitos Diretos</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.sepa-transfers.create') }}"
                           class="btn btn-success btn-sm"
                            data-toggle="modal"
                            data-target="#modal-remote-xs">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li class="fltr-primary w-170px">
                        <strong>Tipo</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-120px">
                            {{ Form::select('type', ['' => 'Todos'] + trans('admin/billing.sepa-types'), Request::has('type') ? Request::get('type') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-170px">
                        <strong>Estado</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-120px">
                            {{ Form::select('status', ['' => 'Todos'] + trans('admin/billing.sepa-status'), Request::has('status') ? Request::get('status') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th class="w-1">Nº</th>
                                <th class="w-200px">Descrição</th>
                                <th>Empresa</th>
                                <th class="w-200px">IBAN</th>
                                <th class="w-1">Transações</th>
                                <th class="w-65px">Estado</th>
                                <th class="w-65px">Criado em</th>
                                <th class="w-70px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.sepa-transfers.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-confirm-title="Apagar selecionados"><i class="fa fa-trash"></i> Apagar Selecionados</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@if(Request::get('action') && Request::get('id'))
    <a href="{{ route('admin.sepa-transfers.edit', Request::get('id')) }}" data-toggle="modal" data-target="#modal-remote-xl" class="modal-auto-open hide"></a>
@endif
@stop

@section('scripts')
<script type="text/javascript">


    $(document).ready(function () {
        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'company', name: 'company'},
                {data: 'bank_iban', name: 'bank_iban'},
                {data: 'transactions_total', name: 'transactions_total'},
                {data: 'status', name: 'status', class: 'text-center'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'bank_name', name: 'bank_name', visible: false},
            ],
            order: [[7, "desc"]],
            ajax: {
                url: "{{ route('admin.sepa-transfers.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.status = $('[name="status"]').val(),
                    d.type   = $('[name="type"]').val()
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete() }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();

            var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);
        });
    });

    @if(Request::get('action') && Request::get('id'))
        $(document).find('.modal-auto-open').trigger('click')
    @endif


</script>
@stop