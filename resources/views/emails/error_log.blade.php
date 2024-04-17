@extends(app_email_layout())

@section('content')
    <h5>Detalhe de erro</h5>
    <table class="table" style="margin-top: 10px">
        <tr>
            <td style="width: 100px" class="column"><b>Método</b></td>
            <td>{{ Request::method() }}</td>
        </tr>
        <tr>
            <td style="width: 100px" class="column"><b>URL</b></td>
            <td>{{ Request::fullUrl() }}</td>
        </tr>
        <tr>
            <td style="width: 100px"><b>Código</b></td>
            <td>{{ $exception->getCode() }}</td>
        </tr>
        <tr>
            <td style="width: 100px" class="column"><b>Motivo</b></td>
            <td>{{ $exception->getMessage() }}</td>
        </tr>
        <tr>
            <td style="width: 100px" class="column"><b>Ficheiro</b></td>
            <td>{{ $exception->getFile() }}</td>
        </tr>
        <tr>
            <td style="width: 100px" class="column"><b>Linha</b></td>
            <td>{{ $exception->getLine() }}</td>
        </tr>
        <tr>
            <td style="width: 100px" class="column"><b>IP</b></td>
            <td>{{ client_ip() }}</td>
        </tr>

    </table>
    <hr/>
    <table style="width: 100%">
        <tr>
            <td style="width: 50px"><b>Data:</b></td>
            <td>{{ date('d/m/Y H:i:s') }}</td>
        </tr>
    </table>
@stop