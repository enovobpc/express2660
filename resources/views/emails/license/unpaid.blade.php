@extends(app_email_layout())

@section('content')
<h5>AVISO: Pagamentos em Atraso</h5>
<p>
    Caro(a) cliente,
    <br/>
    Tem <b style="color: red">{{ $countUnpaid }} pagamentos</b> num total de <b style="color: red">{{ money($totalUnpaid, Setting::get('app_currency')) }}</b> em atraso.
    <br/>
    A data limite do último pagamento terminou no dia <b>{{ $deadlineDate }}</b>
</p>
<p>
    Solicitamos por-favor a liquidação do(s) mesmo(s).
</p>
<p>
    Relembramos que ao acumular <b>2 pagamentos sem liquidação</b> o acesso ao sistema é <b>bloqueado</b> de forma automática.
</p>
<p>
    Agradecemos a sua compreensão.
    <br/>
    Obrigado,<br/>
    A Equipa ENOVO
</p>
@stop