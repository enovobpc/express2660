@extends(app_email_layout())

@section('content')
    <h5>{{ trans('admin/billing.types.'.$receipt->doc_type) . ' '.$receipt->name }}</h5>
    <p>
        Caro(a) cliente,
        <br/>
        Em anexo enviamos o recibo referente às faturas abaixo listadas.
    </p>
    <table style="width: 100%; border: 1px solid #ddd; font-size: 13px" cellspacing="0" cellpadding="3">
        <tr>
            <th style="background: #dddddd; text-align: left">Data</th>
            <th style="background: #dddddd; text-align: left">Documento</th>
            <th style="background: #dddddd; text-align: left">Total</th>
            <th style="background: #dddddd; text-align: left; width: 120px">Vencimento</th>
        </tr>
        @foreach($invoices as $invoice)
            <tr>
                <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->doc_date }}</td>
                <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->doc_series }} {{ $invoice->doc_id }}</td>
                <td style="border-bottom: 1px solid #dddddd;">{{ money($invoice->doc_total, Setting::get('app_currency')) }}</td>
                <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->due_date }}</td>
            </tr>
        @endforeach
    </table>
    <p>
        Pode consultar e efetuar o download de todos os seus documentos emitidos na sua área de cliente.
        <br/>
        Aceda aqui para <a href="{{ route('account.login') }}">Iniciar Sessão</a> na sua conta.
    </p>
@stop