@if($row->is_draft)
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
                <a href="{{ route('account.event-manager.edit', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-lg">
                    <i class="fas fa-fw fa-pencil-alt"></i> {{ trans('account/global.word.edit') }}
                </a>
            </li>
            <li>
                <a href="{{ route('account.event-manager.destroy', $row->id) }}"
                   data-method="delete"
                   data-confirm="{{ trans('account/global.feedback.destroy.question') }}"
                   class="text-red">
                    <i class="fas fa-fw fa-trash-alt"></i> {{ trans('account/global.word.destroy') }}
                </a>
            </li>
        </ul>
    </div>
</div>
@endif