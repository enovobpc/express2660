<a href="{{ route('admin.billing.balance.show', $row->id) }}" data-toggle="modal" data-target="#modal-remote-xl">
    {{ $row->code }}
</a>
<br/>
<span class="label" style="background: {{ @$agencies[$row->agency_id][0]['color'] }}" data-toggle="tooltip" title="{{ @$agencies[$row->agency_id][0]['name'] }}">{{ @$agencies[$row->agency_id][0]['code'] }}</span>