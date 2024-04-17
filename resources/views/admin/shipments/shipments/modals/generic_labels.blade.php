{{ Form::open(['route' => ['admin.printer.shipments.generic-transport-guide'], 'method' => 'GET']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" style="float: right">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Griar guia genérica</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-md-6">
            <div class="row row-5">
                <div class="col-sm-8">
                    <div class="form-group m-b-5">
                        {{ Form::label('guide_date', 'Data Guia', ['class' => 'control-label']) }}
                        <div class="input-group">
                            {{ Form::text('guide_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                            <span class="input-group-addon">
                                <i class="fas fa-fw fa-calendar-alt"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group m-b-5">
                        {{ Form::label('hour', 'Hora', ['class' => 'control-label']) }}
                        {{ Form::select('hour', $hours, $lastHour, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group m-b-5">
                        {{ Form::label('weight', 'Peso', ['class' => 'control-label']) }}
                        <div class="input-group">
                            {{ Form::text('weight', 200, ['class' => 'form-control', 'required']) }}
                            <span class="input-group-addon">kg</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group m-b-5">
                        {{ Form::label('volumes', 'Volumes', ['class' => 'control-label']) }}
                        {{ Form::text('volumes', 20, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('guide_vehicles[]', 'Imprimir guia para as viaturas', ['class' => 'control-label']) }}
                {{ Form::select('guide_vehicles[]', $vehicles, array_keys($vehicles), ['class' => 'form-control select2', 'multiple', 'required']) }}
            </div>
        </div>
    </div>
    <hr class="m-t-10 m-b-5"/>
    <div class="row row-5">
        <div class="col-sm-6">
            <h4 class="text-uppercase text-blue fw-400">Expedidor</h4>
            <div class="form-group m-b-5">
                {{ Form::label('guide_sender', 'Remetente', ['class' => 'control-label']) }}
                {{ Form::text('guide_sender', Setting::get('guide_sender'), ['class' => 'form-control uppercase']) }}
            </div>
            <div class="form-group m-b-5">
                {{ Form::label('guide_sender_address', 'Morada', ['class' => 'control-label']) }}
                {{ Form::text('guide_sender_address', Setting::get('guide_sender_address'), ['class' => 'form-control uppercase']) }}
            </div>
            <div class="row row-5">
                <div class="col-sm-4">
                    <div class="form-group m-b-5">
                        {{ Form::label('guide_sender_zip_code', 'Cod. Postal', ['class' => 'control-label']) }}
                        {{ Form::text('guide_sender_zip_code', Setting::get('guide_sender_zip_code'), ['class' => 'form-control uppercase']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group m-b-5">
                        {{ Form::label('guide_sender_city', 'Localidade', ['class' => 'control-label']) }}
                        {{ Form::text('guide_sender_city', Setting::get('guide_sender_city'), ['class' => 'form-control uppercase']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group m-b-5">
                        {{ Form::label('guide_sender_country', 'País', ['class' => 'control-label']) }}
                        {{ Form::text('guide_sender_country', Setting::get('guide_sender_country'), ['class' => 'form-control uppercase', 'maxlength' => 2]) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <h4 class="text-uppercase text-blue fw-400">Destinatário</h4>
            <div class="form-group m-b-5">
                {{ Form::label('guide_recipient', 'Destinatário', ['class' => 'control-label']) }}
                {{ Form::text('guide_recipient', Setting::get('guide_recipient'), ['class' => 'form-control uppercase']) }}
            </div>
            <div class="form-group m-b-5">
                {{ Form::label('guide_recipient_address', 'Morada', ['class' => 'control-label']) }}
                {{ Form::text('guide_recipient_address', Setting::get('guide_recipient_address'), ['class' => 'form-control uppercase']) }}
            </div>
            <div class="row row-5">
                <div class="col-sm-4">
                    <div class="form-group m-b-5">
                        {{ Form::label('guide_recipient_zip_code', 'Cod. Postal', ['class' => 'control-label']) }}
                        {{ Form::text('guide_recipient_zip_code', Setting::get('guide_recipient_zip_code'), ['class' => 'form-control uppercase']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group m-b-5">
                        {{ Form::label('guide_recipient_city', 'Localidade', ['class' => 'control-label']) }}
                        {{ Form::text('guide_recipient_city', Setting::get('guide_recipient_city'), ['class' => 'form-control uppercase']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group m-b-5">
                        {{ Form::label('guide_recipient_country', 'País', ['class' => 'control-label']) }}
                        {{ Form::text('guide_recipient_country', Setting::get('guide_recipient_country'), ['class' => 'form-control uppercase', 'maxlength' => 2]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A imprimir..."><i class="fas fa-print"></i> Imprimir</button>
    </div>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
</script>