@extends(app_email_layout())

@section('content')
    <h5>API connection details</h5>
    <p>
        Hi {{ @$oauthClient->customer->name }},<br/>

        Thank you for trusting our services. <br/>
        Then we send the documentation and configuration data for our API.
    </p>
    <table style="width: 100%;
    border: 1px solid #ddd;
    padding: 5px 10px 5px 20px;
    background: #eee;">
        <tr>
            <td>
                <p style="font-size: 15px; line-height: 1.5">
                    <b>Documentation</b>: {{ route('api.docs.index') }}<br/>
                    <b>Track & Trace</b>: {{ route('tracking.index', ['tracking' => '']) }}<br/><br/>
                    <b>client_id</b>: {{ $oauthClient->id }}<br/>
                    <b>client_secret</b>: {{ $oauthClient->secret }}<br/>
                    <b>username</b>: {{ @$oauthClient->customer->email ? @$oauthClient->customer->email : '<email area cliente>' }}<br/>
                    <b>password</b>: {{ @$oauthClient->customer->uncrypted_password ? @$oauthClient->customer->uncrypted_password  : '<password area cliente>' }}<br/>
                </p>
            </td>
        </tr>
    </table>
    <p>
        API integration support: suport@enovo.pt
    </p>
@stop