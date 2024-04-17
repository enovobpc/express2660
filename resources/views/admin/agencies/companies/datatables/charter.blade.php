@if($row->charter)
    <div>Alvará {{ $row->charter }}</div>
@endif

@if($row->capital)
    <div>Capital {{ $row->capital }}<br/>
    @if($row->conservatory)
        <small class="italic">{{ $row->conservatory }}</small>
    @endif
    </div>
@endif