<div class="box-recipient-content">
    {{ Form::hidden('recipient_agency_id') }}
    <div class="form-group">
        {{ Form::label('recipient_attn', trans('account/global.word.recipient-attn-abrv'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-6 p-r-0">
            {{ Form::text('recipient_attn', (!$shipment->exists && $shipment->is_collection && !request()->get('intercity')) ? $customer->responsable : null, ['class' => 'form-control uppercase shattn']) }}
        </div>
        {{ Form::label('recipient_vat', trans('account/global.word.tin-abrv.'.Setting::get('app_country')), ['class' => 'col-sm-1 control-label p-l-0 p-r-5']) }}
        <div class="col-sm-3 p-l-0">
            {{ Form::text('recipient_vat', (!$shipment->exists && $shipment->is_collection && !request()->get('intercity')) ? $customer->vat : null, ['class' => 'form-control uppercase nospace shvat', 'maxlength' => 15, Setting::get('customer_recipient_vat_required') ? 'required' : '']) }}
        </div>
    </div>
    @if($shipment->is_pickup && !$customer->departments->isEmpty())
        <div class="form-group select2-fullwidth">
            {{ Form::label('department_id', trans('account/global.word.department'), ['class' => 'col-sm-2 control-label p-0']) }}
            <div class="col-sm-10">
                {{ Form::select('department_id', ['' => $customer->name] + $customer->departments->pluck('name', 'id')->toArray(), null, ['class' => 'form-control select2 trigger-price']) }}
            </div>
        </div>
    @endif
    <div class="form-group {{ $shipment->is_pickup && !$customer->departments->isEmpty() ? 'hide' : '' }}">
        {{ Form::label('recipient_id', trans('account/global.word.recipient'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-10">
            @if($customer->enabled_pudo_providers)
                <div class="input-group">
                    {{ Form::text('recipient_name', null, ['class' => 'form-control search-recipient uppercase shname', 'autocomplete' => 'field-1', 'required']) }}
                    <div class="input-group-addon" style="padding: 2px 25px 0 5px;" data-toggle="tooltip" title="Levantar num Ponto de Entrega">
                        <label>
                            <img src="{{ asset('assets/img/default/pudo.svg') }}"/>
                            {{ Form::checkbox('pudo_delivery', 1, $shipment->recipient_pudo_id) }}
                        </label>
                    </div>
                </div>
            @else
                {{ Form::text('recipient_name', null, ['class' => 'form-control search-recipient uppercase shname', 'autocomplete' => 'field-1', 'required']) }}
            @endif
            {{ Form::hidden('recipient_id', null, ['class' => 'shid']) }}
        </div>
    </div>
    @if($customer->enabled_pudo_providers)
        <div class="form-group m-b-5 pudo-select" style="{{ $shipment->recipient_pudo_id ? '' : 'display: none' }}">
            <label class="col-sm-2 control-label p-0">
                <div class="pudo-loading" style="display: none"><i class="fas fa-spin fa-circle-notch"></i></div>
                Ponto Entrega
            </label>
            <div class="col-sm-10">
                {!! Form::selectWithData('recipient_pudo_id', @$pickupPoints ? @$pickupPoints : [['value' => '', 'display' => '']], null, ['class' => 'form-control select2 uppercase', 'autocomplete' => 'field-1', 'data-placeholder' => 'Escolha um ponto de recolha da lista']) !!}
                <div class="pudo-error"></div>
            </div>
        </div>
    @endif
    <div class="form-group">
        <label class="col-sm-2 control-label p-r-0" for="recipient_address">
            {{ trans('account/global.word.address') }} <i class="fas fa-spin fa-circle-notch hide"></i>
        </label>
        <div class="col-sm-10">
            {{ Form::text('recipient_address', null, ['class' => 'form-control uppercase shaddr', 'required']) }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('recipient_zip_code', trans('account/global.word.zip_code'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 col-zip p-r-0">
            <div class="row row-0 row-state">
                <div class="{{ empty($recipientStates) ? 'col-sm-12' : 'col-sm-7' }}">
                    {{ Form::text('recipient_zip_code', null, ['class' => 'form-control zip-code uppercase trigger-price p-r-2', 'autocomplete'=> 'nofill', 'required']) }}
                </div>
                <div class="col-sm-5 {{ empty($recipientStates) ? 'hide' : '' }}">
                    {{ Form::select('recipient_state', ['' => ''] + ($recipientStates ?? ['' => '']), null, ['class' => 'form-control input-sm select2', $shipment->recipient_pudo_id ? 'readonly' : '', 'autocomplete' => 'field-1']) }}
                </div>
            </div>
        </div>
        {{ Form::label('recipient_city', trans('account/global.word.city'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-5 col-city">
            {{ Form::text('recipient_city', null, ['class' => 'form-control shcity', 'autocomplete'=> 'nofill', 'required']) }}
        </div>
        <div class="col-sm-9 col-sm-offset-2 text-red service-error" style="display: none">
            <small><i class="fas fa-info-circle"></i> <span></span></small>
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('recipient_country', trans('account/global.word.country'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 col-zip p-r-0">
            {{ Form::select('recipient_country', ['' => ''] + trans('country'), $shipment->exists ? $shipment->recipient_country : Setting::get('app_country'), ['class' => 'form-control trigger-price select2-country', 'required']) }}
        </div>
        {{ Form::label('recipient_phone', trans('account/global.word.phone'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-5 col-city">
            {{ Form::text('recipient_phone', null, ['class' => 'form-control nospace phone', Setting::get('customer_shipment_phone_required') ? 'required' : '']) }}
        </div>
    </div>
    <div class="form-group save-checkbox" style="display: none">
        {{ Form::label('recipient_code', trans('account/global.word.customer-code'), ['class' => 'col-sm-2 control-label p-r-0']) }}
        <div class="col-sm-3 p-r-0">
            {{ Form::text('recipient_code', null, ['class' => 'form-control', 'data-toggle' => 'tooltip', 'title' =>'Atribua o número de cliente ao novo destinatário que está a criar.']) }}
        </div>
        <div class="col-sm-6">
            <div class="form-group m-b-0">
                <div class="checkbox">
                    <label>
                        {{ Form::hidden('default_save_recipient', !Setting::get('customers_shipment_default_save_customer')) }}
                        {{ Form::checkbox('save_recipient', 1, false) }}
                        {{ trans('account/shipments.modal-shipment.save-address') }}
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>