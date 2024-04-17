@section('title')
    {{ trans('account/ecommerce-gateway.title') }} -
@stop

@section('account-content')
    <ul class="datatable-filters list-inline hide pull-left m-0" data-target="#datatable">
        <li>
            <a class="btn btn-black btn-sm" data-toggle="modal" data-target="#modal-remote" href="{{ route('account.ecommerce-gateway.create') }}">
                <i class="fas fa-plus"></i> {{ trans('account/global.word.new') }}
            </a>
        </li>
        <li>
            <button class="btn btn-sm btn-filter-datatable btn-default" type="button">
                <i class="fas fa-filter"></i> {{ trans('account/global.word.filter') }} <span class="caret"></span>
            </button>
        </li>
    </ul>
    <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : '' }}" data-target="#datatable">
        <ul class="list-inline pull-left">

        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="table-responsive w-100">
        <table class="table table-condensed table-hover" id="datatable">
            <thead>
                <tr>
                    <th class="w-1" style="padding-right: 0;">{{ Form::checkbox('select-all', '') }}</th>
                    <th class="w-150px">{{ trans('account/global.word.name') }}</th>
                    <th class="w-150px">{{ trans('account/global.word.method') }}</th>
                    <th>Endpoint</th>
                    <th class="w-130px">{{ trans('account/global.word.created_at') }}</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="selected-rows-action hide">
        <div>

        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        var oTable;

        $(document).ready(function() {
            oTable = $('#datatable').DataTable({
                columns: [{
                        data: 'select',
                        name: 'select',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'method',
                        name: 'method'
                    },
                    {
                        data: 'endpoint',
                        name: 'endpoint'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                    },
                ],
                order: [
                    [4, 'asc']
                ],
                ajax: {
                    url: "{{ route('account.ecommerce-gateway.datatable') }}",
                    type: "POST",
                    data: function(d) {

                    },
                    beforeSend: function() {
                        Datatables.cancelDatatableRequest(oTable)
                    },
                    complete: function() {
                        Datatables.complete()
                    }
                }
            });

            $('.filter-datatable').on('change', function(e) {
                oTable.draw();
                e.preventDefault();

                var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('[data-toggle="export-url"]').attr('href', exportUrl);
            });
        });
    </script>
@stop
