<div class="btn-group btn-group-sm">
    <a href="{{ route('admin.website.blog.posts.edit', $row->id) }}" class="btn btn-default">
        <i class="fa fa-pencil"></i> Editar
    </a>
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.website.blog.posts.destroy', $row->id) }}" data-method="delete" data-confirm="Confirma a remoção do registo selecionado?">
                <i class="fa fa-trash bigger-120"></i> Eliminar
            </a>
        </li>
    </ul>
</div>