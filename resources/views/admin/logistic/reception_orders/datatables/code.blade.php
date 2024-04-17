@if(config('app.source') == 'activos24')
    {{ $row->code }}
    <div>
        <small class="italic text-muted" data-toggle="tooltip" title="Criado em: {{ $row->created_at }}">
            {{ $row->created_at->format('Y-m-d') }}
        </small>
    </div>
@else
<a href="{{ route('admin.logistic.reception-orders.edit', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl">
    {{ $row->code }}
</a>
<div>
    <small class="italic text-muted" data-toggle="tooltip" title="Criado em: {{ $row->created_at }}">
        {{ $row->created_at->format('Y-m-d') }}
    </small>
</div>
@endif