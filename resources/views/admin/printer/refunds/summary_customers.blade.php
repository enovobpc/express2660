<?php $showProviderTrk = Setting::get('customer_show_provider_trk'); ?>
@if(@$customer->iban_refunds)
<h5 style="margin: -10px 0 10px 0">
    IBAN Rembolso: {{ @$customer->iban_refunds }}
</h5>
@endif
<table class="table table-bordered table-pdf font-size-7pt">
    <tr>
        <th>N.º Envio</th>
        @if(Setting::get('customer_show_provider_trk'))
            <th style="width: 90px">TRK Secundário</th>
            <th>Ref.</th>
        @else
            <th>Referência</th>
        @endif
        <th>Remetente</th>
        <th>Destinatário</th>
        <th>Cobrança</th>
       {{-- @if(!Setting::get('refunds_control_customers_hide_received_column'))
        <th class="w-70px">Recebido</th>
        @endif--}}
        @if(!Setting::get('refunds_control_customers_hide_paid_column'))
        <th class="w-70px">Reembolso</th>
        @endif
        <th class="w-120px">Observações</th>
    </tr>
    <?php $documentTotal = 0; $countTotal = 0; ?>
    @foreach($shipments as $shipment)
        <?php
        $documentTotal+= $shipment->charge_price;
        $countTotal++;

        $providerTrk = null;
        if($showProviderTrk) {
            $providerTrk = explode(',', @$shipment->provider_tracking_code);
            $providerTrk = $providerTrk[0];
        }

        ?>
        <tr>
            <td>
                <strong style="font-weight: bold">{{ $shipment->tracking_code }}</strong>
                <br/>
                {{ $shipment->date }}
            </td>
            @if($showProviderTrk)
                <td>{{ $providerTrk }}</td>
            @endif
            <td class="text-center">{{ @$shipment->reference }}</td>
            <td>{{ $shipment->sender_name }}<br/>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}</td>
            <td>{{ $shipment->recipient_name }}<br/>{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</td>
            <td style="font-weight: bold; text-align: center">{{ money($shipment->charge_price, Setting::get('app_currency')) }}</td>
            {{--@if(!Setting::get('refunds_control_customers_hide_received_column'))
            <td>
                @if($shipment->refund_control && $shipment->refund_control->received_method)
                    <b>{{ trans('admin/refunds.payment-methods.'.@$shipment->refund_control->received_method) }}</b>
                    <br/>{{ $shipment->refund_control->received_date }}
                @endif
            </td>
            @endif--}}
            @if(!Setting::get('refunds_control_customers_hide_paid_column'))
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
            @endif
            <td>
                {{ @$shipment->refund_control->customer_obs }}
            </td>
        </tr>
    @endforeach
</table>
<h4 class="text-right m-t-0">
    <small>Total de Envios/Recolhas: <b class="bold" style="color: #000;">{{ $countTotal }}</b></small>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <small>Total a Reembolsos:</small> <b class="bold">{{ money($documentTotal, Setting::get('app_currency')) }}</b>
</h4>