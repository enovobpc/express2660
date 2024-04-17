@if(@$row->payment_method)
    <i class="fas fa-check-circle text-green"></i>
    <b>{{ trans('admin/refunds.payment-methods.'.$row->payment_method) }}</b>
@endif

