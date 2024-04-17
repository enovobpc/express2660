{{ Form::open($formOptions) }}
@include('admin.shipments.history.partials.form')
{{ Form::close() }}

{{ Html::script('vendor/jasny-bootstrap/dist/js/jasny-bootstrap.min.js') }}
<script>
    $('.modal [data-toggle="popover"]').popover()
    $(".modal .select2").select2(Init.select2());
    $(".modal .datepicker").datepicker(Init.datepicker());

    /**
     * CHANGE STATUS
     */
    $(document).on('click', '.form-update-history [data-toogle="select-button"]', function(){
        var id = $(this).data('id');

        $('.form-update-history [data-toogle="select-button"]').removeClass('btn-success').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-success');
        $('.form-update-history [name=status_id] option[value="'+id+'"]').prop('selected', true);
        $('.form-update-history [name=status_id]').trigger('change')
    })


    $(document).on('change', '.form-update-history [name=status_id]', function () {
        var status = $(this).val();

        if (status == '5') { //entregue
            $('.form-update-history .form-delivery').show();
        } else {
            $('.form-update-history .form-delivery').hide();
            $('.form-update-history .form-delivery').find('input[name="receiver"]').val('')
        }

        if (status == '9') { //incidencia
            $('.form-update-history .form-incidence').removeClass('hide');
            $('.form-update-history .form-incidence').find('select').prop('required', true);
        } else {
            $('.form-update-history .form-incidence').addClass('hide');
            $('.form-update-history .form-incidence').find('select').prop('required', false);
        }

        if (status == '7') { //devolution
            $('.form-update-history .form-devolution').removeClass('hide');
            $('input[name=devolution]').prop('checked', true);
        } else {
            $('.form-update-history .form-devolution').addClass('hide');
            $('input[name=devolution]').prop('checked', false);
        }

        if (status == '4' || status == '3') { //transporte ou distribiuicao
            $('.form-update-history .trip').show();
            $('.form-update-history .trip input[name=create_manifest]').prop('checked', false);
        } else {
            $('.form-update-history .trip').hide();
            $('.form-update-history .trip input[name=create_manifest]').prop('checked', false);
        }
    })

    @if(app_mode_cargo())
        <?php $now = Date::now() ?>
        $(document).on('change', '.form-update-history [name="city"]', function(){
            var origin = $(this).val()
            var destination  = "{{ $shipment->recipient_zip_code.' '.$shipment->recipient_city. ', ' . $shipment->recipient_country }}"
            var deliveryTime = "{{ $shipment->delivery_date }}"

                    alert(deliveryTime);

            $('.form-update-history .loading-distance').show()

            $.get("{{ config('app.core') . '/helper/maps/distance' }}", {
                origin: origin,
                destination: destination
            }, function (data) {
                if(data.result) {
                    distance = parseFloat(data.distance_value);
                } else {
                    Growl.error('Não foi possível calcular a distância. Verifique os códigos postais e localidades inseridas.')
                }
            }).always(function () {
                $('.form-update-history .loading-distance').hide()
            })

        })
    @endif
</script>
