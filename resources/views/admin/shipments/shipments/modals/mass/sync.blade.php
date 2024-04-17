<div class="modal" id="modal-sync-webservice">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('route' => 'admin.shipments.selected.force-sync', 'class' => 'close-shipments', 'method' => 'POST')) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Submeter por Webservice Envios Selecionados</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-12">
                        <h4 class="message">
                            Pretende submeter por webservice os envios selecionados?<br/>
                            <small>
                                 <i class="fas fa-info-circle text-blue"></i> Envios já submetidos não serão considerados<br/>
                                 <i class="fas fa-info-circle text-blue"></i> Envios sem webservice associado não serão considerados.
                            </small>
                        </h4>
                        <h4 class="text-center loading" style="display:none">
                            <i class="fas fa-spin fa-circle-notch bigger-200"></i><br/>
                            A submeter por webservice os envios selecionados.<br/>
                            Esta operação poderá demorar algum tempo.
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-right">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="Aguarde...">Submeter Envios Selecionados</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>