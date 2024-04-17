@extends(app_email_layout())

@section('content')
<h5>Acesso à Plataforma Bloqueado</h5>
<p>
    Caro(a) cliente,
    <br/>
    <b style="color: red">Por falta de pagamento das mensalidades</b> referentes à nossa Plataforma de Gestão de Transportes e Logística,
    <b>o sistema procedeu a um bloqueio automático</b> das funcionalidades até regularização de parte ou totalidade do valor em aberto.
</p>
<p>
    Possui atualmente <b style="color: red">{{ $countUnpaid }} documentos</b> por liquidar num total de <b style="color: red">{{ money($totalUnpaid, Setting::get('app_currency')) }}</b>.
</p>
<p>
    Agradecemos a sua compreensão.
    <br/>
    Obrigado,<br/>
    A Equipa ENOVO
</p>
@stop