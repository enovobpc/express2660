<div class="adhesive-label">
    <div class="text-center">
        <div style="height: 4mm"></div>
        <img src="{{ asset('assets/img/logo/logo_sm.png') }}" style="height: 35px;" class="m-t-0"/>
        <div style="height: 5mm"></div>
        <div style="display: inline-block; float: left; width: 260px">
            <barcode code="{{ $shippingOrder->code }}" type="C128A" size="1.3" height="1.2"/>
        </div>
        <div style="float: left; width: 80px; margin-left: 10px">
            <img src="{{ $qrCode }}" style="height: 56px"/>
        </div>
        <div style="clear: both"></div>

        <div class="fs-16pt text-center m-t-10 m-b-10 bold">
            ORDEM SAIDA #{{ $shippingOrder->code }}
        </div>
    </div>
    <div style="border-top: 1px solid #000; padding-top: 5px; padding-bottom: 5px;">
        <div class="fs-12 m-t-0 lh-1-1">
            <span style="font-size: 12px; font-weight: bold; text-transform: uppercase">{{ str_limit(@$shippingOrder->customer->name) }}</span><br/>
            Ref.: {{ $shippingOrder->document }} | Data.: {{ $shippingOrder->date }}
        </div>
    </div>
    <div style="border-top: 1px solid #000; padding-top: 0px">
        <table style="width: 100%; font-size: 10px">
            <tr style="font-weight: bold; background: #333;">
                <td class="w-25px" style="color: #fff; font-weight: bold; padding: 1px">QT</td>
                <td class="w-90px" style="color: #fff; font-weight: bold">SKU</td>
                <td style="color: #fff; font-weight: bold">DESIGNAÇÃO</td>
            </tr>
            @foreach($shippingOrder->lines as $line)
            <tr>
                <td style="vertical-align: top">{{ $line->qty }}</td>
                <td style="vertical-align: top">{{ $line->product->sku }}</td>
                <td>{{ $line->product->name }}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>