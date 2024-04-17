@if($row->stock > $row->stock_warning)
<i class="fas fa-check-circle text-green"></i> {{ $row->stock }} {{ strtoupper($row->unity) }}
@elseif($row->stock > 0 && $row->stock <= $row->stock_warning)
<i class="fas fa-exclamation-circle text-yellow"></i> {{ $row->stock }} {{ strtoupper($row->unity) }}
@else
<i class="fas fa-times-circle text-red"></i> Sem stock
@endif
<br/>
<i class="text-muted">Min. compra: {{ $row->stock_min }}</i>
