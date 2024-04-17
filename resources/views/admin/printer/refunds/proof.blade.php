<div style="width: 44.5%; float: left; padding: 0.4cm 0.8cm 0 0.8cm; height: 19cm;  border-right: 1px dashed #999;">
    <div class="header">
        <div style="width: 33%; float: left;">
            @if($shipments->first()->senderAgency->filepath)
                <img src="{{ asset(@$shipments->first()->senderAgency->filehost . $shipments->first()->senderAgency->getCroppa(300)) }}" style="max-width: 45mm; height: 15mm; margin-top: -5px"/>
            @else
                <h5 style="margin:0px"><b>{{ $shipments->first()->senderAgency->company }}</b></h5>
            @endif
        </div>
        <div style="width: 66%; float: left; text-align: right;">
            <h4 class="m-0 m-b-15">
                Comprovativo<br/>
                <small>Pagamento de Reembolsos</small>
            </h4>
        </div>
    </div>
    <h5 style="line-height: 15px; font-size: 12px"><small>Cliente:</small> {{ @$shipments->first()->customer->code }} - {{ @$shipments->first()->customer->name }}</h5>
    <div style="height: 12.6cm;">
    <table class="table table-bordered table-pdf font-size-7pt">
        <tr>
            <th>N.º Envio</th>
            <th>Remetente</th>
            <th>Destinatário</th>
            <th>Cobrança</th>
            <th class="w-80px">Recebimento</th>
            <th class="w-80px">Reembolso</th>
            <th class="w-80px">Obs</th>
        </tr>
        <?php $documentTotal = 0; $countTotal = 0; ?>
        @foreach($shipments as $shipment)
            <?php
            $documentTotal+= $shipment->charge_price;
            $countTotal++;
            ?>
            <tr>
                <td>
                    <b>{{ $shipment->tracking_code }}</b><br/>
                    {{ $shipment->date }}
                </td>
                <td>{{ $shipment->sender_name }}{{--<br/>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}--}}</td>
                <td>{{ $shipment->recipient_name }}{{--<br/>{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}--}}</td>
                <td><b>{{ money($shipment->charge_price, Setting::get('app_currency')) }}</b>
                </td>
                <td>
                    @if($shipment->refund_control && $shipment->refund_control->received_method)
                        <b>{{ trans('admin/refunds.payment-methods.'.$shipment->refund_control->received_method) }}</b>
                        <br/>{{ $shipment->refund_control->received_date }}
                    @endif
                </td>
                <td>
                    @if(@$shipment->refund_control->canceled)
                        #Cancelado#
                        <br/>
                    @else
                        @if($shipment->refund_control && $shipment->refund_control->payment_method)
                            <b>{{ trans('admin/refunds.payment-methods.'.$shipment->refund_control->payment_method) }}</b>
                            <br/>{{ $shipment->refund_control->payment_date }}
                        @endif
                    @endif
                </td>
                <td>{{ @$shipment->refund_control->customer_obs }}</td>
            </tr>
        @endforeach
    </table>
    </div>
    <h4 class="text-right m-t-0">
        <small>Total de Expedições: <b class="bold" style="color: #000;">{{ $countTotal }}</b></small>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <small>Total a Reembolsar:</small> <b class="bold">{{ money($documentTotal, Setting::get('app_currency')) }}</b>
    </h4>
    <hr style="margin-top: 5px; margin-bottom: 5px"/>
    <h5 style="line-height: 15px; font-size: 12px">
        Declaro que recebi da {{ $shipments->first()->senderAgency->company }}, com o NIF {{ $shipments->first()->senderAgency->vat }},
        a quantia e a forma de pagamento acima mencionadas relativas ao reembolso das
        expedições listadas.
    </h5>
    <div style="width: 35%; float: left; padding: 3px; text-align: center">
        <div style="border: 1px solid #ddd;">
            Recebido em<br/><br/>
            ______/ ____________/ _________
        </div>
    </div>
    <div style="width: 62.5%; float: left; padding: 3px;">
        <div style="border: 1px solid #ddd;">
            &nbsp;&nbsp;Assinatura do Receptor<br/><br/><br/>

        </div>
    </div>
</div>
<div style="width: 44.5%; float: left; padding: 0.4cm 0.8cm 0 0.8cm; height: 19cm;">
    <div class="header">
        <div style="width: 33%; float: left;">
            @if($shipments->first()->senderAgency->filepath)
                <img src="{{ asset(@$shipments->first()->senderAgency->filehost . $shipments->first()->senderAgency->getCroppa(300)) }}" style="max-width: 45mm; height: 15mm; margin-top: -5px"/>
            @else
                <h5 style="margin:0px"><b>{{ $shipments->first()->senderAgency->company }}</b></h5>
            @endif
        </div>
        <div style="width: 66%; float: left; text-align: right;">
            <h4 class="m-0 m-b-15">
                Comprovativo<br/>
                <small>Pagamento de Reembolsos</small>
            </h4>
        </div>
    </div>
    <h5 style="line-height: 15px; font-size: 12px"><small>Cliente:</small> {{ @$shipments->first()->customer->code }} - {{ @$shipments->first()->customer->name }}</h5>
    <div style="height: 12.6cm;">
        <table class="table table-bordered table-pdf font-size-7pt">
            <tr>
                <th>N.º Envio</th>
                <th>Remetente</th>
                <th>Destinatário</th>
                <th>Cobrança</th>
                <th class="w-80px">Recebimento</th>
                <th class="w-80px">Reembolso</th>
                <th class="w-80px">Obs</th>
            </tr>
            <?php $documentTotal = 0; $countTotal = 0; ?>
            @foreach($shipments as $shipment)
                <?php
                $documentTotal+= $shipment->charge_price;
                $countTotal++;
                ?>
                    <tr>
                        <td>
                            <b>{{ $shipment->tracking_code }}</b><br/>
                            {{ $shipment->date }}
                        </td>
                        <td>{{ $shipment->sender_name }}{{--<br/>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}--}}</td>
                        <td>{{ $shipment->recipient_name }}{{--<br/>{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}--}}</td>
                        <td><b>{{ money($shipment->charge_price, Setting::get('app_currency')) }}</b>
                        </td>
                        <td>
                            @if($shipment->refund_control && $shipment->refund_control->received_method)
                                <b>{{ trans('admin/refunds.payment-methods.'.$shipment->refund_control->received_method) }}</b>
                                <br/>{{ $shipment->refund_control->received_date }}
                            @endif
                        </td>
                        <td>
                            @if(@$shipment->refund_control->canceled)
                                #Cancelado#
                                <br/>
                            @else
                                @if($shipment->refund_control && $shipment->refund_control->payment_method)
                                    <b>{{ trans('admin/refunds.payment-methods.'.$shipment->refund_control->payment_method) }}</b>
                                    <br/>{{ $shipment->refund_control->payment_date }}
                                @endif
                            @endif
                        </td>
                        <td>{{ @$shipment->refund_control->customer_obs }}</td>
                    </tr>
            @endforeach
        </table>
    </div>
    <h4 class="text-right m-t-0">
        <small>Total de Expedições: <b class="bold" style="color: #000;">{{ $countTotal }}</b></small>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <small>Total a Reembolsar:</small> <b class="bold">{{ money($documentTotal, Setting::get('app_currency')) }}</b>
    </h4>
    <hr style="margin-top: 5px; margin-bottom: 5px"/>
    <h5 style="line-height: 15px; font-size: 12px">
        Declaro que recebi da {{ $shipments->first()->senderAgency->company }}, com o NIF {{ $shipments->first()->senderAgency->vat }},
        a quantia e a forma de pagamento acima mencionadas relativas ao reembolso das
        expedições listadas.
    </h5>
    <div style="width: 35%; float: left; padding: 3px; text-align: center">
        <div style="border: 1px solid #ddd;">
            Recebido em<br/><br/>
            ______/ ____________/ _________
        </div>
    </div>
    <div style="width: 62.5%; float: left; padding: 3px;">
        <div style="border: 1px solid #ddd;">
            &nbsp;&nbsp;Assinatura do Receptor<br/><br/><br/>

        </div>
    </div>
</div>