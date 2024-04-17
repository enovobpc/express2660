@if($row->doc_type == 'receipt')
    Recibo
@elseif($row->doc_type == 'nodoc')
    Sem Doc.
@else
    {{ trans('admin/billing.types.' . $row->doc_type) }}
@endif
@if($row->is_draft && !$row->is_scheduled)
    <span class="label label-warning">Rascunho</span>
@endif