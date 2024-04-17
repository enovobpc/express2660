<div class="btn-group btn-group-sm" role="group">
    <div class="btn-group btn-group-sm" role="group">
        <button type="button" class="btn btn-default dropdown-toggle"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
                data-loading-text="<i class='fas fa-spin fa-sync-alt'></i> @trans('A sincronizar...')"
        >
            <i class="fas fa-wrench"></i> @trans('Ferramentas') <i class="fas fa-angle-down"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a href="{{ route('admin.export.trips', Request::all()) }}" data-toggle="export-url">
                    <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar listagem')
                </a>
            </li>
        </ul>
    </div>
</div>
