<div class="modal" id="modal-change-operator-confirm">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.shipments.history.selected.update']]) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="float: right">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Alterar Operador</h4>
            </div>
            <div class="modal-body p-10">
                <h4>Pretende reiniciar a picagem para o motorista selecionado?</h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">Não</button>
                    <button type="button" class="btn btn-primary" data-answer="1">Sim</button>
                </div>
            </div>
            {{ Form::hidden('ids') }}
            {{ Form::close() }}
        </div>
    </div>
</div>