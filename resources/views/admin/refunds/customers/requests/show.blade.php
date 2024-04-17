<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Envios do pedido de reembolso</h4>
</div>
<div class="modal-body">
    <table class="table m-b-0">
        <tr>
            <th class="w-120px">Envio</th>
            <th class="w-90px">Data</th>
            <th>Destinatário</th>
            <th class="w-1">Reembolso</th>
        </tr>
        @foreach($shipments as $shipment)
            <tr>
                <td>{{ $shipment->tracking_code }}</td>
                <td>{{ $shipment->date }}</td>
                <td>{{ $shipment->recipient_name }}</td>
                <td><b>{{ money($shipment->charge_price, Setting::get('app_currency')) }}</b></td>
            </tr>
        @endforeach
    </table>
</div>
<div class="modal-footer text-right">
    <div class="pull-right">
        <a href="{{ $printUrl }}" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> Imprimir</a>
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
    </div>
</div>