@extends(app_email_layout())

@section('content')
    <div style="min-width: 650px">
        <h5>Notificação de Reembolso</h5>
        <p>
            Estimado parceiro,
            <br/>
            Procedemos ao reembolso de {{ money(@$shipments->sum('charge_price'), Setting::get('app_currency')) }} referente aos envios em anexo.
            <br/>
            Por favor confirme a recepção destes valores na sua área de gestão.
        </p>
        <table style="width: 100%; border: 1px solid #ddd; font-size: 13px" cellspacing="0" cellpadding="4">
            <tr>
                <th style="background: #dddddd; text-align: left; width: 100px;">Envio</th>
                <th style="background: #dddddd; text-align: left">Cliente</th>
                <th style="background: #dddddd; text-align: left; width: 60px">Total</th>
                <th style="background: #dddddd; text-align: left; width: 100px">Reembolso</th>
            </tr>
            @foreach($shipments as $shipment)
                <tr>
                    <td style="border-bottom: 1px solid #dddddd;">
                        {{ @$shipment->tracking_code }}
                    </td>
                    <td style="border-bottom: 1px solid #dddddd;">{{ @$shipment->customer->name }}</td>
                    <td style="border-bottom: 1px solid #dddddd;">{{ money(@$shipment->charge_price, Setting::get('app_currency')) }}</td>
                    <td style="border-bottom: 1px solid #dddddd;">
                        @if(@$shipment->refund_control->payment_method)
                            {{ trans('admin/refunds.payment-methods.' . @$shipment->refund_control->payment_method) }}
                        @elseif(@$shipment->refund_control->payment_method)
                            {{ @$shipment->refund_control->payment_method }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@stop