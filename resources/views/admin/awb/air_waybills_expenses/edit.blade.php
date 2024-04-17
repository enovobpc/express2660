{{ Form::model($expense, $formOptions) }}
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
                {{ Form::text('code', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('type', 'Tipo Taxa') }}
                {{ Form::select('type', ['' => ''] + trans('admin/air_waybills.expenses.types'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>
    <hr class="m-t-0 m-b-5"/>
    <h4>Regras de Preço</h4>
    <table class="table table-condensed table-roles m-b-0">
        <tr>
            <th>Aeroporto</th>
            <th>Emissor</th>
            <th>Medida</th>
            <th style="width: 80px">Valor</th>
            <th style="width: 80px">Valor Min</th>
        </tr>
        @for ($i = 0 ; $i<=25; $i++)
        <tr style="{{ $i == 0 || isset($expense->prices[$i]) ? '' : 'display: none' }}">
            <td>
                {{ Form::select('prices['.$i.'][airport]', ['' => 'Qualquer'] + $airports, null, ['class' => 'form-control select2 search-airport']) }}
            </td>
            <td>
                {{ Form::select('prices['.$i.'][provider]', ['' => 'Qualquer'] + $providers, null, ['class' => 'form-control select2']) }}
            </td>
            <td style="width: 100px;">
                {{ Form::select('prices['.$i.'][unity]', ['' => 'Qualquer', 'weight' => 'Peso Bruto', 'taxable_weight' => 'Peso Taxável', 'volumes' => 'Número Volumes'], null, ['class' => 'form-control select2 w-100']) }}

            </td>
            <td>{{ Form::text('prices['.$i.'][price]', null, ['class' => 'form-control']) }}</td>
            <td>{{ Form::text('prices['.$i.'][price_min]', null, ['class' => 'form-control']) }}</td>
        </tr>
        @endfor
    </table>
    <button type="button" class="btn btn-xs btn-default btn-add-role">
        Adicionar Regra
    </button>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());


    $(".search-airport").select2({
        ajax: {
            url: "{{ route('admin.air-waybills.search.airport') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('.form-waybill select[name=customer_id] option').remove()
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    /**
     * Add flight scale
     */
    $('.btn-add-role').on('click', function(){
        $('.table-roles').find('tr:hidden:first').show();

        if($('.table-roles').find("tr:hidden").length == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    $('.remove-role').on('click', function(){

        if($('.table-roles').find("tr:visible").length > 2) {
            var $tr = $(this).closest('tr');
            $tr.find('select').val('').trigger('change');
            $tr.hide();

            $tr.detach();
            $('.table-roles').append($tr);

            if ($('.table-roles').find("tr:hidden").length == 0) {
                $('.btn-add-role').hide();
            } else {
                $('.btn-add-role').show();
            }
        }
    });
</script>

