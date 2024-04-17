@if (isset($withoutLink))
{{ $row->name }}
@else
<a data-toggle="modal" data-target="#modal-remote" href="{{ route('admin.billing.items.edit', $row->id) }}">
    {{ $row->name }}
</a>
@endif

<div class="text-muted italic">
    {{ @$row->brand->name }} {{ @$row->brandModel->name }}
</div>
