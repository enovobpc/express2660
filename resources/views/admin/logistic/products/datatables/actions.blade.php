<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.logistic.products.show', $row->id) }}" class="btn btn-sm btn-default">
            @trans('Detalhe')
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.change-log.show', ['Logistic\Product', $row->id]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-lg">
                    <i class="fas fa-fw fa-history"></i> @trans('Histórico de Edições')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.logistic.products.labels', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-fw fa-print"></i> @trans('Imprimir Etiquetas')
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.logistic.products.destroy', $row->id) }}" data-method="delete"
                   data-confirm="@trans('Confirma a remoção do registo selecionado? Todas as localizações vão ser libertadas.')" class="text-red">
                    <i class="fas fa-fw fa-trash-alt"></i> @trans('Eliminar Artigo')
                </a>
            </li>
        </ul>
    </div>
</div>