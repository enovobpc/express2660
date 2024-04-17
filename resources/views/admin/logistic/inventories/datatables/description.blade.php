<a href="{{ route('admin.logistic.devolutions.edit', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl">
    {{ @$row->description }}
</a>
@if(1)
    <br/>
    <small>
        {{ @$row->customer->name }}
    </small>
@endif