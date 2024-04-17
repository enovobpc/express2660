<div class="w-130px">
    <div class="checkbox">
        <label>
            {{ Form::checkbox('ignore_billing', 1, $row->total_price_for_recipient ? true : false) }}
            Marcar Pago
        </label>
    </div>

    @if($row->charge_price)
        <div class="checkbox">
            <label>
                {{ Form::checkbox('print_proof', 1, false) }}
                <i class="fas fa-print"></i> Comprovativo
            </label>
        </div>
    @endif

    @if(hasModule('cashier') && Auth::user()->ability(Config::get('permissions.role.admin'), 'cashier,cashier_central'))
        <div class="checkbox">
            <label>
                {{ Form::checkbox('regist_cashier', 1) }}
                Registo na Caixa
            </label>
        </div>
    @endif
</div>