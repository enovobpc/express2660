{{ Html::link(route('admin.customers.edit', $row->id), $row->code) }}
<br/>
<span class="label" style="background: {{ @$agencies[$row->agency_id][0]['color'] }}" data-toggle="tooltip" title="{{ @$agencies[$row->agency_id][0]['name'] }}">
    {{ @$agencies[$row->agency_id][0]['code'] }}
</span>