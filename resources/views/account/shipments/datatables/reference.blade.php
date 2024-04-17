<div style="word-break: break-all;">
    <div>{{ $row->reference }}</div>
    @if($row->reference2 && Setting::get('shipments_reference2_visible'))
        <div>{{ $row->reference2 }}</div>
    @endif
    @if($row->reference3 && Setting::get('shipments_reference3_visible'))
        <div>{{ $row->reference3 }}</div>
    @endif
</div>