<div class="adhesive-label">
    <div class="text-center">
        <div style="height: 4mm"></div>
        <img src="{{ asset('assets/img/logo/logo_sm.png') }}" style="height: 40px;" class="m-t-6"/>
        <div style="height: 5mm"></div>
        @if($product->barcode)
            <div style="display: inline-block;">
                <barcode code="{{ $product->barcode }}" type="C128A" size="1.8" height="1"/>
            </div>
            <div class="fs-12pt text-center m-t-5 m-b-10">
                {{ $product->barcode }}
            </div>
        @else
            <div style="display: inline-block; margin-top: 30px">
                <barcode code="{{ $product->sku }}" type="C128A" size="1.8" height="1"/>
            </div>
            <div class="fs-12pt text-center m-t-5 m-b-10">
                SKU# {{ $product->sku }}
            </div>
        @endif
        <div class="fs-16pt bold text-center text-uppercase m-t-20 lh-1-2">
            {{ str_limit($product->name, 70) }}
        </div>
        <div class="fs-10pt text-center m-t-0 text-uppercase">
            {{ str_limit(@$product->customer->name) }}
        </div>
    </div>
    @if($product->barcode)
    <div class="text-center m-t-15">
        <div style="display: inline-block;">
            <barcode code="{{ $product->sku }}" type="C128A" size="1.1" height="0.6"/>
        </div>
        <div class="fs-10pt text-center m-t-3">
            SKU # <span style="font-weight: bold">{{ $product->sku }}</span>
        </div>
    </div>
    @endif
</div>