@if($row->is_draft && !$row->is_scheduled)
    <span class="label label-warning">Rascunho</span>
@elseif($row->doc_type != 'nodoc')
     @if ($row->doc_type == \App\Models\Invoice::DOC_TYPE_SINC 
    || $row->doc_type == \App\Models\Invoice::DOC_TYPE_SIND)
        {{ $row->doc_series }}
    @else
        <a href="{{ route('admin.invoices.download.pdf', $row->id) }}" target="_blank">
            {{ $row->doc_series ?? trans('admin/billing.types_code.' . $row->doc_type) }} {{ $row->doc_id }}
        </a>
        
    @endif
@endif