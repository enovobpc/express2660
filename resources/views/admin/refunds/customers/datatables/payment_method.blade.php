@if(@$row->refund_control->payment_date && $row->refund_control->payment_method)
    <i class="fas fa-check-circle text-green"></i>
    <b>{{ trans('admin/refunds.payment-methods.'.$row->refund_control->payment_method) }} </b>
    <br/><i class="text-muted">{{ $row->refund_control->payment_date }}</i>
@endif

