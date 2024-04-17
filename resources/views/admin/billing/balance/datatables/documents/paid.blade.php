<div class="text-center bold">
    @if($row->canceled)
        <span class="label label-warning">Anulado</span>
    @else
        @if(!in_array($row->doc_type, ['receipt', 'regularization']) && $row->doc_serie != 'SIND')
            @if($row->is_paid)
                <span class="label label-success"> {{trans('admin/billing.status.paid')}}</span>
            @else
                <span class="label label-danger">{{trans('admin/billing.status.unpaid')}}</span>
            @endif
        @endif

        @if($row->is_hidden)
            <span class="label label-danger"><i class="fas fa-exclamation-triangle"></i> Oculto</span>
        @endif
    @endif
</div>