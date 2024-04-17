@if($row->status == 'customer')
    <span class="label label-success customer">Cliente</span>
@elseif($row->status == 'no_interest')
    <span class="label no-interest" style="background: #ccc;">S/Interesse</span>
@endif