@extends(app_email_layout())

@section('content')
<h5>Manifestação de interesse em ser cliente</h5>
<table class="table" style="margin-top: 10px">
    <tr>
        <td style="width: 100px"><b>Tipo</b></td>
        @if($input['type'] == 'company')
        <td>Empresa</td>
        @else
        <td>Particular</td>
        @endif
    </tr>
    @if(isset($input['company']))
    <tr>
        <td style="width: 100px"><b>Empresa</b></td>
        <td>{{ $input['company'] }}</td>
    </tr>
    @endif
    <tr>
        <td style="width: 100px"><b>Nome</b></td>
        <td>{{ $input['name'] }}</td>
    </tr>
    <tr>
        <td style="width: 100px" class="column"><b>E-mail</b></td>
        <td>{{ $input['email'] }}</td>
    </tr>
    <tr>
        <td style="width: 100px"><b>Telefone</b></td>
        <td>{{ $input['phone'] }}</td>
    </tr>
    <tr>
        <td style="width: 100px"><b>Conselho</b></td>
        <td>{{ $input['district'] }}</td>
    </tr>
    <tr>
        <td style="width: 100px"><b>Localidade</b></td>
        <td>{{ $input['city'] }}</td>
    </tr>
    <tr>
        <td style="width: 100px"><b>Necessidades</b></td>
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