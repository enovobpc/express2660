@section('title')
    {{ trans('account/recipients.title') }} -
@stop

@section('account-content')
    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
        <li>
            <a href="{{ route('account.recipients.create') }}"
               class="btn btn-sm btn-black"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-plus"></i> {{ trans('account/global.word.new') }}
            </a>
        </li>
        <li>
            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-upload"></i> {{ trans('account/recipients.word.import-btn') }} <i class="fas fa-angle-down"></i>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="#" data-toggle="modal" data-target="#modal-import-recipients">
                        <i class="fas fa-fw fa-file-excel"></i> {{ trans('account/recipients.word.import') }}
                    </a>
                </li>
                <li>
                    <a href="{{ coreUrl('/uploads/models/modelo_importacao_destinatarios.xlsx') }}">
                        <i class="fas fa-fw fa-download"></i> {{ trans('account/recipients.word.download') }}
                    </a>
                </li>
            </ul>
        </li>
    </ul>
    <div class="table-responsive w-100">
        <table id="datatable" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th class="w-1" style="padding-right: 0;">{{ Form::checkbox('select-all', '') }}</th>
                    <th class="w-60px">{{ trans('account/global.word.customer') }}</th>
                    <th>{{ trans('account/global.word.recipient-name') }}</th>
                    <th class="w-70px">{{ trans('account/global.word.phone') }}</th>
                    <th class="w-70px">{{ trans('account/global.word.mobile') }}</th>
                    <th class="w-1">{{ trans('account/global.word.shipments') }}</th>
                    <th class="w-30px"></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="selected-rows-action hide">
        <div>
            {{ Form::open(array('route' => 'account.recipients.selected.destroy')) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-confirm-title="{{ trans('account/global.word.destroy-selected') }}">
                <i class="fas fa-trash-alt"></i> {{ trans('account/global.word.destroy-selected') }}
            </button>
            {{ Form::close() }}
        </div>
    </div>
    @include('account.recipients.modals.import_recipients')
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'mobile', name: 'mobile'},
                {data: 'shipments', name: 'shipments', searchable: false, 'class': 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'address', name: 'address', visible: false},
                {data: 'zip_code', name: 'zip_code', visible: false},
                {data: 'city', name: 'city', visible: false},
                {data: 'email', name: 'email', visible: false},
            ],
            order: [[2, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.recipients.datatable') }}",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            },
        });


        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();

            var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);
        });
    });
</script>
@stop