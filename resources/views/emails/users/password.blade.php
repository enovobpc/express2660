@extends(app_email_layout())

@section('content')
    @if($isOperator)
        <h5>Bem-vindo à aplicação móvel</h5>
    @else
        <h5>Bem-vindo à área de gestão!</h5>
    @endif
    <p>
        Olá {{ $user->name }},
        <br/>
        @if($isOperator)
            Foi-lhe aberto acesso à aplicação móvel para gestão de recolhas e serviços para entrega.
        @else
            Foi-lhe dado acesso à área de gestão do software ENOVO TMS com o perfíl {{ @$user->roles()->first()->display_name }}.
        @endif
    </p>
    <table style="width: 800px;
    border: 1px solid #ddd;
    padding: 5px 10px 5px 20px;
    background: #eee;">
        <tr>
            <td>
                <p>
                    @if($isOperator)
                        @if(hasModule('app_apk'))
                            Download App: <b>{{ route('home.index') }}/apk</b>
                        @else
                            Acesso: <b>{{ route('mobile.index') }}</b>
                        @endif
                            <br/>
                            Código Motorista: <b>{{ $user->code }}</b>
                            <br/>
                            Palavra-passe: <b>{{ $user->uncrypted_password }}</b>
                    @else
                        Acesso: <b>{{ route('admin.login') }}</b>
                        <br/>
                        E-mail: <b>{{ $user->email }}</b>
                        <br/>
                        Palavra-passe: <b>{{ $user->uncrypted_password }}</b>
                    @endif
                </p>
            </td>
            <td>
                @if($isOperator)
                    @if(hasModule('app_apk'))
                        <a href="{{ route('home.index') }}/apk" class="button-link" style="float: right">Download App Android</a>
                    @else
                        <a href="{{ route('mobile.index') }}" class="button-link" style="float: right">Entrar na App Mobile</a>
                    @endif
                @else
                <a href="{{ route('admin.login') }}" class="button-link" style="float: right">Entrar na Área de Gestão</a>
                @endif
            </td>
        </tr>
    </table>
    <p>
        Depois de iniciar sessão, poderá alterar a sua palavra-passe nas definições de conta.
    </p>
@stop