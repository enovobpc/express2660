{{ Form::model($shipment , $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('date', 'Data Recolha') }}
                <div class="input-group">
                    {{ Form::text('shipping_date', $shipment->date ? $shipment->date : null, ['class' => 'form-control datepicker']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('shipping_hour', 'Hora') }}
                {{ Form::select('shipping_hour', ['' => '--:--'] + $hours, $shipment->shipping_date ? $shipment->shipping_date->format('H:i') : null , ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('delivery_date', 'Data Entrega') }}
                <div class="input-group">
                    {{ Form::text('delivery_date', $shipment->delivery_date ? $shipment->delivery_date->format('Y-m-d') : null, ['class' => 'form-control datepicker']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('delivery_hour', 'Hora') }}
                {{ Form::select('delivery_hour', ['' => '--:--'] + $hours, $shipment->delivery_date ? $shipment->delivery_date->format('H:i') : null , ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
</div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</div>
{{ Form::hidden('ids') }}
{{ Form::close() }}

<script>
    $('.datepicker').datepicker(Init.datepicker());
    $('#modal-remote-xs .select2').select2(Init.select2());

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.modal form.ajax-form').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                $(document).find('.dt-{{ $shipment->id }}').closest('td').html(data.html);
                Growl.success(data.feedback)
                $('#modal-remote-xs').modal('hide');
            } else {
                Growl.error(data.feedback)
            }
        }).error(function () {
            Growl.error500()
        }).always(function(){
            $button.button('reset');
        })
    });
</script>