@if(Auth::user()->isGuest())
    <div class="alert alert-warning alert-dismissable hidden-print bigger-110">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <strong class="bigger-120">
            <i class="fas fa-info-circle"></i>
            @trans('Está a usar uma conta experimental. Muitas funcionalidades não estão disponíveis.')'
        </strong>
        <button class="btn btn-sm btn-default" data-toggle="show-more">@trans('Saiba mais sobre esta aplicação.')' <i class="fas fa-caret-down"></i></button>
        <div class="more-about" style="display: none;">
            <hr/>
            <p>
                <b>@trans('Disponha de uma aplicação totalmente sua e interligada com os seus parceiros.')</b>
            <p>
            <ul>
                <li>@trans('Desenvolva o website da sua empresa, com área de cliente e track & trace') </li>
                <li>@trans('Faça a gestão da faturação aos seus clientes, fornecedores e agências parceiras.')</li>
                <li>@trans('Faça a gestão dos seus envios e recolhas.')</li>
                <li>@trans('Receba pedidos de envio e recolha de outras agências, diretamente na sua aplicação.')</li>
                <li>@trans('Possibilidade de ligar diretamente com Enviália, GLS, Tipsa, CTT, Chonopost entre outros.')</li>
                <li>@trans('Faça a gestão completa de fichas de cliente, tabelas de preços e contactos.')</li>
                <li>@trans('Ofereça aos seus clientes acesso a uma área de cliente.')</li>
                <li>@trans('Faça a gestão das suas frotas automóveis.')</li>
                <li>@trans('Possibilidade de desenvolver funcionalidades por medida.')</li>
            </ul>
            <hr/>
            <p>
                @trans('Gostaria de experimentar na totalidade ou mais informação? Desenvolvemos software à medida das suas necessidades.')<br/>
                @trans('Ligue') <b>910 431 010</b> @trans('ou contacte por e-mail para') <b>geral@enovo.pt</b>
            </p>
        </div>
        <p>
            @trans('Gostaria de experimentar ou adquirir o software na sua totalidade ou quer mais informações sobre as funcionalidades?')'
            @trans('Visite') <b>www.quickbox.pt</b> | <i class="fas fa-phone"></i> <b>910 431 010</b> | <i class="fas fa-envelope"></i> <b>info@quickbox.pt</b>
        </p>
    </div>
@endif