<a href="{{ route('admin.refunds.customers.show', [$row->customer_id, 'type' => 'devolved']) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl">
    {{ @$row->customer->code }}
</a>
<br/>
<span class="label" style="background: {{ @$agencies[$row->agency_id][0]['color'] }}" data-toggle="tooltip" title="{{ @$agencies[@$row->customer->agency_id][0]['name'] }}">
    {{ @$agencies[@$row->customer->agency_id][0]['code'] }}
</span>