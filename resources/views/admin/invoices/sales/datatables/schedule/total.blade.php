@if($row->doc_type == 'nodoc')
    <div class="text-right text-blue"
         data-subtotal="{{ $row->doc_subtotal }}"
         data-vat="{{ $row->doc_vat }}"
         data-total="{{ $row->doc_total }}">
        <b>{{ money($row->doc_subtotal, '€') }}</b>
    </div>
@else
    <div class="text-right">
        {{ money($row->doc_subtotal, '€') }}
    </div>
    <div class="text-right text-blue"
         data-subtotal="{{ $row->doc_subtotal }}"
         data-vat="{{ $row->doc_vat }}"
         data-total="{{ $row->doc_total }}">
        <b>{{ money($row->doc_total, '€') }}</b>
    </div>
@endif