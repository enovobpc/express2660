@if(@$row->cod_control->payment_method)
    <i class="fas fa-check-circle text-green"></i> <b>{{ trans('admin/refunds.payment-methods.'.$row->cod_control->payment_method) }} </b>
    <br/>
    <i class="text-muted">{{ $row->cod_control->payment_date }}</i>
@elseif(($row->ignore_billing || $row->invoice_doc_id) && !@$row->cod_control->payment_method)
    <i class="text-muted">Não especificado</i>
@endif