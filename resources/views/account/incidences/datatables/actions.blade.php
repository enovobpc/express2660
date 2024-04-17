<a href="{{ route('account.incidences.resolve', [$row->shipment_id, 'history' => $row->history_id]) }}"
   class="btn btn-xs btn-default"
   data-toggle="modal"
   data-target="#modal-remote">
    <i class="fas fa-reply"></i> {{ trans('account/global.word.resolved-incidence') }}
</a>