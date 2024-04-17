<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.fleet.vehicles.edit', $row->id) }}" class="btn btn-sm btn-default">
            <i class="fas fa-pencil-alt"></i> @trans('Editar')
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">@trans('Opções Extra')</span>
        </button>
        <ul class="dropdown-menu pull-right">
            {{--<li>
                <a href="{{ route('admin.customers.inactivate', $row->id) }}" data-method="post" data-confirm-title="Inativar Cliente" data-confirm-class="btn-success" data-confirm-label="Inativar" data-confirm="Pretende inativar o cliente envia-lo para a lista de prospects?">
                    <i class="fas fa-user-times"></i> Registar Abastecimento
                </a>
            </li>
            <div class="divider"></div>--}}
            <li>
                <a href="{{ route('admin.fleet.vehicles.destroy', $row->id) }}" data-method="delete"
                   data-confirm="@trans('Confirma a remoção do registo selecionado?')" class="text-red">
                    <i class="fas fa-trash-alt"></i> @trans('Eliminar')
                </a>
            </li>
        </ul>
    </div>
</div>