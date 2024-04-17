<a href="{{ route('admin.equipments.show', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote-lg">
    <b>{{ @$row->name }}</b>
</a>
<br/>
<small class="text-muted"><i>{{ @$row->customer->name }}</i></small>