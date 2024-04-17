<?php
if(($row->doc_type == 'credit-note' || $row->doc_type == 'receipt' || $row->doc_type == 'regularization') && $row->doc_total > 0.00) {
    $row->doc_total = $row->doc_total * -1;
}
?>

@if($row->doc_type == 'nodoc')
    <div class="text-right text-blue"
         data-subtotal="{{ $row->doc_subtotal }}"
         data-vat="{{ $row->doc_vat }}"
         data-total="{{ $row->doc_total }}">
        <b>{{ money($row->doc_subtotal, '€') }}</b>
    </div>
@else
    <div class="text-right text-blue"
         data-subtotal="{{ $row->doc_subtotal }}"
         data-vat="{{ $row->doc_vat }}"
         data-total="{{ $row->doc_total }}">
        <b>{{ money($row->doc_total, '€') }}</b>
    </div>
@endif

@if($row->is_deleted || $row->is_reversed)
    @if($row->is_reversed || $row->credit_note_id)
    <span class="label label-warning" data-toggle="tooltip" data-html="true" title="{{ $row->delete_date }}<br/>Motivo: {{ $row->delete_reason }}">
        <i class="fa fa-copy"></i> ESTORNADO
    </span>
    @else
    <span class="label label-danger" data-toggle="tooltip" data-html="true" title="{{ $row->delete_date }}<br/>Motivo: {{ $row->delete_reason }}">
        <i class="fa fa-exclamation-triangle"></i> ANULADO
    </span>
    @endif
@endif