{{ Form::model($shippingExpense, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    @include('admin.expenses.partials.info_fuel')
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::hidden('type', 'fuel') }}
{{ Form::hidden('code', 'FUEL') }}
{{ Form::close() }}
<style>
    .table-expense-prices td {
        padding: 2px !important;
    }

    .table-expense-prices .input-group-addon {
        border: none;
        position: absolute;
        z-index: 3;
        background: #fff;
        padding: 7px 7px 7px 0;
        top: 1px;
        right: 1px;
        width: 15px;
        text-align: center;
    }
</style>
<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker());

    $('.modal .btn-add-line').on('click', function (e) {
        e.preventDefault();
        $clone = $('.table-expense-prices tbody tr:first-child').clone();
        $clone.find('input,select').val('');
        $clone.find('.select2-container').remove();
        $clone.find('[name="unity_arr[]"]').val('euro');
        $('.table-expense-prices tbody').append($clone)
        $('.table-expense-prices tbody tr:last-child .select2').select2(Init.select2());

    });

    $('.modal [name="type"]').on('change', function () {
        $('.trigger_value, .trigger_services').hide().find('select, input').val('').trigger('change');

        if($('.modal [name="type"]').val() == 'weight') {
            $('.trigger_value').show();
        } else if($.inArray($("[name=type]").val(), ['weekend', 'out_hour', 'discharge', 'weight', 'devolution'])) {
            $('.trigger_services').show();
        }
    })

    $('[name="type"]').on('change', function () {

        if($("[name=type]").val() == 'percent') {
            $('.price-input .input-group-addon').html('%')
        } else {
            $('.price-input .input-group-addon').html('{{ Setting::get('app_currency') }}')
        }

    })


    $('[name="zones[]"]').on('change', function(){
        originalValues = $(this).val();

        $('[name="zones[]"] :selected').each(function(){
            var country = $(this).val();
            var countryName = $(this).text();

            if(!$('[data-country="'+country+'"]').length) {
                $html = '<div class="col-sm-3 price-input" data-country="' + country + '">' +
                        '<div class="form-group">' +
                        '<label>PVP '+ countryName +'</label>' +
                        '<div class="input-group">' +
                        '<input type="text" name="price['+country+']" class="form-control"/>' +
                        '<div class="input-group-addon">{{ Setting::get('app_currency') }}</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                $('.prices-inputs').append($html);
            }
        });

        $('.price-input').each(function(){
            var country = $(this).data('country');

            if(!$('[name="zones[]"] option[value='+country+']:selected').length) {
                $(this).remove();
            }
        });
    })
    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-ajax').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTable.draw(); //update datatable
                $('#modal-remote-lg').modal('hide');
                Growl.success(data.feedback);
            } else {
                Growl.error(data.feedback);
            }

        }).fail(function () {
            Growl.error500();
        }).always(function(){
            $button.button('reset');
        })
    });
</script>

