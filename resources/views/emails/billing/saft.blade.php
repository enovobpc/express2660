@extends(app_email_layout())

@section('content')
    <h5>Envio ficheiro SAF-T {{ trans('datetime.month.'.$saft->month) }} {{ $saft->year }}</h5>
    <p>
        Estimado TOC,
        <br/>
        Enviamos em anexo o ficheiro SAF-T referente ao movimentos fiscais da nossa empresa<br/>
        {{ @$company->vat }} {{ @$company->name }} no mês <b>{{ trans('datetime.month.'.$saft->month) }} de {{ $saft->year }}</b>.
    </p>
    <p>
        Qualquer dúvida, não hesite em contactar.
        <br/>
        Obrigado desde já.
    </p>
@stop