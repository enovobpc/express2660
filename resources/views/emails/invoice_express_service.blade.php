@extends(app_email_layout())

@section('content')
<h5>Faturação de serviços expresso</h5>
<p>
    Estimado cliente,
    <br/>
    Junto enviamos a fatura referente aos serviços expresso abaixo listados.
</p>
<table style="width: 100%; border: 1px solid #ddd; font-size: 13px" cellspacing="0" cellpadding="3">
    <tr>
        <th style="background: #dddddd; text-align: left">Data</th>
        <th style="background: #dddddd; text-align: left">Serviço</th>
        <th style="background: #dddddd; text-align: left">Total</th>
    </tr>
    @foreach($expressServices as $service)
        <tr>
            <td style="border-bottom: 1px solid #dddddd;">{{ $service->date }}</td>
            <td style="border-bottom: 1px solid #dddddd;">{{ $service->title }}</td>
            <td style="border-bottom: 1px solid #dddddd;">{{ money($service->total_price, Setting::get('app_currency')) }}</td>
        </tr>
    @endforeach
</table>
<p>
    Estes documentos estão disponíveis a qualquer momento na sua Área de Cliente para consulta ou download.
    Para mais informações sobre a sua fatura ou resumo de envios, contacte-nos através dos canais habituais.
</p>
<br/>
<div style="text-align: center">
    <a href="{{ route('account.index') }}" class="button-link">Entrar na Área de Cliente</a>
</div>
<br/>
@stop