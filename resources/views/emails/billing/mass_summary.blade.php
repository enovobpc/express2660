@extends(app_email_layout())

@section('content')
    @if($progress == '100')
        <h5>Download Resumo Mensal de Faturação - {{ trans('datetime.month.'.$month) }} de {{ $year }}</h5>
        <p>O processamento do ficheiro em massa está concluido.</p>
        <table class="table" style="margin-top: 10px">
            <tr>
                <td style="width: 80px"><b>Ficheiro: </b></td>
                <td><a href="{{ asset($outputFile) }}">Download do Ficheiro</a></td>
            </tr>
        </table>
    @else
        <h5>Download Resumo Mensal de Faturação - {{ trans('datetime.month.'.$month) }} de {{ $year }}</h5>
        <p>
            Não foi possível gerar o ficheiro devido a um erro de sistema.<br/>
            Tente de novo ou contacte o suporte informático.
        </p>
    @endif
@stop