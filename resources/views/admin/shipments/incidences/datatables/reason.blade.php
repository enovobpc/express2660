<a href="{{ route('admin.shipments.show', [$row->shipment_id, 'tab' => 'incidences']) }}"
   data-toggle="modal"
   data-target="#modal-remote-xl"
    class="text-red bold">
    {{ @$incidences[$row->incidence_id] }}
</a>
@if(@$row->incidence_obs)
    <br/><small style="line-height: 1.4; display: inline-block;">{{ @$row->incidence_obs }}</small>
@endif