@if($row->count_repetitions)
    Agendado {{ $row->count_repetitions }} vezes<br/>
@endif
@if($row->last_schedule)
<small class="text-muted">
    Ultima vez: {{ $row->last_schedule }}
</small>
@endif