<div class="action-buttons text-center">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ trans('account/global.word.options') }} <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('account.ecommerce-gateway.edit', $row->id) }}"
                    data-toggle="modal"
                    data-target="#modal-remote">
                    <i class="fas fa-fw fa-pencil-alt"></i> {{ trans('account/global.word.edit') }}
                </a>
            </li>
            <li>
                <a href="{{ route('account.ecommerce-gateway.mapping', $row->id) }}"
                    data-toggle="modal"
                    data-target="#modal-remote">
                    <i class="fas fa-fw fa-globe"></i> {{ trans('account/global.word.mapping') }}
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ route('account.ecommerce-gateway.destroy', $row->id) }}"
                    data-method="delete"
                    data-confirm="{{ trans('account/ecommerce-gateway.feedback.destroy.question') }}"
                    class="text-red">
                    <i class="fas fa-fw fa-trash-alt"></i> {{ trans('account/global.word.destroy') }}
                </a>
            </li>
        </ul>
    </div>
</div>
