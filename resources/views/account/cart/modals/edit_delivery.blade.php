<div class="modal fade" id="modal-edit-shipment" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="font-size-32px" aria-hidden="true">&times;</span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Editar Morada de Entrega</h4>
            </div>
            <div class="modal-body">
                <span id="delivery-details">
                    <div class="form-group is-required">
                        {{ Form::label('delivery_name', 'Destinatário') }}
                        {{ Form::text('delivery_name', null, array('class' => 'form-control', 'autocomplete' => 'off')) }}
                    </div>
                    <div class="form-group is-required">
                        {{ Form::label('delivery_address', 'Morada') }}
                        {{ Form::text('delivery_address', null, array('class' => 'form-control', 'autocomplete' => 'off')) }}
                    </div>
                    <div class="row row-10">
                        <div class="col-sm-3">
                            <div class="form-group is-required">
                                {{ Form::label('delivery_zip_code', 'Código Postal') }}
                                {{ Form::text('delivery_zip_code', null, array('class' => 'form-control', 'autocomplete' => 'off')) }}
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group is-required">
                                {{ Form::label('delivery_city', 'Localidade') }}
                                {{ Form::text('delivery_city', null, array('class' => 'form-control', 'autocomplete' => 'off')) }}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group is-required">
                                {{ Form::label('delivery_country', 'País') }}
                                {{ Form::select('delivery_country', trans('country'), 'pt', array('class' => 'form-control selectpicker-search', 'autocomplete' => 'off')) }}
                            </div>
                        </div>
                    </div>
                    <div class="row row-10">
                        <div class="col-sm-4">
                            <div class="form-group is-required">
                                {{ Form::label('delivery_phone', 'Telemóvel') }}
                                {{ Form::text('delivery_phone', null, array('class' => 'form-control', 'autocomplete' => 'off')) }}
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                {{ Form::label('delivery_email', 'E-mail') }}
                                {{ Form::text('delivery_email', null, array('class' => 'form-control', 'autocomplete' => 'off')) }}
                            </div>
                        </div>
                    </div>
                </span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>