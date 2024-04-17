<?php
    $appCountry  = Setting::get('app_country');
    $appCurrency = Setting::get('app_currency');
    $hashId = str_random(8);
    $nonEU  = !in_array($shipment->recipient_country, trans('countries_eu'));
    $modulePudos = hasModule('pudos');
    $showLdm     = Setting::get('shipments_show_ldm');
    $ldmRequired = ($shipment->exists && @$shipment->service->unity == 'ldm');
    $appMode     = Setting::get('app_mode');
?>

@include('admin.customers.customers.modals.create_customer')
{{ Form::model($shipment, $formOptions) }}
<div id="modal-{{ $hashId }}" class="{{ @$schedule ? 'modal-schedule' : '' }}">
<div class="modal-header">
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
                <i class="fas fa-truck"></i> @trans('Expedição')
            </a>
        </li>
        <li>
            <a href="#tab-shp-goods" data-toggle="shptab">
                <i class="fas fa-box-open"></i> @trans('Mercadoria')
            </a>
        </li>
        <li>
            <a href="#tab-shp-map" data-toggle="shptab">
                <i class="fas fa-map-marked-alt"></i> @trans('Rota')
            </a>
        </li>
        @if(hasPermission('show_prices'))
        <li>
            <a href="#tab-shp-prices" data-toggle="shptab">
                <i class="fas fa-euro-sign"></i> @trans('Preços e Taxas')
            </a>
        </li>
        @endif
    </ul>
</div>
    {{--<div class="h-10px"></div>--}}
<div class="modal-body p-t-0 p-b-0 modal-shipment">
    <div class="tab-content m-b-0">
        <div class="tab-pane active" id="tab-shp-info">
            @if($appMode == 'cargo')
                @include('admin.shipments.shipments.partials.edit.info_cargo')
            @else
                @include('admin.shipments.shipments.partials.edit.info')
            @endif
        </div>
        <div class="tab-pane" id="tab-shp-goods">
            @include('admin.shipments.shipments.partials.edit.dimensions')
        </div>
        <div class="tab-pane" id="tab-shp-map">
            @include('admin.shipments.shipments.partials.edit.map')
        </div>
        <div class="tab-pane" id="tab-shp-prices">
            @include('admin.shipments.shipments.partials.edit.prices')
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options m-t-8">
            <div class="pull-left">
                <p style="margin: 2px 8px 0 0;"><b>@trans('Imprimir')</b></p>
            </div>
            <div class="checkbox">
                <label>
                    {{ Form::checkbox('print_guide', 1, $shipment->exists ? false : ((Setting::get('shipment_print_default') == 'guide' || Setting::get('shipment_print_default') == 'all') ? true : false)) }}
                    @trans('Guias')
                </label>
            </div>
            @if($appMode == 'cargo')
            <div class="checkbox" style="margin-left: -8px">
                <label>
                    {{ Form::checkbox('print_cmr', 1, $shipment->exists ? false : ((Setting::get('shipment_print_default') == 'cmr' || Setting::get('shipment_print_default') == 'all') ? true : false)) }}
                    @trans('CMR')
                </label>
            </div>
            @endif
            <div class="checkbox" style="margin-left: -8px">
                <label>
                    {{ Form::checkbox('print_label', 1, $shipment->exists ? false : ((Setting::get('shipment_print_default') == 'labels' || Setting::get('shipment_print_default') == 'all') ? true : false)) }}
                    @trans('Etiquetas')
                </label>
            </div>

            <div class="pull-left left-separator h-25px">
                <p style="margin: 3px 8px 0 0;"><b>@trans('Notificar')</b></p>
            </div>
           <div class="checkbox" style="{{ $shipment->recipient_email ? 'display: none' : '' }}">
                <label>
                    {{ Form::checkbox('active_email', 1) }}
                    <i class="fas fa-envelope"></i> @trans('E-mail')
                </label>
            </div>

            <div class="input-group input-group-sm input-group-email pull-left p-r-5" style="width: 250px; margin-top: -3px; {{ $shipment->recipient_email ? '' : 'display: none' }}">
                <div class="input-group-addon" style="padding: 5px;">
                    <i class="fas fa-envelope" style="vertical-align: middle"></i>
                    {{ Form::checkbox('send_email', 1, $shipment->recipient_email ? true : false) }}
                </div>
                {{ Form::text('recipient_email', $shipment->recipient_email, ['class' => 'form-control pull-left nospace lowercase', 'placeholder' => 'E-mail(s) para notificação', 'style' => 'padding-left:0']) }}
            </div>

            @if(hasModule('sms') && hasPermission('sms'))
                <div class="checkbox" data-toggle="popover" data-placement="top" data-content="{!! smsTip(@$smsPacks) !!}" data-html="true">
                    <label>
                        {{ Form::checkbox('sms', 1) }}
                        <i class="fas fa-mobile-alt"></i> SMS
                    </label>
                </div>
            @else
                <div class="checkbox" data-toggle="popover" data-placement="top" data-content="{{ !hasModule('sms') ? 'O plano contratado não permite a utilização desta ferramenta.<br/>Contacte-nos para mais informação.' : 'Não tem permissão para envio de SMS'}}" data-html="true">
                    <label style="color: #999">
                        {{ Form::checkbox('sms', 1, false, ['disabled']) }}
                        <i class="fas fa-mobile-alt"></i> SMS
                    </label>
                </div>
            @endif


        {{--<div class="checkbox left-separator">
            <label>
                {{ Form::checkbox('ignore_billing', 1) }}
                Excluir da Faturação
            </label>
        </div>--}}

        {{--@if(Auth::user()->isAdmin())
        <div class="checkbox">
            <label>
                {{ Form::checkbox('debug', 1) }}
                Debug
            </label>
        </div>
        @endif--}}
        <div class="clearfix"></div>
    </div>

    <button class="btn btn-primary btn-confirm-dimensions pull-right" style="display: none">@trans('Confirmar Dimensões')</button>
    @if(Setting::get('shipment_save_other'))
    <div class="btn-group pull-right">
        <button class="btn btn-primary btn-submit" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> @trans('A gravar...')">@trans('Gravar')</button>
        <button type="button" class="btn btn-primary btn-save-submit" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> @trans('A gravar...')"><i class="fas fa-undo"></i></button>
    </div>
    @else
    <button class="btn btn-primary btn-submit pull-right" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> @trans('A gravar...')">@trans('Gravar')</button>
    @endif
    <button type="button" class="btn btn-default pull-right m-r-5" data-dismiss="modal">@trans('Fechar')</button>

    @if(hasPermission('show_prices'))
    <div class="pull-right m-r-10">
        {{--<h5 class="pull-left m-0 p-r-10">
            <small>Combustível</small><br/>
            <span class="fuel-tax">{{ money($shipment->fuel_tax) }}</span>%
        </h5>--}}
        <h5 class="pull-left m-0 p-r-10">
            <small>@trans('Subtotal') <b class="pvp"></b></small><br/>
            <span class="loading-prices" style="display: none"><i class="fas fa-spin fa-circle-notch"></i></span>
            <span class="billing-subtotal">{{ number($shipment->billing_subtotal) . $shipment->currency }}</span>
        </h5>
       {{-- <h5 class="pull-left m-0 p-r-10">
            <small>IVA</small><br/>
            <span class="billing-vat">{{ money($shipment->billing_vat, $currency) }}</span>
        </h5>--}}
        <h5 class="pull-left m-0 p-r-10">
            <small>@trans('Total')</small><br/>
            <span class="billing-total">{{ number($shipment->billing_total) . $shipment->currency }}
        </h5>
        <h5 class="pull-left m-0">
            <small>@trans('Ganho')</small><br/>
            <span class="billing-balance">
                @if($shipment->gain_money > 0.00)
                    <span class="text-green">
                        <i class="fas fa-caret-up"></i> {{ number($shipment->gain_money) . $shipment->currency}}
                    </span>
                @elseif($shipment->gain_money < 0.00)
                    <span class="text-red">
                        <i class="fas fa-caret-down"></i> {{ number($shipment->gain_money) . $shipment->currency}}
                    </span>
                @else
                    <span class="text-muted">
                        <i class="fas fa-caret-up"></i> {{ number($shipment->gain_money) . $shipment->currency}}
                    </span>
                @endif
            </span>
        </h5>
    </div>
    @endif
</div>

<div class="modal" id="modal-confirm-vols">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@trans('Alterar número volumes')</h4>
            </div>
            <div class="modal-body">
                <h4 class="m-0">
                    @trans('Pretende alterar o número de volumes da expedição para') <span class="cvol"></span>?
                </h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">@trans('Não')</button>
                    <button type="button" class="btn btn-default" data-answer="1">@trans('Sim')</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{--@include('admin.shipments.shipments.modals.dimensions')--}}
@include('admin.shipments.shipments.modals.confirm.confirm_change_customer')
{{--@include('admin.shipments.shipments.modals.confirm.confirm_change_date')--}}
@include('admin.shipments.shipments.modals.confirm.confirm_sync_error')
@include('admin.shipments.shipments.modals.confirm.customer_blocked')
@include('admin.shipments.shipments.modals.confirm.customer_payment')
@include('admin.shipments.shipments.modals.expenses')

@if($shipment->hasSync())
@include('admin.shipments.shipments.modals.confirm.confirm_change_provider')
@endif

@if(!$shipment->invoice_id)
    <div style="display: none">
        {{ Form::checkbox('price_fixed', 1) }}
    </div>
@endif

{{ Form::hidden('save_other', 0) }}
{{ Form::hidden('count_discharges', count(@$shipment->multiple_addresses)?:1) }}
{{ Form::hidden('shipping_order_id', @$shipment->shipping_order_id) }}
{{ Form::hidden('sync_agencies') }}
{{ Form::hidden('is_import', 0) }}
{{ Form::hidden('deleted_expenses') }}
{{ Form::hidden('agency_zp', @$shipment->customer->agency->zip_code) }}
{{ Form::hidden('agency_city', @$shipment->customer->agency->city) }}
{{ Form::hidden('customer_km', $shipment->exists ? @$shipment->customer->distance_km : 0) }}
{{ Form::hidden('shp_type', $shipment->type) }}
{{ Form::hidden('shp_parent', $shipment->parent_tracking_code) }}
{{ Form::hidden('provider_sender_agency', $shipment->provider_sender_agency) }}
{{ Form::hidden('provider_recipient_agency', $shipment->provider_recipient_agency) }}
{{ Form::hidden('status_id', $shipment->status_id) }}
<input type="hidden" name="tags" value="{{ implode(',', $shipment->tags) }}">
<input type="hidden" name="waint_ajax" value="0">

@if(!Setting::get('shipments_reference3') && $shipment->reference3)
    {{ Form::hidden('reference3', $shipment->reference3) }} {{-- garante que o campo vai ser gravado mesmo que esteja desligada a opção de o mostrar --}}
@endif

<div class="has-return">
@if($shipment->has_return)
    @foreach($shipment->has_return as $item)
        @if($item != 'rpack')
        {{ Form::hidden('has_return[]', $item, ['id' => 'input-' . $hashId . '-' . $item]) }}
        @endif
    @endforeach
@endif
</div>
@if(in_array($shipment->type, [\App\Models\Shipment::TYPE_RETURN, \App\Models\Shipment::TYPE_PICKUP]))
    {{ Form::hidden('type') }}
    {{ Form::hidden('parent_tracking_code') }}
@endif
{{ Form::hidden('zone', ($shipment->exists && empty($shipment->zone)) ? App\Models\Shipment::getBillingCountry($shipment->sender_country, $shipment->recipient_country, $shipment->is_import) : $shipment->zone) }}
<input type="hidden" name="is_collection" value="0">
{{ Form::close() }}

<script>
    STR_HASH_ID             = "{{ $hashId }}"
    SHIPMENT_EXISTS         = "{{ $shipment->exists }}";
    ROUTE_SYNC_RESET        = "{{ $shipment->exists ? route('admin.shipments.sync.reset.store', @$shipment->id) : '' }}";
    ROUTE_GET_EXPENSE_PRICE = "{{ ($shipment->exists ? route('admin.shipments.expenses.get.price', @$shipment->id) : route('admin.shipments.expenses.get.price', '0')) }}";
    FULL_RESET              = {{ (int) Setting::get('shipment_save_other_fullreset') }}
    VAT_RATES_VALUES        = {!! json_encode($vatRatesValues) !!};
    ADICIONAL_ADDR_MODE     = "{{ Setting::get('shipment_adicional_addr_mode') }}"
    
    shpmap = new google.maps.Map(document.getElementById('shpmap'), shpProps);

    @if(!$shipment->invoice_id)
    $('.form-shipment .price-lock').on('click', function(){
        if($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.form-shipment [name="price_fixed"]').prop('checked', false);
        } else {
            $(this).addClass('active');
            $('.form-shipment [name="price_fixed"]').prop('checked', true);
        }
    })
    @endif

</script>