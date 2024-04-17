<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Sem saldo em conta</h4>
</div>
<div class="modal-body">
    <h4 class="m-0 text-center p-t-30 p-b-0 text-blue">
        {{--<i class="fas fa-piggy-bank fs-50 m-b-0"></i>--}}
        <img src="{{ asset('assets/img/default/pig.svg') }}" style="height: 90px; margin-bottom: 10px"/>
        <br/>
        <b>Primeiro precisa carregar a sua conta!</b>
    </h4>
    <h3 class="text-center m-t-0"><small>Saldo Atual</small><br/>{{ money($customer->wallet_balance, Setting::get('app_currency')) }}</h3>
    <p class="text-black text-center p-b-30 fs-16">
        @if(Setting::get('wallet_min_amount'))
            Para poder criar um envio, precisa ter um saldo de pelo menos {{ money(Setting::get('wallet_min_amount'), Setting::get('app_currency')) }} na sua conta.
            <br/>
            Por favor, carregue a sua conta antes de continuar.
        @else
            Para poder criar um envio, precisa primeiro carregar saldo na sua conta.
            <br/>

        @endif
    </p>
    <div class="text-center m-b-30">
        <a href="{{ route('account.wallet.create') }}"
           class="btn btn-black"
           data-toggle="modal"
           data-target="#modal-remote-xs">Carregar Conta</a>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>
