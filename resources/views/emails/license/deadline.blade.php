@extends(app_email_layout())

@section('content')
<h5>Data limite de pagamento próxima</h5>
<p>
    Caro(a) cliente,
    <br/>
    Restam <b style="color: red">{{ $days }} dias</b> para a data limite do último pagamento ({{ $deadlineDate }}).
</p>
<p>
    Por favor, solicitamos que liquide o valor em aberto até à data em questão.
    <br/>
    Atualmente possui {{ $countUnpaid ? $countUnpaid . ' pagamentos num total de ' : '' }} {{ money($totalUnpaid, Setting::get('app_currency')) }} por liquidar.
</p>
<p>
    Agradecemos a sua compreensão.
    <br/>
    Obrigado,<br/>
    A Equipa ENOVO
</p>
@stop