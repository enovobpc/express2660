@extends(app_email_layout())

@section('content')
    <h3>Recuperação de palavra-passe</h3>
    <p>
        Está a receber este e-mail porque recebemos um pedido para reposição da palavra-passe da sua conta. Clique no botão abaixo para redefinir a sua palavra-passe.
    </p>
    <div style="text-align: center">
        <br/>
        <a href="{{ $url }}" class="button-link">Repôr palavra-passe</a>
    </div>
    <p>
        <br/>
        Se não solicitou a alteração de palavra-passe, ignore este e-mail.
    </p>
@stop