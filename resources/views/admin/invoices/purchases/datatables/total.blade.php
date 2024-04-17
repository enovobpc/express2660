<b class="text-blue" data-total="{{ $row->total }}">
    {{ money($row->total, $row->currency) }}
</b>
{{--
@if($row->doc_type != 'payment-note')
<br/>
<small class="text-muted">IVA: {{ money($row->vat_total, $row->currency) }}</small>
@endif--}}
