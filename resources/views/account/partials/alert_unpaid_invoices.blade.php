@if($isShippingBlocked)
<div class="notice notice-danger text-red">
    <h4 class="m-0">
        <i class="fas fa-exclamation-triangle"></i> <b>Conta suspensa por pagamentos em atraso.</b><br/>
        @if($isShippingBlocked['reason'] == 'credit')
            <small>
                Ultrapassou o limite de crédito de {{ money($isShippingBlocked['limit'], Setting::get('app_currency')) }}. 
                Por favor, regularize a situação ou entre em contacto connosco.
            </small>
        @elseif ($isShippingBlocked['reason'] == 'days')
            <small>
                Possui faturas por liquidar há mais de {{ $isShippingBlocked['limit'] }} dias. 
                Por favor, regularize a situação ou entre em contacto connosco.
            </small>
        @elseif ($isShippingBlocked['reason'] == 'plafound')
            <small>
                Atingiu o plafound mensal máximo de {{ money($isShippingBlocked['limit'], Setting::get('app_currency')) }}.
                Por favor, regularize a situação ou contacte o apoio ao cliente.
            </small>
        @endif
    </h4>
</div>
@endif