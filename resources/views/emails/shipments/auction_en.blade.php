@extends(app_email_layout())

@section('content')
<div>
    <h2>Dear {{ @$provider->name }},</h2>
    <h3>You have a New Service Request: </h3>
    <h4>#{{$shipment->tracking_code}}</h4>
</div>
<table style="width: 700px">
            <tr>
                <td style="width: 50%">
                    <h4 style="margin: 0; font-size: 18px" >Origin</h4>
                    <p>
                        {{ $shipment->sender_name }}<br/>
                        {{ $shipment->sender_address }}<br/>
                        {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                    </p>
                   
                </td>
                <td style="width: 50%">
                    <h4 style="margin: 0; font-size: 18px">Distination</h4>
                    <p>
                        {{ $shipment->recipient_name }}<br/>
                        {{ $shipment->recipient_address }}<br/>
                        {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                    </p>
                 </td>
            </tr>
        </table>
        <br>
        <br>
        
        <h3>More information</h3>
        <hr>

        <table style="width: 100%">
            <tr>
                        
            </tr>
        </table>

        <table style="width: 100%">
            <tr>
                <td style="width: 50%">
                    <p>
                        Volume
                    </p>
                </td>
                <td style="width: 50%">
                    <p>
                        {{ $shipment->weight }}
                       
                    </p>
                </td>
            </tr>
            <tr>
                <td style="width: 50%">
                    <p>
                        Service date
                    </p>
                </td>
                <td style="width: 50%">
                    <p>
                        {{ $shipment->date }}
                       
                    </p>
                </td>
            </tr>
            <tr>
                <td style="width: 50%">
                    <p>
                        Quantity
                    </p>
                </td>
                <td style="width: 50%">
                    <p>
                        {{ $shipment->volumes }}
                       
                    </p>
                </td>
            </tr>
            <tr>
                <td style="width: 50%">
                    <p>
                        Observations
                    </p>
                </td>
                <td style="width: 50%">
                    <p>
                        {{ $shipment->obs }}
                       
                    </p>
                </td>
            </tr>
        </table>
        <br/><br/>
        <b>You can submit your service application until: {{$limitDay}}</b> <br/>
        <h3>
            You can cote by the email <b>{{env('EMAIL_AUCTION')}}</b> or by skype. 
        </h3>
</p>
@stop

