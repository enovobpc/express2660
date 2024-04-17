@if(@$row->refund_control->received_method)
    <span class="text-green">
        <i class="fas fa-fw fa-check-circle"></i> {{ str_replace(' Bancária', '', trans('admin/refunds.payment-methods.'.$row->refund_control->received_method)) }}
    </span>
    <br/>
    <small>
        <i class="far fa-fw fa-calendar-alt"></i> {{ $row->refund_control->received_date }}
    </small>
@else
    <span class="text-yellow" style="display: inline-block; line-height: 1.2">
        <i class="far fa-clock"></i> {{ trans('account/refunds.word.waiting-reception') }}
    </span>
@endif