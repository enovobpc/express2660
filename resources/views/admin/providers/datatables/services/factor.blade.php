@if($row->zones)
    @foreach($row->zones as $zone)
    <?php
        $factor = null;
        if(!$row->volumetricFactors->isEmpty()) {
            $factor = $row->volumetricFactors->filter(function($q) use($zone) {
                return $q->zone == $zone;
            })->first();

            if($factor) {
                $factor = $factor->factor;
            }
        }
    ?>

    {{ Form::open(['route' => ['admin.providers.volumetric-factor.store', $providerId, $row->id], 'class' => 'form-update-price']) }}
    <div class="input-group">
        <span class="input-group-addon text-uppercase" style="width: 65px !important;">{{ $zone }}</span>
        {{ Form::text('factor', $factor, ['class' => 'form-control'])  }}
        {{ Form::hidden('zone', $zone) }}
        <span class="input-group-btn">
            <button class="btn btn-default" type="button"><i class="fas fa-save"></i></button>
        </span>
    </div>
    {{ Form::close() }}
    @endforeach
@endif