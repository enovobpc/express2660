{{ Form::model($receptionOrder, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-10">
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('document', 'Fatura/Documento associado') }}
                {{ Form::text('document', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('requested_date', 'Data Recepção') }}
                <div class="input-group">
                    {{ Form::text('requested_date', ($receptionOrder->exists ? null : date('Y-m-d')), ['class' => 'form-control datepicker', 'required']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-condensed m-b-5 table-products">
        <tr>
            <th style="padding-left: 0">Artigo (SKU ou Designação)</th>
            <th class="w-90px">Qtd</th>
            <th class="w-180px">SKU</th>
            <th class="w-1"></th>
        </tr>

        @if ($receptionOrder->exists)
            @foreach ($receptionOrder->lines as $line)
            <tr>
                <td style="padding-left: 0">
                    {{ Form::text('name[]', $line->product->name, ['class' => 'form-control search-product', 'autocomplete'=> 'nofill']) }}
                    {{ Form::hidden('product_id[]', $line->product_id) }}
                </td>
                <td>{{ Form::text('qty[]', $line->qty, ['class' => 'form-control', 'maxlength' => 4]) }}</td>
                <td>{{ Form::text('sku[]', $line->product->sku, ['class' => 'form-control']) }}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-remove-product"><i class="fas fa-times"></i></button>
                </td>
            </tr>
            @endforeach
        @else
        @for($i = 0; $i <= 3; $i++)
            <tr>
                <td style="padding-left: 0">
                    {{ Form::text('name[]', null, ['class' => 'form-control search-product', ($i == 0 ? 'required' : ''), 'autocomplete'=> 'nofill']) }}
                    {{ Form::hidden('product_id[]') }}
                </td>
                <td>{{ Form::text('qty[]', null, ['class' => 'form-control', 'maxlength' => 4, ($i == 0 ? 'required' : '')]) }}</td>
                <td>{{ Form::text('sku[]', null, ['class' => 'form-control']) }}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-remove-product" {{ $i == 0 ? 'disabled' : '' }}><i class="fas fa-times"></i></button>
                </td>
            </tr>
        @endfor
        @endif
    </table>
    <button class="btn btn-xs btn-default btn-add-product" type="button"><i class="fas fa-plus"></i> Adicionar Novo Produto</button>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-black">Gravar</button>
</div>
{{ Form::close() }}

{{ Html::script(asset('vendor/devbridge-autocomplete/dist/jquery.autocomplete.js')) }}
<script>
    $('.datepicker').datepicker(Init.datepicker());
    var select2 = $('.select2').select2(Init.select2());

    var autocompleteOptions = {
        serviceUrl: '{{ route('account.logistic.products.search') }}',
        onSearchStart: function () {
            $(this).closest('tr').find('[name="product_id[]"]').val('');
            $(this).closest('tr').find('[name="sku[]"]').val('');
        },
        onSelect: function (suggestion) {
            $(this).closest('tr').find('[name="product_id[]"]').val(suggestion.data);
            $(this).closest('tr').find('[name="sku[]"]').val(suggestion.sku);
        },
    };

    $('.btn-add-product').on('click', function () {
        select2.select2('destroy');

        var trClone = $('#modal-remote-lg .table-products tr:last-child').clone();
        trClone.find('input').val('');
        trClone.find('.btn-remove-product').prop('disabled', false);

        $('.table-products').append(trClone);
        $('.search-product').autocomplete(autocompleteOptions);
        select2 = $('.select2').select2(Init.select2());
    });

    $(document).on('click', '.btn-remove-product', function () {
        $(this).closest('tr').remove();
    });

    $('.search-product').autocomplete(autocompleteOptions);

    $(document).on('change', '[name="name[]"]', function () {
        var tr = $(this).closest('tr');
        var text = $(this).val();

        if(text == "") {
            tr.find('[name="qty[]"]').prop('required', false);
            // tr.find('[name="location[]"]').prop('required', false).val('').trigger('change');
        } else {
            tr.find('[name="qty[]"]').prop('required', true);
            // tr.find('[name="location[]"]').prop('required', true).val('').trigger('change');
        }
    })

    /**
     * Submit form
     */
    $('.form-reception-order').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTable.draw(); //update datatable
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
                $('#modal-remote-xl, #modal-remote-lg, #modal-remote').modal('hide');
            } else {
                $('.form-billing .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> ' + data.feedback);
            }

        }).catch(function () {
            $('.form-billing .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function() {
            $button.button('reset');
        })
    });
</script>

