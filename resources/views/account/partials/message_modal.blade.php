<div class="modal" id="modal-message-{{ $messagePopup->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['account.messages.read', $messagePopup->id], 'method' => 'POST','class' => 'ajax-form']) }}
            <div class="modal-header" style="padding: 12px">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-info-circle"></i> {{ $messagePopup->subject }}</h4>
            </div>
            <div class="modal-body">
                <p class="line-height-1p5">{!! nl2br($messagePopup->message) !!}</p>
            </div>
            <div class="modal-footer" style="padding: 10px">
                <div class="pull-left">
                    <div class="checkbox pull-left m-b-0 m-t-5">
                        <label style="padding-left: 0; display: block; margin-top: -1px;">
                            {{ Form::checkbox('is_read', 1) }}
                            Não voltar a apresentar esta mensagem
                        </label>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-default btn-cancel pull-right" data-dismiss="modal">Fechar</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>