@if($row->billing_code)
    <span class="label bg-blue" style="font-size: 10px">{{ $row->billing_code }}</span>
@elseif(!$row->billing_code && $row->is_sales)
    <span class="label label-danger" style="font-size: 10px"><i class="fas fa-exclamation-triangle"></i></span>
@endif