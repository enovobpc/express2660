@if($row->balance_total_debit > 0.00)
    {{ money($row->balance_total_debit, $currency) }}
@endif