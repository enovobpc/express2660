@if($row->customer->id != $customerId)
    {{ @$row->customer->code }} - {{ @$row->customer->name }}
@endif