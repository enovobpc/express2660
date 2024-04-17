@section('title')
    {{ trans('account/customers-support.title') }} -
@stop

@section('account-content')
    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
        <li>
            <a href="{{ route('account.customer-support.create') }}"
               class="btn btn-sm btn-black"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-plus"></i> Novo pedido
            </a>
        </li>
        <li>
            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> {{ trans('account/global.word.filter') }} <i class="fas fa-angle-down"></i>
            </button>
        </li>
    </ul>
    <div class="datatable-filters-extended m-t-0 hide {{ Request::has('filter') ? ' active' : null }}" data-target="#datatable">
        <ul class="list-inline pull-left">
            <li style="width: 230px" class="input-sm">
                <strong>{{ trans('account/global.word.shipment-date') }}</strong><br/>
                <div class="input-group input-group-sm">
                    {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable', 'placeholder' => 'De', 'style' => 'padding-right: 0; margin-right: -10px;']) }}
                    <span class="input-group-addon">{{ trans('account/global.word.to') }}</span>
                    {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable']) }}
                </div>
            </li>
            <li style="width: 130px" class="input-sm">
                <strong>{{ trans('account/global.word.category') }}</strong><br/>
                {{ Form::select('category', ['' => trans('account/global.word.all')] + trans('admin/customers_support.categories') , Request::has('category') ? Request::get('category') : '', array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li style="width: 160px" class="input-sm">
                <strong>{{ trans('account/global.word.status') }}</strong><br/>
                {{ Form::select('status', ['' => trans('account/global.word.all')] + trans('admin/customers_support.status'), Request::has('status') ? Request::get('status') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="table-responsive w-100">
        <table id="datatable" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th class="w-1" style="padding-right: 0;">{{ Form::checkbox('select-all', '') }}</th>
                    <th class="w-60px">{{ trans('account/global.word.request') }}</th>
                    <th>{{ trans('account/global.word.subject') }}</th>
                    <th class="w-70px">{{ trans('account/global.word.shipment') }}</th>
                    <th class="w-70px">{{ trans('account/global.word.date') }}</th>
                    <th class="w-70px">{{ trans('account/global.word.status') }}</th>
                    <th class="w-30px"></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="selected-rows-action hide">
        <div>
            {{--{{ Form::open(array('route' => 'account.customer-support.selected.destroy')) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-confirm-title="{{ trans('account/global.word.destroy-selected') }}">
                <i class="fas fa-trash-alt"></i> {{ trans('account/global.word.destroy-selected') }}
            </button>
            {{ Form::close() }}--}}
        </div>
    </div>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'code', name: 'code'},
                {data: 'subject', name: 'subject'},
                {data: 'shipment', name: 'shipment', searchable: false, 'class': 'text-center'},
                {data: 'created_at', name: 'created_at'},
                {data: 'status', name: 'status'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false}
            ],
            order: [[2, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.customer-support.datatable') }}",
                data: function (d) {
                    d.date_min  = $('input[name=date_min]').val();
                    d.date_max  = $('input[name=date_max]').val();
                    d.category  = $('select[name=category]').val();
                    d.status    = $('select[name=status]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            },
        });


        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
/*
            var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);*/
        });
    });
</script>
@stop