<div class="modal fade" id="modal-edit-billing" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="font-size-32px" aria-hidden="true">&times;</span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Editar Dados de Faturação</h4>
            </div>
            <div class="modal-body">
                <div class="row row-10">
                    <div class="col-sm-8">
                        <div class="form-group is-required">
                            {{ Form::label('name', 'Nome/Designação Social') }}
                            {{ Form::text('name', null, array('class' => 'form-control', 'autocomplete' => 'off')) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group is-required">
                            {{ Form::label('vat', 'NIF') }}
                            {{ Form::text('vat', null, array('class' => 'form-control', 'autocomplete' => 'off', 'required' => true)) }}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('address is-required', 'Morada') }}
                    {{ Form::text('address', null, array('class' => 'form-control', 'autocomplete' => 'off', 'required' => true)) }}
                </div>
                <div class="row row-10">
                    <div class="col-sm-3">
                        <div class="form-group is-required">
                            {{ Form::label('zip_code', 'Código Postal') }}
                            {{ Form::text('zip_code', null, array('class' => 'form-control', 'autocomplete' => 'off', 'required' => true)) }}
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group is-required">
                            {{ Form::label('city', 'Localidade') }}
                            {{ Form::text('city', null, array('class' => 'form-control', 'autocomplete' => 'off', 'required' => true)) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group is-required">
                            {{ Form::label('country', 'País') }}
                            {{ Form::select('country', trans('country'), null, array('class' => 'form-control selectpicker-search', 'autocomplete' => 'off', 'required' => true)) }}
                        </div>
                    </div>
                </div>
                <div class="row row-10">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('phone', 'Telemóvel') }}
                            {{ Form::text('phone', null, array('class' => 'form-control', 'autocomplete' => 'off')) }}
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            {{ Form::label('email', 'E-mail') }}
                            {{ Form::text('email', null, array('class' => 'form-control', 'autocomplete' => 'off')) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>