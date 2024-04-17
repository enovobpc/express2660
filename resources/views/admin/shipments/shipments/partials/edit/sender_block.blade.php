<div class="box-sender-content main-addr">
    <div class="form-group form-group-sm m-b-5">
        <label class="col-sm-2 control-label p-r-0" for="sender_attn">P. Contacto</label>
        <div class="col-sm-5">
            {{ Form::text('sender_attn', $shipment->sender_attn, ['class' => 'form-control input-sm shattn']) }}
        </div>
        <label class="col-sm-1 control-label label-ref-2">{{  Setting::get('locale_label_vat') }}</label>
        <div class="col-sm-4">
            {{ Form::text('sender_vat', $shipment->sender_vat, ['class' => 'form-control input-sm shvat trigger-price']) }}
        </div>
    </div>
    {{--<div class="form-group form-group-sm m-b-5">
        <label class="col-sm-2 control-label p-r-0" for="recipient_attn">
            Remetente
        </label>
        <div class="col-sm-10">
            {{ Form::text('sender_name', $shipment->sender_name, ['class' => 'form-control input-sm search-sender uppercase', 'autocomplete' => 'field-1']) }}
            {{ Form::hidden('sender_id') }}
        </div>
    </div>--}}
    <div class="form-group form-group-sm m-b-5">
        <label class="col-sm-2 control-label p-r-0">
            Entidade
        </label>
        <div class="col-sm-10">
            {{ Form::hidden('sender_id') }}
            @if($modulePudos)
                <div class="input-group">
                    {{ Form::text('sender_name', $shipment->sender_name, ['class' => 'form-control input-sm search-sender uppercase shname', 'autocomplete' => 'field-1', 'required']) }}
                    <div class="input-group-addon" style="padding: 2px 25px 0 5px;" data-toggle="tooltip" title="Levantar num Ponto de Recolha">
                        <label style="margin:0">
                            <img src="{{ asset('assets/img/default/pudo.svg') }}"/>
                            {{ Form::checkbox('pudo_pickup', 1, $shipment->pickup_pudo_id) }}
                        </label>
                    </div>
                </div>
            @else
                {{ Form::text('sender_name', $shipment->sender_name, ['class' => 'form-control input-sm search-sender uppercase shname', 'autocomplete' => 'field-1', 'required']) }}
            @endif
        </div>
    </div>
    @if($modulePudos)
        <div class="form-group form-group-sm m-b-5 pudo-select" style="{{ $shipment->pickup_pudo_id ? '' : 'display: none' }}">
            <label class="col-sm-2 control-label p-0">
                Ponto Recolha
            </label>
            <div class="col-sm-10">
                {!! Form::selectWithData('sender_pudo_id', @$pickupPoints ? @$pickupPoints : [['value' => '', 'display' => '']], $shipment->sender_pudo_id, ['class' => 'form-control select2 uppercase', 'autocomplete' => 'field-1', 'data-placeholder' => 'Escolha um ponto de recolha da lista']) !!}
                <div class="pudo-loading" style="display: none"><i class="fas fa-spin fa-circle-notch"></i></div>
                <div class="pudo-error"></div>
                {{--<p class="m-b-0 pudo-address-box">
                    <div class="pudo-address">{{ $shipment->recipient_address }}&nbsp;</div>
                    <span class="pudo-zip">{{ $shipment->recipient_zip_code }}&nbsp;</span>
                    <span class="pudo-city">{{ $shipment->recipient_city }}</span>
                </p>--}}
            </div>
        </div>
    @endif
    <div class="form-group form-group-sm m-b-5">
        <label class="col-sm-2 control-label p-r-0" for="sender_address">
            Morada <i class="fas fa-spin fa-circle-notch hide"></i>
        </label>
        <div class="col-sm-10">
            {{ Form::text('sender_address', $shipment->sender_address, ['class' => 'form-control uppercase shaddr', 'required', 'autocomplete' => 'field-1']) }}
        </div>
    </div>

    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('sender_zip_code', 'Cód. Postal', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 p-r-0">
            <div class="row row-0 row-state">
                <div class="{{ empty($senderStates) ? 'col-sm-12' : 'col-sm-8' }}">
                    {{ Form::text('sender_zip_code', $shipment->sender_zip_code, ['class' => 'form-control trigger-price input-sm uppercase zip-code', $shipment->sender_pudo_id ? 'readonly' : '', 'required', 'autocomplete' => 'field-1']) }}
                </div>
                <div class="col-sm-4 {{ empty($senderStates) ? 'hide' : '' }}">
                    {{ Form::select('sender_state', ['' => ''] + $senderStates, $shipment->sender_state, ['class' => 'form-control input-sm select2', $shipment->sender_pudo_id ? 'readonly' : '', 'autocomplete' => 'field-1']) }}
                </div>
            </div>
        </div>
        {{ Form::label('sender_city', 'Localidade', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-5">
            {{ Form::text('sender_city', $shipment->sender_city, ['class' => 'form-control uppercase shcity', 'required', 'autocomplete' => 'field-1']) }}
        </div>
    </div>

    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('sender_country', 'País', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 p-r-0">
            {{ Form::select('sender_country', ['' => ''] + trans('country'), $shipment->sender_country ? $shipment->sender_country : $appCountry, ['class' => 'form-control trigger-price select2-country', 'data-zp-zip-code' => '#sender_zip_code', 'required']) }}
        </div>
        {{ Form::label('sender_phone', 'Telefone', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-5">
            {{ Form::text('sender_phone', $shipment->sender_phone, ['class' => 'form-control phone', Setting::get('shipments_phone_required') ? 'required' : '']) }}
        </div>
    </div>

    @if(Setting::get('shipments_obs_sender_recipient'))
        <div class="form-group form-group-sm m-t-5 m-b-5">
            <label class="col-sm-2 control-label p-r-0">
                Observações
            </label>
            <div class="col-sm-10">
                {{ Form::textarea('obs', $shipment->obs, ['class' => 'form-control', 'rows' => 2, 'maxlength' => 150, 'style' => 'height:40px']) }}
            </div>
        </div>
    @endif
    <div class="form-group form-group-sm m-b-0">
        <label class="col-sm-2 control-label p-r-0">Centro Oper.<i class="fas fa-spin fa-circle-notch sender-agency-loading" style="display: none"></i></label>
        <div class="col-sm-10">
            @if($shipment->is_back_shipment)
                {{ Form::select('sender_agency_id',  $userAgencies, $shipment->sender_agency_id, ['class' => 'form-control trigger-price select2 shagency', 'required']) }}
            @else
                {{ Form::hidden('sender_agency_id', $shipment->sender_agency_id) }}
                {{ Form::text('', @$userAgencies[$shipment->sender_agency_id], ['class' => 'form-control', 'readonly']) }}
            @endif
        </div>
    </div>
    <div class="form-group form-group-sm save-checkbox m-b-0" style="display: none">
        <div class="col-sm-10 col-sm-offset-2">
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::hidden('default_save_sender', Setting::get('shipment_default_save_customer')) }}
                    {{ Form::checkbox('save_sender', 1, Setting::get('shipment_default_save_customer')) }}
                    Memorizar esta morada
                </label>
            </div>
        </div>
    </div>
</div>