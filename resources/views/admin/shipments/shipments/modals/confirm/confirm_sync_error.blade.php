<div class="modal" id="modal-confirm-sync-error">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header bg-red">
                <h4 class="modal-title">Erro de submissão</h4>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-red fs-40"></i>
                    {{--<h4>
                        Ocorreu um erro ao tentar sincronizar o envio.<br/>
                        <small>O envio foi gravado mas não foi possível submeter ao fornecedor <span class="error-provider"></span></small>
                    </h4>--}}
                    <h4>
                        <span class="error-provider"></span>: Documentação Rejeitada<br/>
                        <small>
                            O fornecedor não permitiu a gravação do envio pelo motivo indicado abaixo.
                            <a href="{{ knowledgeArticle(112) }}" target="_blank">Saber Mais</a>
                        </small>
                    </h4>
                </div>
                <div class="sync-reason text-red bold text-center error-msg"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm btn-confirm-no">Corrigir Erro</button>
                <button type="button" class="btn btn-default btn-sm btn-confirm-yes">Ignorar e corrigir depois</button>
            </div>
        </div>
    </div>
</div>