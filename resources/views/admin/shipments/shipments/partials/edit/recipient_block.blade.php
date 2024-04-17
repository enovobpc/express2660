<div class="box-recipient-content main-addr">
    <div class="form-group form-group-sm m-b-5">
        <label class="col-sm-2 control-label p-r-0">P. Contacto</label>
        <div class="col-sm-5">
            {{ Form::text('recipient_attn', $shipment->recipient_attn, ['class' => 'form-control input-sm shattn']) }}
        </div>
        <label class="col-sm-1 control-label label-ref-2">{{  Setting::get('locale_label_vat') }}</label>
        <div class="col-sm-4">
            {{ Form::text('recipient_vat', $shipment->recipient_vat, ['class' => 'form-control input-sm shvat trigger-price']) }}
        </div>
    </div>
    <div class="form-group form-group-sm m-b-5">
        <label class="col-sm-2 control-label p-r-0">
            Entidade
        </label>
        <div class="col-sm-10">
            @if($modulePudos)
            <div class="input-group">
                {{ Form::text('recipient_name', $shipment->recipient_name, ['class' => 'form-control input-sm search-recipient uppercase shname', 'autocomplete' => 'field-1', 'required']) }}
                <div class="input-group-addon" style="padding: 2px 25px 0 5px;" data-toggle="tooltip" title="Entregar num Ponto de Entrega">
                    <label style="margin:0">
                        <img src="{{ asset('assets/img/default/pudo.svg') }}"/>
                        {{ Form::checkbox('pudo_delivery', 1, $shipment->recipient_pudo_id) }}
                    </label>
                </div>
            </div>
            @else
                {{ Form::text('recipient_name', $shipment->recipient_name, ['class' => 'form-control input-sm search-recipient uppercase shname', 'autocomplete' => 'field-1', 'required']) }}
            @endif
            {{ Form::hidden('recipient_id', $shipment->recipient_id, ['class'=>'shid']) }}
        </div>
    </div>
    @if($modulePudos)
    <div class="form-group form-group-sm m-b-5 pudo-select" style="{{ $shipment->recipient_pudo_id ? '' : 'display: none' }}">
        <label class="col-sm-2 control-label p-0">
            Ponto Entrega
        </label>
        <div class="col-sm-10">
            {!! Form::selectWithData('recipient_pudo_id', @$pickupPoints ? @$pickupPoints : [['value' => '', 'display' => '']], $shipment->recipient_pudo_id, ['class' => 'form-control select2 uppercase', 'autocomplete' => 'field-1', 'data-placeholder' => 'Escolha um ponto de recolha da lista']) !!}
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
        <label class="col-sm-2 control-label p-r-0" for="recipient_address">
            Morada <i class="fas fa-spin fa-circle-notch hide"></i>
        </label>
        <div class="col-sm-10">
            {{ Form::text('recipient_address', $shipment->recipient_address, ['class' => 'form-control input-sm uppercase shaddr', 'required', $shipment->recipient_pudo_id ? 'readonly' : '', 'autocomplete' => 'field-1']) }}
        </div>
    </div>
    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('recipient_zip_code', 'Cód. Postal', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 p-r-0">
            <div class="row row-0 row-state">
                <div class="{{ empty($recipientStates) ? 'col-sm-12' : 'col-sm-8' }}">
                    {{ Form::text('recipient_zip_code', $shipment->recipient_zip_code, ['class' => 'form-control trigger-price input-sm uppercase zip-code', $shipment->recipient_pudo_id ? 'readonly' : '', 'required', 'autocomplete' => 'field-1']) }}
                </div>
                <div class="col-sm-4 {{ empty($recipientStates) ? 'hide' : '' }}">
                    {{ Form::select('recipient_state', ['' => ''] + $recipientStates, $shipment->recipient_state, ['class' => 'form-control input-sm select2', $shipment->recipient_pudo_id ? 'readonly' : '', 'autocomplete' => 'field-1']) }}
                </div>
            </div>
        </div>
        {{ Form::label('recipient_city', 'Localidade', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-5">
            {{ Form::text('recipient_city', $shipment->recipient_city, ['class' => 'form-control input-sm uppercase shcity', $shipment->recipient_pudo_id ? 'readonly' : '', 'required', 'autocomplete' => 'field-1']) }}
        </div>
    </div>
    <div class="form-group form-group-sm m-b-5">
        {{ Form::label('recipient_country', 'País', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 p-r-0">
            {{ Form::select('recipient_country', ['' => ''] + trans('country'), ($shipment->exists || $shipment->collection_tracking_code || !empty($shipment->recipient_country)) ? $shipment->recipient_country : $appCountry, ['class' => 'form-control trigger-price select2-country', 'required']) }}
        </div>
        {{ Form::label('recipient_phone', 'Telefone', ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-5">
            {{ Form::text('recipient_phone', $shipment->recipient_phone, ['class' => 'form-control phone', Setting::get('shipments_phone_required') ? 'required' : '']) }}
        </div>
    </div>

    @if(Setting::get('shipments_obs_sender_recipient'))
        <div class="form-group form-group-sm m-t-5 m-b-5">
            <label class="col-sm-2 control-label p-r-0">
                Observações
            </label>
            <div class="col-sm-10">
                {{ Form::textarea('obs_delivery', $shipment->obs_delivery, ['class' => 'form-control', 'rows' => 2, 'maxlength' => 150, 'style' => 'height:40px']) }}
            </div>
        </div>
    @endif

    <div class="form-group form-group-sm m-b-0">
        <label class="col-sm-2 control-label p-r-0">Centro Oper.<i class="fas fa-spin fa-circle-notch recipient-agency-loading" style="display: none"></i></label>
        <div class="col-sm-10">
            {{--@if($shipment->is_back_shipment)--}}
                {{ Form::select('recipient_agency_id',  $providerAgencies, $shipment->recipient_agency_id, ['class' => 'form-control trigger-price select2 shagency', 'required']) }}
            {{--@else
                {{ Form::hidden('recipient_agency_id', $shipment->recipient_agency_id) }}
                {{ Form::text('', @$providerAgencies[$shipment->recipient_agency_id], ['class' => 'form-control', 'readonly']) }}
            @endif--}}
        </div>
    </div>

    <div class="form-group save-checkbox m-b-0" style="display: none">
        <div class="col-sm-10 col-sm-offset-2">
            <div class="checkbox">
                <label style="padding-left: 0">
                    {{ Form::hidden('default_save_recipient', Setting::get('shipment_default_save_customer')) }}
                    {{ Form::checkbox('save_recipient', 1, false) }}
                    Memorizar esta morada
                </label>
            </div>
        </div>
    </div>
</div>