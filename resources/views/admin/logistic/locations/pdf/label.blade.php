<div class="adhesive-label">
    <div style="height: 10mm"></div>
    <div style="margin: 0 12mm">
        <div style="float: left; width: 50%">
            <img src="{{ asset('assets/img/logo/logo_sm.png') }}" style="height: 45px;" class="m-t-10"/>
        </div>
        <div style="float: right; width: 49%">
            <div style="text-align: right">
                <img src="{{ $qrCode }}" style="height: 70px"/>
            </div>
        </div>
    </div>
    <div style="clear: both"></div>
    <div class="text-center">
        <div style="height: 8mm"></div>
        <div style="display: inline-block;">
            <barcode code="{{ $location->barcode }}" type="C128A" size="1.9" height="1"/>
        </div>
        <div class="fs-75 bold text-center m-t-40 text-uppercase">
            {{ $location->code }}
        </div>
    </div>
</div>