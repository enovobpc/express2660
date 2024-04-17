<div class="btn-group">
    <a href="{{ route('admin.customer-support.show', $row->id) }}" class="btn btn-sm btn-default">
        @trans('Detalhe')
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.customer-support.edit', $row->id) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-pencil-alt"></i> @trans('Editar Informação Pedido')
            </a>
        </li>
        <li>
            <a href="{{ route('admin.customer-support.merge', $row->id) }}"
               data-toggle="modal"
               data-target="#modal-remote">
                <i class="fas fa-compress"></i> @trans('Mesclar a outro pedido')
            </a>
        </li>
        @if(!$row->user_id)
        <li>
            <a href="{{ route('admin.customer-support.adjudicate', $row->id) }}"
               data-method="post"
               data-confirm-title="@trans('Adjudicar Pedido de Suporte')"
               data-confirm-class="btn-success"
               data-confirm-label="Adjudicar"
               data-confirm="@trans('Pretende ficar responsável por este pedido de suporte?')">
                <i class="fas fa-user-plus"></i> @trans('Adjudicar-me o Pedido')
            </a>
        </li>
        @endif
        @if(@$row->shipment)
        <li>
            <a class="text-purple" href="{{ route('admin.shipments.index', ['trk' => $row->shipment->tracking_code]) }}" target="_blank">
                <i class="fas fa-fw fa-search"></i> @trans('Ir para o Envio')
            </a>
        </li>
        @endif
        <div class="divider"></div>
        <li>
            <a href="{{ route('admin.customer-support.destroy', $row->id) }}" data-method="delete"
               data-confirm="@trans('Confirma a remoção do registo selecionado?')" class="text-red">
                <i class="fas fa-trash-alt"></i> @trans('Eliminar')
            </a>
        </li>
    </ul>
</div>
