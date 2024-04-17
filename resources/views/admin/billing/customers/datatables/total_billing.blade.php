<?php
$route = route('admin.billing.customers.edit', [$row->id, 'month' => $row->month, 'year' => $row->year, 'total' => $total, 'period' => $period]);
?>

@if($total == 0.00)
    <div class="sp-7"></div>
    <div class="label label-success"><i class="fas fa-check"></i> Faturado</div>
@else
    @if(empty($row->id))
        {{ money($total, Setting::get('app_currency')) }}
    @else
        <a href="{{ $route }}" data-toggle="modal" data-target="#modal-remote-xl" class="bold">
            {{ money($total, Setting::get('app_currency')) }}
        </a>
    @endif
    <span class="label label-default">Não Faturado</span>
@endif