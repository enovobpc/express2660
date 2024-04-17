@if($row->payment_date)
    {{ $row->payment_date }}
    <br/>
    <small class="text-muted">
        {{ trans('admin/refunds.payment-methods.' . $row->payment_method) }}
    </small>
@endif
