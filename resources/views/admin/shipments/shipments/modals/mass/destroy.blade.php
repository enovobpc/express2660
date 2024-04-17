<div class="modal fade" id="modal-mass-destroy" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.shipments.selected.destroy'], 'class' => 'form-ajax', 'method' => 'post']) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Apagar selecionados</h4>
            </div>
            <div class="modal-body">
                <div class="bootbox-body">
                    <h4>Confirma a remoção dos items selecionados?</h4>
                </div>
            </div>
            <div class="modal-footer">
                <div class="checkbox pull-left m-t-4 m-b-0">
                    <label style="padding-left: 0">
                        <div class="icheckbox_minimal-blue checked" aria-checked="false" aria-disabled="false" style="position: relative;">
                            <input checked="checked" name="delete_provider" type="checkbox" value="1" style="position: absolute; opacity: 0;">
                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                        </div>
                        Eliminar envio no fornecedor se possível
                    </label>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-danger" data-loading-text="A eliminar...">Eliminar</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>