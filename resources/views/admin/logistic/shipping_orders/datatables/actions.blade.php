@if(config('app.source') == 'activos24')
    <div class="btn-group" disabled>
        <button class="btn btn-sm btn-default" disabled>
            @trans('Editar')
        </button>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" disabled>
            <span class="caret"></span>
            <span class="sr-only"></span>
        </button>
    </div>
@else
<div class="btn-group btn-table-actions">
    @if(in_array($row->status_id, [\App\Models\Logistic\ShippingOrderStatus::STATUS_PENDING, \App\Models\Logistic\ShippingOrderStatus::STATUS_PROCESSING]))
        <a href="{{ route('admin.logistic.shipping-orders.confirmation.edit', $row->id) }}"
           class="btn btn-sm btn-default"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            <b>@trans('Picking Out')</b>
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only"></span>
        </button>
    @else
        <a href="{{ route('admin.logistic.shipping-orders.show', $row->id) }}"
           class="btn btn-sm btn-default"
           data-toggle="modal"
           data-target="#modal-remote-lg">
                <i class="fas fa-search"></i> @trans('Detalhe')
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only"></span>
        </button>
    @endif

    <ul class="dropdown-menu pull-right">
        @if(in_array($row->status_id, [\App\Models\Logistic\ShippingOrderStatus::STATUS_PENDING, \App\Models\Logistic\ShippingOrderStatus::STATUS_PROCESSING]))
        <li>
            <a href="{{ route('admin.logistic.shipping-orders.edit', $row->id) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-fw fa-pencil-alt"></i> @trans('Editar Ordem Saída')
            </a>
        </li>
        @else
        <li>
            <a href="{{ route('admin.logistic.shipping-orders.show', $row->id) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-fw fa-search"></i> @trans('Ver Detalhe')
            </a>
        </li>
        @endif
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.logistic.shipping-orders.print.summary', $row->id) }}" target="_blank">
                <i class="fas fa-fw fa-print"></i> @trans('Imprimir Packing List')
            </a>
        </li>
        <li>
            <a href="{{ route('admin.logistic.shipping-orders.print.label', $row->id) }}" target="_blank">
                <i class="fas fa-fw fa-print"></i> @trans('Imprimir Etiqueta')
            </a>
        </li>
        @if($row->status_id == \App\Models\Logistic\ShippingOrderStatus::STATUS_CONCLUDED)
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.logistic.devolutions.create', ['order' => $row->id, 'direct-creation' => '1']) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-fw fa-arrow-left"></i> @trans('Criar Devolução')
                </a>
            </li>
        @endif
        <li class="divider"></li>
        @if($row->shipment_id && @$row->shipment->tracking_code)
            <li>
                <a href="{{ route('admin.shipments.edit', $row->shipment_id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-fw fa-truck"></i> @trans('Editar Envio')
                </a>
            </li>
        @else
        <li>
            <a href="{{ route('admin.shipments.create', ['logistic-shipping-order' => $row->id]) }}"
                data-toggle="modal"
                data-target="#modal-remote-xl">
                <i class="fas fa-fw fa-truck"></i> @trans('Criar Envio')
            </a>
        </li>
        @endif
        @if(in_array($row->status_id, [\App\Models\Logistic\ShippingOrderStatus::STATUS_PENDING, \App\Models\Logistic\ShippingOrderStatus::STATUS_PROCESSING]))
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.logistic.shipping-orders.destroy', $row->id) }}" data-method="delete"
               data-confirm="@trans('Confirma a remoção do registo selecionado?')" class="text-red">
                <i class="fas fa-fw fa-trash-alt"></i> @trans('Eliminar')
            </a>
        </li>
        @endif
    </ul>
</div>
@endif
