<a href="{{ route('admin.air-waybills.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-xl">
    {{ $row->awb_no }}
</a>
<br/>
<span>{{ $row->date->format('Y-m-d') }}</span>
@if($row->has_hawb)
    <br/>
    <span class="label label-info">HAWB</span>
@endif