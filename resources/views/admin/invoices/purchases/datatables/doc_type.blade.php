{{ trans('admin/billing.types.' . $row->doc_type) }}
@if(!$row->is_scheduled)
    <br/>
    @if($row->is_deleted)
        <span class="label label-danger">
            <i class="fas fa-exclamation-triangle"></i> Apagado
        </span>
    @elseif($row->doc_id)
       {{-- <small class="text-muted">
            FTC {{ $row->doc_id }}
        </small>--}}
    @elseif($row->is_draft)
        <span class="label label-warning">
            Rascunho
        </span>
    @endif
@endif