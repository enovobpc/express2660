@extends(app_email_layout())

@section('content')
    <h5>Nota de Pagamento {{ $paymentNote->code }}</h5>
    <p>
        Estimado fornecedor,
        <br/>
        Junto enviamos a nossa nota de pagamento N.º {{ $paymentNote->code }} no valor de {{ money($paymentNote->total, Setting::get('app_currency')) }}
    </p>
@stop