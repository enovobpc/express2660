@extends(app_email_layout())

@section('content')
<h5>Candidatura Espontânea</h5>
<br/>
<p>
    Alguém submeteu uma candidatura espontânea.
</p>
<p>
    Para consultar os dados da candidatura aceda ao painel de administração ou utilize o seguinte 
    endereço:<br/>
    <a href="{{ route('admin.website.recruitments.show', $recruitment->hash) }}">{{ route('admin.website.recruitments.show', $recruitment->hash) }}</a>
</p>
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