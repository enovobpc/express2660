{{ Form::model($route, $formOptions) }}
<div class="modal-header">
    <button class="close" data-dismiss="modal" type="button">
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
                        {{ Form::text('code', null, ['class' => 'form-control uppercase nospace', 'required', 'maxlength' => 5]) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group is-required">
                        {{ Form::label('type', 'Tipo') }}
                        {{ Form::select('type', ['' => 'Recolha + Entrega', 'pickup' => 'Rota só para Recolha', 'delivery' => 'Rota só para entrega'], null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::label('name', 'Designação') }}
                        {{ Form::text('name', null, ['class' => 'form-control ucwords', 'required']) }}
                    </div>
                </div>

                <div class="col-sm-9">
                    <div class="form-group">
                        {{ Form::label('services[]', 'Serviços') }}
                        {{ Form::selectMultiple('services[]', $services, array_map('intval', @$route->services ?? []), ['class' => 'form-control select2', 'data-placeholder' => 'Todos']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('route_group_id', __('Grupo')) }}
                        {{ Form::select('route_group_id', ['' => ''] + $routesGroups, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <table class="w-100" id="table-schedules">
                <tbody>
                    <tr>
                        <th><span data-toggle="tooltip" title="Hora Mínima">H.Min</span></th>
                        <th><span data-toggle="tooltip" title="Hora Máxima">H.Max</span></th>
                        <th>Operador</th>
                        <th>Fornecedor</th>
                    </tr>
                    @php
                        $schedulesCount = @$route->schedules ? count($route->schedules) : 3;
                    @endphp
                    @for ($i = 0; $i < ($schedulesCount < 3 ? 3 : $schedulesCount); $i++)
                        @php
                            $schedule = @$route->schedules[$i];
                        @endphp
                        <tr>
                            <td class="w-50px">
                                {{ Form::select('schedules[min_hour][]', ['' => ''] + $hours, @$schedule['min_hour'], ['class' => 'form-control select2']) }}
                            </td>
                            <td class="w-50px">
                                {{ Form::select('schedules[max_hour][]', ['' => ''] + $hours, @$schedule['max_hour'], ['class' => 'form-control select2']) }}
                            </td>
                            <td class="w-80px">
                                {{ Form::select('schedules[operator][]', ['' => ''] + ($operators ?? []), @$schedule['operator'], ['class' => 'form-control select2']) }}
                            </td>
                            <td class="w-80px">
                                {{ Form::select('schedules[provider][]', ['' => ''] + ($providers ?? []), @$schedule['provider'], ['class' => 'form-control select2']) }}
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <button class="btn btn-default btn-xs m-t-5" id="btn-add-schedule" type="button" style="float: right">
                Adicionar Linha
            </button>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-sm-5">
            <label>Agências a que pertence a rota</label>
            @if ($agencies->count() >= 4)
                <div style="max-height: 90px;overflow: scroll;border: 1px solid #ddd;padding: 0 8px;">
            @endif
            @foreach ($agencies as $agency)
                <div class="checkbox m-t-5 m-b-8">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('agencies[]', $agency->id) }}
                        <span class="label" style="background: {{ $agency->color }}">{{ $agency->code }}</span> {{ $agency->print_name }}
                    </label>
                </div>
            @endforeach
            @if ($agencies->count() >= 4)
        </div>
        @endif
    </div>

    <div class="col-sm-7">
        <div class="form-group">
            {{ Form::label('color', 'Idêntificador') }}<br />
            {{ Form::select('color', trans('admin/global.colors')) }}
        </div>
    </div>
</div>

<hr class="m-t-5 m-t-5" />
<div class="zip-codes-list">
    <div class="form-group">
        {{ Form::label('zip_codes', 'Que códigos postais que fazem parte desta rota? (separados por vírgula)') }}
        <textarea class="form-control" name="zip_codes" rows="6">{{ $route->exists ? $route->zip_codes_str : null }}</textarea>
    </div>
    <div class="row row-5 modal-filters">
        <?php $country = 'pt';
        $district = ''; ?>
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
    <button class="btn btn-default" data-dismiss="modal" type="button">Fechar</button>
    <button class="btn btn-primary" type="submit">Gravar</button>
</div>
{{ Form::close() }}

{{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
<script>
    $(document).ready(function() {
        $('select[name="color"]').simplecolorpicker({
            theme: 'fontawesome'
        });
    })

    $('.modal .select2').select2(Init.select2());

    $('[name="unity"]').on('change', function() {
        var unity = $(this).val();

        if (unity == 'country') {
            $('.country-list').show().find('select').val('').prop('required', true);
            $('.zip-codes-list').hide().find('textarea').val('').prop('required', false);
        } else {
            $('.country-list').hide().find('select').val('').prop('required', false);
            $('.zip-codes-list').show().find('textarea').val('').prop('required', true);
        }
    });

    $(document).on('click', '.search-zip-codes', function() {
        var country = $('[name="country"]').val();
        var district = $('[name="district"]').val();
        var county = $('[name="county"]').val();

        $('.import-search-results').html('<div class="helper">' +
            '<i class="fas fa-spin fa-circle-notch"></i> A procurar Códigos Postais...' +
            '</div>')

        $.post('{{ route('admin.billing.zones.search.zip-codes') }}', {
            district: district,
            county: county,
            country: country
        }, function(data) {
            $('.import-search-results').html(data);
        }).always(function() {

            var zipCodesSelected = $('[name="zip_codes"]').val();

            $('.select-zip-code').each(function() {
                existsIndex = zipCodesSelected.indexOf($(this).val());

                if (existsIndex >= 0) {
                    $(this).prop('checked', true);
                }
            })

        });
    })

    $(document).on('change', '[name=select-all-zip-codes]', function() {
        if ($(this).is(':checked')) {
            $('.select-zip-code').prop('checked', true)
            $('.select-zip-code:last-child').prop('checked', true).trigger('change');
        } else {
            $('.select-zip-code').prop('checked', false)
            $('.select-zip-code:last-child').prop('checked', false).trigger('change');
        }
    })

    $(document).on('change', '.select-zip-code', function() {
        var zipCode = $(this).val();
        var zipCodesSelected = $('[name="zip_codes"]').val();

        if (zipCodesSelected != "") {
            zipCodesSelected = zipCodesSelected.split(","); //convert input data to array
        } else {
            zipCodesSelected = []; //convert input data to array
        }

        if ($(this).is(':checked')) { //check

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

    $(document).on('change', '#modal-remote-lg [name="district"]', function() {
        var district = $(this).val();

        var options = $('[name="all_counties"]').find('optgroup[label="' + district + '"]').html();
        $('select[name="county"]').html('<option></option>' + options)
    })

    $(document).on('change', '#modal-remote-lg [name="country"]', function() {
        var country = $(this).val()

        $('.modal-filters').find('.fa-spin').removeClass('hide')
        $.post("{{ route('admin.zip-codes.filters.country') }}", {
            country: country
        }, function(data) {
            $('.modal-filters').html(data)
            $('.modal-filters').find('.select2').select2(Init.select2());
        })
    })

    $(document).on('change', '#modal-remote-lg [name="district"]', function() {
        var country = $('#modal-remote-lg [name="country"]').val();
        var district = $(this).val()

        $('.modal-filters').find('.fa-spin').removeClass('hide')
        $.post("{{ route('admin.zip-codes.filters.country') }}", {
            country: country,
            district: district
        }, function(data) {
            $('.modal-filters').html(data)
            $('.modal-filters').find('.select2').select2(Init.select2());
        })
    })


    $('#btn-add-schedule').on('click', function() {
        var $tbody = $('#table-schedules > tbody');
        var $tr = $('#table-schedules > tbody tr:last');

        var $clone = $tr.clone();
        $clone.find('select').val('');
        $clone.find('.select2-container').remove();
        $tbody.append($clone);
        $clone.find('.select2').select2(Init.select2());
    });
</script>
