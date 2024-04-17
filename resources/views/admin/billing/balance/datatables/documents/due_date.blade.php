@if(($row->sense == 'debit' && $row->due_date && $row->doc_serie != 'SIND' || $row->doc_type == 'credit-note'))
    <?php $date = new Date($row->due_date); ?>

    @if(!$row->is_paid && $date < $today)
        <span class="text-red">
            <i class="fas fa-exclamation-triangle"></i> {{ $date->format('d F Y') }}<br/>
            <small>{{ $date->diffInDays($today) }} dias em atraso</small>
        </span>
    @else
        {{ $date->format('d F Y') }}
    @endif
@endif