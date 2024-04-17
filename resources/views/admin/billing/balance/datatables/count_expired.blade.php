@if($row->balance_unpaid_count)
    {{ $row->balance_unpaid_count }} doc.<br/>
    @if($row->balance_expired_count)
        <small>
            <span class="text-red" data-toggle="tooltip" title="Documentos Vencidos">{{ $row->balance_expired_count }} Venc.</span>
        </small>
    @endif
@endif