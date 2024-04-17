<?php
$color = 'red bold';
if($row->is_paid) {
    $color = 'green bold';
}


if(in_array($row->doc_type, ['receipt', 'regularization'])) {
    $color = 'muted';
}
?>
@if($row->sense == 'debit')
    <div data-total="{{ $row->total }}">
        @if($row->doc_serie != 'SIND')
            <span class="text-{{ $color }}">{{ money($row->total, Setting::get('app_currency')) }}</span>
        @else
            <span class="text-muted">{{ money($row->total, Setting::get('app_currency')) }}</span>
        @endif
    </div>
@else
    <div data-total="-{{ $row->total }}">
        <span class="text-{{ $color }}">-{{ money($row->total, Setting::get('app_currency')) }}</span>
    </div>
@endif