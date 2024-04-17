{{ Form::model($zipCodeZone, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('code', 'Código') }}
                {{ Form::text('code', null, ['class' => 'form-control uppercase nospace', 'required', 'maxlength' => 5]) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control ucwords', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('provider_id', 'Fornecedor') }}
                {{ Form::select('provider_id', ['' => 'Qualquer'] + @$providers, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('zone_country', 'País') }}
                {{ Form::select('zone_country', ['' => '- Qualquer -'] + trans('country'), $zipCodeZone->exists ? $zipCodeZone->country : Setting::get('app_country'), ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('services[]', 'Serviços associados') }} {!! tip('Limita esta zona apenas aos serviços selecionados') !!}
                {{ Form::select('services[]', $services, array_map('intval', @$zipCodeZone->services ? $zipCodeZone->services : []), ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todos']) }}
            </div>
        </div>
    </div>
    <hr class="m-t-5 m-t-5"/>
    <div class="zip-codes-list">
        <div class="form-group">
            {{ Form::label('zip_codes', 'Que códigos postais que fazem parte desta zona? (separados por vírgula)') }}
            {{ Form::textarea('zip_codes', @$zipCodeZone->zip_codes_str, ['class' => 'form-control', 'rows' => 3]) }}
        </div>
        <div class="row row-5 modal-filters">
            <?php $country = 'pt'; $district = '' ?>
            @include('admin.zip_codes.agencies.partials.filters')
        </div>
        <div class="import-search-results">
            <div class="helper">
                <i class="fas fa-search"></i>
                Escolha um distrito ou concelho para procurar códigos postais.
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::hidden('type', $zipCodeZone->type) }}
{{ Form::close() }}

{{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
<script>
    $(document).ready(function () {
        $('select[name="color"]').simplecolorpicker({theme: 'fontawesome'});
    })
    
    $('.modal .select2').select2(Init.select2());

    $('[name="unity"]').on('change', function () {
        var unity = $(this).val();

        if(unity == 'country') {
            $('.country-list').show().find('select').val('').prop('required', true);
            $('.zip-codes-list').hide().find('textarea').val('').prop('required', false);
        } else {
            $('.country-list').hide().find('select').val('').prop('required', false);
            $('.zip-codes-list').show().find('textarea').val('').prop('required', true);
        }
    });

    $(document).on('click', '.modal .search-zip-codes', function(){
        var country  = $('.modal [name="country"]').val();
        var district = $('.modal [name="district"]').val();
        var county   = $('.modal [name="county"]').val();

        $('.import-search-results').html('<div class="helper">' +
            '<i class="fas fa-spin fa-circle-notch"></i> A procurar Códigos Postais...' +
            '</div>')

        $.post('{{ route('admin.billing.zones.search.zip-codes') }}', {district: district, county:county, country:country}, function(data){
            $('.import-search-results').html(data);
        }).always(function () {

            var zipCodesSelected = $('[name="zip_codes"]').val();

            $('.select-zip-code').each(function () {
                existsIndex = zipCodesSelected.indexOf($(this).val());

                if(existsIndex >= 0) {
                    $(this).prop('checked', true);
                }
            })

        });
    })

    $(document).on('change', '[name=select-all-zip-codes]', function(){
        if($(this).is(':checked')) {
            $('.select-zip-code').prop('checked', true)
            $('.select-zip-code:last-child').prop('checked', true).trigger('change');
        } else {
            $('.select-zip-code').prop('checked', false)
            $('.select-zip-code:last-child').prop('checked', false).trigger('change');
        }
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

    $(document).on('change', '#modal-remote-lg [name="district"]', function () {
        var district = $(this).val();

        var options = $('[name="all_counties"]').find('optgroup[label="'+district+'"]').html();
        $('select[name="county"]').html('<option></option>' + options)
    })

    $(document).on('change', '#modal-remote-lg [name="country"]', function(){
        var country = $(this).val()

        $('.modal-filters').find('.fa-spin').removeClass('hide')
        $.post("{{ route('admin.zip-codes.filters.country') }}", {country:country}, function(data){
            $('.modal-filters').html(data)
            $('.modal-filters').find('.select2').select2(Init.select2());
        })
    })

    $(document).on('change', '#modal-remote-lg [name="district"]', function(){
        var country = $('#modal-remote-lg [name="country"]').val();
        var district = $(this).val()

        $('.modal-filters').find('.fa-spin').removeClass('hide')
        $.post("{{ route('admin.zip-codes.filters.country') }}", {country:country, district:district}, function(data){
            $('.modal-filters').html(data)
            $('.modal-filters').find('.select2').select2(Init.select2());
        })
    })
</script>
