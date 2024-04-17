{{ Form::open(['route' => ['admin.trips.store'], 'class' => 'create-manifest']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Criar Manifesto Entrega</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-7">
            <div class="row row-5">
                <div class="col-sm-7">
                    <div class="form-group is-required">
                        {{ Form::label('date', 'Data', ['class' => 'control-label']) }}
                        <div class="input-group">
                            {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required', 'style' => 'padding: 0 0 0 5px;']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group is-required">
                        {{ Form::label('hour', 'Hora', ['class' => 'control-label']) }}
                        {{ Form::time('hour', date('H:i'), ['class' => 'form-control hourpicker', 'required', 'style' => 'padding: 5px']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('status_id', 'Alterar envios para') }}
                {{ Form::select('status_id', $status, 4, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('operator_id', 'Motorista', ['class' => 'control-label']) }}
                {!! Form::selectWithData('operator_id', $operators, null, ['class' => 'form-control select2']) !!}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('vehicle', 'Viatura') }}
                {{ Form::select('vehicle', ['' => ''] + $vehicles, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="row row-5 manifest-details" style="display: none">
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('provider_id', 'Subcontrato') }}
                {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group">
                {{ Form::label('delivery_route_id', 'Rota') }}
                {{ Form::select('delivery_route_id', ['' => 'Automático'] + $routes, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('auxiliar_id', 'Auxiliar', ['class' => 'control-label']) }}
                {!! Form::selectWithData('auxiliar_id', $operators, null, ['class' => 'form-control select2']) !!}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group">
                {{ Form::label('trailer', 'Reboque') }}
                {{ Form::select('trailer', ['' => ''] + $trailers, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <a href="#" class="show-details">Adicionar mais detalhes <i class="fas fa-angle-down"></i></a>
    <a href="#" class="hide-details" style="display: none">Ocultar mais detalhes <i class="fas fa-angle-up"></i></a>
    <div class="row row-5">
        <div class="col-sm-12">
            <div>
                <span style="position: absolute;
                    background: #fff;
                    padding: 3px 10px;
                    margin-top: -13px;
                    margin-left: 44%;">
                ou
                </span>
                <hr style="border-color: #999"/>
            </div>
            <div class="form-group m-0">
                {{ Form::label('assign_manifest_id', 'Associar a um manifesto já existente', ['class' => 'control-label']) }}
                {{ Form::select('assign_manifest_id', ['' => ''] + $trips, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="checkbox pull-left m-t-4 m-b-0">
        <label style="padding-left: 0">
            {{ Form::checkbox('print_manifest', 1, true) }}
            Imprimir Manifesto
        </label>
    </div>
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary">Criar Manifesto</button>
    </div>
</div>
{{ Form::hidden('ids', $ids) }}
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2())

    $('.create-manifest .show-details, .create-manifest .hide-details').on('click', function(e) {
        e.preventDefault()
        $('.create-manifest .manifest-details').slideToggle();
        $('.create-manifest .show-details').toggle();
        $('.create-manifest .hide-details').toggle();
    })

    $('#modal-remote-xs [name="operator_id"]').on('change', function(){
        var vehicle = $(this).find('option:selected').data('vehicle');
        $('#modal-remote-xs [name="vehicle"]').val(vehicle).trigger('change');
    })

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('form.create-manifest').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                Growl.success(data.feedback);

                if (data.printManifest) {
                    window.open(data.printManifest, '_blank')
                }

                $('#modal-remote-xs').modal('hide');

                oTable.draw();

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
