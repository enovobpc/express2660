@if(isset($row['dimensions']))
    <tr>
        <td colspan="{{ count($headerRow) }}" style="border-top: none; background: #fff; padding: 0;">
            <table style="width: 100%;">
                <tr style="background: #ececec;">
                    <th class="w-70px" style="padding-left: 5px;">@trans('Quantid.')</th>
                    <th class="w-160px">@trans('SKU')</th>
                    <th class="w-250px">@trans('Artigo')</th>
                    @if($importType == 'shipments_dimensions')
                        <th class="w-170px">@trans('Montagem')</th>
                        <th class="w-170px">@trans('Peso')</th>
                        <th class="w-170px">@trans('Comprimento')</th>
                        <th class="w-170px">@trans('Largura')</th>
                        <th class="w-170px">@trans('Altura')</th>
                    @endif
                    <th class="w-170px">@trans('Nº Série')</th>
                    <th class="w-170px">@trans('Lote')</th>
                    <th></th>
                </tr>
                @foreach($row['dimensions'] as $dimension)
                    <tr>
                        <td style="border-bottom: 1px solid #ccc; text-align: center">{{ @$dimension['qty'] }}</td>
                        <td style="border-bottom: 1px solid #ccc">{{ @$dimension['sku'] }}</td>
                        <td style="border-bottom: 1px solid #ccc">{{ @$dimension['name']  ?? @$dimension['article_name']}}</td>
                        @if($importType == 'shipments_dimensions')
                            <td style="border-bottom: 1px solid #ccc">{{ @$dimension['assembly'] }}</td>
                            <td style="border-bottom: 1px solid #ccc">{{ @$dimension['weight'] }}</td>
                            <td style="border-bottom: 1px solid #ccc">{{ @$dimension['article_length'] }}</td>
                            <td style="border-bottom: 1px solid #ccc">{{ @$dimension['article_width'] }}</td>
                            <td style="border-bottom: 1px solid #ccc">{{ @$dimension['article_height'] }}</td>
                        @endif
                        <td style="border-bottom: 1px solid #ccc">{{ @$dimension['serial_no'] }}</td>
                        <td style="border-bottom: 1px solid #ccc">{{ @$dimension['lote'] }}</td>
                        <td style="border-bottom: 1px solid #ccc"></td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
@endif