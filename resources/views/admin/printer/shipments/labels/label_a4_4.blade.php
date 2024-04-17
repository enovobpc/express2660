@for($i = 1 ; $i < $firstPosition ; $i++)
    <div style="width: 104.4mm; height: 148.5mm; float: left; border: 1px solid #fff; ">
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

        <div style="width: 95.4mm; height: 140.7mm; float: left; padding: 15px 17px; border: 1px solid #000; ">
            <div class="adhesive-label" style="width: 100mm; height: 103mm">
                <div class="adhesive-row">
                    <div class="adhesive-block" style="width: 50mm; height: 12mm; text-align: left">
                        @if($shipment->agency->filepath_black && File::exists($shipment->agency->filepath_black))
                            <img src="{{ asset($shipment->agency->filepath_black) }}" style="height: 60px; max-width: 51mm; margin-top: 2mm" class="margin-left"/>
                        @elseif($shipment->agency->filepath && File::exists($shipment->agency->filepath))
                            <img src="{{ asset($shipment->agency->filepath) }}" style="height: 60px; max-width: 51mm; margin-top: 2mm" class="margin-left"/>
                        @else
                            <h4 style="margin:0px">{{ $shipment->agency->company }}</h4>
                        @endif
                    </div>
                    <div class="adhesive-block" style="width: 40.8mm; height: 15mm; text-align: right; margin-right: -3mm">
                        {{-- <div style="width: 1.1cm; height: 0.9cm; margin-right: 0mm; padding: 17px 5px 0 5px; font-weight: bold; font-size: 16px; border: 1px solid #000; text-align: center; float: right; color: #000">
                             {{ @$shipment->service->code }}
                         </div>--}}
                        <div style="float: right;text-align: right; width: 14mm;">
                            <img src="{{ @$qrCode }}" height="50"/>
                        </div>
                        <div style="width: 2.5cm; height: 0.9cm; margin-right: 1mm; padding-top: 1px; line-height: 1.3; font-size: 9pt; border: 1px solid #fff; text-align: right; float: right; color: #000">
                            {{ @$shipment->date }}<br/>
                            {{ @$shipment->tracking_code }}<br>
                            @if($shipment->senderAgency->code == $shipment->recipientAgency->code)
                                {{ $shipment->senderAgency->code }}
                            @else
                                {{ $shipment->senderAgency->code }} > {{ $shipment->recipientAgency->code }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bold text-left text-center text-uppercase" style="background: #000; color: #fff; padding: 5px 5px; font-size: 22px; line-height: 22px; height: 2mm;">
                    {{ $shipment->recipientAgency->print_name }}
                </div>

                <div style="height: 70mm; float: left; margin-top: 0mm;">
                    <div class="adhesive-content m-t-2" style="border: none">
                        <div class="adhesive-row pull-left" style="border-bottom: 0; width: 100mm;">
                            <div class="adhesive-block border-bottom">
                                <div class="adhesive-block-title">
                                    <div style="float: left; width: 50%">EXPEDIDOR</div>
                                    <div class="fs-8pt text-right" style="float: right; width: 50%">
                                        @if($shipment->sender_phone)
                                            Tlf: {{ $shipment->sender_phone }}
                                        @else
                                            &nbsp;
                                        @endif
                                    </div>
                                </div>
                                <div class="fs-8pt">
                                    {{ str_limit($shipment->sender_name, 60) }}<br>
                                    {{ $shipment->sender_address }}<br/>
                                    {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} ({{ strtoupper($shipment->sender_country) }})
                                </div>
                            </div>

                            <div class="adhesive-block border-bottom">
                                <div class="adhesive-block-title"></div>
                                <div style="float: left; width: 50%; font-size: 7pt">DESTINATÁRIO</div>
                                <div class="fs-8pt text-right" style="float: right; width: 50%">
                                    @if($shipment->recipient_phone)
                                        Tlf: {{ $shipment->recipient_phone }}
                                    @else
                                        &nbsp;
                                    @endif
                                </div>
                                <div>
                                    <div class="fs-12pt bold lh-1-2">
                                        {{ str_limit(strtoupper($shipment->recipient_name), 34) }}<br>
                                        {{ $shipment->recipient_address }}<br/>
                                        {{ zipcodeCP4($shipment->recipient_zip_code) }} {{ $shipment->recipient_city }} ({{ strtoupper($shipment->recipient_country) }})
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="adhesive-block" style="width: 100mm; margin-top: 5px">
                        @if($shipment->obs)
                            <div class="fs-9pt m-t-0 p-b-6">{{ $shipment->obs }}</div>
                        @endif

                        @if($shipment->reference)
                            <div class="fs-9pt m-t-0 p-b-6">Ref #{{ $shipment->reference }}</div>
                        @endif

                        @if($shipment->charge_price != 0.00)
                            <div class="fs-8pt p-t-6">
                                <span class="guide-payment fs-7pt" style="width: 21mm">
                                    À cobrança
                                </span>
                                &nbsp;
                                {{ str_pad($shipment->recipientAgency_id, 2, "0", STR_PAD_LEFT) }}{{ str_pad($shipment->customer_id, 4, "0", STR_PAD_LEFT) }}.{{ $shipment->charge_price }}
                            </div>
                        @endif

                        @if ($shipment->cod == 'D' || $shipment->cod == 'S')
                            <div class="fs-8pt p-t-6">
                                <span class="guide-payment fs-7pt" style="width: 21mm">
                                    {{ $shipment->cod == 'D' ? 'Portes Destino' : 'Portes Remetente' }}
                                </span>
                                @if(!Setting::get('labels_show_cod'))
                                &nbsp;
                                {{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}
                                @endif
                            </div>
                        @endif

                        @if(!empty($shipment->has_return))
                            @if(in_array('rpack', $shipment->has_return) || in_array('rguide', $shipment->has_return))
                                <div class="fs-8pt p-t-6">
                    <span class="guide-payment fs-7pt" style="width: 21mm">
                        Com retorno
                    </span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <barcode code="{{ $shipment->tracking_code . str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) . str_pad($count, 3, '0', STR_PAD_LEFT) }}" type="C128A" size="1.08" height="1.4" style="margin-left:-4mm"/>
            <div class="adhesive-row" style="margin-top: 5px; margin-bottom: 5px; padding: 2px 10px 2px 10px; background: #000; color: #fff; border-bottom: 0; width: 100mm">
                <div style="width: 2.5cm; font-size: 16px; margin: 1px 15px 1px 0; padding: 2px 10px 0 0; text-align: left; color: #fff; float: left">
                    {{ @$shipment->service->display_code }}
                </div>
                <div class="adhesive-block" style="width: 3.7cm; height: 6.1mm; float: left; text-align: center">
                    <div class="fs-14pt bold text-center">
                        {{ str_pad($count, 3, "0", STR_PAD_LEFT) }}/{{ str_pad($shipment->volumes, 3, "0", STR_PAD_LEFT) }}
                    </div>
                </div>
                <div class="adhesive-block" style="width: 2.5cm; font-size: 16px; height: 6.1mm; float: left; text-align: right;">
                    <div class="p-t-3 text-right">
                        {{ $shipment->weight }}KG
                    </div>
                </div>
            </div>
            <div class="text-center">
                <div style="float: right; font-size: 17px; font-weight: bold; text-align: center">
                    {{ $shipment->recipientAgency->web }}
                </div>
            </div>
        </div>
   @endfor
@endforeach