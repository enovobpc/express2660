<div class="action-buttons text-center">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ trans('account/global.word.options') }}  <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('account.logistic.reception-orders.show', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xl"
                   class="text-blue">
                    <i class="fas fa-fw fa-list"></i> {{ trans('account/global.word.details') }}
                </a>
            </li>
            @if ($row->status_id == $statusRequested)
            <li>
                <a href="{{ route('account.logistic.reception-orders.edit', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-lg"
                   class="text-blue">
                    <i class="fas fa-fw fa-edit"></i> {{ trans('account/global.word.edit') }}
                </a>
            </li>

            <li>
                <a href="{{ route('account.logistic.reception-orders.destroy', $row->id) }}"
                    data-method="delete"
                    data-confirm="Pretende eliminar esta ordem de receção?"
                    data-confirm-title="Eliminar"
                    data-confirm-label="Eliminar ordem receção"
                    data-confirm-class="btn-success"
                    class="text-red"
                >
                    <i class="fas fa-fw fa-trash"></i> {{ trans('account/global.word.destroy') }}
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>