@section('title')
    Controlo Logístico -
@stop

@section('account-content')
    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
        @if(config('app.source') == 'activos24')
        <li>
            <button class="btn btn-default btn-sm" disabled="">
                <i class="fas fa-upload"></i> Importar
            </button>
        </li>
        @endif
        {{--<li>
            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> {{ trans('account/global.word.filter') }} <span class="caret"></span>
            </button>
        </li>--}}
    </ul>
    {{--<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : '' }}" data-target="#datatable">
        <ul class="list-inline pull-left">
            <li style="width: 120px" class="input-sm">
                <strong>{{ trans('account/global.word.category') }}</strong><br/>
                {{ Form::select('category', ['' => 'Todas'] + $categories, Request::has('category') ? Request::get('category') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li style="width: 100px" class="input-sm">
                <strong>{{ trans('account/global.word.subcategory') }}</strong><br/>
                {{ Form::select('subcategory', ['' => 'Todas'] + $subcategories, Request::has('subcategory') ? Request::get('subcategory') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li class="input-sm" style="width: 100px">
                <strong>{{ trans('account/global.word.family') }}</strong><br/>
                {{ Form::select('family', ['' => 'Todas'] + $families, Request::has('family') ? Request::get('family') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li class="input-sm" style="width: 100px">
                <strong>{{ trans('account/global.word.brand') }}</strong><br/>
                {{ Form::select('brand', ['' => 'Todas'] + $brands, Request::has('brand') ? Request::get('brand') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li class="input-sm" style="width: 100px">
                <strong>{{ trans('account/global.word.model') }}</strong><br/>
                {{ Form::select('model', ['' => 'Todas'] + $models, Request::has('model') ? Request::get('model') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li class="input-sm">
                <strong>{{ trans('account/global.word.stock') }}</strong><br/>
                {{ Form::select('stock', [''=>'Tudo', '0' => 'Sem Stock', '1' => 'Com Stock', '2' => 'Stock Reduzido'], Request::has('stock') ? Request::get('stock') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>--}}
    <div class="table-responsive w-100">
        <table id="datatable" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th class="w-100px">Processo</th>
                    <th>Documento</th>
                    <th class="w-120px">Nº Envio</th>
                    <th class="w-95px">Data Envio</th>
                    <th class="w-70px">Estado</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'code', name: 'code'},
                {data: 'document', name: 'document'},
                {data: 'shipment_id', name: 'shipment_id'},
                {data: 'date', name: 'date'},
                {data: 'status_id', name: 'status_id'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[4, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.logistic.shipping-orders.datatable') }}",
                data: function (d) {
                    /*d.category    = $('select[name=category]').val();
                    d.subcategory = $('select[name=subcategory]').val();
                    d.family      = $('select[name=family]').val();
                    d.brand       = $('select[name=brand]').val();
                    d.model       = $('select[name=model]').val();
                    d.stock       = $('select[name=stock]').val();*/
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            },
        });


        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();

            /*var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);*/
        });
    });
</script>
@stop