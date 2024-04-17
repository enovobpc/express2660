@if($row->doc_type == 'proforma-invoice' && !$row->is_draft)
    @if($row->assigned_invoice_id)
        <a href="{{ route('admin.invoices.download.pdf', $row->assigned_invoice_id) }}" target="_blank">
            <span class="label label-success" data-toggle="tooltip" title="Proforma sem fatura gerada.">
                {{ trans('admin/billing.types_code.'. @$row->invoice->doc_type) }} {{ @$row->invoice->doc_id }}
            </span>
        </a>
    @else
        <span class="label label-danger" data-toggle="tooltip" title="Proforma sem fatura gerada.">Sem FT</span>
    @endif
@elseif(!in_array($row->doc_type, ['receipt', 'internal-doc', 'proforma-invoice']))
    @if($row->doc_type == 'nodoc' && $row->is_settle && $row->settle_method)
        <span class="label label-success" data-toggle="tooltip" data-html="true"
              title="Forma Pag.: {{ @$row->settleMethod->name ?? "" }}<br/>Data: {{ $row->settle_date }}<br/>Obs: {{ $row->settle_obs }}">Pago</span>
    @elseif(($row->doc_type != 'nodoc' && $row->is_settle) || $row->doc_total_pending > $row->doc_total)
        <span class="label label-success">Pago</span>
    @elseif($row->doc_type != 'nodoc'  && $row->doc_total_pending > 0.00 && $row->doc_total_pending < $row->doc_total)
        <span class="label label-danger">Não Pago</span>
    @else
        <span class="label label-danger">Não Pago</span>
    @endif
@endif

