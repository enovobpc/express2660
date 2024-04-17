@extends(app_email_layout())

@section('content')
    <h5>{{ $customerMessage->subject }}</h5>
    <p>
        Caro(a) cliente,<br/>
    </p>
    <p>
        {!! nl2br($customerMessage->message) !!}
    </p>
@stop