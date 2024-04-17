<?php $readOnly = Setting::get('customer_block_edit_sender_address') ? 'readonly' : '' ?>
<div class="box-sender-content">
    {{ Form::hidden('sender_agency_id', null, ['class' => 'trigger-price']) }}
    <div class="form-group">
        {{ Form::label('sender_attn', trans('account/global.word.recipient-attn-abrv'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-6 p-r-0">
            {{ Form::text('sender_attn', (!$shipment->exists && !$shipment->is_collection) ? $customer->responsable : null, ['class' => 'form-control uppercase shattn', $readOnly]) }}
        </div>
        {{ Form::label('sender_vat', trans('account/global.word.tin-abrv.'.Setting::get('app_country')), ['class' => 'col-sm-1 control-label p-l-0 p-r-5']) }}
        <div class="col-sm-3 p-l-0">
            {{ Form::text('sender_vat', (!$shipment->exists && !$shipment->is_collection) ? $customer->vat : null, ['class' => 'form-control uppercase nospace shvat', 'maxlength' => 15, $readOnly, Setting::get('customer_recipient_vat_required') ? 'required' : '']) }}
        </div>
    </div>

    @if(!$shipment->is_pickup && !$customer->departments->isEmpty())
        <div class="form-group select2-fullwidth">
            {{ Form::label('department_id', trans('account/global.word.department'), ['class' => 'col-sm-2 control-label p-0']) }}
            <div class="col-sm-10">
                {{ Form::select('department_id', ['' => $customer->name] + $customer->departments->pluck('name', 'id')->toArray(), null, ['class' => 'form-control select2 trigger-price']) }}
            </div>
        </div>
    @endif

    <div class="form-group">
        {{ Form::label('sender_name', trans('account/global.word.sender'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-10">
            @if($customer->enabled_pudo_providers)
                <div class="input-group">
                    {{ Form::text('sender_name', null, ['class' => 'form-control search-sender uppercase trigger-price shname', 'autocomplete' => 'field-1', 'required']) }}
                    <div class="input-group-addon" style="padding: 2px 25px 0 5px;" data-toggle="tooltip" title="Deixar num Ponto de Entrega">
                        <label>
                            <img src="{{ asset('assets/img/default/pudo.svg') }}"/>
                            {{ Form::checkbox('pudo_pickup', 1, $shipment->pickup_pudo_id) }}
                        </label>
                    </div>
                </div>
            @else
                {{ Form::text('sender_name', null, ['class' => 'form-control search-sender uppercase trigger-price shname', 'autocomplete' => 'field-1', 'required']) }}
            @endif
            {{ Form::hidden('sender_id', null, ['class' => 'shid']) }}
        </div>
    </div>
    @if($customer->enabled_pudo_providers)
        <div class="form-group m-b-5 pudo-select" style="{{ $shipment->sender_pudo_id ? '' : 'display: none' }}">
            <label class="col-sm-2 control-label p-0">
                <div class="pudo-loading" style="display: none"><i class="fas fa-spin fa-circle-notch"></i></div>
                Ponto Entrega
            </label>
            <div class="col-sm-10">
                {!! Form::selectWithData('sender_pudo_id', @$pickupPoints ? @$pickupPoints : [['value' => '', 'display' => '']], null, ['class' => 'form-control select2 uppercase', 'autocomplete' => 'field-1', 'data-placeholder' => 'Escolha um ponto de recolha da lista']) !!}
                <div class="pudo-error"></div>
            </div>
        </div>
    @endif
    <div class="form-group">
        <label class="col-sm-2 control-label p-r-0" for="sender_address">
            {{ trans('account/global.word.address') }} <i class="fas fa-spin fa-circle-notch hide"></i>
        </label>
        <div class="col-sm-10">
            {{ Form::text('sender_address', null, ['class' => 'form-control uppercase shaddr', 'required', $readOnly]) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('sender_zip_code', trans('account/global.word.zip_code'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 col-zip p-r-0">
            <div class="row row-0 row-state">
                <div class="{{ empty($senderStates) ? 'col-sm-12' : 'col-sm-7' }}">
                    {{ Form::text('sender_zip_code', null, ['class' => 'form-control uppercase trigger-price zip-code', 'required', $readOnly]) }}
                </div>
                <div class="col-sm-5 {{ empty($senderStates) ? 'hide' : '' }}">
                    {{ Form::select('sender_state',  ['' => ''] + ($senderStates ?? ['' => '']), null, ['class' => 'form-control input-sm select2', 'autocomplete' => 'field-1']) }}
                </div>
            </div>
        </div>
        {{ Form::label('sender_city', trans('account/global.word.city'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-5 col-city">
            {{ Form::text('sender_city', null, ['class' => 'form-control uppercase shcity', 'required', $readOnly]) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('sender_country', trans('account/global.word.country'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 col-zip p-r-0">
            @if($readOnly)
                {{ Form::text('_sender_country', trans('country.'.$shipment->sender_country), ['class' => 'form-control uppercase', 'required', $readOnly]) }}
                {{ Form::select('sender_country', ['' => ''] + trans('country'), $shipment->exists ? $shipment->sender_country : Setting::get('app_country'), ['class' => 'hide']) }}
            @else
                {{ Form::select('sender_country', ['' => ''] + trans('country'), $shipment->exists ? $shipment->sender_country : Setting::get('app_country'), ['class' => 'form-control trigger-price select2-country', 'required']) }}
            @endif
        </div>
        {{ Form::label('sender_phone', trans('account/global.word.phone'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-5 col-city">
            {{ Form::text('sender_phone', null, ['class' => 'form-control nospace phone', 'required']) }}
        </div>
    </div>

    <div class="form-group save-checkbox" style="display: none">
        {{ Form::label('sender_code', trans('account/global.word.customer-code'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 p-r-0">
            {{ Form::text('sender_code', null, ['class' => 'form-control', 'data-toggle' => 'tooltip', 'title' =>'Atribua o número de cliente ao novo remetente que está a criar.']) }}
        </div>
        <div class="col-sm-6">
            <div class="form-group m-b-0">
                <div class="checkbox">
                    <label>
                        {{ Form::hidden('default_save_sender', !Setting::get('customers_shipment_default_save_customer')) }}
                        {{ Form::checkbox('save_sender', 1, false) }}
                        {{ trans('account/shipments.modal-shipment.save-address') }}
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>