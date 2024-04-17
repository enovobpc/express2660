<div class="action-buttons text-center">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
            {{ trans('account/global.word.options') }} <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('account.customer-support.show', $row->code) }}">
                    <i class="fas fa-fw fa-search"></i> {{ trans('account/global.word.details') }}
                </a>
            </li>
            @if($row->status == \App\Models\CustomerSupport\Ticket::STATUS_PENDING)
            <li>
                <a href="{{ route('account.customer-support.edit', $row->code) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-lg">
                    <i class="fas fa-fw fa-pencil-alt"></i> {{ trans('account/global.word.edit') }}
                </a>
            </li>
            <li>
                <a href="{{ route('account.customer-support.destroy', $row->code) }}"
                   data-method="delete"
                   data-confirm="{{ trans('account/global.feedback.destroy.question') }}">
                    <i class="fas fa-fw fa-trash-alt"></i> {{ trans('account/global.word.destroy') }}
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>