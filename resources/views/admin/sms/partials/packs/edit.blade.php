{{ Form::model($pack, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-10">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('total_sms', 'SMS Totais') }}
                {{ Form::text('total_sms', null, ['class' => 'form-control number', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('remaining_sms', 'SMS Disponíveis') }}
                {{ Form::text('remaining_sms', null, ['class' => 'form-control number', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('price_un', 'Preço/SMS') }}
                {{ Form::text('price_un', null, ['class' => 'form-control decimal', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="checkbox p-t-15">
                <label>
                    {{ Form::checkbox('is_active', 1) }}
                    Pacote Ativo
                </label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button class="btn btn-primary btn-submit">Gravar</button>
</div>
{{ Form::close() }}
