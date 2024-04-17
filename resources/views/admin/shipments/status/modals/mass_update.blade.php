<div class="modal" id="modal-mass-update">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.tracking.status.selected.update'], 'method' => 'POST']) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Alterar estados em massa</h4>
            </div>
            <div class="modal-body">
                <div class="form-group m-b-0">
                    {{ Form::label('is_visible', 'Estado') }}
                    {{ Form::select('is_visible', ['' => 'Inativar selecionados', '1' => 'Ativar selecionados'], null, ['class' => 'form-control select2']) }}
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>