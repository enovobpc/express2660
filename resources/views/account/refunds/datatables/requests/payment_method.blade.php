@if(@$row->payment_date)
    <span class="text-green">
        <i class="fas fa-fw fa-check-circle"></i>
        {{ str_replace(' Bancária', '', trans('admin/refunds.payment-methods.'.$row->payment_method)) }}
    </span>
@endif