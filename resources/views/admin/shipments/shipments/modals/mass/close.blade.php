<div class="modal fade" id="modal-close-shipments">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('route' => 'admin.shipments.selected.close-shipment', 'class' => 'close-shipments', 'method' => 'POST')) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Fechar Envios Selecionados</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-12">
                        <h4 class="message">
                            Pretende fechar os envios selecionados?
                        </h4>
                        <h4 class="text-center loading" style="display:none">
                            <i class="fas fa-spin fa-circle-notch bigger-200"></i><br/>
                            A fechar envios selecionados.<br/>
                            Esta operação poderá demorar algum tempo.
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-right">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="Aguarde...">Fechar Envios Selecionados</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>