@if(@$row->refund_control->payment_date)
    @if(@$row->refund_control->confirmed)
        <i class="fas fa-check-circle text-green" data-toggle="tooltip" title="O cliente confirmou a recepção do valor."></i>
    @else
        <i class="fas fa-times-circle text-muted" data-toggle="tooltip" title="O cliente não confirmou a recepção do valor."></i>
    @endif
@endif
