{{ Form::model($shippingExpense, $formOptions) }}
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
                Configurar Preços
            </a>
        </li>
        <li>
            <a href="#tab-triggers" data-toggle="tab">
                Ativação automática e Personalização
            </a>
        </li>
    </ul>
</div>
<div class="modal-body p-t-0 p-b-0">
    <div class="tab-content m-b-0">
        <div class="tab-pane active" id="tab-info">
            @include('admin.expenses.partials.info')
        </div>
        <div class="tab-pane" id="tab-triggers">
            @include('admin.expenses.partials.triggers')
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}
<div style="display: none">
    <span class="input-weekdays">@include('admin.expenses.partials.input_weekdays')</span>
    <span class="input-hours">@include('admin.expenses.partials.input_hours')</span>
    <span class="input-country">@include('admin.expenses.partials.input_country')</span>
    <span class="input-decimal">@include('admin.expenses.partials.input_decimal')</span>
    <span class="input-status">@include('admin.expenses.partials.input_status')</span>
    <span class="input-services">@include('admin.expenses.partials.input_services')</span>
    <span class="input-zones">@include('admin.expenses.partials.input_zones')</span>
    <span class="input-cod">@include('admin.expenses.partials.input_cod')</span>
    <span class="input-remote-zones">@include('admin.expenses.partials.input_remote_zones')</span>
</div>
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
    $('.select2, .trigger-values select').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker());

    $('.modal [name="has_range_prices"]').on('change', function(){
        if($('.modal [name="has_range_prices"]').is(':checked')) {
           $('.range-unity').show();
           $('.range-unity-mesure').html($('.modal [name="range_unity"]').val())
        } else {
            $('.range-unity').hide();
        }
    })

    $('.modal [name="range_unity"]').on('change', function(){
        $('.range-unity-mesure').html($(this).val())
    })

    $('.modal .btn-add-line').on('click', function (e) {
        e.preventDefault();
        $clone = $('.table-expense-prices tbody tr:first-child').clone();
        $clone.find('input,select').val('');
        $clone.find('.select2-container').remove();
        $clone.find('[name="unity_arr[]"]').val('euro');
        $('.table-expense-prices tbody').append($clone)
        $('.table-expense-prices tbody tr:last-child .select2').select2(Init.select2());

        $clone.find('[name="trigger_arr[]"]').siblings('div').hide();
        $clone.find('[name="every_arr[][value]"]').val();
        $clone.find('[name="every_arr[][field]"]').val('weight').trigger('change');
    });

    $('.modal .trigger-fields, .modal .trigger-operators').on('change', function () {

        var $tr = $(this).closest('tr');

        if($tr.find('.trigger-fields').val() == '' || $tr.find('.trigger-operators').val() == '') {
            $tr.find('.trigger-join').hide();
            $tr.prev().find('.trigger-join select').val('').trigger('change')
        } else {
            $tr.find('.trigger-join').show();
            if($tr.prev().find('.trigger-join select').val() == '') {
                $tr.prev().find('.trigger-join select').val('and').trigger('change')
            }
        }
    })

    $('.modal .trigger-fields').on('change', function () {

        var $tr    = $(this).closest('tr');
        var value  = $(this).val();
        var target = $tr.find('.trigger-values');

        if(value == 'weekday') {
            target.html($('.modal .input-weekdays').html());
            $tr.find('.trigger-values select').select2(Init.select2());
        } else if(value == 'start_hour' || value == 'end_hour') {
            target.html($('.modal .input-hours').html());
            $tr.find('.trigger-values select').select2(Init.select2());
        } else if(value == 'sender_country' || value == 'recipient_country') {
            target.html($('.modal .input-country').html());
            $tr.find('.trigger-values select').select2(Init.select2());
        } else if(value == 'service_id') {
            target.html($('.modal .input-services').html());
            $tr.find('.trigger-values select').select2(Init.select2());
        } else if(value == 'status_id') {
            target.html($('.modal .input-status').html());
            $tr.find('.trigger-values select').select2(Init.select2());
        } else if(value == 'zone' || value == 'origin_zone') {
            target.html($('.modal .input-zones').html());
            $tr.find('.trigger-values select').select2(Init.select2());
        } else if(value == 'origin_remote_zone' || value == 'destination_remote_zone') {
            target.html($('.modal .input-remote-zones').html());
            $tr.find('.trigger-values select').select2(Init.select2());
        } else if(value == 'cod') {
            target.html($('.modal .input-cod').html());
            $tr.find('.trigger-values select').select2(Init.select2());
        } else {
            target.html($('.modal .input-decimal').html());
        }
    })

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

    $('.modal').on('change', '[name="trigger_arr[]"]', function () {
        var $this = $(this);
        if ($this.val() == 'every') {
            $this.siblings('div').show();
        } else {
            $this.siblings('div').hide();
        }
    });

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
                $('#modal-remote-xl').modal('hide');
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

