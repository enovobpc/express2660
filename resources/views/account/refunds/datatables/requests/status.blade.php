@if($row->status == 'refunded' || !empty($row->payment_method))
    <span class="label label-success">Reembolsado</span>
@elseif($row->status == 'requested')
    <span class="label label-warning">Solicitado</span>
@elseif($row->status == 'canceled')
    <span class="label label-danger">Cancelado</span>
@endif