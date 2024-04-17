<a href="{{ route('account.customer-support.show', $row->code) }}">

    @if($row->status == \App\Models\CustomerSupport\Ticket::STATUS_WAINTING_CUSTOMER)
        <b>{{ $row->subject }}</b> <span class="label bg-blue"><i class="fas fa-star"></i> Novas respostas</span>
    @else
        {{ $row->subject }}
    @endif
</a>