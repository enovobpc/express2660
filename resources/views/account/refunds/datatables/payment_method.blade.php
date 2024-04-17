@if(@$row->refund_control->payment_date)
    <span class="text-green">
        <i class="fas fa-fw fa-check-circle"></i>
        @if($row->refund_control->payment_method)
            {{ str_replace(' Bancária', '', trans('admin/refunds.payment-methods.'.$row->refund_control->payment_method)) }}
        @endif
    </span>
    <br/>
    <small>
        <i class="far fa-fw fa-calendar-alt"></i> {{ $row->refund_control->payment_date }}
    </small>
@elseif(@$row->refund_control->received_date && !Setting::get('refunds_request_mode'))
    <span class="text-yellow" style="display: inline-block; line-height: 1.2">
        <i class="far fa-clock"></i> {{ trans('account/refunds.word.waiting-devolution') }}
    </span>
@endif