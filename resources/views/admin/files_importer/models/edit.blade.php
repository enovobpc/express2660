{{ Form::model($importerModel, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group is-required">
        {{ Form::label('name', __('Designação')) }}
        {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('type', __('Tipo de Importação'), ['class' => 'control-label']) }}
                {{ Form::select('type', trans('admin/importer.import_types'), null, ['class' => 'form-control select2', $importerModel->exists ? 'disabled' : '']) }}
            </div>
            @if($importerModel->type == 'shipments' || $importerModel->type == 'shipments_fast')
                @include('admin.files_importer.models.partials.shipment')
            @elseif($importerModel->type == 'customers')
                @include('admin.files_importer.models.partials.customer')
            @elseif($importerModel->type == 'providers')
                @include('admin.files_importer.models.partials.provider')
            @elseif($importerModel->type == 'providers_agencies')
                @include('admin.files_importer.models.partials.providers_agencies')
            @elseif($importerModel->type == 'fleet_fuel')
                @include('admin.files_importer.models.partials.fleet_fuel')
            @elseif($importerModel->type == 'logistic_products')
                @include('admin.files_importer.models.partials.logistic_products')
            @elseif($importerModel->type == 'logistic_lite_products')
                @include('admin.files_importer.models.partials.logistic_lite_products')
            @elseif($importerModel->type == 'shipments_logistic')
                @include('admin.files_importer.models.partials.shipment_logistic')
            @elseif($importerModel->type == 'reception_orders')
                @include('admin.files_importer.models.partials.reception_orders')
            @elseif($importerModel->type == 'prices_table')
                @include('admin.files_importer.models.partials.prices_table')
            @elseif($importerModel->type == 'shipments_dimensions')
                @include('admin.files_importer.models.partials.shipment_dimensions')
            @elseif($importerModel->type == 'fleet_vehicles')
                @include('admin.files_importer.models.partials.fleet_vehicles')
            @endif
        </div>
        <div class="col-sm-8">
            <table class="table table-condensed m-0">
                <tr class="bg-gray">
                    <th>@trans('Campo')</th>
                    <th class="w-200px">@trans('Tipo Dados')</th>
                    <th class="w-110px">@trans('Coluna Excel') <i class="fas fa-info-circle" data-toggle="tooltip" title="@trans('Indique a coluna no ficheiro Excel correspondente ao campo a ser lido. Ex: A,B,C,D,...')"></i></th>
                </tr>
                <tr class="bg-gray">
                    <td colspan="2" style="padding: 1px">
                        <input name="filter-field" class="form-control input-sm" placeholder="@trans('Procurar nome do campo...')">
                    </td>
                    <td class="w-110px" style="padding: 1px">
                        <input name="filter-column" class="form-control input-sm" placeholder="@trans('Procurar coluna...')">
                    </td>
                </tr>
            </table>
            <div style="height: 325px; overflow: scroll; border: 1px solid #ccc;">
                @if($fields)
                <table class="table table-content table-condensed m-0">
                    @foreach($fields as $key => $values)
                        @php
                        $required = ($values['required'] ?? false);
                        @endphp
                        <tr>
                            <td class="col-field-name text-middle">
                                {{ @$values['name'] }}
                                @if ($required)
                                <span class="text-red text-bold">*</span>
                                @endif
                            </td>
                            <td class="w-200px text-middle">{{ @$values['type'] }}</td>
                            <td class="col-mapping w-110px">
                                {{ Form::text('mapping['.$key.']', null, ['class' => 'form-control input-sm text-uppercase text-center', 'maxlength' => '2', 'style' => 'height: 25px;margin: -4px 0 -4px 0;', ($required ? 'required' : '')]) }}
                            </td>
                        </tr>
                    @endforeach
                </table>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>

    // OVERWRITES old selecor
    jQuery.expr[':'].contains = function(a, i, m) {
        return jQuery(a).text().toUpperCase()
            .indexOf(m[3].toUpperCase()) >= 0;
    };


    $('[name="filter-field"]').on('keyup', function(){
        var value = $(this).val().toUpperCase();

        if(value == '') {
            $('.table-content tr').show();
        } else {
            $('.table-content tr').hide();
            $('.table-content tr').filter(":contains('" + value + "')").show()
        }
    })

    $('[name="filter-column"]').on('keyup', function(){
        var value = $(this).val().toLowerCase();

        if(value == '') {
            $('.table-content tr').show();
        } else {
            $('.table-content tr').hide();
            $('.table-content tr :input[value="'+value+'"]').closest('tr').show();
        }
    })

    $('.modal [name="type"]').on('change', function(){
        var type = $(this).val();
        var name = $('.modal-lg [name="name"]').val();

        var html = '<div class="modal-body">'
            +'<h4 class="modal-title text-center m-t-40 m-b-40 text-muted">'
            +'<i class="fas fa-circle-notch fa-spin"></i> A carregar...'
            +'</h4>'
            +'</div>';

        $('#modal-remote-lg .modal-content').html(html);
        $.get('{{ route('admin.importer.models.create') }}?type=' + type + '&name=' + name, function(data){
            $('#modal-remote-lg .modal-content').html(data);
        })
    })

    $('.select2').select2(Init.select2());
</script>