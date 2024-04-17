<a href="{{ route('admin.cashier.edit', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote">
    {{ $row->description }}
</a>
@if($row->obs)
    {!! tip($row->obs) !!}
@endif