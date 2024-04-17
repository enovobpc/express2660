<div>
    <div style="height: 30px"></div>
    <div style="width: 100%">
        <div style="float: left; width: 170px">
            <h4 class="pull-left text-left m-t-0 m-b-0" style="padding-top: 0px; padding-left: 10px">
                <small>Inventário N.º:<br/>
                    <b class="bold" style="color: #000; font-size: 20px">{{ $inventory->code }}</b>
                </small>
                <div style="margin-top: 5px">
                    <small>Data: <b style="color: #000;">{{ $inventory->date }}</b></small>
                </div>
            </h4>
            <div style="clear: both"></div>
            {{--<barcode code="{{ $inventory->code }}" type="C128A" size="1" height="0.75" style="margin-left: -10px; margin-top: 4px"/>--}}
        </div>
        <div style="float: left; width: 320px; margin-right: 25px">
            <h4 class="pull-right text-left m-t-0 line-height-1p5" style="font-size: 18px">
                <b class="bold" style="color: #000; ">{{ @$inventory->description }}</b>
            </h4>
            <h4 class="pull-right text-left m-t-0 line-height-1p5" style="font-size: 18px">
                <small>
                    Cliente #{{ @$inventory->customer->code }}<br/>
                    <b style="color: #000; ">{{ @$inventory->customer->billing_name }}</b><br/>
                </small>
            </h4>
        </div>
        <div style="float: left; width: 120px;">
            <p style="font-size: 13px">
                Artigos: <b class="bold" style="color: #000;">{{ $inventory->items }}</b><br/>
                Existente: <b class="bold" style="color: #000;">{{ $inventory->qty_existing }}</b><br/>

                Balanço: <b class="bold" style="color: #000;">{{ $inventory->qty_existing - $inventory->qty_real }}</b><br/>
            </p>
        </div>
        <div style="float: left; width: 120px;">
            <p style="font-size: 13px">
                &nbsp;<br/>
                Real: <b class="bold" style="color: #000;">{{ $inventory->qty_real }}</b><br/>
                Danos: <b class="bold" style="color: #000;">{{ $inventory->qty_damaged ? $inventory->qty_damaged : 0 }}</b>
            </p>
        </div>
    </div>
    <hr style="margin: 15px 0 10px"/>
    <div class="clearfix"></div>
    <h4 style="margin-top: 0; font-weight: bold">Resumo do Pedido</h4>
    <table class="table table-bordered table-pdf m-b-5" style="font-size: 8pt;">
        <tr>
            {{--<th style="width: 100px">Cód. Barras</th>--}}
            <th style="width: 90px">SKU</th>
            <th>Produto</th>
            <th>Cliente</th>
            <th>Localização</th>
            <th class="w-40px">Exist.</th>
            <th class="w-40px">Real</th>
            <th class="w-40px">Danif.</th>
        </tr>

        @foreach($inventory->lines as $line)
            <tr>
                {{--<td style="text-align: center">
                    <barcode code="{{ @$line->product->sku }}" type="C128A" size="0.5" height="1" style="padding: 2px 0"/>
                </td>--}}
                <td>{{ @$line->product->sku }}</td>
                <td>{{ @$line->product->name }}</td>
                <td>{{ @$line->product->customer->name }}</td>
                <td>{{ @$line->location->code }}</td>
                <td class="text-center">{{ $line->qty_existing }}</td>
                <td class="text-center">{{ $line->qty_real }}</td>
                <td class="text-center">{{ $line->qty_damaged }}</td>
            </tr>
        @endforeach
    </table>
    <div class="clearfix"></div>
</div>