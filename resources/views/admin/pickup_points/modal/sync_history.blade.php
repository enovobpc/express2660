<div class="modal" id="modal-sync-pudo">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(array('route' => 'admin.webservices.sync.pickup-points', 'class' => 'webservice-sync-pickup-points', 'method' => 'POST')) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Forçar sincronização de PUDOs')</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-12">
                        <h4 class="message">
                            @trans('Pretende forçar a sincronização de PUDOs?')'
                            <br/>
                            <div class="text-muted" style="font-size: 14px; line-height: 16px; margin-top: 10px;">
                                <i class="fas fa-info-circle"></i> @trans('Esta operação poderá demorar algum tempo e poderá deixar o sistema lento.')'
                            </div>
                        </h4>
                        <h4 class="text-center loading" style="display:none">
                            <i class="fas fa-spin fa-circle-notch bigger-200"></i><br/>
                            @trans('A sincronizar estados dos envios selecionados.')<br/>@trans('Aguarde, por favor.')'
                        </h4>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group  is-required">
                            {{ Form::label('provider_id', __('Fornecedor')) }}
                            {{ Form::select('provider_id', ['' => ''] + $syncProviders, null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-right">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Cancelar')</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="Aguarde...">@trans('Sincronizar Pontos Pickup')</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>