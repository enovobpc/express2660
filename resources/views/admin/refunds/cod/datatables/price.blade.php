@if($row->ignore_billing || $row->invoice_doc_id)
    <strike><i>
@endif
    <a href="{{ route('admin.refunds.cod.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-xs" class="{{ !$row->ignore_billing ? 'bold' : '' }}">
        {{ money(($row->billing_total), Setting::get('app_currency')) }}
    </a>
@if($row->ignore_billing || $row->invoice_doc_id)
    </i></strike>
@endif


@if(!empty($row->requested_by) && $row->customer_id != $row->requested_by)
    <div data-toggle="tooltip" title="Paga o cliente {{ @$row->customer->code }} - {{ @$row->customer->name }}" class="text-center">
        <i class="fas fa-user"></i>
        {{ @$row->customer->code_abbrv ? @$row->customer->code_abbrv : @$row->customer->code }}
    </div>
@endif