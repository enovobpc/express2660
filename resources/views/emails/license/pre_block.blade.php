@extends(app_email_layout())

@section('content')
<h5>Pré-aviso de Bloqueio</h5>
<p>
    Caro(a) cliente,
    <br/>
    Restam <b style="color: red">{{ $days }} dias</b> para liquidar o último pagamento emitido.
    <br/>
    Atualmente possui <b style="color: red">{{ $countUnpaid }} pagamentos</b> em atraso num total de <b style="color: red">{{ money($totalUnpaid, Setting::get('app_currency')) }}</b>.
</p>
<p>
    Caso não proceda ao pagamento de parte ou totalidade do valor em aberto até dia <b>{{ $deadlineDate }}</b>,
    o acesso ao <b>sistema será bloqueado de forma automática</b>.
</p>
<p>
    Agradecemos a sua compreensão.
    <br/>
    Obrigado,<br/>
    A Equipa ENOVO
</p>
@stop