<div>
    <div style="height: 30px"></div>
    <h4 style="margin-top: 0; font-weight: bold">Resumo de Pedidos <small>({{ $shippingOrders->count() }})</small></h4>
    <table class="table table-bordered table-pdf m-b-5" style="font-size: 8pt; border: none">
        <tr>
            <th style="width: 70px">Pedido</th>
            <th style="width: 70px">Data</th>
            <th style="width: 150px">Referência</th>
            <th style="width: 90px">Expedição</th>
            <th>Cliente</th>
            <th style="width: 55px">Artigos</th>
            <th style="width: 60px">Qtd</th>
        </tr>
        <?php $totalItems = $totalQty = 0; ?>
        @foreach($shippingOrders as $shippingOrder)
            <?php
            $lineItems = @$shippingOrder->lines->count();
            $lineQty   = @$shippingOrder->lines->sum('qty');
            $totalItems+= $lineItems;
            $totalQty+= $lineQty
            ?>
            <tr>
                <td>{{ $shippingOrder->code }}</td>
                <td>{{ $shippingOrder->date }}</td>
                <td>{{ $shippingOrder->document }}</td>
                <td>{{ $shippingOrder->shipment_trk }}</td>
                <td>{{ @$shippingOrder->customer->name }}</td>
                <td class="text-center">{{ $lineItems }}</td>
                <td class="text-center">{{ $lineQty }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="5" style="border: none"></td>
            <td class="text-center bold">{{ $totalItems }}</td>
            <td class="text-center bold">{{ $totalQty }}</td>
        </tr>
    </table>

    <hr style="margin: 15px 0 10px"/>
    <div class="clearfix"></div>
    <h4 style="margin-top: 0; font-weight: bold">Resumo de Artigos <small>({{ $shippingOrdersLines->count() }})</small></h4>
    <table class="table table-bordered table-pdf m-b-5" style="font-size: 8pt; border: none">
        <tr>
            <th style="width: 50px">Armazém</th>
            <th style="width: 80px">Localização</th>
            <th style="width: 100px">SKU</th>
            <th style="width: 100px">Cód. Barras</th>
            <th style="width: 450px">Produto</th>
            <th>Lote/N.º Série</th>
            <th class="w-70px">Validade</th>
            <th class="w-55px">Pedidos</th>
            <th class="w-60px">Qtd Total</th>
        </tr>
        <?php $totalOrders = $totalQty = 0; ?>
        @foreach($shippingOrdersLines as $location => $product)
            <?php
            $totalOrders+= $product->total_count;
            $totalQty+= $product->total_qty
            ?>
            <tr>
                <td>{{ @$product->location->warehouse->code }}</td>
                <td>{{ @$product->location->code }}</td>
                <td>{{ @$product->product->sku }}</td>
                <td>{{ @$product->product->barcode }}</td>
                <td>{{ @$product->product->name }}</td>
                <td>{{ @$product->product->lote ? @$product->product->lote : @$product->product->serial_no }}</td>
                <td>{{ @$product->product->expiration_date }}</td>
                <td class="text-center">{{ $product->total_count }}</td>
                <td class="text-center">{{ $product->total_qty }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="7" style=" border: none"></td>
            <td class="text-center bold">{{ $totalOrders }}</td>
            <td class="text-center bold">{{ $totalQty }}</td>
        </tr>
    </table>
    <div class="clearfix"></div>
</div>