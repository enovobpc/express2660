@extends(app_email_layout())

@section('content')
<h5>Aviso de Vencimento - {{ $invoice->doc_series }} {{ $invoice->doc_id }}</h5>
<p>
    Caro(a) cliente,
<br/>
    @if($daysLeft == 0)
        Relembramos que hoje é o <b>último dia para pagamento</b> da nossa fatura N.º <b>{{ $invoice->doc_series }} {{ $invoice->doc_id }}</b>,
        no valor de <b>{{ money($invoice->doc_total, Setting::get('app_currency')) }}</b> e emitida no dia  {{ $invoice->doc_date }}.</b>
    @else
        Relembramos que ainda se encontra para pagamento a fatura <b>{{ $invoice->doc_series }} {{ $invoice->doc_id }}</b>,
        no valor de <b>{{ money($invoice->doc_total, Setting::get('app_currency')) }}</b>, emitida em {{ $invoice->doc_date }}.
        <br/>
        <b style="color: red">Restam {{ $daysLeft }} {{ $daysLeft > 1 ? 'dias' : 'dia' }} para a data de vencimento.</b>
    @endif
</p>

@if(0)
    <div style="color: #FF0000; background: rgba(255,0,0,0.29); padding: 15px 20px;">
        <p>
            Possui atualmente <b>{{ @$countDocuments }} faturas em atraso</b>
            por liquidar num total de <b style="color: red">{{ money(@$totalDocuments, Setting::get('app_currency'))  }}</b>.
        </p>
    </div>
@endif

<p>
    Solicitamos, por favor, que proceda à sua regularização da mesma dentro do prazo legal.<br/>
    A não regularização dentro do prazo, poderá implicar outros custos ou o bloqueio de conta.
</p>
<table style="width: 100%; border: 1px solid #ddd; font-size: 13px" cellspacing="0" cellpadding="3">
    <tr>
        <th style="background: #dddddd; text-align: left">Data</th>
        <th style="background: #dddddd; text-align: left">Documento</th>
        <th style="background: #dddddd; text-align: left">Referência</th>
        <th style="background: #dddddd; text-align: left">Total</th>
        <th style="background: #dddddd; text-align: left; width: 120px">Vencimento</th>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->doc_date }}</td>
        <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->doc_series }} {{ $invoice->doc_id }}</td>
        <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->reference }}</td>
        <td style="border-bottom: 1px solid #dddddd;">{{ money($invoice->doc_total, Setting::get('app_currency')) }}</td>
        <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->due_date }}</td>
    </tr>
</table>

@if(Setting::get('bank_iban'))
    <hr/>
    <p>
        <b>IBAN para pagamentos: {{ Setting::get('bank_iban') }}</b><br/>
        @if(Setting::get('bank_name'))
            Banco: {{ Setting::get('bank_name') }}
        @endif
    </p>
@endif

<p>
    Pode consultar e efetuar o download de todos os seus documentos emitidos na sua área de cliente.
    <br/>
    Aceda aqui para <a href="{{ route('account.login') }}">Iniciar Sessão</a> na sua conta.
</p>
@stop