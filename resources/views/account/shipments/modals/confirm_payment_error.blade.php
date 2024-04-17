<div class="modal" id="modal-confirm-payment-error">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header bg-red">
                <h4 class="modal-title">Pagamento rejeitado</h4>
            </div>
            <div class="modal-body">
                <div class="text-center">
                   {{-- <i class="fas fa-piggy-bank text-red fs-40"></i>--}}
                    <img src="{{ asset('assets/img/default/pig.svg') }}" style="height: 30px"/>
                    <h4>
                        O envio não pode ser pago porque a sua conta não tem saldo suficiente.
                    </h4>
                </div>
                <div class="sync-reason text-red bold text-center error-msg">
                    Saldo conta:
                    {{ money($customer->wallet_balance, Setting::get('app_currency')) }}
                    &bull; Total a Pagar: <span class="total"></span>{{ Setting::get('app_currency') }}
                </div>
                <div class="m-t-30">
                    <p class="lh-1-3">
                        <b>O que fazer agora?</b>
                        <br/>
                        O envio foi gravado mas ficará pendente de pagamento até que carregue de novo a sua conta.
                        <br/>
                        Após carregar a conta, aceda ao envio e clique no menu "Opções > Efetuar Pagamento" para liquidar o envio.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm btn-confirm-ok">Compreendi</button>
            </div>
        </div>
    </div>
</div>