{{ $row->code }}<br/>

@if($row->type == 'dd')
    <span class="label" style="background: #953dcb">Débito</span>
@else
    <span class="label" style="background: #0285bd">Transfer.</span>
@endif