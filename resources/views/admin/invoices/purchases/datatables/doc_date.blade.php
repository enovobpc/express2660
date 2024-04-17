{{ $row->doc_date }}
@if($row->received_date)
<div class="text-muted italic" data-toggle="tooltip" title="Data de recebimento da fatura.">
    <small>{{ $row->received_date }}</small>
</div>
@endif

@if($row->doc_type == 'provider-invoice')
    <span class="label" style="background: #27aae1">Fatura Compra</span>
@elseif($row->doc_type == 'provider-invoice-receipt')
    <span class="label" style="background: #27aae1">Fatura-Recibo</span>
@elseif($row->doc_type == 'provider-simplified-invoice')
    <span class="label" style="background: #27aae1">Fatura-Simpl.</span>
@elseif($row->doc_type == 'provider-credit-note')
    <span class="label" style="background: #ed3c31">Nota Crédito</span>
@elseif($row->doc_type == 'payment-note')
    <span class="label" style="background: #97cf47">Pagamento</span>
@elseif($row->doc_type == 'provider-order')
    <span class="label" style="background: #d8a200">Encomenda</span>
@endif