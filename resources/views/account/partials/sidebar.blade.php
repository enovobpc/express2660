@if(!@$auth->is_validated && @$auth->is_active)
    <style>
        .account-sidebar {
            z-index: -10;
            position: relative;
        }

        .account-sidebar:after {
            content: '';
            background: rgba(255,255,255,0.8);
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 30;
            border-radius: 5px;
            cursor: none;
        }
    </style>
@endif
@if(hasModule('budgeter') && !@$auth->hide_budget_btn)
    <a href="{{ route('account.budgeter.index') }}"
       class="btn btn-primary btn-new-shipment">
        <i class="fas fa-coins"></i> {{ trans('account/global.word.budgeter') }}
    </a>
    <div class="h-55px"></div>
@else
    @if(!isset($auth->customer_id) && !@$auth->settings['hide_btn_shipments'])

        <a href="{{ route('account.shipments.create') }}"
        data-toggle="modal"
        data-target="#modal-remote-xl"
        class="btn btn-primary btn-new-shipment">
            {{ trans('account/global.menu.create-shipment') }}
        </a>
        <div class="h-55px"></div>

    @elseif(isset($auth->customer_id) && empty($auth->hide_btn_shipments) )

        <a href="{{ route('account.shipments.create') }}"
        data-toggle="modal"
        data-target="#modal-remote-xl"
        class="btn btn-primary btn-new-shipment">
            {{ trans('account/global.menu.create-shipment') }}
        </a>
        <div class="h-55px"></div>
    @endif
@endif

{{--<div class="list-group">
    <a href="{{ route('account.index') }}" id="menu-dashboard" class="list-group-item">
        <i class="fas fa-poll-h"></i> {{ trans('account/global.menu.dashboard') }}
    </a>
</div>--}}

<h4>
    <i class="fas fa-fw fa-box-open"></i> {{ trans('account/global.menu.separator.shipments') }}
</h4>
<div class="list-group">
    @if(hasModule('budgeter') && !@$auth->hide_budget_btn)
        <a href="{{ route('account.budgeter.index') }}" id="menu-budgeter" class="list-group-item">
            {{ trans('account/global.word.budgeter') }}
        </a>
    @endif

    <a href="{{ route('account.shipments.index') }}" id="menu-shipments" class="list-group-item">
        {{  Setting::get('account_shipments_menuname') ? Setting::get('account_shipments_menuname') : trans('account/global.menu.shipments') }}
        <i class="fas fa-angle-right pull-right"></i>
    </a>

    @if(hasModule('collections') && !(@$auth->settings['hide_menu_pickups'] || @$auth->parent_customer->settings['hide_menu_pickups']))
    <a href="{{ route('account.pickups.index') }}" id="menu-pickups" class="list-group-item">
        {{ Setting::get('account_pickups_menuname') ? Setting::get('account_pickups_menuname') : trans('account/global.menu.pickups') }}
        <i class="fas fa-angle-right pull-right"></i>
    </a>
    @endif
</div>

@if(hasModule('customer_support') || (hasModule('incidences') && Setting::get('account_show_incidences') && !@$auth->settings['hide_incidences_menu']))
    <h4>
        <i class="fas fa-fw fa-headset"></i> {{ trans('account/global.menu.separator.customer-support') }}
    </h4>
    <div class="list-group">

        @if(hasModule('incidences') && Setting::get('account_show_incidences') && !@$auth->settings['hide_incidences_menu'])
            <a href="{{ route('account.incidences.index') }}" id="menu-incidences" class="list-group-item">
                    {{ trans('account/global.menu.incidences') }}
                <i class="fas fa-angle-right pull-right"></i>
            </a>
        @endif
        @if(hasModule('customer_support'))
            <a href="{{ route('account.customer-support.index') }}" id="menu-customers-support" class="list-group-item">
                Pedidos de Suporte
                <i class="fas fa-angle-right pull-right"></i>
            </a>
        @endif
    </div>
@endif

@if(hasModule('account_billing') || (hasModule('account_refunds') && Setting::get('customers_show_charge_price') && Setting::get('shipments_show_charge_price')))
    <h4>
        <i class="fas fa-fw {{ Setting::get('app_currency_icon') }}"></i> {{ trans('account/global.menu.separator.billing') }}
    </h4>
    <div class="list-group">
        @if(hasModule('account_refunds') && Setting::get('customers_show_charge_price') && Setting::get('shipments_show_charge_price'))
        <a href="{{ route('account.refunds.index') }}" id="menu-refunds" class="list-group-item">
            {{ trans('account/global.menu.refunds') }}
        </a>
        @endif
        @if(hasModule('account_billing'))
        <a href="{{ route('account.billing.index') }}" id="menu-billing" class="list-group-item">
            @if(hasModule('invoices'))
            {{ trans('account/global.menu.invoices') }}
            @else
            {{ trans('account/global.menu.extracts') }}
            @endif
        </a>
        @endif
    </div>
@endif

@if(hasModule('logistic') && Setting::get('show_customers_logistic') && !(@$auth->settings['logistic_hide_menu'] || @$auth->parent_customer->settings['logistic_hide_menu']))
    <h4><i class="fas fa-fw fa-cubes"></i>  {{ trans('account/global.menu.separator.logistic') }}</h4>
    <a href="{{ route('account.logistic.products.index') }}" id="menu-logistic" class="list-group-item">
        @if(config('app.source') == 'activos24')
            {{ trans('account/global.menu.logistic_activos') }}
        @else
            {{ trans('account/global.menu.logistic') }}
        @endif
    </a>
    @if(config('app.source') != 'activos24')
    <a href="{{ route('account.logistic.reception-orders.index') }}" id="menu-logistic-reception-orders" class="list-group-item">
        Ordens de Recepção
    </a>
    @endif
    <a href="{{ route('account.logistic.shipping-orders.index') }}" id="menu-logistic-shipping-orders" class="list-group-item">
        Ordens de Saída
    </a>
    @if(config('app.source') == 'activos24')
    <a href="{{ route('account.logistic.cart.index') }}" id="menu-logistic-cart-orders" class="list-group-item">
        Encomendas
    </a>
    @endif
@endif

{{-- || !Setting::get('show_customers_ballance') --}}
@if(hasModule('events_management'))
    <h4><i class="fas fa-fw fa-calendar-week"></i> {{ trans('account/global.menu.event-manager') }}</h4>
    <a href="{{ route('account.event-manager.index') }}" id="menu-event-manager" class="list-group-item">
        {{ trans('account/global.menu.separator.event-manager') }}
    </a>
@endif

<h4><i class="fas fa-fw fa-user"></i> {{ trans('account/global.menu.separator.account') }}</h4>
<div class="list-group">
    @if($auth->show_billing)
    <a href="{{ route('account.index') }}" id="menu-dashboard" class="list-group-item">
        {{ trans('account/global.menu.dashboard') }}
    </a>
    @endif
    <a href="{{ route('account.recipients.index') }}" id="menu-recipients" class="list-group-item">
        {{ trans('account/global.menu.recipients') }}
        <i class="fas fa-angle-right pull-right"></i>
    </a>
    @if(empty($auth->customer_id))
        <a href="{{ route('account.departments.index') }}" id="menu-departments" class="list-group-item">
            {{ trans('account/global.menu.departments') }}
        </a>
    @endif
    <a href="{{ route('account.details.index') }}" id="menu-details" class="list-group-item">
        {{ trans('account/global.menu.settings') }}
    </a>
    <a href="{{ route('account.messages.index') }}" id="menu-messages" class="list-group-item">
        {{ trans('account/global.menu.inbox') }}
    </a>

    @if(config('app.source') == 'okestafetas')
        <a href="https://www.canva.com/design/DAEc8YOH0Us/zMfag2RWKCVECdgfeVh2uA/view?website#4:perguntas-frequentes" class="list-group-item" target="_blank">
            <i class="fas fa-question-circle"></i> Central de Ajuda
        </a>
    @endif
</div>
