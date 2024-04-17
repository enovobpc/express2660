{{ Form::model($shipment, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Reagendar Envio</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('recipient_address', 'Morada') }}
                {{ Form::text('recipient_address', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('recipient_zip_code', 'Código Postal') }}
                {{ Form::text('recipient_zip_code', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('recipient_city', 'Localidade') }}
                {{ Form::text('recipient_city', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('date', 'Data Entrega') }}
                {{ Form::text('date', null, ['class' => 'form-control trigger-price datepicker', 'required']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('obs', 'Observação') }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => '2', 'maxlength' => 150]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}