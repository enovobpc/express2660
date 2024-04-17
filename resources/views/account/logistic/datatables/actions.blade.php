<div class="action-buttons text-center">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ trans('account/global.word.options') }}  <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('account.logistic.products.details', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-lg"
                   class="text-blue">
                    <i class="fas fa-fw fa-search"></i> {{ trans('account/global.word.details') }}
                </a>
            </li>

            @if(config('app.source') == 'activos24')
                <li>
                    <a href="#" style="cursor: not-allowed; color: #999">
                        <i class="fas fa-fw fa-pencil-alt"></i> {{ trans('account/global.word.edit') }}
                    </a>
                </li>
                <li>
                    <a href="#" style="cursor: not-allowed; color: #999">
                        <i class="fas fa-fw fa-history"></i> {{ trans('account/global.word.historic') }}
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ route('account.logistic.products.edit', $row->id) }}"
                       data-toggle="modal"
                       data-target="#modal-remote-lg"
                       class="text-blue">
                        <i class="fas fa-fw fa-pencil-alt"></i> {{ trans('account/global.word.edit') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.logistic.products.show', $row->id) }}"
                       data-toggle="modal"
                       data-target="#modal-remote-xl"
                       class="text-blue">
                        <i class="fas fa-fw fa-history"></i> {{ trans('account/global.word.historic') }}
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>