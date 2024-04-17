@if($row->action == 'order_out' && $row->document_id)
    <a href="{{ route('admin.logistic.shipping-orders.show', $row->document_id) }}"
       data-toggle="modal"
       data-target="#modal-remote-lg">
       @trans('Ordem Saída') {{ @$row->shipping_order->code }}
    </a>
@elseif($row->action == 'add' && $row->document_id)
    <a href="{{ route('admin.logistic.reception-orders.confirmation.edit', $row->document_id) }}"
       data-toggle="modal"
       data-target="#modal-remote-xl">
       @trans('Recepção') {{ @$row->reception_order->code }}
    </a>
@elseif($row->action == 'devolution' && $row->document_id)
    <a href="{{ route('admin.logistic.devolutions.edit', $row->document_id) }}"
       data-toggle="modal"
       data-target="#modal-remote-xl">
       @trans('Devolução') {{ @$row->devolution->code }}
    </a>
@elseif($row->action == 'inventory' && $row->document_id)
    <a href="{{ route('admin.logistic.inventories.edit', $row->document_id) }}"
       data-toggle="modal"
       data-target="#modal-remote-xl">
       @trans('Inventário') {{ @$row->inventory->code }}
    </a>
@endif
<div>{{ $row->document }}</div>