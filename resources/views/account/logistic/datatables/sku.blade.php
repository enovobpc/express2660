<div style="word-break: break-word">
    {{ $row->sku }}

    @if (config('app.source') == 'activos24' && $row->customer_ref)
        <br />
        <small class="italic">{{ $row->customer_ref }}</small>
    @endif
</div>
