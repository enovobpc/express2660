<div class="cmr-label" style="height: 290mm; width: 210mm;">
    <div style="position: absolute; width: 100%; margin-left: -7mm">
        <div style="font-size: 30px; float: left; width: 25px;margin-top: -10px">1</div>
        <div style="font-size: 8px; float: left; width: 300px; padding-top: 9px; line-height: 9px;">
            <span>
                Notwithstanding any clause to the contrary, this carriage is subject to the Convention on the contract for the international
                carriage of goods by road (CMR).
            </span>
        </div>
    </div>
    <div class="cmr-row">
        <div class="cmr-block" style="width: 470px; padding: 8px 10px; float: left; height: 18mm;">
            <div class="fs-8pt bold lh-1-1 text-uppercase">
                {{ str_limit($shipment->shipper_name ?? $shipment->sender_name, 45) }}<br>
                {{ $shipment->shipper_address ?? $shipment->sender_address }}<br />
                {{ $shipment->shipper_zip_code ?? $shipment->sender_zip_code }} {{ $shipment->shipper_city ?? $shipment->sender_city }}<br />
                {{ trans('country.' . strtolower($shipment->shipper_country ?? $shipment->sender_country)) }}
            </div>
        </div>
        <div class="cmr-block" style="width: 190px; float: left;">
            <div class="text-center" style="margin-top: -14px">
                <div style="font-weight: bold; font-size: 15px; text-align: right">{{ $shipment->tracking_code }}
                </div>
                <div style="display: inline-block; margin-top: 0px">
                    <barcode code="{{ $shipment->tracking_code }}" type="C128A" size="0.78" height="1" />
                </div>
            </div>
        </div>
    </div>
    <div class="cmr-row">
        <div class="cmr-block" style="width: 360px; height: 250px; padding: 17px 10px; float: left;">
            <div>
                <div class="fs-8pt bold lh-1-1 text-uppercase" style="height: 90px;">
                    <div class="fs-8pt bold lh-1-1">
                        {{ str_limit($shipment->receiver_name ?? $shipment->recipient_name, 47) }}<br>
                        {{ $shipment->receiver_address ?? $shipment->recipient_address }}<br />
                        {{ $shipment->receiver_zip_code ?? $shipment->recipient_zip_code }} {{ $shipment->receiver_city?? $shipment->recipient_city }}<br />
                        {{ trans('country.' . strtolower($shipment->receiver_country ?? $shipment->recipient_country)) }}
                    </div>
                </div>
                <div class="cmr-block" style="padding: 8px 0; float: left; height: 45px; line-height:8pt">
                    <div style="float: right; text-align: right; margin-top: -15px; margin-bottom: 0px width: 170px;">
                        @if(!empty($shipment->delivery_date))
                            Data: <b class="bold">{{ $shipment->delivery_date->format('Y-m-d') }}</b> 
                            Hora: <b class="bold">
                                @if($shipment->end_hour)
                                    {{ $shipment->delivery_date->format('H:i') }}
                                @else
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                @endif
                                </b>
                        @endif
                    </div>
                    <div class="fs-8pt bold text-uppercase">
                        {{ $shipment->recipient_name }}<br/>
                        {{ $shipment->recipient_address }}<br/>
                        {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }} -
                        {{ trans('country.' . strtolower($shipment->recipient_country)) }}
                    </div>
                </div>
                <div class="cmr-row">
                    <div style="float: right; text-align: right; margin-top: -10px; margin-bottom: 0px width: 170px;">
                        @if(!empty($shipment->shipping_date))
                            Data: <b class="bold">{{ $shipment->shipping_date->format('Y-m-d') }}</b> 
                            Hora: <b class="bold">
                            @if($shipment->start_hour)
                                {{ $shipment->shipping_date->format('H:i') }}
                            @else
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            @endif
                            </b>
                        @endif
                    </div>
                    <div class="cmr-block" style="padding: 8px 0; float: left; height: 52px; line-height:8pt; margin-top: -7px">
                        <div class="fs-8pt bold text-uppercase">
                            {{ $shipment->sender_name }}<br/>
                            {{ $shipment->sender_address }}<br/>
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }} -
                            {{ trans('country.' . strtolower($shipment->sender_country)) }}
                            
                        </div>
                    </div>
                </div>
                {{-- DOCUMENTOS ANEXOS --}}
                <div class="cmr-row">
                    <div class="cmr-block" style="width: 360px; float: left; height: 30px;">
                        <div class="fs-8pt bold text-uppercase">
                            {{ $shipment->reference2 }}&nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cmr-block" style="width: 310px; float: left;">
            <div class="text-center" style="height: 225px; ">
                <div class="pull-left" style="width: 100%; text-align: center">
                    @if ($shipment->agency->filepath)
                        <img src="{{ @$shipment->agency->filehost . @$shipment->agency->getCroppa(300) }}"
                            style="max-width: 40mm; max-height: 12mm" class="m-t-20" />
                    @else
                        <h5 style="margin:0px"><b>{{ $shipment->agency->company }}</b></h5>
                    @endif
                </div>
                <div style="width: 100%; margin-top: 0; line-height: 11px" class="fs-7pt">
                    NIF: {{ $shipment->agency->vat }}
                    @if ($shipment->agency->charter)
                        &bull; Alvará {{ $shipment->agency->charter }}
                    @endif
                    <br />
                    {{-- {{ str_limit($shipment->agency->company, 28) }}
                    <br/> --}}
                    <span class="bold">{{ $shipment->agency->address }}
                        {{ $shipment->agency->zip_code }} {{ $shipment->agency->city }}</span><br />
                    Telef: {{ $shipment->agency->phone }}
                    @if ($shipment->agency->mobile)
                        / {{ $shipment->agency->mobile }}
                    @endif
                    @if ($shipment->agency->email)
                        &bull; E-mail: {{ $shipment->agency->email }}
                    @endif
                    <div class="fs-6pt lh-1-2">
                        <span class="fs-8pt bold">{{ $shipment->agency->web }}</span>
                    </div>
                </div>
            </div>
            <div class="cmr-block"
                style="width: 280px; padding: 0px 5px; float: left; margin-top: -20px; height: 55px">
                <div class="fs-8pt bold text-uppercase">
                    {{ @$shipment->service->name }}<br />
                    @if (@$shipment->vehicle)
                        Viatura {{ @$shipment->vehicle }} 
                        @if(@$shipment->trailer)
                        / Reboque {{ @$shipment->trailer }}
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{-- CARGA --}}
    <div class="cmr-row" style="height: 55.5mm; float: left; padding: 25px 10px;">
        <?php $dimension = null; ?>
        @if (!$shipment->pack_dimensions->isEmpty())
            @foreach ($shipment->pack_dimensions as $key => $dimension)
                <div style="width: 425px; float: left;">
                    <div class="cmr-block" style="width: 400px; float: left; height: 10px;">
                        <div class="fs-8pt bold ">
                            {{ $dimension->qty ? $dimension->qty : 1 }}x <span
                                class="text-uppercase">{{ @$dimension->type }}</span>
                            <span
                                class="text-uppercase">{{ $dimension->description ? ' - ' . $dimension->description : '' }}&nbsp;</span>

                            @if ($dimension->description)
                                <small
                                    style="font-weight: normal">({{ $dimension->length }}x{{ $dimension->width }}x{{ $dimension->height }})</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div style="width: 245px; float: left; text-align: center;">
                    <div class="cmr-block" style="width: 85px; float: left; height: 10px;">
                        <div class="fs-8pt bold text-uppercase">
                            &nbsp;
                        </div>
                    </div>
                    <div class="cmr-block" style="width: 80px; float: left; height: 10px;">
                        <div class="fs-8pt bold text-uppercase">
                            {{ money($dimension->weight * $dimension->qty) }}
                        </div>
                    </div>
                    <div class="cmr-block" style="width: 80px; float: left; height: 10px;">
                        <div class="fs-8pt bold text-uppercase">
                            {{ money($dimension->volume, 3) }}
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    {{-- ADR --}}
    <div class="cmr-row" style="height: 0.2mm; padding: 1px 0 0 8mm">
        <div class="cmr-block" style="width: 10px; padding: 1px 0 0 30mm; float: left;">
            <div class="fs-8pt bold text-uppercase">
                {{-- {{ $shipment->adr_class }} --}}
                {{ @$dimension->adr_class }}
            </div>
        </div>
        <div class="cmr-block" style="width: 115px; padding: 0px 5px; float: left;">
            <div class="fs-8pt bold text-uppercase">
                {{-- {{ $shipment->adr_number }} --}}
                {{ @$dimension->adr_number }}
            </div>
        </div>
        <div class="cmr-block" style="width: 60px; padding: 0px 5px; float: left;">
            <div class="fs-8pt bold text-uppercase">
                {{-- {{ $shipment->adr_letter }} --}}
                {{ @$dimension->adr_letter }}
            </div>
        </div>
        <div class="cmr-block" style="width: 100px; padding: 0px 5px; float: left;">
            <div class="fs-8pt bold text-uppercase">
                {{-- *adr --}}
                &nbsp;
            </div>
        </div>
    </div>

    {{-- OBS --}}
    <div class="cmr-row" style="height: 54mm; padding: 20px 0 0 10px;">
        <div class="cmr-block" style="width: 335px; padding: 1px 0 0 0; float: left;">
            <div class="fs-8pt bold text-uppercase" style="height: 180px;">
                {{ $shipment->obs }}
                &nbsp;
            </div>
            <div class="fs-8pt bold text-uppercase" style="height: 20px;">
                @if ($shipment->payment_at_recipient)
                    Portes a Pagar / Carriage forward
                @else
                    Porte Pago / Carriage paid
                @endif
            </div>
        </div>
        <div class="cmr-block" style="width: 330px; height: 27mm; padding: 0px 5px; float: left;">
            <div class="fs-8pt bold text-uppercase">
                &nbsp;
            </div>
        </div>
    </div>

    {{-- FEITO EM --}}
    <div class="cmr-row" style="height: 7mm; padding: 3px 0 0 17mm;">
        <div class="cmr-block" style="width: 390px; float: left;">
            <div class="fs-8pt bold text-uppercase">
                <?php
                $date = new Date($shipment->date);
                ?>
                {{ $date->day . ' de ' . $date->format('F') . ' de ' . $date->year }}
            </div>
        </div>
        <div class="cmr-block" style="width: 160px; padding: 0px 5px; margin-top: -3px; float: left;">
            <div class="fs-8pt bold text-uppercase">
                @if ($shipment->charge_price)
                    {{ money($shipment->charge_price, ' EUR') }}
                @endif
            </div>
        </div>
    </div>
    {{-- FEITO EM --}}
    <?php
    $signaturePickup   = str_replace('data:image/jpeg', 'data:image/png', @$pickupHistory->signature);
    $signatureDelivery = str_replace('data:image/jpeg', 'data:image/png', @$deliveryHistory->signature);
    ?>
    <div class="cmr-row">
        <div class="cmr-block" style="width: 225px; float: left;">
            <div style="padding: 3px; line-height: 11px">
                <div style="margin-top: 0px; background: #fff; text-align: center; position: absolute; height: 128px; margin-top: 5px">
                    @if($signaturePickup)
                        <img src="{{ $signaturePickup }}" style="height: 105px"/>
                    @elseif(!empty(@$pickupHistory) && empty($signaturePickup)) 
                        <span style="color: blue; font-size: 12px;">
                            <br/><br/>
                            PICKUPED NOT SIGNED<br/>
                            RECOLHA NÃO ASSINADA
                        </span>
                    @endif
                </div>
                
                @if(@$pickupHistory)
                <div style="margin-top: -50px">
                    <div style="color: blue">Por/By: <strong style="font-weight: bold;">{{ @$pickupHistory->receiver }}</strong></div>
                    <div style="color: blue">Data/Date: <strong style="font-weight: bold;">{{ @$pickupHistory->created_at }}</strong></div>
                    <div style="color: blue">GPS: <strong style="font-weight: bold; ">{{ @$pickupHistory->latitude }}, {{ @$pickupHistory->longitude }}</strong></div>
                    @if($shipment->sender_email)
                    <div style="color: blue">Email: <strong style="font-weight: bold; ">{{ @$shipment->sender_email }}</strong></div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        <div class="cmr-block" style="width: 220px; padding: 0px 5px; margin-top: -5px; float: left;">
            <div class="text-center">
                <div class="pull-left" style="width: 100%; text-align: center">
                    @if ($shipment->agency->filepath)
                        <img src="{{ @$shipment->agency->filehost . @$shipment->agency->getCroppa(300) }}"
                            style="max-width: 30mm; max-height: 12mm" class="m-t-20" />
                    @else
                        <h5 style="margin:0px"><b>{{ $shipment->agency->company }}</b></h5>
                    @endif
                </div>
                <div style="width: 100%; margin-top: 0; line-height: 11px" class="fs-7pt">
                    NIF: {{ $shipment->agency->vat }}
                    @if ($shipment->agency->charter)
                        &bull; Alvará {{ $shipment->agency->charter }}
                    @endif
                    <br />
                    {{-- {{ str_limit($shipment->agency->company, 28) }}
                    <br/> --}}
                    <span class="bold">{{ $shipment->agency->address }}
                        {{ $shipment->agency->zip_code }} {{ $shipment->agency->city }}</span><br />
                    Telef: {{ $shipment->agency->phone }}
                    @if ($shipment->agency->mobile)
                        / {{ $shipment->agency->mobile }}
                    @endif
                    @if ($shipment->agency->email)
                        &bull; E-mail: {{ $shipment->agency->email }}
                    @endif
                </div>
            </div>
        </div>
        <div class="cmr-block" style="width: 225px; float: left;">
            <div style="padding: 3px; line-height: 11px">
                <div style="margin-top: 0px; background: #fff; text-align: center; position: absolute; height: 128px; margin-top: 8px">
                    @if($signatureDelivery)
                    <img src="{{ $signatureDelivery }}" style="height: 105px"/>
                    @elseif(!empty(@$deliveryHistory) && empty($signatureDelivery)) 
                    <span style="color: blue; font-size: 12px;">
                        <br/><br/>
                        DELIVERY NOT SIGNED<br/>
                        ENTREGA NÃO ASSINADA
                    </span>
                    @endif
                </div>
                <div style="margin-top: -50px">
                    @if(@$deliveryHistory)
                        <div style="font-weight: bold; color: blue">Por/By: {{ @$deliveryHistory->receiver }}</div>
                        <div style="font-weight: bold; color: blue">Data/Date: {{ @$deliveryHistory->created_at }}</div>
                        <div style="font-weight: bold; color: blue">GPS: {{ @$deliveryHistory->latitude }}, {{ @$deliveryHistory->longitude }}</div>
                        @if($shipment->sender_email)
                        <div style="font-weight: bold; color: blue">Email: {{ @$shipment->recipient_email }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
