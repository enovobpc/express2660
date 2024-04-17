@section('title')
    Arquivo de Ficheiros
@stop

@section('content-header')
    Arquivo de Ficheiros
@stop

@section('breadcrumb')
    <li class="active">@trans('Arquivo de Ficheiros')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-navigation" data-toggle="tab"><i class="fas fa-folder"></i> @trans('Navegação por pasta')</a></li>
                <li><a href="#tab-list" data-toggle="tab"><i class="fas fa-list"></i> @trans('Todos os ficheiros')</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-navigation">
                    @include('admin.repository.tabs.navigation_view')
                </div>
                <div class="tab-pane" id="tab-list">
                    @include('admin.repository.tabs.list_view')
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable, oTableList;
    $(document).ready(function () {
        oTable = $('#datatable-nav').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'target_type', name: 'target_type', orderable: false, searchable: false},
                {data: 'filesize', name: 'filesize'},
                {data: 'extension', name: 'extension'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[6, "desc"]],
            ajax: {
                url: "{{ route('admin.repository.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.folder     = "{{ Request::get('folder') }}"
                    d.type       = $('select[name=type]').val();
                    d.extension  = $('select[name=extension]').val();
                    d.date_min   = $('input[name=date_min]').val();
                    d.date_max   = $('input[name=date_max]').val();
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

        oTableList = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'target_type', name: 'target_type', orderable: false, searchable: false},
                {data: 'filesize', name: 'filesize'},
                {data: 'extension', name: 'extension'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[6, "desc"]],
            ajax: {
                url: "{{ route('admin.repository.datatable', ['mode' => 'list']) }}",
                type: "POST",
                data: function (d) {
                    d.type       = $('select[name=type]').val();
                    d.extension  = $('select[name=extension]').val();
                    d.date_min   = $('input[name=date_min]').val();
                    d.date_max   = $('input[name=date_max]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableList) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTableList.draw();
            e.preventDefault();
        });
    });
</script>
@stop