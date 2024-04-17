@if(Auth::user()->isGuest())
    <div class="alert alert-warning alert-dismissable hidden-print bigger-110">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <strong class="bigger-120">
            <i class="fas fa-info-circle"></i>
            @trans('O acesso a esta funcionalidade está limitado na sua conta.')'
        </strong>

        <button class="btn btn-sm btn-default" data-toggle="show-more">@trans('Saiba mais') <i class="fas fa-caret-down"></i></button>
        <p style="font-weight: 600;">
            @trans('Crie fichas de cliente, configure tabelas de preços, tenha acesso às contas corrente e dê acesso à área de cliente.')'
        </p>
        <div class="more-about" style="display: none;">
            <hr/>
            <p>
                <b>@trans('Crie ficha para os seus clientes e dê-lhes acesso à área de cliente para que eles possam fazer os seus envios.')</b>
            </p>
            <ul>
                <li>@trans('Registe os seus clientes')</li>
                <li>@trans('Gestão dos destinatários')</li>
                <li>@trans('Configuração das tabelas de preços e avenças mensais')</li>
                <li>@trans('Acesso à conta corrente e faturas por liquidar')</li>
                <li>@trans('Atribuição de acesso à conta de cliente no site.')</li>
            </ul>
            <hr/>
            <p>
                @trans('Gostaria de experimentar na totalidade ou mais informação? Desenvolvemos software à medida das suas necessidades.')<br/>
                @trans('Visite') <b>www.quickbox.pt</b> | <i class="fas fa-phone"></i> <b>910 431 010</b> <i class="fas fa-envelope"></i> <b>info@quickbox.pt</b>
            </p>
        </div>
    </div>
@endif