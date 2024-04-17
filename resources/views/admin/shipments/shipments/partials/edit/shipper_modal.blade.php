<div class="modal" id="modal-edit-shipper">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Expedidor e Destinatário</h4>
            </div>
            <div class="modal-body">
                <small class="pull-right text-muted italic"><i class="fas fa-info-circle"></i> Se vazio, serão assumidos os dados do cliente.</small>
                <h4 class="bold text-blue m-t-0 text-uppercase">Expedidor</h4>
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-2 control-label p-r-0" for="shipper_name">
                        Designação
                    </label>
                    <div class="col-sm-10">
                        {{ Form::text('shipper_name', null, ['class' => 'form-control input-sm uppercase search-shipper', 'autocomplete' => 'field-1']) }}
                    </div>
                </div>
                
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-2 control-label p-r-0" for="shipper_address">
                        Morada <i class="fas fa-spin fa-circle-notch hide"></i>
                    </label>
                    <div class="col-sm-10">
                        {{ Form::text('shipper_address', null, ['class' => 'form-control input-sm uppercase', 'autocomplete' => 'field-1']) }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('shipper_zip_code', 'Cód. Postal', ['class' => 'col-sm-2 control-label p-r-0']) }}
                    <div class="col-sm-3 p-r-0">
                        <div class="row row-0 row-state">
                            <div class="col-sm-12">
                                {{ Form::text('shipper_zip_code', null, ['class' => 'form-control trigger-price input-sm uppercase zip-code', 'autocomplete' => 'field-1']) }}
                            </div>
                        </div>
                    </div>
                    {{ Form::label('shipper_city', 'Localidade', ['class' => 'col-sm-2 control-label p-r-0']) }}
                    <div class="col-sm-5">
                        {{ Form::text('shipper_city', null, ['class' => 'form-control input-sm uppercase', 'autocomplete' => 'field-1']) }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('shipper_country', 'País', ['class' => 'col-sm-2 control-label p-r-0']) }}
                    <div class="col-sm-3 p-r-0">
                        {{ Form::select('shipper_country', ['' => ''] + trans('country'), null, ['class' => 'form-control trigger-price select2-country']) }}
                    </div>
                    {{ Form::label('shipper_phone', 'Telefone', ['class' => 'col-sm-2 control-label p-r-0']) }}
                    <div class="col-sm-5">
                        {{ Form::text('shipper_phone', null, ['class' => 'form-control phone']) }}
                    </div>
                </div>
                <small class="pull-right text-muted italic m-t-27"><i class="fas fa-info-circle"></i> Se vazio, serão assumidos os dados do local descarga.</small>
                <h4 class="bold text-blue m-t-15 p-t-15 text-uppercase" style="border-top: 1px solid #ddd">Destinatário</h4>
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-2 control-label p-r-0" for="receiver_name">Designação</label>
                    <div class="col-sm-10">
                        {{ Form::text('receiver_name', null, ['class' => 'form-control input-sm search-receiver uppercase', 'autocomplete' => 'field-1']) }}
                    </div>
                </div>
                
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-2 control-label p-r-0" for="receiver_address">
                        Morada <i class="fas fa-spin fa-circle-notch hide"></i>
                    </label>
                    <div class="col-sm-10">
                        {{ Form::text('receiver_address', null, ['class' => 'form-control input-sm uppercase', 'autocomplete' => 'field-1']) }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('receiver_zip_code', 'Cód. Postal', ['class' => 'col-sm-2 control-label p-r-0']) }}
                    <div class="col-sm-3 p-r-0">
                        <div class="row row-0 row-state">
                            <div class="col-sm-12">
                                {{ Form::text('receiver_zip_code', null, ['class' => 'form-control trigger-price input-sm uppercase zip-code', 'autocomplete' => 'field-1']) }}
                            </div>
                        </div>
                    </div>
                    {{ Form::label('receiver_city', 'Localidade', ['class' => 'col-sm-2 control-label p-r-0']) }}
                    <div class="col-sm-5">
                        {{ Form::text('receiver_city', null, ['class' => 'form-control input-sm uppercase', 'autocomplete' => 'field-1']) }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('receiver_country', 'País', ['class' => 'col-sm-2 control-label p-r-0']) }}
                    <div class="col-sm-3 p-r-0">
                        {{ Form::select('receiver_country', ['' => ''] + trans('country'), null, ['class' => 'form-control trigger-price select2-country']) }}
                    </div>
                    {{ Form::label('receiver_phone', 'Telefone', ['class' => 'col-sm-2 control-label p-r-0']) }}
                    <div class="col-sm-5">
                        {{ Form::text('receiver_phone', null, ['class' => 'form-control phone']) }}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-primary btn-confirm-shipper" data-answer="1">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</div>