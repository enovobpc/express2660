@if($row->zones)
    @foreach($row->zones as $zone)
    <?php
    $volumeMin = null;
    if(!$row->volumetricFactors->isEmpty()) {
        $volumeMin = $row->volumetricFactors->filter(function($q) use($zone) {
            return $q->zone == $zone;
        })->first();

        if($volumeMin) {
            $volumeMin = $volumeMin->volume_min;
        }
    }
    ?>
    {{ Form::open(['route' => ['admin.providers.volumetric-factor.store', $providerId, $row->id], 'class' => 'form-update-price']) }}
    <div class="input-group">
        <span class="input-group-addon text-uppercase" style="width: 65px !important;">{{ $zone }}</span>
        {{ Form::text('volume_min', $volumeMin, ['class' => 'form-control'])  }}
        {{ Form::hidden('zone', $zone) }}
        <span class="input-group-btn">
            <button class="btn btn-default" type="button"><i class="fas fa-save"></i></button>
        </span>
    </div>
    {{ Form::close() }}
    @endforeach
@endif