<div class="btn-group btn-table-actions" style="min-width: 1%">
    <a href="{{ route('admin.trips.show', $row->id) }}" class="btn btn-sm btn-default">
        @trans('Ver')
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Opções Extra</span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.trips.print', [$row->id, 'summary']) }}"
               target="_blank">
                <i class="fas fa-print"></i> @trans('Mapa de Viagem')
            </a>
        </li>
        @if(hasPermission('billing'))
            <li>
                <a href="{{ route('admin.trips.print', [$row->id, 'summary', 'prices' => 1]) }}" target="_blank">
                    <i class="fas fa-print"></i> @trans('Resumo Faturação')
                </a>
            </li>
        @endif
        <li>
            <a href="{{ route('admin.trips.print', [$row->id, 'delivery']) }}"
               target="_blank">
                <i class="fas fa-print"></i> @trans('Manifesto Entrega')
            </a>
        </li>

        @if ($row->type != 'R' && empty($row->parent_code) && empty($row->children_code))
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.trips.return.edit', [$row->id]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-lg"
                    class="text-green">
                    <i class="fas fa-fw fa-undo"></i> @trans('Criar mapa retorno')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.trips.return.auto', [$row->id, 'direct' => 1]) }}"
                   data-method="post"
                   data-confirm="Pretende gerar um retorno direto para este mapa?"
                   data-confirm-title="Gerar retorno diteto"
                   data-confirm-label="Gerar retorno diteto"
                   data-confirm-class="btn-success"
                   class="text-green">
                    <i class="fas fa-fw fa-undo"></i> @trans('Gerar retorno direto')
                </a>
            </li>
        @endif

        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.trips.edit', $row->id) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-pencil-alt"></i> @trans('Editar mapa')
            </a>
        </li>
        <li>
            <a href="{{ route('admin.change-log.show', ['Trip', $row->id]) }}"
               data-toggle="modal"
               data-target="#modal-remote-lg">
                <i class="fas fa-fw fa-history"></i> @trans('Histórico de edições')
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.trips.destroy', $row->id) }}" data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-trash-alt"></i> @trans('Eliminar')
            </a>
        </li>
    </ul>
</div>