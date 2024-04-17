<div class="awb-label" style="border: 1px solid #000; border-bottom: none; height: 124mm">
    <div class="awb-row" style="border-bottom: 1px solid #000; height: 10mm">
        <div class="awb-block" style="width: 100%;">
            <p class="awb-block-title">Airline</p>
            <h4 style="font-size: 15pt; margin: 0; text-align: center; font-weight: bold">{{ strtoupper($waybill->provider->name) }}</h4>
        </div>
    </div>
    <?php
    $barcode = str_replace('-', '', str_replace(' ', '', $waybill->awb_no)) . str_pad($count, 5, '0', STR_PAD_LEFT)
    ?>
    <div class="awb-row" style="border-bottom: 1px solid #000; height: 10mm; padding: 10px 0">
        <div class="awb-block" style="width: 100%; text-align: center">
            <div style="display: inline-block;">
                <barcode code="{{ $barcode }}" type="C128A" size="0.7" height="2.5"/>
            </div>
            <p style="font-weight: normal; margin: 0; text-align: center">{{ $barcode }}</p>
        </div>
    </div>
    <div class="awb-row" style="border-bottom: 1px solid #000; height: 14mm;">
        <div class="awb-block" style="width: 100%;">
            <p class="awb-block-title">Air Waybill No.</p>
            <h4 class="awb-block-value">{{ str_replace(' ', '', $waybill->awb_no) }}</h4>
        </div>
    </div>

    <div class="awb-row" style="border-bottom: 1px solid #000;">
        <div class="awb-block" style="width: 42mm; height: 12mm; border-right: 1px solid #000">
            <p class="awb-block-title">Destination</p>
            <h4 class="awb-block-value">{{ $waybill->recipient_airport }}</h4>
        </div>
        <div class="awb-block" style="width: 42mm;">
            <p class="awb-block-title">Destination Total No. of Pieces</p>
            <h4 class="awb-block-value">{{ $waybill->volumes }}</h4>
        </div>
    </div>

    <div class="awb-row" style="border-bottom: 1px solid #000;">
        <div class="awb-block" style="width: 42mm; height: 12mm; border-right: 1px solid #000">
            <p class="awb-block-title">Weight of Consignement</p>
            <h4 class="awb-block-value">{{ money($waybill->weight) }}K</h4>
        </div>
        <div class="awb-block" style="width: 42mm;">
            <p class="awb-block-title">Origin</p>
            <h4 class="awb-block-value">{{ $waybill->source_airport }}</h4>
        </div>
    </div>

    <div class="awb-row" style="border-bottom: 1px solid #000;">
        <div class="awb-block" style="width: 42mm; height: 12mm; border-right: 1px solid #000">
            <p class="awb-block-title">Via (1)</p>
            <h4 class="awb-block-value">{{ @$waybill->flight_scales[0]->airport }}</h4>
        </div>
        <div class="awb-block" style="width: 42mm;">
            <p class="awb-block-title">Via (2)</p>
            <h4 class="awb-block-value">{{ @$waybill->flight_scales[1]->airport }}</h4>
        </div>
    </div>

    <div class="awb-row">
        <div class="awb-block" style="width: 42mm;">
            <p class="awb-block-title">Additional Information</p>
        </div>
        <div class="awb-block" style="width: 42mm;">
            <p class="awb-block-title">Piece No. {{ $count }} / {{ $waybill->volumes }}</p>
        </div>
        <div class="awb-block" style="width: 100%; height: 12mm;">
            <p style="font-size: 10pt">{{ $waybill->obs }}</p>
        </div>
    </div>
</div>
<div style="background: #000; color: #fff; padding: 5px; text-align: center">
    <b style="font-weight: bold">{{ $website ? $website : 'www.quickbox.pt' }}</b>
</div>
<div style="font-size: 7pt; text-align: center">
    QuickBox - Software para transportes e Logística - www.quickbox.pt
</div>