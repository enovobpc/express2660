@for($i = 1 ; $i < $firstPosition ; $i++)
    <div style="width: 95.4mm; height: 68mm; float: left; padding: 15px 17px; border: 1px solid #fff;">
        &nbsp;
    </div>
@endfor

@foreach($shipments as $shipment)
    @for($count = 1 ; $count<= $shipment->volumes ; $count++)

        <?php
        $qrCode = new \Mpdf\QrCode\QrCode($shipment->tracking_code . str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) . str_pad($count, 3, '0', STR_PAD_LEFT));
        $qrCode->disableBorder();
        $output = new \Mpdf\QrCode\Output\Png();
        $qrCode = 'data:image/png;base64,'.base64_encode($output->output($qrCode, 70));
        ?>

        <div style="width: 95.4mm; height: 69.7mm; float: left; padding: 12px 17px 5px 17px; border: 1px solid #fff; ">
            <div class="adhesive-row">
                <div class="adhesive-block" style="width: 34mm; height: 13mm; float: left; text-align: left;">
                    @if($shipment->agency->filepath_black && File::exists($shipment->agency->filepath_black))
                        <img src="{{ asset($shipment->agency->filepath_black) }}" style="height: 30px; max-width: 51mm; margin-top: 1mm" class="margin-left"/>
                    @elseif($shipment->agency->filepath && File::exists($shipment->agency->filepath))
                        <img src="{{ asset($shipment->agency->filepath) }}" style="height: 30px; max-width: 51mm; margin-top: 1mm" class="margin-left"/>
                    @else
                        <h4 style="margin:0px">{{ $shipment->agency->company }}</h4>
                    @endif
                </div>
                <div class="adhesive-block" style="width: 60mm; height: 10mm; float: left; text-align: right; margin-right: -3mm">
                    <barcode code="{{ $shipment->tracking_code . str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) . str_pad($count, 3, '0', STR_PAD_LEFT) }}" type="C128A" size="0.7" height="1.2" style="margin-right: -4mm"/>
                    <div style="font-size: 12px; font-weight: bold">
                        <div style="float: left; text-align: left; width: 160px; letter-spacing: 3px">{{ $shipment->tracking_code }}</div>
                        <div style="float: left; width: 65px;">{{ $shipment->date }}</div>
                    </div>
                </div>
            </div>
            <div class="adhesive-ro" style="background: #111; height: 2mm; color: #fff; text-align: center; padding: 0">
                <div class="adhesive-bloc">
                    <h4 style="font-weight: bold; text-transform: uppercase; font-size: 15px; color: #fff; line-height: 10px; padding: 0">
                        @if(@$shipment->route->code)
                            {{ @$shipment->route->code }} - {{ @$shipment->route->name }}
                        @else
                            @if($shipment->recipient_country == 'pt')
                                RN - Expedição Nacional
                            @elseif($shipment->recipient_country == 'es')
                                RE - Expedição Espanha
                            @else
                                RI - Expedição Internacional
                            @endif
                        @endif
                    </h4>
                </div>
            </div>
            <div class="adhesive-row" style="padding-top: 1mm; border-top: 1px solid #000">
                <div class="adhesive-block" style="height: 33mm;">
                    <p style="margin-bottom: 0">
                        <span style="font-size: 11px;">Remetente: {{ $shipment->sender_name }}</span><br/>
                    </p>
                    <p style="font-size: 16px; line-height: 18px; width: 100%; margin: 0; height: 20mm;">
                        @if($shipment->recipient_attn)
                            <span style="font-size: 11px">A/C: {{ $shipment->recipient_attn }}</span><br/>
                        @endif
                        <b style="font-weight: bold">{{ substr($shipment->recipient_name, 0, 50) }}</b><br/>
                        <span style="font-weight: bold">{{ $shipment->recipient_address }}</span><br/>
                        <b style="font-weight: bold">{{ $shipment->recipient_zip_code }} {{ substr($shipment->recipient_city, 0, 28) }} ({{ strtoupper($shipment->recipient_country) }})</b>
                    </p>
                </div>
            </div>
            <div class="adhesive-row" style="margin-bottom: 0mm;">

                <div class="adhesive-block" style="width: 35mm; float: left;">
                    <p style="margin: 0">
                        {{ $shipment->reference ? 'Ref.:' . $shipment->reference : '&nbsp;' }}<br/>
                        <span style="font-weight: bold">Serviço {{ @$shipment->service->display_code }}</span>
                    </p>
                </div>
                <div class="adhesive-block" style="width: 32mm; float: left; ">
                    <p style="margin: 0">
                        @if($shipment->charge_price)
                            <br/>
                            <span style="font-weight: bold">Cobrança {{ $shipment->charge_price }}</span>
                        @endif
                        &nbsp;
                    </p>
                </div>
                <div class="adhesive-block" style="width: 27mm; float: left; text-align: left; margin-top: -10px">
                    <p style="margin: 0 0 0px; text-align: right">{{ $shipment->weight }}kg</p>
                    <h4 style="margin: 0px; padding: 0; font-weight: bold; font-size: 25px; text-align: right; margin-top: -1.5mm">
                        {{ str_pad($count, 3, "0", STR_PAD_LEFT) }}/{{ str_pad($shipment->volumes, 3, "0", STR_PAD_LEFT) }}
                    </h4>
                </div>
                <div style="float: none"></div>
            </div>
        </div>
   @endfor
@endforeach