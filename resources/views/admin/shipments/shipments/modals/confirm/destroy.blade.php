{{ Form::open(array('route' => ['admin.shipments.destroy', $shipment->id], 'method' => 'DELETE')) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Confirmar remoção</h4>
</div>
<div class="modal-body">
    <h4>Confirma a remoção do registo selecionado?</h4>
</div>
<div class="modal-footer">
   @if((!$shipment->is_collection && in_array($shipment->webservice_method, ['gls_zeta', 'envialia', 'tipsa', 'fedex', 'nacex', 'seur', 'vasp', 'db_schenker', 'ups', 'enovo_tms', 'lfm']))
   || ($shipment->is_collection && in_array($shipment->webservice_method, ['enovo_tms','rangel', 'correos_express'])))
    <div class="checkbox pull-left m-t-4 m-b-0">
        <label style="padding-left: 0">
            {{ Form::checkbox('delete_provider', 1, true) }}
            Eliminar envio na {{ $shipment->provider->name }}
        </label>
    </div>
    @elseif(!empty($shipment->webservice_method))
        <div class="pull-left m-t-4 m-b-0 text-red">
            <i class="fas fa-exclamation-triangle"></i> Não será {{ $shipment->is_collection ? 'eliminada a recolha' : 'eliminado o envio' }} {{ $shipment->webservice_method == 'ctt' ? 'nos' : 'na' }} {{ @$shipment->provider->name }}
        </div>
    @endif
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-danger" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A anular...">Remover</button>
</div>
{{ Form::close() }}