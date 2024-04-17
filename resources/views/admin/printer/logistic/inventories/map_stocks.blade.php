<div>
    <div style="height: 30px"></div>
    <hr style="margin: 15px 0 10px"/>
    <div class="clearfix"></div>
    @if($groupByProduct)

        @if($groupByCustomer)

             @foreach($stockHistories as $customerName => $customerHistories)
                <?php $customer = @$customerHistories->first()->customer; ?>
                <div style="background: #333; width: 100%; padding: 2px 5px; color: #fff; font-weight: bold; font-size: 12px;">
                    <div style="float: left; width: 60%">{{ $customer->code }} - {{ $customer->name }}</div>
                    <div style="float: left; width: 40%; text-align: right">{{ count($customerHistories) }} Artigos</div>
                </div>
                <table class="table table-bordered table-pdf m-b-5" style="font-size: 8pt;">
                    <tr>
                        <th style="width: 90px">SKU</th>
                        <th>Produto</th>
                        <th>Lote/N.º Serie</th>
                        <th class="w-80px">Criado em</th>
                        <th class="w-80px">Ultimo Mov.</th>
                        <th class="w-40px">Stock.</th>
                        <th class="w-40px">Alocado</th>
                        <th class="w-40px">Disp.</th>
                    </tr>
                    @foreach($customerHistories as $history)
                        <tr>
                            <td>{{ @$history->product->sku }}</td>
                            <td>{{ @$history->product->name }}</td>
                            <td>{{ @$product->serie_no ? @$history->product->serie_no : @$history->product->lote }}</td>
                            <td class="text-center">{{ @$history->product->created_at->format('Y-m-d') }}</td>
                            <td class="text-center">{{ $history->date }}</td>
                            <td class="text-center">{{ $history->stock_total }}</td>
                            <td class="text-center">{{ $history->stock_allocated }}</td>
                            <td class="text-center">{{ $history->stock_available }}</td>
                        </tr>
                    @endforeach
                </table>
            @endforeach

        @else

            <table class="table table-bordered table-pdf m-b-5" style="font-size: 8pt;">
                <tr>
                    <th style="width: 90px">SKU</th>
                    <th>Produto</th>
                    <th>Cliente</th>
                    <th>Lote/N.º Serie</th>
                    <th class="w-80px">Criado em</th>
                    <th class="w-80px">Ultimo Mov.</th>
                    <th class="w-40px">Stock.</th>
                    <th class="w-40px">Alocado</th>
                    <th class="w-40px">Disp.</th>
                </tr>
                @foreach($stockHistories as $history)
                    <tr>
                        <td>{{ @$history->product->sku }}</td>
                        <td>{{ @$history->product->name }}</td>
                        <td>{{ @$history->product->customer->name }}</td>
                        <td>{{ @$history->product->serie_no ? @$history->product->serie_no : @$history->product->lote }}</td>
                        <td class="text-center">{{ @$history->product->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">{{ @$history->date }}</td>
                        <td class="text-center">{{ $history->stock_total }}</td>
                        <td class="text-center">{{ $history->stock_allocated }}</td>
                        <td class="text-center">{{ $history->stock_available }}</td>
                    </tr>
                @endforeach
            </table>
        @endif

    @else


        @if($groupByCustomer)

            @foreach($stockHistories as $customerName => $customerHistories)
                <?php $customer = @$customerHistories->first()->customer; ?>
                <div style="background: #333; width: 100%; padding: 2px 5px; color: #fff; font-weight: bold; font-size: 12px;">
                    <div style="float: left; width: 60%">{{ $customer->code }} - {{ $customer->name }}</div>
                    <div style="float: left; width: 40%; text-align: right">{{ count($customerHistories) }} Artigos</div>
                </div>

                <table class="table table-bordered table-pdf m-b-5" style="font-size: 8pt;">
                    <tr>
                        <th style="width: 90px">SKU</th>
                        <th>Produto</th>
                        <th class="w-200px">Lote/N.º Serie</th>
                        <th class="w-80px">Criado em</th>
                        <th class="w-80px">Ultimo Mov.</th>
                        <th class="w-80px">Localização</th>
                        <th class="w-40px">Stock.</th>
                        <th class="w-40px">Alocado</th>
                        <th class="w-40px">Disp.</th>
                    </tr>

                    @foreach($customerHistories as $history)
                        <tr>
                            <td>{{ @$history->product->sku }}</td>
                            <td>{{ @$history->product->name }}</td>
                            <td>{{ @$history->product->serie_no ? @$history->product->serie_no : @$history->product->lote }}</td>
                            <td class="text-center">{{ @$history->product->created_at ? @$history->product->created_at->format('Y-m-d') : '' }}</td>
                            <td class="text-center">{{ @$history->date }}</td>
                            <td class="text-center">{{ @$history->location->code }}</td>
                            <td class="text-center">{{ @$history->stock_total }}</td>
                            <td class="text-center">{{ @$history->stock_allocated }}</td>
                            <td class="text-center">{{ @$history->stock_available }}</td>
                        </tr>
                    @endforeach
                </table>
            @endforeach

        @else

            <table class="table table-bordered table-pdf m-b-5" style="font-size: 8pt;">
                <tr>
                    <th style="width: 90px">SKU</th>
                    <th>Produto</th>
                    <th>Cliente</th>
                    <th>Lote/N.º Serie</th>
                    <th class="w-80px">Criado em</th>
                    <th class="w-80px">Ultimo Mov.</th>
                    <th class="w-80px">Localização</th>
                    <th class="w-40px">Stock.</th>
                    <th class="w-40px">Alocado</th>
                    <th class="w-40px">Disp.</th>
                </tr>
                @foreach($stockHistories as $history)
                    <tr>
                        <td>{{ @$history->product->sku }}</td>
                        <td>{{ @$history->product->name ? @$history->product->name : '-- APAGADO #'.@$history->product_id.'--'}}</td>
                        <td>{{ @$history->customer->name }}</td>
                        <td>{{ @$history->product->serie_no ? @$history->product->serie_no : @$history->product->lote }}</td>
                        <td class="text-center">{{ @$history->product->created_at ? @$history->product->created_at->format('Y-m-d') : '' }}</td>
                        <td class="text-center">{{ @$history->date }}</td>
                        <td class="text-center">{{ @$history->location->code }}</td>
                        <td class="text-center">{{ @$history->stock_total }}</td>
                        <td class="text-center">{{ @$history->stock_allocated }}</td>
                        <td class="text-center">{{ @$history->stock_available }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    @endif
    <div class="clearfix"></div>
</div>