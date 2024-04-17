@extends(app_email_layout())

@section('content')

    @if(count($data['shipments']) > 1)
        <h5>Faturação de serviços</h5>
    @elseif(count($data['shipments']) == 1)
        <h5>Fatura do envio {{ @$data['shipment']->tracking_code }}</h5>
    @elseif(count($data['covenants']) >= 1)
        <h5>Fatura de Avença Mensal</h5>
    @endif
    <p>
        Estimado cliente,
        <br/>
        @if(count($data['shipments']) > 1)
            Junto enviamos os documentos referentes à faturação de serviços por nós prestados.
        @elseif(count($data['shipments']) == 1)
            Junto enviamos os documentos referente à liquidação do envio N.º <b>{{ @$data['shipment']->tracking_code }}</b> para <b>{{ @$data['shipment']->recipient_name }}</b>.
        @elseif(count($data['covenants']) >= 1)
            Junto enviamos a fatura referente à avença mensal por nós acordada.
        @endif
    </p>
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