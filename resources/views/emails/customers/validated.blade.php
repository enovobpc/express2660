@extends(app_email_layout())

@section('content')
    @if($customer->is_validated)
    <h5>Estimado {{ $customer->name }},</h5>
    <p>
        O seu pedido para registo na nossa área de cliente foi aprovado.
        <br/>
        A partir de agora tem acesso à nossa área de cliente onde pode criar e gerir os seus envios, reembolsos, faturação e muito mais.
    </p>
    <table style="width: 100%;
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
    @else
        <h5 style="font-size: 16px">Estimado {{ $customer->name }},</h5>
        <p>
            Lamentamos, mas o seu pedido de registo na nossa área de cliente não foi aprovado.
            <br/>
            Para mais detalhes, por favor entre em contacto connosco.
        </p>
    @endif
@stop