<a href="{{ route('admin.logistic.locations.edit', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote-lg">
    <span class="text-uppercase bold"><i class="fa fa-square" style="color: {{$row->color }}"></i> {{ $row->code }}</span>
</a>