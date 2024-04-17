<div>{{ @$row->paymentCondition->name }}</div>
@if(@$row->doc_after_payment)
    <div><small>Gerar {{ trans('admin/billing.types_code.'.@$row->doc_after_payment) }}</small></div>
@else
    <div><small>Não gerar doc.</small></div>
@endif