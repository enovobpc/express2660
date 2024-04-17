<div class="action-buttons text-center">
    <div class="btn-group btn-group-xs">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-file-pdf"></i> {{ trans('account/global.word.month-summary')  }}  <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('account.billing.print', [$row->customer_id, 'month' => $row->month, 'year' => $row->year]) }}"
                    target="_blank">
                    <i class="fas fa-fw fa-file-pdf"></i> {{ trans('account/global.word.download-summary')  }} PDF
                </a>
            </li>
            <li>
                <a href="{{ route('account.export.billing.month', [$row->customer_id, 'month' => $row->month, 'year' => $row->year]) }}">
                    <i class="fas fa-fw fa-file-excel"></i> {{ trans('account/global.word.download-summary')  }} Excel
                </a>
            </li>
            <li>
                <a href="{{ route('account.billing.recipients', [$row->customer_id, 'month' => $row->month, 'year' => $row->year]) }}"
                    data-toggle="modal"
                    data-target="#modal-remote-lg">
                    <i class="fas fa-users"></i> {{ trans('account/global.word.recipients-detail')  }}
                </a>
            </li>
        </ul>
    </div>
</div>