@if($row->phone)
<i class="fas fa-fw fa-phone"></i> {{ $row->phone }}<br/>
@endif

@if($row->mobile)
<i class="fas fa-fw fa-mobile-alt"></i> {{ $row->mobile }}
@endif