@if($row->doc_type == \App\Models\Invoice::DOC_TYPE_RC || $row->doc_type == \App\Models\Invoice::DOC_TYPE_RG)
<div class="text-muted" data-total="{{ $row->doc_total }}">
    {{ money($row->doc_total, Setting::get('app_currency')) }}
</div>
@else
<div class="bold" data-total="{{ $row->doc_total }}">
    @if(!$row->is_settle)
        <span class="text-red">{{ money($row->doc_total, Setting::get('app_currency')) }}</span>
    @else
        <span class="text-green">{{ money($row->doc_total, Setting::get('app_currency')) }}</span>
    @endif
</div>
@endif