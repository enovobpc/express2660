@section('title')
    Controlo Logístico -
@stop

@section('account-content')
    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
        <li>
            <button class="btn btn-black btn-sm" disabled>
                <i class="fas fa-plus"></i> Novo Produto
            </button>
        </li>
        <li>
            <button class="btn btn-default btn-sm" disabled>
                <i class="fas fa-upload"></i> Importar
            </button>
        </li>
        <li>
            <a href="{{ route('account.logistic.products.export') }}"
               class="btn btn-sm btn-default"
               data-toggle="export-url">
                <i class="fas fa-file-excel"></i> {{ trans('account/global.word.export-list') }}
            </a>
        </li>
        <li>
            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> {{ trans('account/global.word.filter') }} <span class="caret"></span>
            </button>
        </li>
    </ul>
    <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : '' }}" data-target="#datatable">
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
                {{ Form::select('stock', [''=>'Tudo', '0' => 'Sem Stock', '1' => 'Com Stock', '2' => 'Stock Reduzido', '3' => 'Stock Bloqueado'], Request::has('stock') ? Request::get('stock') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="table-responsive w-100">
        <table id="datatable" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th class="w-50px"></th>
                    <th class="w-100px">SKU</th>
                    <th>Designação</th>
                    <th class="w-180px">Lote/Série</th>
                    <th class="w-70px">Validade</th>
                    <th class="w-50px">Stock</th>
                    <th class="w-50px">Un.</th>
                    @if (config('app.source') == 'activos24')
                    <th class="w-50px">Subcat.</th>
                    @endif
                    @if (config('app.source') == 'corridadotempo')
                    <th class="w-150px">Localização</th>
                    @endif
                    <th class="w-1">Estado</th>
                    {{--<th class="w-1"></th>--}}
                    @if(config('app.source') == 'activos24')
                    <th class="w-1"></th>
                    @endif
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

@stop

@section('styles')
    {{ Html::style('vendor/magnific-popup/dist/magnific-popup.css') }}
@stop

@section('scripts')
{{ Html::script('vendor/magnific-popup/dist/jquery.magnific-popup.js') }}
<script type="text/javascript">
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'image', name: 'image', orderable: false, searchable: false},
                {data: 'sku', name: 'sku'},
                {data: 'name', name: 'name'},
                {data: 'lote', name: 'lote'},
                {data: 'expiration_date', name: 'expiration_date'},
                {data: 'stock_total', name: 'stock_total'},
                {data: 'unity', name: 'unity'},
                @if (config('app.source') == 'activos24')
                {data: 'subcategory', name: 'subcategory', class:'text-center', searchable: false},
                @endif
                @if (config('app.source') == 'corridadotempo')
                {data: 'location', name: 'location', class:'text-center', orderable: false, searchable: false},
                @endif
                {data: 'stock_status', name: 'stock_status', class:'text-center'},
                @if(config('app.source') == 'activos24')
                {data: 'cart', name: 'cart', class:'text-center', orderable: false, searchable: false},
                @endif
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'serial_no', name: 'serial_no', visible: false},
                {data: 'customer_ref', name: 'customer_ref', visible: false},
            ],
            order: [[4, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.logistic.products.datatable') }}",
                data: function (d) {
                    d.category    = $('select[name=category]').val();
                    d.subcategory = $('select[name=subcategory]').val();
                    d.family      = $('select[name=family]').val();
                    d.brand       = $('select[name=brand]').val();
                    d.model       = $('select[name=model]').val();
                    d.stock       = $('select[name=stock]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () {
                    Datatables.complete();
                    $('.preview-img').magnificPopup({
                        type:'image'
                    });
                }
            },
        });


        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();

            /*var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);*/
        });

        $(document).on('click', '.btn-add-cart', function(e){
            e.preventDefault();

            var $this = $(this);
            var url   = $this.attr('href');
            var savesTxt = $this.html();

            $this.html('<i class="fas fa-spin fa-circle-notch"></i>')
            $.post(url, function(data){
                if(data.result) {
                    $('.cart-logistic-total').html(data.cart_total)
                    Growl.success(data.feedback);
                } else {
                    Growl.error(data.feedback);
                }

            }).fail(function(){
                Growl.error500()
            }).always(function(){
                $this.html(savesTxt)
            })
        })
    });
</script>
@stop