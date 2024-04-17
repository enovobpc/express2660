@extends(app_email_layout())

@section('content')
@if($locale == 'pt' || $locale == 'ao')
    <h5 style="font-size: 16px">Report Diário</h5>
    <p>
        Caro(a) cliente,<br/>
        Em anexo remetemos o Report Diário do estado atual dos seus envios.<br/>
        Pode consultar o estado dos seus envios em tempo real na sua área de cliente em: <br/>
        <a href="{{ route('account.index') }}">{{ route('account.index') }}</a>
    </p>
@else
    <h5 style="font-size: 16px">Daily Report</h5>
    <p>
        Dear Customer,<br/>
        Attached is the Daily Report of the current status of your shipments.<br/>
        You can check the status of your shipments by accessing your customer area in: <br/>
        <a href="{{ route('account.index') }}">{{ route('account.index') }}</a>
    </p>
@endif
@stop