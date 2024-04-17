@if($row->phone)
<i class="fas fa-fw fa-phone"></i> {{ $row->phone }}<br/>
@endif

@if($row->email)
<i class="fas fa-fw fa-envelope"></i> {{ $row->email }}<br/>
@endif