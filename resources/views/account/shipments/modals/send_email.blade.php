
        <div class="modal-content">
            {{ Form::open(['route' => ['account.shipments.email.send', $shipment->id]]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Enviar Novo Email de Envio</h4>
            </div>
            <div class="modal-body">
                <div class="form-group w-100">
                    {{ Form::label('email', 'Enviar novamente o email sobre o envio') }}<br/>
                    {{ Form::text('email', $shipment->recipient_email, ['class' => 'form-control w-100']) }}
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">Enviar E-mail</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>