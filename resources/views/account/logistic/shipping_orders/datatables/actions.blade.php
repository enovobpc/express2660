<div class="action-buttons text-center">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ trans('account/global.word.options') }}  <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('account.logistic.shipping-orders.show', $row->code) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-lg"
                   class="text-blue">
                    <i class="fas fa-fw fa-search"></i> {{ trans('account/global.word.details') }}
                </a>
            </li>
        </ul>
    </div>
</div>