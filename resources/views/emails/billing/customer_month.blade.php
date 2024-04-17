@extends(app_email_layout())

@section('content')

    @if(@$data['period_name'])
        <h5>Resumo de Faturação {{ $data['period_name'] }}</h5>
        <p>
            Estimado cliente,
            <br/>
            Junto enviamos o resumo de serviços e faturação referente a <b>{{ $data['period_name'] }}</b>.
        </p>
    @else
        <h5>{{ trans('admin/billing.types.'.$invoice->doc_type) . ' '.$invoice->name }}</h5>
        <p>
            Estimado cliente,
            <br/>
            @if(@$invoice->doc_type == 'nodoc')
                Junto enviamos o resumo de serviços e faturação aos serviços subscritos.
            @else
                Junto enviamos a o documento {{ trans('admin/billing.types.'.$invoice->doc_type) . ' '.$invoice->name }} aos serviços por nós prestados.
            @endif
        </p>
    @endif
    <p>
        Estes documentos estão disponíveis a qualquer momento na sua Área de Cliente para consulta ou download.
        Para mais informações sobre a sua fatura ou resumo de envios, contacte-nos através dos canais habituais.
    </p>
    @if(config('app.source') == 'agtransportes')
        <p>
            Caso queira receber a fatura em papel, por favor envie o pedido para por e-mail para geral@agtransportes.pt<br/>
            Os comprovativos de entrega têm consulta online. Caso necessite dos originais, por favor envie pedido por e-mail para geral@agtransportes.pt
        </p>
    @endif

    @if(Setting::get('bank_iban'))
        <hr/>
        <p>
            <b>IBAN para pagamentos: {{ Setting::get('bank_iban') }}<br/>
                @if(Setting::get('bank_name'))
                    Banco: {{ Setting::get('bank_name') }}
            @endif
        </p>
    @endif
    <br/>
    <div style="text-align: center">
        <a href="{{ route('account.index') }}" class="button-link">Entrar na Área de Cliente</a>
    </div>
    <br/>
@stop