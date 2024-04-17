{{ Form::model($prospect, $formOptions) }}
<div class="box no-border">
    <div class="box-body">
        <div class="row row-5">
            <div class="col-sm-8">
                <div class="row row-10">
                    <div class="col-sm-9 {{ $routes ? 'col-lg-7' : 'col-lg-9' }}">
                        <div class="col-sm-12">
                            <div class="form-group is-required">
                                {{ Form::label('name', __('Designação para Expedição')) }}
                                {{ Form::text('name', null, ['class' => 'form-control', 'required', $prospect->final_consumer ? 'readonly' : '']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-lg-3">
                        <div class="form-group is-required">
                            {{ Form::label('agency_id', __('Agência')) }}
                            {{ Form::select('agency_id', (count($agencies) == 1) ? $agencies : ['' => ''] + $agencies, null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                    @if($routes)
                    <div class="col-lg-2">
                        <div class="form-group">
                            {{ Form::label('route_id', __('Rota')) }}
                            {{ Form::select('route_id', ['' => ''] + $routes, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    @endif
                </div>
                <div class="form-group">
                    {{ Form::label('address', __('Morada')) }}
                    {{ Form::text('address', null, ['class' => 'form-control']) }}
                </div>
                <div class="row row-5">
                    <div class="col-sm-3">
                        {{ Form::label('zip_code', __('Código Postal')) }}
                        {{ Form::text('zip_code', null, ['class' => 'form-control']) }}
                    </div>
                    <div class="col-sm-6">
                        {{ Form::label('city', __('Localidade')) }}
                        {{ Form::text('city', null, ['class' => 'form-control']) }}
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('country', __('País')) }}
                            {{ Form::select('country', ['' => ''] + trans('country'), $prospect->exists ? null : Setting::get('app_country'), ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('contact_email', __('E-mail para comunicações gerais')) }}
                            {{ Form::text('contact_email', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('mobile', __('Telemóvel')) }}
                            {{ Form::text('mobile', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('phone', __('Telefone')) }}
                            {{ Form::text('phone', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
                <hr style="margin: 10px 0 15px"/>
                <h4 class="m-t-0 bold text-blue">@trans('Dados de Faturação')</h4>
                <div class="row row-5">
                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('billing_country', __('País')) }}
                            {{ Form::select('billing_country', ['' => ''] + trans('country'), $prospect->exists ? ($prospect->has_billing_info ? null : $prospect->country) : null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group is-required">
                            <a href="#" class="pull-right set-cfinal-vat"><small>@trans('NIF Consumidor Final')</small></a>
                            {{ Form::label('vat', __('NIF')) }}
                            <div class="input-group">
                                {{ Form::text('vat', null, ['class' => 'form-control nospace vat', 'maxlength' => '25', 'required', 'data-country' => 'billing_country']) }}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-validate-nif"
                                            data-toggle="modal"
                                            data-target="#modal-vat-validation"
                                            data-href="{{ coreUrl('helper/vat/info') }}"
                                            data-vv-country="#billing_country"
                                            data-vv-name="#billing_name"
                                            data-vv-address="#billing_address"
                                            data-vv-zip-code="#billing_zip_code"
                                            data-vv-city="#billing_city"
                                            data-vv-phone="#phone"
                                            data-vv-mobile="#mobile">
                                            @trans('Validar')
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="form-group">
                            {{ Form::label('billing_name', __('Designação Social')) }}
                            {{ Form::text('billing_name', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
                <div style="font-size: 13px;position: relative;top: -10px;margin-bottom: -5px; display: none" class="text-red vat-feedback"></div>
                <div class="form-group">
                    {{ Form::label('billing_address', __('Morada')) }}
                    {{ Form::text('billing_address', null, ['class' => 'form-control']) }}
                </div>
                <div class="row row-5">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('billing_zip_code', __('Código Postal')) }}
                            {{ Form::text('billing_zip_code', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="form-group">
                            {{ Form::label('billing_city', __('Localidade')) }}
                            {{ Form::text('billing_city', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('responsable', __('Pessoa de contacto')) }}
                            {{ Form::text('responsable', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('payment_method', __('Condição Pgto')) }}
                            {{ Form::select('payment_method', ['' => ''] + $paymentConditions, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('default_invoice_type', __('Doc. por defeito')) }}
                            {{ Form::select('default_invoice_type', ['' => __('Sem documento')] + trans('admin/billing.types-list'), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('billing_email', __('E-mail para assuntos de faturação')) }}
                            {{ Form::text('billing_email', $prospect->exists ? (!empty($prospect->billing_email) ? null : '') : null, ['class' => 'form-control', 'placeholder' => 'Manter o mesmo e-mail para comunicações.']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('refunds_email', __('E-mail para reembolsos')) }}
                            {{ Form::text('refunds_email', null, ['class' => 'form-control', 'placeholder' => __('Manter o mesmo e-mail para comunicações.')]) }}
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            {{ Form::label('iban_payments', __('IBAN para pagamentos')) }}
                            {{ Form::text('iban_payments', null, ['class' => 'form-control uppercase iban']) }}
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            {{ Form::label('iban_refunds', __('IBAN para reembolsos')) }}
                            {{ Form::text('iban_refunds', null, ['class' => 'form-control uppercase iban']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="p-l-30">
                    <button type="button" class="btn btn-xs btn-default pull-right mark-on-map"><i class="fas fa-map-marker-alt"></i> Localizar no mapa</button>
                    <h4 class="m-t-0 bold text-blue pull-left">@trans('Localização')</h4>
                    <div id="map" style="width: 100%; height: 220px; margin-bottom: 5px"></div>
                    {{ Form::hidden('map_lat', null) }}
                    {{ Form::hidden('map_lng', null) }}
                    <div class="form-group">
                        {{ Form::label('obs', __('Observações')) }}
                        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 4, 'style' => 'min-height:100px']) }}
                    </div>
                    <h4 class="m-t-0 bold text-blue">@trans('Outros Dados')</h4>
                    <div class="form-group is-required">
                        {{ Form::label('type_id', __('Categoria')) }}
                        {{ Form::select('type_id', ['' => ''] + $types, null, ['class' => 'form-control select2', 'required']) }}
                    </div>

                    @if(Auth::user()->hasRole([config('permissions.role.seller')]))
                        {{ Form::hidden('seller_id', Auth::user()->id) }}
                    @elseif(!Auth::user()->hasRole([config('permissions.role.seller')]) && $sellers)
                        <div class="form-group">
                            {{ Form::label('seller_id', __('Comercial Associado')) }}
                            {{ Form::select('seller_id', ['' => ''] + $sellers, null, ['class' => 'form-control select2']) }}
                        </div>
                    @endif
                    <div class="form-group">
                        {{ Form::label('website', __('Website')) }}
                        {{ Form::text('website', null, ['class' => 'form-control']) }}
                    </div>
                   <div class="form-group">
                        {{ Form::label('facebook', 'Facebook') }}
                        {{ Form::text('facebook', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
        </div>
        <hr/>
        <button type="submit" class="btn btn-primary pull-left">@trans('Gravar')</button>
        <div class="clearfix"></div>
    </div>
</div>
@if($prospect->exists)
{{ Form::hidden('average_weight') }}
{{ Form::select('enabled_services[]', $servicesList, null, ['class' => 'form-control hide', 'multiple' => true]) }}
@endif
{{ Form::close() }}
