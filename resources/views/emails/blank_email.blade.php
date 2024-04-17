@extends(app_email_layout())

@section('content')
    <div style="width: 700px">
        {!! $email->message !!}
    </div>
@stop