<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.allowances.edit', [$row->id, 'month' => $month, 'year' => $year]) }}"
       class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote-lg">
        Detalhe
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Opções Extra</span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="">
                <i class="fas fa-print"></i> Imprimir
            </a>
        </li>
    </ul>
</div>