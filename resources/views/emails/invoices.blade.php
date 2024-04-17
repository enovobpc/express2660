@extends(app_email_layout())

@section('content')
<h5>Reenvio de Documentos</h5>
<p>
    Caro(a) cliente,
    <br/>
    @if($attachInvoice && $attachReceipt)
    Em anexo reenviamos os documentos abaixo listados assim como os respetivos recibos.
    @elseif($attachInvoice && !$attachReceipt)
    Em anexo reenviamos os documentos abaixo listados.
    @else
    Em anexo reenviamos os recibos referentes às faturas abaixo listadas.
    @endif
</p>
<table style="width: 100%; border: 1px solid #ddd; font-size: 13px" cellspacing="0" cellpadding="3">
    <tr>
        <th style="background: #dddddd; text-align: left">Data</th>
        <th style="background: #dddddd; text-align: left">Documento</th>
        <th style="background: #dddddd; text-align: left">Referência</th>
        <th style="background: #dddddd; text-align: left">Total</th>
        <th style="background: #dddddd; text-align: left; width: 120px">Vencimento</th>
    </tr>
    @foreach($invoices as $invoice)
        <tr>
            <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->date->format('Y-m-d') }}</td>
            <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->doc_serie }} {{ $invoice->doc_id }}</td>
            <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->reference }}</td>
            <td style="border-bottom: 1px solid #dddddd;">{{ money($invoice->total, Setting::get('app_currency')) }}</td>
            <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->due_date->format('Y-m-d') }}</td>
        </tr>
    @endforeach
</table>
<p>
    Pode consultar e efetuar o download de todos os seus documentos emitidos na sua área de cliente.
    <br/>
    Aceda aqui para <a href="{{ route('account.login') }}">Iniciar Sessão</a> na sua conta.
</p>
@stop