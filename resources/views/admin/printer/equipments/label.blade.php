<div class="adhesive-label">
    <div class="text-center">
        <div style="display: inline-block; margin-top: 30px">
            <barcode code="{{ $product->sku }}" type="C128A" size="1.8" height="1"/>
        </div>
        <div class="fs-18pt text-center m-t-5 m-b-10 text-uppercase" style="font-weight: bold; letter-spacing: 2px">
            {{ $product->sku }}
        </div>
        <div class="fs-10pt text-center m-t-0 text-uppercase">
            {{ str_limit(@$product->customer->name) }}
        </div>
    </div>
</div>