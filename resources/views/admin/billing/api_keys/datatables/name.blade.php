<a href="{{ route('admin.billing.api-keys.edit', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote">
    {{ $row->name }}
    @if($row->is_default)
        <span class="label label-warning"><i class="fas fa-star"></i> Principal</span>
    @endif
</a>