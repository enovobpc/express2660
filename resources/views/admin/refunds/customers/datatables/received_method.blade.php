@if(@$row->refund_control->received_method && @$row->refund_control->received_date)
    @if($row->refund_control->received_method == 'claimed')
    <span class="text-red">
        <i class="fas fa-times-circle"></i>
        <b>Reclamado</b>
    </span>
    <br/>
    <i class="text-muted">{{ $row->refund_control->received_date }}</i>
    @else
    <i class="fas fa-check-circle text-green"></i> <b>{{ trans('admin/refunds.payment-methods.'.@$row->refund_control->received_method) }} </b>
    <br/><i class="text-muted">{{ $row->refund_control->received_date }}</i>
    @endif
@elseif($row->refund_method)
    <br/>
    <small class="text-muted italic" data-toggle="tooltip" title="Recebimento pelo Motorista">Prev. {{ trans('admin/refunds.payment-methods.'.$row->refund_method) }}</small>
@endif