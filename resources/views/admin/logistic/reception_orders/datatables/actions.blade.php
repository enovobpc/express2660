<div class="btn-group btn-table-actions">
    @if(config('app.source') == 'activos24')
        <a href="{{ route('admin.logistic.reception-orders.print', $row->id) }}"
           class="btn btn-sm btn-default"
           target="_blank">
           @trans('Imprimir')
        </a>
    @else
    <a href="{{ route('admin.logistic.reception-orders.confirmation.edit', $row->id) }}"
       class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote-xl">
        @if($row->status_id == \App\Models\Logistic\ReceptionOrderStatus::STATUS_CONCLUDED)
            <i class="fas fa-search"></i> @trans('Detalhe')
        @else
            <b>@trans('Picking In')</b>
        @endif
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.logistic.reception-orders.edit', $row->id) }}"
               data-toggle="modal"
               data-target="#modal-remote-xl">
                @if($row->status_id != \App\Models\Logistic\ReceptionOrderStatus::STATUS_CONCLUDED)
                    <i class="fas fa-fw fa-pencil-alt"></i> @trans('Editar')
                @else
                    <i class="fas fa-fw fa-search"></i> @trans('Ver Pedido')
                @endif
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.logistic.reception-orders.print', $row->id) }}" target="_blank">
                <i class="fas fa-fw fa-print"></i> @trans('Imprimir')
            </a>
        </li>

        @if($row->status_id != \App\Models\Logistic\ReceptionOrderStatus::STATUS_CONCLUDED)
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.logistic.reception-orders.destroy', $row->id) }}"
               data-method="delete"
               data-confirm="@trans('Confirma a remoção do registo selecionado?')"
               class="text-red">
                <i class="fas fa-fw fa-trash-alt"></i> @trans('Eliminar')
            </a>
        </li>
        @endif
    </ul>
    @endif
</div>
