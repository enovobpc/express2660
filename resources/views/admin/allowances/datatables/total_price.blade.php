{{ money($row->trips->sum('allowances_price') + $row->trips->sum('weekend_price'), $currency) }}
