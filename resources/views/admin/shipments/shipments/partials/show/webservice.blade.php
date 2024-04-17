<div class="spacer-5"></div>
<h4 class="m-t-0"><i class="fas fa-plug"></i> Estado da conexão atual - Webservice {{ ucwords(str_replace('_', ' ', $shipment->webservice_method)) }}</h4>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group m-b-10">
            <label class="col-sm-3 control-label p-l-0 p-r-5 m-t-5 text-right">Nº Envio {{ ucwords(str_replace('_', ' ', $shipment->webservice_method)) }}</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    {{ @$shipment->provider_tracking_code }}
                </p>
            </div>
        </div>
        <div class="form-group m-b-10">
            <label class="col-sm-3 control-label p-l-0 p-r-5 m-t-5 text-right">Agência Origem</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    {{ @$shipment->provider_sender_agency }}
                </p>
            </div>
        </div>
        <div class="form-group m-b-10">
            <label class="col-sm-3 control-label p-l-0 p-r-5 m-t-5 text-right">Agência Pagadora</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    {{ @$shipment->provider_cargo_agency }}
                </p>
            </div>
        </div>
        <div class="form-group m-b-10">
            <label class="col-sm-3 control-label p-l-0 p-r-5 m-t-5 text-right">Agência Destino</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    {{ @$shipment->provider_recipient_agency }}
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        @if($shipment->webservice_method)
        <div class="form-group m-b-10">
            <label class="col-sm-3 control-label p-l-0 p-r-5 m-t-5 text-right">Ligação via</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    {{ ucwords(str_replace('_', ' ', $shipment->webservice_method)) }}
                </p>
            </div>
        </div>
        <div class="form-group m-b-10">
            <label class="col-sm-3 control-label p-l-0 p-r-5 m-t-5 text-right">Submetido em</label>
            <div class="col-sm-9">
                @if($shipment->submited_at)
                <p class="form-control-static">
                    {{ @$shipment->submited_at }}
                </p>
                @else
                <p class="form-control-static text-red">
                    <i class="fas fa-exclamation-triangle"></i> Submissão falhada
                </p>
                @endif
            </div>
        </div>
        
        <div class="form-group m-b-10">
            <label class="col-sm-3 control-label p-l-0 p-r-5 m-t-5 text-right">Motivo do Erro</label>
            <div class="col-sm-9">
                <p class="form-control-static" style="min-height: 65px;">
                    @if($shipment->webservice_error)
                        <span class="text-red"><i class="fas fa-exclamation-circle"></i> {{ @$shipment->webservice_error }}</span>
                    @else
                        <span class="text-green"><i class="fas fa-check"></i> Submetido sem erros</span>
                    @endif
                </p>
                @if($shipment->hasSyncError())
                    <a href="{{ route('admin.shipments.sync.manual', $shipment->id) }}" class="btn btn-sm btn-success btn-sync" data-loading-text="<i class='fas fa-spin fa-sync-alt'></i> A submeter...">
                        <i class="fas fa-sync-alt"></i> Forçar submissão via webservice
                    </a>
                @endif
            </div>
        </div>
        @else
        <p class="text-muted text-center m-t-40"><i class="fas fa-info-circle bigger-140"></i><br/>Não foi detectado nenhum webservice associado a este envio.</p>
        @endif

    </div>
</div>