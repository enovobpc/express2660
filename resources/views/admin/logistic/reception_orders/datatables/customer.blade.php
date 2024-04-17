@if(config('app.source') == 'activos24')
    {{ @$row->customer->name }}
@else
<a href="{{ route('admin.logistic.reception-orders.edit', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote-lg">
    {{ @$row->customer->name }}
</a>
@endif