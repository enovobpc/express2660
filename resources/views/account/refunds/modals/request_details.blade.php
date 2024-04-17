<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ trans('account/refunds.request.modal.title') }}</h4>
</div>
<div class="modal-body">
    <table class="table m-b-0">
        <tr>
            <th class="w-1">#</th>
            <th class="w-120px">Envio</th>
            <th class="w-100px">Data</th>
            <th>Destinatário</th>
            <th class="w-1">Reembolso</th>
        </tr>
        <?php $total = 0; ?>
        @foreach($shipments as $key => $shipment)
            <?php $total+= $shipment->charge_price; ?>
            <tr>
                <td><i class="text-muted">{{ $key+1 }}</i></td>
                <td>{{ $shipment->tracking_code }}</td>
                <td>{{ $shipment->date }}</td>
                <td>{{ $shipment->recipient_name }}</td>
                <td class="text-right"><b>{{ money($shipment->charge_price, Setting::get('app_currency')) }}</b></td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right">Total</td>
            <td class="text-right"><b>{{ money($total, Setting::get('app_currency')) }}</b></td>
        </tr>
    </table>
</div>
<div class="modal-footer text-right">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
    </div>
</div>