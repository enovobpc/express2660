<?php
    $expenses = $provider->expenses_expedition;
    $row->zones = empty($row->zones) ? [Setting::get('app_country')] : $row->zones;
?>
{{ Form::open(['route' => ['admin.providers.expenses.store', $providerId, $row->id], 'class' => 'form-update-price']) }}
@foreach($row->zones as $zone)
    <div class="input-group input-group-sm">
        <span class="input-group-addon text-uppercase">{{ $zone }}</span>
        {{ Form::text('expenses_expedition['.$agencyId.']['.$row->code.']['.$zone.']', @$expenses[$agencyId][$row->code][$zone], ['class' => 'form-control decimal'])  }}
        {{ Form::hidden('agency', $agencyId) }}
        <span class="input-group-btn">
            <button class="btn btn-default" type="button"><i class="fas fa-save"></i></button>
        </span>
    </div>
@endforeach
{{ Form::close() }}