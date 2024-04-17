@if(!hasModule('prices_tables'))
    @include('admin.partials.denied_message')
@else
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-prices-tables" data-toggle="subtab">@trans('Tabelas Preço')</a></li>
            <li><a href="#tab-expenses" data-toggle="subtab">@trans('Taxas Adicionais')</a></li>
            <li><a href="#tab-cubing" data-toggle="subtab">@trans('Volumetrias')</a></li>
            <li><a href="#tab-billing-items" data-toggle="subtab">@trans('Artigos Faturação')</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab-prices-tables" data-empty="1">
                @include('admin.customers.customers.partials.prices_tables')
            </div>
            <div class="tab-pane" id="tab-expenses" data-empty="1">
                @include('admin.customers.customers.partials.prices_expenses')
            </div>
            <div class="tab-pane" id="tab-cubing" data-empty="1">
                @include('admin.customers.customers.partials.prices_m3')
            </div>
            <div class="tab-pane" id="tab-billing-items" data-empty="1">
                @include('admin.customers.customers.partials.prices_billing_items')
            </div>
        </div>
    </div>
@endif
<style>
    .table-expenses td {
        padding: 3px 5px !important;
        vertical-align: middle !important;
    }

    .table-expenses .input-group-xs input {
        height: 27px;
    }


    .panel-prices-tables {
        position: relative;
        margin-bottom: 0;
        border-radius: 3px;
        border: 1px solid #83878b2b;
        box-shadow: 0 1px 1px rgb(154 161 168);
    }

    .panel-prices-tables .panel-heading {
        display: block;
        color: #222;
        background-color: #8d949a1c;
        border-color: transparent;
    }

    .panel-prices-tables .panel-title {
        color: #195f9b;
        font-weight: bold;
    }

    .panel-prices-tables .panel-title small {
        color: #777;
    }

    .increment-prices {
        padding: 5px 10px;
        z-index: 2;
        position: relative;
        border-radius: 0px !important;
    }

    .prices-tables-toggle {
        float: right;
        padding: 6px 0 5px;
    }

    .brd-black {
        border-top: 2px solid #333;
    }
</style>