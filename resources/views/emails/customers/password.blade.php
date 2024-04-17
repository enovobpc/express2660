@extends(app_email_layout())

@section('content')
    <h5>Bem-vindo à área de cliente!</h5>
    <p>
        Estimado {{ $customer->name }},<br/>

        Obrigado por confiar nos nossos serviços. Para uma maior flexibilidade e gestão da sua parte,
        abrimos-lhe um acesso à nossa área de cliente onde pode criar e
        gerir os seus envios, reembolsos, faturação e muito mais.
    </p>
    <table style="width: 800px;
    border: 1px solid #ddd;
    padding: 5px 10px 5px 20px;
    background: #eee;">
        <tr>
            <td>
                <p>
                    E-mail: <b>{{ $customer->email }}</b>
                    <br/>
                    Palavra-passe: <b>{{ $customer->uncrypted_password }}</b>
                </p>
            </td>
            <td>
                <a href="{{ route('account.index') }}" class="button-link" style="float: right">Entrar na Área de Cliente</a>
            </td>
        </tr>
    </table>
    <p>
        Depois de iniciar sessão, poderá alterar a sua palavra-passe e os seus dados de expedição e faturação nas definições de conta.
    </p>
@stop