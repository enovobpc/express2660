<?php
if(!$row->is_paid && !in_array($row->doc_type, ['receipt', 'regularization'])) {

    $color = 'yellow';

    if(!$row->pending) {
        $color = 'red';
        $row->pending = $row->total;
    }

    if($row->doc_type == 'credit-note') {
        $row->pending = $row->pending * -1;
    }


} else {
    $color = '';
    $row->pending = null;
}
?>

@if($row->pending)
    <b class="text-{{ $color }}">{{ money($row->pending, Setting::get('app_currency')) }}</b>
@endif

