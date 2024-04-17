<div class="box no-border">
    <div class="box-body p-t-0">
        <div class="col-sm-6">
            <h4 class="section-title">Definições Guias Globais</h4>
            <div class="row">
                <div class="col-sm-6" style="padding-right: 20px">
                    <h4 class="m-b-0" style="margin-left: -15px">Expedidor</h4>
                    <div class="form-group m-b-5">
                        {{ Form::label('guide_sender', 'Remetente', ['class' => 'control-label']) }}
                        {{ Form::text('guide_sender', Setting::get('guide_sender'), ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group m-b-5">
                        {{ Form::label('guide_sender_address', 'Morada', ['class' => 'control-label']) }}
                        {{ Form::text('guide_sender_address', Setting::get('guide_sender_address'), ['class' => 'form-control']) }}
                    </div>
                    <div class="row row-5">
                        <div class="col-sm-4">
                            <div class="form-group m-b-5">
                                {{ Form::label('guide_sender_zip_code', 'Cod. Postal', ['class' => 'control-label']) }}
                                {{ Form::text('guide_sender_zip_code', Setting::get('guide_sender_zip_code'), ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group m-b-5">
                                {{ Form::label('guide_sender_city', 'Localidade', ['class' => 'control-label']) }}
                                {{ Form::text('guide_sender_city', Setting::get('guide_sender_city'), ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group m-b-5">
                                {{ Form::label('guide_sender_country', 'País', ['class' => 'control-label']) }}
                                {{ Form::text('guide_sender_country', Setting::get('guide_sender_country'), ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6" style="padding-left: 20px">
                    <h4 class="m-b-0" style="margin-left: -15px">Destinatário</h4>
                    <div class="form-group m-b-5">
                        {{ Form::label('guide_recipient', 'Destinatário', ['class' => 'control-label']) }}
                        {{ Form::text('guide_recipient', Setting::get('guide_recipient'), ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group m-b-5">
                        {{ Form::label('guide_recipient_address', 'Morada', ['class' => 'control-label']) }}
                        {{ Form::text('guide_recipient_address', Setting::get('guide_recipient_address'), ['class' => 'form-control']) }}
                    </div>
                    <div class="row row-15">
                        <div class="col-sm-4">
                            <div class="form-group m-b-5">
                                {{ Form::label('guide_recipient_zip_code', 'Cod. Postal', ['class' => 'control-label']) }}
                                {{ Form::text('guide_recipient_zip_code', Setting::get('guide_recipient_zip_code'), ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group m-b-5">
                                {{ Form::label('guide_recipient_city', 'Localidade', ['class' => 'control-label']) }}
                                {{ Form::text('guide_recipient_city', Setting::get('guide_recipient_city'), ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group m-b-5">
                                {{ Form::label('guide_recipient_country', 'País', ['class' => 'control-label']) }}
                                {{ Form::text('guide_recipient_country', Setting::get('guide_recipient_country'), ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <hr/>
            {{ Form::submit('Gravar', array('class' => 'btn btn-primary' ))}}
        </div>
    </div>
</div>

