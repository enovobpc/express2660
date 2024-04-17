<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.prospects.edit', $row->id) }}" class="btn btn-sm btn-default">
            @trans('Editar')
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">@trans('Opções Extra')</span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.prospects.convert', $row->id) }}" data-method="post" data-confirm-title="Converter prospect em cliente" data-confirm-class="btn-success" data-confirm-label="@trans('Converter')" data-confirm="@trans('Pretende converter este prospect em cliente?')">
                    <i class="fas fa-user-plus"></i> @trans('Converter em Cliente')
                </a>
            </li>
            <div class="divider"></div>
            <li>
                <a href="{{ route('admin.prospects.destroy', $row->id) }}" data-method="delete"
                   data-confirm="@trans('Confirma a remoção do registo selecionado?')" class="text-red">
                    <i class="fas fa-trash-alt"></i> @trans('Eliminar')
                </a>
            </li>
        </ul>
    </div>
</div>