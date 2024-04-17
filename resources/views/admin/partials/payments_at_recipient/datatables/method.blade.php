@if(@$row->payment_at_recipient_control->method)
<i class="fas fa-check-circle text-green"></i> <b>{{ trans('admin/shipments.charge_payment_methods.'.$row->payment_at_recipient_control->method) }} </b>
    <br/><i class="text-muted">{{ $row->payment_at_recipient_control->paid_at }}</i>
@endif