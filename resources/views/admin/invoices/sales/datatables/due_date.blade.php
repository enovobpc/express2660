{{--
@if(!in_array($row->doc_type, ['receipt']))
    @if(!$row->is_settle && $row->due_date < date('Y-m-d'))
        <span class="text-red">{{ $row->due_date }}</span>
    @else
        {{ $row->due_date }}
    @endif
@endif--}}
<?php
$today    = Date::today();
$dueDate  = new Date($row->due_date);
$diffDays = $dueDate->diffInDays($today);
?>
@if(!in_array($row->doc_type, ['receipt']))
    @if(!$row->is_settle)
        @if($row->due_date < date('Y-m-d'))
            <div class="text-red"><i class="fas fa-exclamation-triangle"></i> {{ $row->due_date }}</div>
            <div class="text-red"><small>Há {{ $diffDays }} dias</small></div>
        @elseif($diffDays < 5)
            <div class="text-yellow"><i class="fas fa-clock"></i> {{ $row->due_date }}</div>
            <div class="text-yellow"><small>Restam {{ $diffDays }} dias</small></div>
        @else
            <div>{{ $row->due_date }}</div>
            <div class="text-muted"><small>Restam {{ $diffDays }} dias</small></div>
        @endif
    @else
        {{ $row->due_date }}
    @endif
@endif