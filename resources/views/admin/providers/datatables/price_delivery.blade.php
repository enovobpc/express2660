<?php
$expenses = $provider->expenses_delivery
?>
{{ Form::open(['route' => ['admin.providers.expenses.store', $providerId, $row->id], 'class' => 'form-update-price']) }}
@foreach($row->zones as $zone)
    <div class="input-group input-group-sm">
        <span class="input-group-addon text-uppercase">{{ $zone }}</span>
        {{ Form::text('expenses_delivery['.$agencyId.']['.$row->code.']['.$zone.']', @$expenses[$agencyId][$row->code][$zone], ['class' => 'form-control'])  }}
        {{ Form::hidden('agency', $agencyId) }}
        <span class="input-group-btn">
            <button class="btn btn-default" type="button"><i class="fas fa-save"></i></button>
        </span>
    </div>
@endforeach
{{ Form::close() }}