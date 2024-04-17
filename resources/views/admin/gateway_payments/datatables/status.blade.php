@if($row->status == App\Models\GatewayPayment\Base::STATUS_SUCCESS)
    <span class="label label-success">Aceite</span>
@elseif($row->status == App\Models\GatewayPayment\Base::STATUS_WAINTING)
    <span class="label label-warning">Aguarda</span>
@elseif($row->status == App\Models\GatewayPayment\Base::STATUS_REJECTED)
    <span class="label label-danger">Rejeitado</span>
@else
    <span class="label" style="background-color: #777">Pendente</span>
@endif