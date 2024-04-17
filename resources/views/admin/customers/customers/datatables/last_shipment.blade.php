@if($row->last_shipment)
    <?php
    $today    = new \Carbon\Carbon();
    $lastDate = new \Carbon\Carbon($row->last_shipment);
    $days = $today->diff($lastDate)->days;
    ?>
    @if(!Setting::get('alert_max_days_without_shipments') || (Setting::get('alert_max_days_without_shipments') && $days < Setting::get('alert_max_days_without_shipments')))
    <span>
        <span data-toggle="tooltip" data-target="Há {{ $days }} dias">{{ $row->last_shipment }}</span><br/>
        <i>{{ $row->total_shipments }} @trans('serviços')</i>
    </span>
    @elseif(Setting::get('alert_max_days_without_shipments') && $days >= Setting::get('alert_max_days_without_shipments'))
    <span class="text-red">
        <span data-toggle="tooltip" data-target="Há {{ $days }} dias">
            <i class="fas fa-exclamation-triangle"></i> {{ $row->last_shipment }}
        </span>
        <br/>
        <i>{{ $row->total_shipments }} @trans('serviços')</i>
    </span>
    @endif
@else
    <span class="text-red">
        <i class="fas fa-exclamation-triangle"></i> @trans('Sem serviços')
    </span>
@endif