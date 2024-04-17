@extends(app_email_layout())

@section('content')
<h5>Pedido de informação</h5>
<table class="table" style="margin-top: 10px">
    <tr>
        <td style="width: 80px"><b>Nome</b></td>
        <td>{{ $input['name'] }}</td>
    </tr>
    <tr>
        <td style="width: 80px" class=".column"><b>E-mail</b></td>
        <td>{{ $input['email'] }}</td>
    </tr>
    <tr>
        <td style="width: 80px"><b>Telefone</b></td>
        <td>{{ $input['phone'] }}</td>
    </tr>
    <tr>
        <td style="width: 80px"><b>Mensagem</b></td>
        <td>{{ $input['message'] }}</td>
    </tr>
</table>
<hr/>
<table style="width: 100%">
    <tr>
        <td style="width: 20px"><b>IP</b></td>
        <td style="width: 100px">{{ $input['ip'] }}</td>
        <td style="width: 50px"><b>Data:</b></td>
        <td>{{ date('d/m/Y H:i:s') }}</td>
    </tr>
    
</table>
@stop