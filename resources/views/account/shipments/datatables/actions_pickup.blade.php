<div class="action-buttons text-center">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ trans('account/global.word.options') }}  <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('account.shipments.show', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xl"
                   class="text-blue">
                    <i class="fas fa-fw fa-search"></i> {{ trans('account/global.word.details') }}
                </a>
            </li>

            @if(empty($row->submited_at) && in_array($row->status_id, (Setting::get('services_can_delete') ?? [])))
                <li class="divider"></li>
                <li>
                    <a href="{{ route('account.pickups.edit', $row->id) }}"
                       data-toggle="modal"
                       data-target="#modal-remote-xl"
                        class="text-green">
                        <i class="fas fa-fw fa-pencil-alt"></i> {{ trans('account/global.word.edit') }}
                    </a>
                </li>
                @if(empty($row->submited_at))
                    <li>
                        <a href="{{ route('account.shipments.destroy', $row->id) }}"
                           data-method="delete"
                           data-confirm="{{ trans('account/shipments.feedback.destroy.question') }}"
                            class="text-red">
                            <i class="fas fa-fw fa-trash-alt"></i> {{ trans('account/global.word.destroy') }}
                        </a>
                    </li>
                @endif
            @endif

            <li role="separator" class="divider"></li>
            <li>
                <a href="{{ route('account.shipments.get.pickup-manifest', $row->id) }}"
                   target="_blank"
                   class="text-purple">
                    <i class="fas fa-fw fa-print"></i> {{ trans('account/shipments.print.pickup-manifest') }}
                </a>
            </li>
        </ul>
    </div>
</div>
