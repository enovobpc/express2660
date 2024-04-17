<?php
$appCountry  = Setting::get('app_country');
$appCurrency = Setting::get('app_currency');
$hashId = str_random(8);
$nonEU  = !in_array($shipment->recipient_country, trans('countries_eu'));
$modulePudos = false; //força a que nas recolhas nao tenha opcao de pudos hasModule('pudos');
$showLdm     = Setting::get('shipments_show_ldm');
$ldmRequired = ($shipment->exists && @$shipment->service->unity == 'ldm');

?>

{{ Form::model($shipment, $formOptions) }}
<div id="modal-{{ $hashId }}">
    <div class="modal-header" style="background: #ffcf00">
        <button type="button" class="close" data-dismiss="modal">
            <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
            <span class="sr-only">Fechar</span>
        </button>
        <h4 class="modal-title">{{ $action }}</h4>
    </div>
    <div class="tabbable-line m-b-15">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab-shp-info" data-toggle="shptab">
                    <i class="fas fa-dolly"></i> Pedido Recolha
                </a>
            </li>
            <li>
                <a href="#tab-shp-goods" data-toggle="shptab">
                    <i class="fas fa-box-open"></i> Mercadoria
                </a>
            </li>
            {{--<li>
                <a href="#tab-shp-prices" data-toggle="shptab">
                    <i class="fas fa-euro-sign"></i> Preços e Taxas
                </a>
            </li>--}}
        </ul>
    </div>
    <div class="modal-body p-t-0 p-b-0 modal-shipment">
        <div class="tab-content m-b-0">
            <div class="tab-pane active" id="tab-shp-info">
                @include('admin.shipments.pickups.partials.edit.info')
            </div>
            <div class="tab-pane" id="tab-shp-goods">
                @include('admin.shipments.shipments.partials.edit.dimensions')
            </div>
            {{--<div class="tab-pane" id="tab-shp-prices">
                @include('admin.shipments.shipments.partials.edit.prices')
            </div>--}}
        </div>
    </div>
    
    <div class="modal-footer">
        <div class="extra-options">
            <div class="pull-left">
                <p style="margin: 2px 8px 0 0;"><b>Imprimir</b></p>
            </div>
            <div class="checkbox">
                <label>
                    {{ Form::checkbox('print_guide', 1, $shipment->exists ? false : ((Setting::get('shipment_print_default') == 'guide' || Setting::get('shipment_print_default') == 'all') ? true : false)) }}
                    Manifesto Recolha
                </label>
            </div>
            <div class="checkbox">
                <label>
                    {{ Form::checkbox('price_fixed', 1) }}
                    Bloquear Preço
                </label>
            </div>
            @if(Auth::user()->isAdmin())
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('debug', 1) }}
                        Debug
                    </label>
                </div>
            @endif
            <div class="clearfix"></div>
            <div class="text-red m-t-5 m-b-0 modal-feedback text-left"></div>
        </div>

        <button class="btn btn-primary btn-confirm-dimensions pull-right" style="display: none">Confirmar Dimensões</button>
        <button type="submit" class="btn btn-yellow btn-submit pull-right" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A criar recolha...">
            @if($shipment->exists)
                Gravar
            @else
                Pedir Recolha
            @endif
        </button>
        <button type="button" class="btn btn-default pull-right m-r-5" data-dismiss="modal">Fechar</button>
    </div>
</div>

<div class="modal" id="modal-confirm-vols">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alterar número volumes</h4>
            </div>
            <div class="modal-body">
                <h4 class="m-0">
                    Pretende alterar o número de volumes da expedição para <span class="cvol"></span>?
                </h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">Não</button>
                    <button type="button" class="btn btn-default" data-answer="1">Sim</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{--@include('admin.shipments.shipments.modals.dimensions')--}}
@include('admin.shipments.pickups.modals.confirm_change_customer')
@include('admin.shipments.shipments.modals.confirm.confirm_change_customer')
@include('admin.shipments.shipments.modals.confirm.confirm_sync_error')
@include('admin.shipments.shipments.modals.confirm.customer_blocked')

@if($shipment->hasSync())
    @include('admin.shipments.shipments.modals.confirm.confirm_change_provider')
@endif

{{ Form::hidden('sync_agencies') }}
{{ Form::hidden('customer_km', $shipment->exists ? @$shipment->customer->distance_km : 0) }}
<input type="hidden" name="is_collection" value="1">
{{ Form::close() }}

{{ Html::script(asset('vendor/devbridge-autocomplete/dist/jquery.autocomplete.min.js')) }}
<script>
    var APP_MODE                = "{{ Setting::get('app_mode') }}";
    var ROUTE_GET_PRICE         = "{{ route('admin.shipments.get.price') }}";
    var ROUTE_GET_AGENCY        = "{{ route('admin.shipments.get.agency') }}";
    var ROUTE_SEARCH_SENDER     = "{{ route('admin.shipments.search.sender') }}";
    var ROUTE_SEARCH_RECIPIENT  = "{{ route('admin.shipments.search.recipient') }}";
    var ROUTE_SEARCH_CUSTOMER   = "{{ route('admin.shipments.search.customer') }}";
    var ROUTE_GET_CUSTOMER      = "{{ route('admin.shipments.get.customer') }}";
    var ROUTE_GET_RECIPIENT     = "{{ route('admin.shipments.get.recipient') }}";
    var ROUTE_SEARCH_SKU        = "{{ route('admin.shipments.search.sku') }}";
    var ROUTE_COMPARE_PRICES    = "{{ route('admin.shipments.compare.prices') }}";
    var ROUTE_SYNC_RESET        = "{{ $shipment->exists ? route('admin.shipments.sync.reset.store', @$shipment->id) : '' }}";
    var ROUTE_GET_EXPENSE_PRICE = "{{ ($shipment->exists ? route('admin.shipments.expenses.get.price', @$shipment->id) : route('admin.shipments.expenses.get.price', '0')) }}";
    var SHIPMENT_EXISTS         = "{{ $shipment->exists ? 1 : 0 }}";
    var SHIPMENT_CALC_AUTO_KM   = "{{ Setting::get('shipments_km_calc_auto') }}";
    var VOLUMES_MESURE_UNITY    = "{{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}"
    var STR_HASH_ID             = "{{ $hashId }}"
    var IS_PICKUP               = "1"
    var VAT_PERCENT             = "{{ Setting::get('vat_rate_normal')/100 }}"
    var ADICIONAL_ADDR_MODE     = "{{ Setting::get('shipment_adicional_addr_mode') }}"
    

    $('#modal-remote-xl [name="provider_id"]').on('change', function(){
        var method = $(this).find('option:selected').attr('method');

        $('#modal-remote-xl [name="guide_required"]').prop('disabled', false);
        $('#modal-remote-xl [name="volumes"]').prop('disabled', false);
        $('#modal-remote-xl [name="weight"]').prop('disabled', false);
        $('#modal-remote-xl [name="reference"]').prop('disabled', false)
        $('#modal-remote-xl [name="total_price_when_collecting"]').prop('disabled', true); //adiantamento
        $('#modal-remote-xl [name="insurance_price"]').prop('disabled', true); //seguro
        $('#modal-remote-xl [name="guide_required"]').closest('.form-group').hide();

        if(method == '' || typeof method === 'undefined') {
            $('#modal-remote-xl [name="total_price_when_collecting"]').prop('disabled', false);
            $('#modal-remote-xl [name="insurance_price"]').prop('disabled', false);

        } else if(method == 'envialia' || method == 'tipsa') {
            $('#modal-remote-xl [name="total_price_when_collecting"]').prop('disabled', false);
            $('#modal-remote-xl [name="insurance_price"]').prop('disabled', false);
        } else if(method == 'gls_zeta') {
            $('#modal-remote-xl [name="volumes"]').prop('disabled', true);
            $('#modal-remote-xl [name="weight"]').prop('disabled', true);
            $('#modal-remote-xl [name="reference"]').prop('disabled', true);
        } else if(method == 'ctt') {
            $('#modal-remote-xl [name="guide_required"]').closest('.form-group').show();
        } else if(method == 'chronopost') {
            $('#modal-remote-xl [name="total_price_when_collecting"]').prop('disabled', true);
            $('#modal-remote-xl [name="insurance_price"]').prop('disabled', true);
            $('#modal-remote-xl [name="reference"]').prop('disabled', true);
        }
    })

</script>
{{ Html::script(asset('assets/admin/js/shipments.js')) }}
