{{ Form::model($service, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="tabbable-line m-b-15">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab-info" data-toggle="tab">
                Dados Base
            </a>
        </li>
        <li>
            <a href="#tab-providers" data-toggle="tab">
                Config. Fornecedor
            </a>
        </li>
        <li>
            <a href="#tab-zip-codes" data-toggle="tab">
                Limitar Códigos Postais
            </a>
        </li>
        <li>
            <a href="#tab-budget" data-toggle="tab">
                Orçamentador
            </a>
        </li>
        {{--<li>
            <a href="#tab-matrix" data-toggle="tab">
                Matriz de CP
            </a>
        </li>--}}
    </ul>
</div>
<div class="modal-body p-t-0 p-b-0">
    <div class="tab-content m-b-0">
        <div class="tab-pane" id="tab-zip-codes">
            @include('admin.services.partials.zip_codes')
        </div>
        <div class="tab-pane" id="tab-providers">
            @include('admin.services.partials.providers')
        </div>
        <div class="tab-pane" id="tab-budget">
            @include('admin.services.partials.budget')
        </div>
       {{-- <div class="tab-pane" id="tab-matrix">
            @include('admin.services.partials.matrix')
        </div>--}}
        <div class="tab-pane active" id="tab-info">
            @include('admin.services.partials.info')
        </div>

    </div>
</div>
<div class="modal-footer">
    <div class="pull-left text-left w-30">
        <div class="checkbox m-b-0 m-t-4">
            <label style="padding-left: 10px !important;">
                {{ Form::checkbox('custom_prices', 1) }}
                Serviço ativo e disponível
            </label>
            {!! tip('Caso não seja indicada a viatura nos envios, a guia de transporte assumirá esta viatura como por defeito.') !!}
        </div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::hidden('delete_photo') }}
{{ Form::close() }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
<script>
    $('.modal .select2').select2(Init.select2());

    $('[data-dismiss="fileinput"]').on('click', function () {
        $('[name=delete_photo]').val(1);
    })

    $('.modal [name="unity"]').on('change', function () {
        $('[name="allow_kms"]').prop('checked', false);
        if($(this).val() == 'km') {
            $('[name="allow_kms"]').prop('checked', true);
        }
    })

    $('.select-all-agencies').on('click', function(e){
        e.preventDefault();

        if($('.row-agency:checked').length) {
            $('.row-agency').prop('checked', false);
        } else {
            $('.row-agency').prop('checked', true);
        }
    })

    $('.select-all-zones').on('click', function(e){
        e.preventDefault();

        if($('.row-zone:checked').length) {
            $('.row-zone').prop('checked', false);
        } else {
            $('.row-zone').prop('checked', true);
        }
    })

    $(document).on('change', '#modal-remote-xl .row-zone', function(e){
        e.preventDefault();
        var length = $('#modal-remote-xl .row-zone:checked').length
        $('#modal-remote-xl .count-selected').html(length);
    });

    $('[name="filter_box"]').on('keyup', function(){
        var value = $(this).val().toLowerCase();
        var regex = new RegExp('\\b\\w*' + value + '\\w*\\b');
        $('[data-filter-text]').hide().filter(function () {
            return regex.test($(this).data('filter-text'));
        }).show();

        $('[data-label="zip_code"]').show();
        $('[data-label="country"]').show();

        if($('[data-unity="zip_code"]:visible').length == 0) {
            $('[data-label="zip_code"]').hide();
        }

        if($('[data-unity="country"]:visible').length == 0) {
            $('[data-label="country"]').hide();
        }
    })

    $(document).on('keyup', '[name="filter_box_provider"]', function(){
        var value = $(this).val().toLowerCase();
        var regex = new RegExp('\\b\\w*' + value + '\\w*\\b');
        $(document).find('[data-filter-provider-text]').hide().filter(function () {
            return regex.test($(this).data('filter-provider-text'));
        }).show();

        $(document).find('.provider-zones [data-label="zip_code"]').show();
        $(document).find('.provider-zones [data-label="country"]').show();

        if($(document).find('.provider-zones [data-unity="zip_code"]:visible').length == 0) {
            $(document).find('.provider-zones [data-label="zip_code"]').hide();
        }

        if($(document).find('.provider-zones [data-unity="country"]:visible').length == 0) {
            $(document).find('.provider-zones [data-label="country"]').hide();
        }
    })

    $(document).on('change', '[name="is_mail"]', function(){
        if($(this).is(':checked')) {
            $('[name="vat_rate"]').val('M99').trigger('change');
        } else {
            $('[name="vat_rate"]').val('').trigger('change');
        }
    })

    $('[name="is_collection"]').on('change', function () {
        if(!$(this).is(':checked')) {
            $('[name="assigned_service_id"], [name="assigned_intercity_service_id"]')
                .prop('disabled', false)
                /*.prop('required', true)*/
                .trigger('change.select2')
                .closest('.form-group')
                .addClass('is-required')
        } else {
            $('[name="assigned_service_id"], [name="assigned_intercity_service_id"]')
                .prop('disabled', true)
                /*.prop('required', false)*/
                .trigger('change.select2')
                .closest('.form-group')
                .removeClass('is-required')
        }
    })

    $(".search-customers").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
    });

    $(document).on('click', '.search-zip-codes', function(){
        var district = $('.modal [name="district"]').val();
        var country  = $('.modal [name="country"]').val();
        var county   = $('.modal [name="county"]').val();

        $('.import-search-results').html('<div class="helper">' +
            '<i class="fas fa-spin fa-circle-notch"></i> A procurar Códigos Postais...' +
            '</div>')

        $.post('{{ route('admin.zip-codes.search') }}', {district: district, county:county, country:country}, function(data){
            $('.import-search-results').html(data);


            var selectedZipCodes = $('.modal [name="zip_codes"]').val();
            selectedZipCodes = selectedZipCodes.split(',');

            $('.select-zip-code').each(function(){
                var zipCode = $(this).val().toString();
                if(selectedZipCodes.includes(zipCode)) {
                    $(this).prop('checked', true);
                }
            })

        })
    })

    $(document).on('change', '[name=select-all-zip-codes]', function(){

        if($(this).is(':checked')) {
            $('.select-zip-code').prop('checked', true)
        } else {
            $('.select-zip-code').prop('checked', false)
        }
    })

    $(document).on('change', '.datatable-filters-area-extended [name="district"]', function () {
        var district = $(this).val();

        var options = $('[name="all_counties"]').find('optgroup[label="'+district+'"]').html();
        $('select[name="county"]').html('<option></option>' + options)
    })

    $(document).on('change', '.modal [name="country"]', function(){
        var country = $(this).val()

        $('.modal-filters').find('.fa-spin').removeClass('hide')
        $.post("{{ route('admin.zip-codes.filters.country') }}", {country:country}, function(data){
            $('.modal-filters').html(data)
            $('.modal-filters').find('.select2').select2(Init.select2());
        })
    })

    $(document).on('change', '.modal [name="district"]', function(){
        var country = $('.modal [name="country"]').val();
        var district = $(this).val()

        $('.modal-filters').find('.fa-spin').removeClass('hide')
        $.post("{{ route('admin.zip-codes.filters.country') }}", {country:country, district:district}, function(data){
            $('.modal-filters').html(data)
            $('.modal-filters').find('.select2').select2(Init.select2());
        })
    })

    $(document).on('change', '.select-zip-code', function(){
        var zipCode = $(this).val();
        var zipCodesSelected = $('[name="zip_codes"]').val();

        if(zipCodesSelected != "") {
            zipCodesSelected = zipCodesSelected.split(","); //convert input data to array
        } else {
            zipCodesSelected = []; //convert input data to array
        }

        if($(this).is(':checked')) { //check

            existsIndex = zipCodesSelected.indexOf(zipCode); //verify if exists

            if (existsIndex == -1) { //add zip code if dont exists
                zipCodesSelected.push(zipCode)
                zipCodesSelected = zipCodesSelected.join(',')
            }


        } else { //remove check

            existsIndex = zipCodesSelected.indexOf(zipCode); //verify if exists

            if (existsIndex > -1) { //remove zip code if exists
                zipCodesSelected.splice(existsIndex, 1);
                zipCodesSelected = zipCodesSelected.join(',')
            }
        }

        $('[name="zip_codes"]').val(zipCodesSelected);
    });

    $('.btn-add-matrix').on('click', function() {
        var $tr = $('.table-matrix tbody tr:first-child').clone();
        $tr.find('.select2-container').remove();
        $tr.find('select, textarea').val('');
        $tr.find('select').select2(Init.select2());
        $('.table-matrix tbody').append($tr)
    })

    $('.modal [name="is_internacional"]').on('change', function(event){
        if($(this).is(':checked')) {
            $(document).find('.modal .provider-int').prop('disabled', false)
        } else {
            $(document).find('.modal .provider-int').prop('disabled', true)
        }
    });

    $('.providers-sidebar-list .list-group-item').on('click', function() {
        $('.providers-sidebar-list .list-group-item').removeClass('active');
        $(this).addClass('active');

        var providerUrl = $(this).data('provider-url');
        //var providerId  = $(this).data('provider-id');
        var $formData  = $('.modal form').serialize();


        $('.provider-options').html('<div class="m-t-180 text-center text-muted"><h4><i class="fas fa-spin fa-circle-notch"></i> Aguarde...</h4></div>')

        $.post(providerUrl, $formData, function(data){
            $('.provider-options').html(data);

            if($('.modal [name="is_internacional"]').is(':checked')) { //se o serviço é internacional
                $(document).find('.modal .provider-int').prop('disabled', false)
            } else {
                $(document).find('.modal .provider-int').prop('disabled', true)
            }

            $('.provider-options .select2').select2(Init.select2());
        }).fail(function() {
            Growl.error500();
        });

    })

    $('.btn-marker a').on('click', function(e) {
        e.preventDefault();
        var url = $(this).find('img').attr('src');
        $('.service-marker-icn').prop('src', url);
        $('[name="marker_icon"]').val(url);
    })
</script>

<style>
    .billing-zones .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        height: 200px;
        overflow: scroll;
    }

    .modal .billing-zones .select2-container--default .select2-selection--multiple .select2-selection__rendered li {
        width: 100%;
    }

    .import-search-results {
        overflow: scroll;
        height: 140px;
        border: 1px solid #ccc;
    }

    .import-search-results .helper {
        margin-top: 60px;
        text-align: center;
        color: #777;
    }

    .providers-sidebar-list {
        height: 410px;
        overflow-y: auto;
        margin: -15px;
        border-radius: 0;
        box-shadow: 0 0 3px #ddd;
    }

    .providers-sidebar-list .list-group-item:first-child {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .providers-sidebar-list .list-group-item:last-child{
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .zones-transit {
        padding: 0 4px 1px 4px;
        height: 18px;
        text-align: center;
    }

    .zones-transit::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
        color: #ddd;
        opacity: 1; /* Firefox */
    }

    .zones-transit:-ms-input-placeholder { /* Internet Explorer 10-11 */
        color: #ddd;
    }

    .zones-transit::-ms-input-placeholder { /* Microsoft Edge */
        color: #ddd;
    }

    .zones-list td {
        padding: 0
    }
    .zones-list th {
        border-bottom: 1px solid #999
    }

    .zones-list .zone-group{
        display: block;
        background: #403f3f;
        color: #fff;
        margin: 0 -8px;
        padding: 3px 10px;
        font-weight: bold;
        text-transform: uppercase;
        border-bottom: 1px solid;
    }

    .input-hour .select2-selection.select2-selection--single{
        padding: 6px;
    }

    .input-unity .select2-selection.select2-selection--single{
        border-width: 2px;
        border-color: #bdd8fe;
        background: #ecf4ff;
        padding: 4px 10px;
    }
</style>