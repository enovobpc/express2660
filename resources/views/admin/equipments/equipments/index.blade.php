@section('title')
    Equipamentos
@stop

@section('content-header')
    Equipamentos
@stop

@section('breadcrumb')
    <li class="active">Equipamentos</li>
    <li class="active">Artigos e Stocks</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-equipments" data-toggle="tab">Equipamentos</a></li>
                <li><a href="#tab-locations" data-toggle="tab">Equipamentos por Localização</a></li>
                <li><a href="#tab-stats" data-toggle="tab">Resumo Geral</a></li>
                <li><a href="#tab-picking" data-toggle="tab"><i class="fas fa-barcode"></i> Picking</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-equipments">
                    @include('admin.equipments.equipments.tabs.equipments_list')
                </div>
                <div class="tab-pane" id="tab-locations">
                    @include('admin.equipments.equipments.tabs.locations_list')
                </div>
                <div class="tab-pane" id="tab-picking">
                    @include('admin.equipments.equipments.tabs.picking')
                </div>
                <div class="tab-pane" id="tab-stats">
                    @include('admin.equipments.equipments.tabs.stats')
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.equipments.equipments.modals.conference')
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
                {data: 'serial_no', name: 'serial_no'},
                {data: 'category_id', name: 'category_id'},
                {data: 'stock_total', name: 'stock_total'},
                {data: 'location_id', name: 'location_id', searchable: false, orderable: false},
                {data: 'status', name: 'status', class:'text-center'},
                {data: 'ot_code', name: 'ot_code'},
                {data: 'last_update', name: 'last_update'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'serial_no', name: 'serial_no', visible: false},
            ],
            ajax: {
                url: "{{ route('admin.equipments.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.unity       = $('#tab-equipments select[name=unity]').val();
                    d.location    = $('#tab-equipments select[name=location]').val();
                    d.customer    = $('#tab-equipments select[name=dt_customer]').val();
                    d.date_min    = $('#tab-equipments input[name=date_min]').val();
                    d.date_max    = $('#tab-equipments input[name=date_max]').val();
                    d.date_unity  = $('#tab-equipments select[name=date_unity]').val();
                    d.status      = $('#tab-equipments select[name=status]').val();
                    d.images      = $('#tab-equipments select[name=images]').val();
                    d.category    = $('#tab-equipments select[name=category]').val();
                    d.type_file   = $('#tab-equipments select[name=type_file]').val();
                    d.deleted     = $('#tab-equipments input[name=deleted]:checked').length;
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () {
                    Datatables.complete();
                    $('.preview-img').magnificPopup({
                        type:'image'
                    });
                },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-equipments .filter-datatable').on('change', function (e) {
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
                {data: 'warehouse_id', name: 'warehouse_id'},
                {data: 'name', name: 'name'},
                {data: 'equipments', name: 'equipments', searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.equipments.datatable.locations') }}",
                type: "POST",
                data: function (d) {
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableLocations) },
                complete: function () {
                    Datatables.complete();
                },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-locations .filter-datatable').on('change', function (e) {
            oTableLocations.draw();
            e.preventDefault();
        });

    });

     $("select[name=dt_customer], select[name=customer_id]").select2({
         minimumInputLength: 2,
         allowClear: true,
         ajax: Init.select2Ajax("{{ route('admin.equipments.search.customer') }}")
     });


    // $(document).on('change', '#tab-picking select[name=location_id]', function() {
    //     if($('#tab-picking select[name=location_id]').val() == '') {
    //         $('#tab-picking [name=sku], #tab-picking [name=ot_code]').val('').prop('disabled', true);
    //     } else {
    //         $('#tab-picking [name=sku], #tab-picking [name=ot_code]').val('').prop('disabled', false);
    //     }
    // });

    // $(document).on('change', '#tab-picking select[name=action]', function() {

    //     $('#tab-picking select[name=location_id]').val('').prop('disabled', false).trigger('change');

    //     if($(this).val() == '') {
    //         $('.reception-fields').hide();
    //         $('#tab-picking select[name=customer_id],#tab-picking select[name=name],#tab-picking select[name=category_id]').val('').trigger('change');
    //         $('#tab-picking select[name=location_id],#tab-picking [name=sku]').val('').prop('disabled', true);
    //     } else {

    //         $('.location-field').show();
    //         $('#tab-picking select[name=location_id]').val('').prop('disabled', false);

    //         if ($(this).val() == 'reception') {
    //             $('.reception-fields').show();
    //         } else if ($(this).val() == 'out') {
    //             $('.reception-fields, .location-field').hide();
    //             $('#tab-picking select[name=location_id]').val('').trigger('change');
    //             $('#tab-picking [name=sku]').val('').prop('disabled', false);
    //         } else if ($(this).val() != 'out') {
    //             $('.reception-fields').hide();
    //             $('#tab-picking [name=sku]').val('').prop('disabled', false);
    //             $('#tab-picking select[name=customer_id],#tab-picking select[name=name],#tab-picking select[name=category_id]').val('').trigger('change');
    //         } else {
    //             $('.reception-fields').hide();
    //             $('#tab-picking select[name=customer_id],#tab-picking select[name=name],#tab-picking select[name=category_id]').val('').trigger('change');
    //         }
    //     }
    // })

    // $(document).on('change', '#tab-picking select[name=customer_id]', function() {
    //     if($(this).val() != '') {
    //         $('#tab-picking [name="sku"]').prop('disabled', false);
    //     } else {
    //         $('#tab-picking [name="sku"]').prop('disabled', true).val('');
    //     }
    // })

    $(document).on('change', '#tab-picking select[name=action]', function() {

        $('#tab-picking select[name=location_id]').val('').prop('disabled', false).trigger('change');

        if($(this).val() == '') {

        }else if($(this).val() == 'reception') {
            $('.reception-fields, .location-field').show();
            $('.code-ot').hide();
            $('#tab-picking select[name=location_id]').val('').trigger('change');
            // $('#tab-picking [name=ot_code]').val('').prop('disabled', true);
            $('#tab-picking [name=sku]').val('').prop('disabled', false);
        
        } else if($(this).val() == 'out'){
            $('.reception-fields, .location-field').hide();
            $('.code-ot').show();
            $('#tab-picking [name=sku]').val('').prop('disabled', false);
            $('#tab-picking [name=ot_code]').val('').prop('disabled', false);
        
        }else{
            $('.reception-fields').hide();
            $('.location-field .code-ot').show();
            $('#tab-picking [name=sku]').val('').prop('disabled', false);
            $('#tab-picking [name=ot_code]').val('').prop('disabled', false);
        }
    })

    $('#tab-picking [name="sku"]').on('keyup', function (e) {
        e.preventDefault();
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            selectCode($(this).val())
        }
    });

    function selectCode(trkCode) {

        $('[name=read_point]').prop('disabled', true);

        fullTrkCode = trkCode;
        fullTrkCode = fullTrkCode.trim();

        var exists = false;
        var urlPrint   = "{{ route('admin.equipments.printer.inventory') }}"
        var urlExport  = "{{ route('admin.equipments.export') }}"
        var action     = $('#tab-picking [name=action]').val();
        var customerId = $('#tab-picking [name=customer_id]').val();
        var locationId = $('#tab-picking [name=location_id]').val();
        var categoryId = $('#tab-picking [name=category_id]').val();
        var name       = $('#tab-picking [name=name]').val();
        var otCode     = $('#tab-picking [name=ot_code]').val();
        //var autocreate = $('[name=autocreate]').is(':checked') ? true : false;

        if (fullTrkCode != '') {

            trkCode = trkCode.trim();

            $('.selected-codes tr').each(function () {
                if ($(this).data('id') == trkCode || $(this).data('readed-code') == trkCode) {
                    exists = true;
                }
            })

            $('.wellcome-image').hide();
            $('.scanner-result-block').show();

            $.post("{{ route('admin.equipments.picking.store') }}", {
                'code': fullTrkCode,
                'action': action,
                'customer_id': customerId,
                'location_id': locationId,
                'category_id': categoryId,
                'ot_code': otCode,
                'name': name,
                //'autocreate': autocreate
            }, function (data) {

                if(!data.result) {
                    Growl.error(data.feedback)
                } else {
                    if(data.html) {
                        if($(document).find('.selected-codes tr[data-id="'+data.id+'"]').length > 0) {
                            $(document).find('.selected-codes tr[data-id="'+data.id+'"]').replaceWith(data.html)
                        } else {
                            $(document).find('.selected-codes').append(data.html)
                        }
                    }

                    queryStr = '';
                    $(document).find('.selected-codes tbody tr').each(function(){
                        queryStr+= 'id[]=' + $(this).data('id') + '&';
                    });

                    urlPrint = Url.removeQueryString(urlPrint);
                    urlPrint = urlPrint+'?'+queryStr.slice(0, -1);

                    urlExport = Url.removeQueryString(urlExport);
                    urlExport = urlExport+'?'+queryStr.slice(0, -1);

                    $('.scanner-result-block .print-summary').attr('href', urlPrint)
                    $('.scanner-result-block .export-summary').attr('href', urlExport)
                }
            }).fail(function () {
                Growl.error500()
            })
        }
        $('[name=sku]').val('');
    }
</script>
@stop