<div class="modal" id="modal-sync-history">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('route' => 'admin.webservices.sync.history', 'class' => 'webservice-sync-history', 'method' => 'POST')) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Forçar sincronização de estados</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-12">
                        <h4 class="message">
                            Pretende forçar a sincronização de estados para os envios selecionados?
                            <br/>
                            <div class="text-muted" style="font-size: 14px; line-height: 32px;">
                                <i class="fas fa-info-circle"></i> Esta operação poderá demorar algum tempo e poderá deixar o sistema lento.
                            </div>
                        </h4>
                        <h4 class="text-center loading" style="display:none">
                            <i class="fas fa-spin fa-circle-notch bigger-200"></i><br/>
                            A sincronizar estados dos envios selecionados.<br/>Aguarde, por favor.
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-right">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="Aguarde...">Sincronizar Estados</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>