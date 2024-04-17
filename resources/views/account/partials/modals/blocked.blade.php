<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Conta suspensa</h4>
</div>
<div class="modal-body">
    <h4 class="m-0 text-center p-t-30 p-b-15 text-red">
        <i class="fas fa-ban fs-40 m-b-10"></i>
        <br/>
        @if($isShippingBlocked['reason'] == 'plafound' && hasModule('invoices'))
            <b>Atingiu o plafound {{ Setting::get('billing_method') == '30d' ? 'mensal' : 'quinzenal' }}</b>
        @else
            <b>Conta suspensa por pagamentos em atraso.</b>
        @endif
    </h4>
    @if($isShippingBlocked['reason'] == 'plafound')
        @if(!hasModule('invoices'))
            <p class="text-black text-center p-b-30 fs-16">
                Para mais informações contacte o apoio ao cliente.
            </p>
        @else
            <p class="text-black text-center p-b-30 fs-16">
                Atingiu o plafound {{ Setting::get('billing_method') == '30d' ? 'mensal' : 'quinzenal' }} de {{ money($isShippingBlocked['limit'], Setting::get('app_currency')) }}.
                <br/>
                Para mais informações contacte o apoio ao cliente.
            </p>
        @endif
    @elseif($isShippingBlocked['reason'] == 'credit')
        <p class="text-black text-center p-b-30 fs-16">
            Atingiu o plafound máximo de crédito em dívida de {{ money($isShippingBlocked['limit'], Setting::get('app_currency')) }}.
            <br/>
            Por favor, regularize a situação ou contacte o apoio ao cliente.
        </p>
    @else
        <p class="text-black text-center p-b-30 fs-16">
            Possui pagamentos em atraso há mais de {{ $isShippingBlocked['limit'] }} dias.
            <br/>
            Por favor, regularize a situação ou contacte o apoio ao cliente.
        </p>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>