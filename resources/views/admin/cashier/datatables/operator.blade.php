@if($row->operator_id)
    {{ @$row->operator->code ? @$row->operator->code. ' - ' : '' }}{{ @$row->operator->name }}
@endif
