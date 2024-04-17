@if(Auth::user()->isGuest())
    <div class="alert alert-warning alert-dismissable hidden-print bigger-110">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <strong class="bigger-120">
            <i class="fas fa-info-circle"></i>
            O acesso a esta funcionalidade está limitado na sua conta.
        </strong>
        <p style="font-weight: 600;">
            Use o leitor de código de barras para atribuir envios aos seus motoristas ou alterar o estado dos envios.
        </p>
    </div>
@endif

