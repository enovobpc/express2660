@if(Auth::user()->isGuest())
    <div class="alert alert-warning alert-dismissable hidden-print bigger-110">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <strong class="bigger-120">
            <i class="fas fa-info-circle"></i>
            Emita as faturas dos seus clientes em um clique.
        </strong>

        <button class="btn btn-sm btn-default" data-toggle="show-more">Saiba mais <i class="fas fa-caret-down"></i></button>
        <p style="font-weight: 600;">
            Emita intantâneamente faturas mensais e envie-as automáticamente por email, com apenas um clique.
        </p>
        <div class="more-about" style="display: none;">
            <hr/>
            <p>
                <b>O nosso programa está directamente ligado com o programa de faturação keyinvoice.</b>
            </p>
            <ul>
                <li>Cálculo automático dos valores a faturar</li>
                <li>Emita diretamente faturas pelo programa</li>
                <li>Acesso à conta corrente dos clientes</li>
                <li>Envio automático por e-mail de faturas e resumo de envios do mês</li>
            </ul>
            <hr/>
            <p>
                Gostaria de experimentar na totalidade ou mais informação? Desenvolvemos software à medida das suas necessidades.<br/>
                Visite <b>www.quickbox.pt</b> | <i class="fas fa-phone"></i> <b>910 431 010</b> <i class="fas fa-envelope"></i> <b>info@quickbox.pt</b>
            </p>
        </div>
    </div>
@endif