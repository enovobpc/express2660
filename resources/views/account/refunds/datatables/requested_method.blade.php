@if(@$row->refund_control->requested_method)
    <span class="text-green">
        <i class="fas fa-fw fa-check-circle"></i> {{ str_replace(' Bancária', '', trans('admin/refunds.payment-methods.'.$row->refund_control->requested_method)) }}
    </span>
    <br/>
    <small>
        <i class="far fa-fw fa-calendar-alt"></i> {{ $row->refund_control->requested_date }}
    </small>
@endif