@section('title')
    Artigos e Stocks
@stop

@section('content-header')
    Artigos e Stocks
@stop

@section('breadcrumb')
    <li class="active">@trans('Gestão Logística')</li>
    <li class="active">@trans('Artigos e Stocks')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-products" data-toggle="tab">@trans('Vista por produto')</a></li>
                <li><a href="#tab-locations" data-toggle="tab">@trans('Vista por Localização')</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-products">
                    @include('admin.logistic.products.tabs.products_list')
                </div>
                <div class="tab-pane" id="tab-locations">
                    @include('admin.logistic.products.tabs.locations_list')
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.logistic.inventories.modals.map_print_stocks')
@include('admin.logistic.inventories.modals.map_export_stocks')
@stop


@section('styles')
    {{ Html::style('vendor/magnific-popup/dist/magnific-popup.css') }}
@stop

@section('scripts')
{{ Html::script('vendor/magnific-popup/dist/jquery.magnific-popup.js') }}
<script type="text/javascript">
    var oTable, oTableLocations;
    $(document).ready(function () {

        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'photo', name: 'photo', orderable: false, searchable: false},
                {data: 'sku', name: 'sku'},
                {data: 'name', name: 'name'},
                {data: 'lote', name: 'lote'},
                {data: 'expiration_date', name: 'expiration_date'},
                {data: 'stock_total', name: 'stock_total'},
                {data: 'pallets', name: 'pallets', class:'text-center', orderable: false, searchable: false},
                {data: 'price', name: 'price', class: 'text-center'},
                {data: 'locations', name: 'locations', searchable: false, orderable: false},
                {data: 'last_update', name: 'last_update'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'serial_no', name: 'serial_no', visible: false},
            ],
            ajax: {
                url: "{{ route('admin.logistic.products.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.unity       = $('#tab-products select[name=unity]').val();
                    d.warehouse   = $('#tab-products select[name=warehouse]').val();
                    d.location    = $('#tab-products select[name=location]').val();
                    d.customer    = $('#tab-products select[name=dt_customer]').val();
                    d.date_min    = $('#tab-products input[name=date_min]').val();
                    d.date_max    = $('#tab-products input[name=date_max]').val();
                    d.date_unity  = $('#tab-products select[name=date_unity]').val();
                    d.deleted     = $('#tab-products input[name=deleted]:checked').length;
                    d.status      = $('#tab-products select[name=status]').val();
                    d.images      = $('#tab-products select[name=images]').val();
                    d.lote        = $('#tab-products select[name=lote]').val();
                    d.serial_no   = $('#tab-products select[name=serial_no]').val();
                    d.brand       = $('#tab-products select[name=brand]').val();
                    d.model       = $('#tab-products select[name=model]').val();
                    d.family      = $('#tab-products select[name=family]').val();
                    d.category    = $('#tab-products select[name=category]').val();
                    d.subcategory = $('#tab-products select[name=subcategory]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-products .filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });


        /**
         * View by location
         * @type {*|jQuery}
         */
        oTableLocations = $('#datatable-locations').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'warehouse.name', name: 'warehouse.name'},
                {data: 'code', name: 'code'},
                {data: 'barcode', name: 'barcode'},
                {data: 'products', name: 'products', searchable: false},
                {data: 'stock', name: 'stock', orderable: false, searchable: false},
                {data: 'status', name: 'status', class: 'text-center'},
                /*{data: 'actions', name: 'actions', orderable: false, searchable: false},*/
            ],
            ajax: {
                url: "{{ route('admin.logistic.products.datatable.locations') }}",
                type: "POST",
                data: function (d) {
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableLocations) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTableLocations.draw();
            e.preventDefault();
        });
    });

    $("select[name=dt_customer], #modal-map-print-stocks select[name=customer], #modal-map-export-stocks select[name=customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.logistic.products.search.customer') }}")
    });

    $('.btn-sync').on('click', function (e) {
        e.preventDefault();

        var $syncIcon = $(this).find('.fa-sync-alt')
        $syncIcon.addClass('fa-spin');
        $.post($(this).attr('href'), function (data) {
            Growl.success(data.feedback);
        }).fail(function (e) {
            Growl.error500();
        }).always(function (e) {
            oTable.draw();
            $syncIcon.removeClass('fa-spin');
        })
    })

    $('.modal-filter-dates .btn-submit').on('click', function(e) {
        $(this).closest('form').submit();
        $(this).button('reset');
        $('.modal-filter-dates').modal('hide')
    })

    // Resets the modal button from "..." to "Print"
    $('#modal-map-print-stocks').on('submit', function () {
        $('#modal-map-print-stocks .btn-submit').prop('disabled', true);
    });

</script>
@stop