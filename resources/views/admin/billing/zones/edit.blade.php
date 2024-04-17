{{ Form::model($billingZone, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-7">
            <div class="row row-5">
                <div class="col-sm-2">
                    <div class="form-group is-required">
                        {{ Form::label('code', 'Código') }}
                        {{ Form::text('code', strtoupper(@$billingZone->code), ['class' => 'form-control nospace uppercase', 'required', 'maxlength' => 10]) }}
                    </div>
                </div>
                <div class="col-sm-10">
                    <div class="form-group is-required">
                        {{ Form::label('name', 'Designação') }}
                        {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::label('unity', 'Tipo Zona') }}
                        {{ Form::select('unity', trans('admin/shipments.billing-zones-types'), null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::label('zone_country', 'Associar zona ao País') }}
                        {{ Form::select('zone_country', ['' => 'Qualquer País'] + trans('country'), $billingZone->exists ? $billingZone->country : Setting::get('app_country') , ['class' => 'form-control select2-country', $billingZone->unity == 'country' ? 'disabled' : '']) }}
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row row-5">
        <div class="col-sm-12 distance-list" style="{{ $billingZone->unity == 'distance' ? '' : 'display: none' }}">
            <div class="row row-0">
                <div class="col-sm-6 p-r-5" style="padding-right: 10px;">
                    <div class="form-group is-required">
                        {{ Form::label('distance_min', 'Distância Min.') }}
                        <div class="input-group">
                            {{ Form::text('distance_min', $billingZone->distance_min, ['class' => 'form-control decimal',  $billingZone->unity == 'distance' ? 'required' : '' ]) }}
                            <div class="input-group-addon">km</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 p-l-5">
                    <div class="form-group is-required">
                        {{ Form::label('distance_max', 'Distância Max.') }}
                        <div class="input-group">
                            {{ Form::text('distance_max', $billingZone->distance_max, ['class' => 'form-control decimal',  $billingZone->unity == 'distance' ? 'required' : '' ]) }}
                            <div class="input-group-addon">km</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 country-list" style="{{ $billingZone->unity == 'country' ? '' : 'display: none' }}">
            <div class="form-group is-required">
                {{ Form::label('countries[]', 'Que Países que fazem parte da zona de faturação?') }}
                {{ Form::select('countries[]', trans('country'), $billingZone->mapping, ['class' => 'form-control select2', 'multiple',  $billingZone->unity == 'country' ? 'required' : '' ]) }}
            </div>
        </div>
        <div class="col-sm-12 pack-types-list" style="{{ in_array($billingZone->unity, ['pack_type', 'pack_zip_code', 'pack_matrix']) ? '' : 'display: none' }}">
            <div class="form-group is-required">
                {{ Form::label('pack_types[]', 'Que tipos de embalagem ou palates fazem parte da zona?') }}
                {{ Form::select('pack_types[]', $packTypes, in_array($billingZone->unity, ['pack_type', 'pack_zip_code', 'pack_matrix']) ? $billingZone->pack_types : $billingZone->mapping, ['class' => 'form-control select2', 'multiple',  in_array($billingZone->unity, ['pack_type', 'pack_zip_code', 'pack_matrix']) ? 'required' : '' ]) }}
            </div>
        </div>
    </div>
    <div class="row row-15 matrix-list" style="{{ in_array($billingZone->unity, ['matrix', 'pack_matrix']) ? 'display:block' : 'display:none' }}">
        <div class="col-sm-12">
            <table class="table table-condensed table-matrix m-b-3">
                <thead>
                    <tr>
                        <th class="bg-gray">Códigos Origem <small class="italic text-muted">(ex: 1000-1999,2500-2999,...)</small></th>
                        <th class="bg-gray eq" style="width: 50px; text-align: center"><i class="fas fa-arrows-alt-h"></i></th>
                        <th class="bg-gray">Códigos Destino <small class="italic text-muted">(ex: 1000-1999,2500-2999,...)</small></th>
                    </tr>
                </thead>
                <tbody>
                    @for($i=0 ; $i<(count(@$billingZone->matrix['origins']) ? count(@$billingZone->matrix['origins']) : 10) ; $i++)
                    <tr class="matrix-rw">
                        <td>{{ Form::text('matrix[origins][]', @$billingZone->matrix['origins'][$i], ['class' => 'form-control input-sm']) }}</td>
                        <td class="eq input-sm">
                            {{--<i class="fas fa-arrows-alt-h"></i>--}}
                            {{ Form::select('matrix[dir][]', ['<=>' => '<=>', '=>' => '=>'], @$billingZone->matrix['dir'][$i], ['class' => 'form-control select2']) }}
                        </td>
                        <td>{{ Form::text('matrix[destinations][]', @$billingZone->matrix['destinations'][$i], ['class' => 'form-control input-sm']) }}</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
            <button class="btn btn-xs btn-default btn-add-matrix" type="button"><i class="fas fa-plus"></i> Adicionar nova linha</button>
        </div>
    </div>
    <div class="zip-codes-list" style="{{ in_array($billingZone->unity, ['zip_code','pack_zip_code']) ? '' : 'display: none' }}">
        <div class="form-group">
            {{ Form::label('zip_codes', 'Que códigos postais que fazem parte da zona de faturação? (separados por vírgula)') }}
            {!! tip('Exemplos de configuração.<hr/> Portugal: 1000-1900,1100-120,1105,1150,..<hr/>Range: 46500-80000,80001-90000<hr/>Range vários paises: FR46500-FR80000,DE32500-DE55000,...') !!}
            {{ Form::textarea('zip_codes', null, ['class' => 'form-control', 'rows' => 3, in_array($billingZone->unity, ['zip_code','pack_zip_code']) ? 'required' : '']) }}
        </div>
        <div class="row row-5 modal-filters">
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
{{ Form::close() }}

<style>
    .select2-selection__rendered .iti-flag {
        margin: 7px 3px 0 0;
    }

    .table-matrix td {
        padding: 1px 0px !important;
        border: none !important;
    }

    .table-matrix td.eq {
        width: 50px;
        vertical-align: middle;
        text-align: left;
    }

    /*.table-matrix td input {
        border-top: none !important;
    }*/
</style>
<script>
    $('.select2').select2(Init.select2());
    $('.select2-country').select2(Init.select2Country());

    $('[name="unity"]').on('change', function () {

        $('[name="zone_country"]').closest('.form-group').find('.select2-selection__rendered').show()
        $('[name="zone_country"]').prop('disabled', false)

        var unity = $(this).val();

        if(unity == 'country') {
            $('.country-list').show().find('select').val('').prop('required', true);
            $('.zip-codes-list').hide().find('textarea').val('').prop('required', false);
            $('.distance-list').hide().find('text').val('').prop('required', false);
            $('.pack-types-list').hide().find('select').val('').prop('required', false);
            $('[name="zone_country"]').closest('.form-group').find('.select2-selection__rendered').hide()
            $('[name="zone_country"]').prop('disabled', true);
            $('.matrix-list').hide().find('.matrix-rw input').val('');
        } else if(unity == 'zip_code' || unity == 'pack_zip_code') {
            $('.country-list').hide().find('select').val('').prop('required', false);
            $('.zip-codes-list').show().find('textarea').val('').prop('required', true);
            $('.distance-list').hide().find('text').val('').prop('required', false);
            $('.pack-types-list').hide().find('select').val('').prop('required', false);
            $('.matrix-list').hide().find('.matrix-rw input').val('');
            if(unity == 'pack_zip_code') {
                $('.pack-types-list').show().find('select').val('').prop('required', true);
            }
        } else if(unity == 'distance') {
            $('.country-list').hide().find('select').val('').prop('required', false);
            $('.zip-codes-list').hide().find('textarea').val('').prop('required', false);
            $('.distance-list').show().find('text').val('').prop('required', true);
            $('.pack-types-list').hide().find('select').val('').prop('required', false);
            $('.matrix-list').hide().find('.matrix-rw input').val('');
        } else if(unity == 'pack_type') {
            $('.country-list').hide().find('select').val('').prop('required', false);
            $('.zip-codes-list').hide().find('textarea').val('').prop('required', false);
            $('.distance-list').hide().find('text').val('').prop('required', false);
            $('.pack-types-list').show().find('select').val('').prop('required', true);
            $('.matrix-list').hide().find('.matrix-rw input').val('');
        } else if(unity == 'matrix' || unity == 'pack_matrix') {
            $('.country-list').hide().find('select').val('').prop('required', false);
            $('.zip-codes-list').hide().find('textarea').val('').prop('required', false);
            $('.distance-list').hide().find('text').val('').prop('required', false);
            $('.pack-types-list').hide().find('select').val('').prop('required', false);
            $('.matrix-list').show().find('.matrix-rw input').val('');

            if(unity == 'pack_matrix' ) {
                $('.pack-types-list').show().find('select').val('').prop('required', true);
            }
        }
    });


    $('.btn-add-matrix').on('click', function () {
        $clone = $('.table-matrix tbody tr:last-child').clone();
        $clone.find('input').val('');
        $clone.find('input').val('');
        $('.table-matrix tbody').append($clone)
    })

    $(document).on('click', '.search-zip-codes', function(){
        var country  = $('[name="country"]').val();
        var district = $('[name="district"]').val();
        var county   = $('[name="county"]').val();

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
            $('.select-zip-code').prop('checked', true);
        } else {
            $('.select-zip-code').prop('checked', false);
        }

        $('.select-zip-code').trigger('change');
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
