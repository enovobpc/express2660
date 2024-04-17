<div class="text-center">
    <a data-toggle="modal" data-target="#modal-remote" href="{{ route('admin.brands.models.index', $row->id) }}">
        {{ $row->models->count() }}
    </a>
</div>
