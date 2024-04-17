<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Instalar aplicação web para motoristas</h4>
</div>
<div class="modal-body" style="overflow: scroll">
    <ul class="list-inline" style="width: 1560px; overflow: scroll">
        <li class="float-left w-250px text-center">
            <div class="">
                <h4 class="text-center">
                    Passo 1<br/>
                    <small class="bold">
                        Instalar Google Chrome
                    </small>
                </h4>
                <p>
                    Ao usar o Google Chrome permitirá que a app possa emitir notificações.
                </p>
            </div>
            <img src="{{ asset('assets/mobile/tips/01.png') }}" class="img-responsive" style="border: 1px solid #000"/>
        </li>
        <li class="float-left w-250px text-center">
            <h4 class="text-center">
                Passo 2<br/>
                <small class="bold">
                    Aceder à aplicação online
                </small>
            </h4>
            <p>
                Aceda ao URL <u>{{ route('mobile.index') }}</u> e entre com os dados de motorista.
            </p>
            <img src="{{ asset('assets/mobile/tips/02.png') }}" class="img-responsive" style="border: 1px solid #000"/>
        </li>
        <li class="float-left w-250px text-center">
            <h4 class="text-center">
                Passo 3<br/>
                <small class="bold">
                    Adicionar ao Ecrã Principal
                </small>
            </h4>
            <p>
                Clique no icone <i class="fas fa-list-ul" style="width: 4px;overflow: hidden;margin-left: 2px;"></i> e clique na opção "Adicionar ao Ecrã Principal"
            </p>
            <img src="{{ asset('assets/mobile/tips/03.png') }}" class="img-responsive" style="border: 1px solid #000"/>
        </li>
        <li class="float-left w-250px text-center">
            <h4 class="text-center">
                Passo 4<br/>
                <small class="bold">
                    Dar nome ao atalho
                </small>
            </h4>
            <p>
                Pode alterar o nome do atalho da aplicação para o nome que entender.
            </p>
            <img src="{{ asset('assets/mobile/tips/04.png') }}" class="img-responsive" style="border: 1px solid #000"/>
        </li>
        <li class="float-left w-250px text-center">
            <h4 class="text-center">
                Passo 5<br/>
                <small class="bold">
                    Aceitar Pedidos Permissão
                </small>
            </h4>
            <p>
                Entre na aplicação e aceite todos os pedidos de permissão.
            </p>
            <img src="{{ asset('assets/mobile/tips/05.png') }}" class="img-responsive" style="border: 1px solid #000"/>
        </li>
        <li class="float-left w-250px" style="position: absolute; margin-left: 25px;">
            <h4 class="text-center">
                <i class="fas fa-info-circle"></i> Notas Importantes<br/>
            </h4>
            <hr/>
            <p>
                <b><i class="fas fa-bell"></i> Permitir Notificações</b><br/>
                Deve confirmar que pretende receber notificações quando a aplicação o questionar.
                <br/>
                Ao permitir as notificações, estará a permitir que a aplicação emita uma notificação sempre que exista um novo envio ou recolha.
            </p>
            <p>
                <b><i class="fas fa-send"></i> Permitir Localização</b><br/>
                Deve confirmar que aceita o acesso à localização quando a aplicação o questionar.
                <br/>
                A aplicação regista a localização do motorista apenas enquanto está a ser utilizada.
                Caso a localização do telemóvel seja desativada ou não permitida, a aplicação ficará bloqueada.
            </p>
        </li>
    </ul>
</div>
<div class="modal-footer" style="margin-top: -8px; margin-bottom: -10px;">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>