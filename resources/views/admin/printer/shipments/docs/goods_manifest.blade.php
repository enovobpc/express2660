<div>
    @foreach($shipments as $shipment)
        <?php
        $qrCode = new \Mpdf\QrCode\QrCode($shipment->tracking_code);
        $qrCode->disableBorder();
        $output = new \Mpdf\QrCode\Output\Png();
        $qrCode = 'data:image/png;base64,'.base64_encode($output->output($qrCode, 49));

        ?>
        <div>
            <div style="width: 10%; float: left">
                <img src="{{ $qrCode }}"/>
            </div>
            <div style="width: 90%; float: left">
                <h4 style="margin: 0; width: 14cm; display: block; float: left">
                    {{ @$shipment->tracking_code }} - {{ $shipment->recipient_name }}
                </h4>
                <div style="font-size: 14px; text-align: right; width: 3cm;">{{ $shipment->date }}</div>
                <div class="clearfix"></div>
                <div style="width: 60%; float: left">
                    <p>
                        {{ $shipment->recipient_address }}<br/>
                        {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                        <br/>
                        Tlf: {{ $shipment->recipient_phone }}
                    </p>
                </div>
                <div style="width: 39%; float: left">
                    <p class="text-right">
                        Ref.: <strong>{{ $shipment->reference }}</strong><br/>
                        @if($shipment->obs)
                            Observações:
                            {{ $shipment->obs }}
                        @endif
                    </p>
                </div>
            </div>

        </div>
        <div class="clearfix"></div>
        @if($shipment->pack_dimensions)
        <table class="table table-bordered table-pdf m-b-3" style="font-size: 6.3pt;">
            <tr>
                <th class="w-50px">Qtd</th>
                <th class="w-80px">Embalagem</th>
                <th>Artigo</th>
                <th class="w-50px">Peso</th>
                <th class="w-50px">Comp.</th>
                <th class="w-50px">Altura</th>
                <th class="w-50px">Largura</th>
                <th class="w-20px">Montagem</th>
            </tr>
            @foreach($shipment->pack_dimensions as $pack)
                <tr>
                    <td>{{ $pack->qty }}</td>
                    <td>{{ @$pack->type->name ? @$pack->type->name : '' }}</td>
                    <td>{{ $pack->description }}</td>
                    <td>{{ $pack->weight }}</td>
                    <td>{{ $pack->width }}</td>
                    <td>{{ $pack->height }}</td>
                    <td>{{ $pack->length }}</td>
                    <td>{{ @$pack->optional_fields["Montagem"] ? 'Sim' : '' }}</td>

                </tr>
            @endforeach
        </table>
        @endif
        <div class="clearfix"></div>
        <hr class="m-b-10 m-t-10"/>
    @endforeach
</div>