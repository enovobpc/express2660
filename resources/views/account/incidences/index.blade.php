@section('title')
    {{ trans('account/global.menu.incidences') }} -
@stop

@section('account-content')
    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
        {{--<li>
            <a href="{{ route('account.customer-support.create') }}"
               class="btn btn-sm btn-black"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-plus"></i> Novo pedido
            </a>
        </li>--}}
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
            <li style="width: 100px" class="input-sm">
                <strong>{{ trans('account/global.word.resolved') }}</strong><br/>
                {{ Form::select('resolved', [''=>trans('account/global.word.all'),'1' => trans('account/global.word.yes'), '0' => trans('account/global.word.no')], Request::has('resolved') ? Request::get('resolved') : 0, array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li style="width: 100px" class="input-sm">
                <strong>{{ trans('account/global.word.solution') }}</strong><br/>
                {{ Form::select('solution', [''=>trans('account/global.word.all'),'1' => trans('account/global.word.yes'), '0' => trans('account/global.word.no')], Request::has('solution') ? Request::get('solution') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li style="width: 160px" class="input-sm">
                <strong>{{ trans('account/global.word.service') }}</strong><br/>
                {{ Form::select('service', ['' => trans('account/global.word.all')] + $services, Request::has('service') ? Request::get('service') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li style="width: 160px" class="input-sm">
                <strong>{{ trans('account/global.word.incidence') }}</strong><br/>
                {{ Form::select('incidence', ['' => trans('account/global.word.all')] + $incidences, Request::has('incidence') ? Request::get('incidence') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="table-responsive w-100">
        <table id="datatable" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th class="w-90px">TRK</th>
                    <th>{{ trans('account/global.word.recipient') }}</th>
                    <th class="w-230px">{{ trans('account/global.word.incidence') }}</th>
                    <th class="w-230px">{{ trans('account/global.word.last-solution') }}</th>
                    <th class="w-1"><i class="fas fa-check-circle" data-toggle="tooltip" title="Resolvido?"></i></th>
                    <th class="w-65px">{{ trans('account/global.word.actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="selected-rows-action hide">
    <div>
        {{--{{ Form::open(array('route' => 'account.recipients.selected.destroy')) }}
        <button class="btn btn-sm btn-danger" data-action="confirm" data-confirm-title="{{ trans('account/global.word.destroy-selected') }}">
            <i class="fas fa-trash-alt"></i> {{ trans('account/global.word.destroy-selected') }}
        </button>
        {{ Form::close() }}--}}
        <a href="{{ route('account.export.incidences') }}" class="btn btn-sm btn-default" data-toggle="export-selected">
            <i class="fas fa-fw fa-file-excel"></i> Exportar
        </a>
    </div>
        <style>
            .brdr-lft {
                border-left: 2px solid #333;
            }
        </style>
@stop

@section('scripts')
<script type="text/javascript">
    var oTableIncidences;

    $(document).ready(function () {
        oTableIncidences = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id'},
                {data: 'recipient_name', name: 'shipments.recipient_name'},

                {data: 'reason', name: 'reason', orderable: false, searchable: false, class:'brdr-lft'},
                {data: 'solution', name: 'solution', orderable: false, searchable: false},
                {data: 'resolved', name: 'resolved', class:'text-center', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'tracking_code', name: 'shipments.tracking_code', visible: false},
            ],
            order: [[1, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.incidences.datatable') }}",
                data: function (d) {
                    d.resolved      = $('select[name=resolved]').val();
                    d.solution      = $('select[name=solution]').val();
                    d.zone          = $('select[name=zone]').val();
                    d.status        = $('select[name=status]').val();
                    d.source        = $('select[name=source]').val();
                    d.service       = $('select[name=service]').val();
                    d.provider      = $('select[name=provider]').val();
                    d.incidence     = $('select[name=incidence]').val();
                    d.date_min      = $('input[name=date_min]').val();
                    d.date_max      = $('input[name=date_max]').val();
                    d.date_unity    = $('select[name=date_unity]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableIncidences) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            },
        });


        $('.filter-datatable').on('change', function (e) {
            oTableIncidences.draw();
            e.preventDefault();

            var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);
        });
    });

    //export selected
    $(document).on('change', '.row-select',function(){
        var queryString = '';
        $('input[name=row-select]:checked').each(function(i, selected){
            queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
        });

        var exportUrl = Url.removeQueryString($('[data-toggle="export-selected"]').attr('href'));
        $('[data-toggle="export-selected"]').attr('href', exportUrl + '?' + queryString);
    });
</script>
@stop