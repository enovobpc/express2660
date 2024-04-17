@if(@$row->equipments)
    @foreach($row->equipments as $equipment)
        <div><span class="label" style="background: #999">{{ $equipment->stock_total }}</span> {{ $equipment->name }}</div>
    @endforeach
@endif