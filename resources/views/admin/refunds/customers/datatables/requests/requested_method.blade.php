@if(@$row->requested_method)
    <i class="fas fa-check-circle text-green"></i> <b>{{ trans('admin/refunds.payment-methods.'.@$row->requested_method) }} </b>
    {{--<br/><i class="text-muted">{{ $row->refund_control->received_date }}</i>--}}
@endif