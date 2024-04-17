<?php
$qrCode = new \Mpdf\QrCode\QrCode($shippingOrder->code);
$qrCode->disableBorder();
$output = new \Mpdf\QrCode\Output\Png();
$qrCode = 'data:image/png;base64,'.base64_encode($output->output($qrCode, 70));
?>
<div>
    <div style="height: 30px"></div>
    <div style="width: 100%">
        <div style="float: left; width: 245px">
            <div style="width: 70px; float: left;">
                <img src="{{ $qrCode }}" style="width: 70px"/>
            </div>
            <h4 class="pull-left text-left m-t-0 m-b-0" style="padding-top: 5px; padding-left: 10px">
                <small>Ordem N.º:<br/>
                    <b class="bold" style="color: #000; font-size: 20px">{{ $shippingOrder->code }}</b>
                </small>
                <div style="margin-top: 5px">
                    <small>Data: <b style="color: #000;">{{ $shippingOrder->created_at->format('Y-m-d') }}</b></small>
                </div>
            </h4>
            <div style="clear: both"></div>
            <barcode code="{{ $shippingOrder->code }}" type="C128A" size="1" height="0.75" style="margin-left: -10px; margin-top: 4px"/>
        </div>
        <div style="float: left; width: 300px; margin-right: 25px">
            <h4 class="pull-right text-left m-t-0 line-height-1p5" style="font-size: 18px">
                <small>
                    Cliente #{{ @$shippingOrder->customer->code }}<br/>
                    <b class="bold" style="color: #000; ">{{ @$shippingOrder->customer->billing_name }}</b><br/>
                </small>
            </h4>
            <p style="font-size: 13px">
                Ref.: {{ $shippingOrder->document }}
                <br/>
                Notas: {{ $shippingOrder->obs }}
            </p>
        </div>
        <div style="float: left;">
            <h4 class="pull-right text-left m-t-0 line-height-1p5">
                <small>
                    @if($shippingOrder->shipment_trk)
                        Expedição <b style="color: #000; font-weight: bold">TRK# {{ $shippingOrder->shipment_trk }} (Via {{ @$shippingOrder->shipment->provider->name }})</b>
                    @endif
                </small>
            </h4>
            <p style="font-size: 13px">
                <b class="bold" style="color: #000;">{{ $shippingOrder->recipient_name }}</b><br/>
                {{ $shippingOrder->recipient_address }}<br/>
                {{ $shippingOrder->recipient_zip_code }} {{ $shippingOrder->recipient_city }}
            </p>
        </div>

    </div>
    <hr style="margin: 15px 0 10px"/>
    <div class="clearfix"></div>
    <h4 style="margin-top: 0; font-weight: bold">Resumo do Pedido</h4>
    <table class="table table-bordered table-pdf m-b-5" style="font-size: 8pt;">
        <tr>
            <th style="width: 90px">SKU</th>
            <th style="width: 90px">Cód. Barras</th>
            <th>Produto</th>
            <th>Lote/N.º Série</th>
            <th class="w-70px">Validade</th>
            {{--<th>Armazém</th>--}}
            <th class="w-100px">Localização</th>
            <th class="w-70px">Qtd Pedido</th>
            <th class="w-70px">Qtd Satisf.</th>
        </tr>

        @foreach($shippingOrder->lines as $line)
            <tr>
                <td>{{ @$line->product->sku }}</td>
                <td>{{ @$line->product->barcode }}</td>
                <td>{{ @$line->product->name }}</td>
                <td>{{ @$line->product->lote ? @$line->product->lote : @$line->product->serial_no }}</td>
                <td>{{ @$line->product->expiration_date }}</td>
                {{--<td>{{ @$line->location->warehouse->name }}</td>--}}
                <td>{{ @$line->location->code }}</td>
                <td>{{ $line->qty }}</td>
                <td>{{ $line->qty_satisfied }}</td>
            </tr>
        @endforeach
    </table>
    <div class="clearfix"></div>
</div>