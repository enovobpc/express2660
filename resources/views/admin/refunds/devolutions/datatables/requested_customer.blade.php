@if($row->requested_by)
    {{ @$row->customer_requested->code }} - {{ @$row->customer_requested->name }}
@else
    {{ @$row->customer->code }} - {{ @$row->customer->name }}
@endif
