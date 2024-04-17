<div class="modal" id="modal-create-customer">
    <div class="modal-dialog modal-lg">
        {{ Form::open(['route' => 'admin.customers.modal.create', 'method' => 'post']) }}
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@trans('Criar novo cliente')</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-6" style="padding-right: 20px;">
                        <h4 class="form-divider no-border" style="margin-top: 0; padding: 0">@trans('Dados de Expedição')</h4>
                        <div class="row row-5">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('is_particular', __('Tipo')) }}
                                    {!! tip(__('Se o cliente for particular, o sistema irá adicionar sempre IVA ao cálculo de preços, faturação e orçamentação')) !!}
                                    {{ Form::select('is_particular', ['' => 'Empresa', '1' => 'Particular'], null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group is-required m-b-0">
                                    {{ Form::label('customer_agency_id', __('Agência')) }}
                                    {{ Form::select('customer_agency_id', (count($agencies) == 1) ? $agencies : ['' => ''] + $agencies, null, ['class' => 'form-control select2', 'required']) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group is-required m-b-0">
                                    {{ Form::label('type_id', __('Categoria')) }}
                                    {{ Form::select('type_id',['' => ''] + $customerCategories, null, ['class' => 'form-control select2', 'required']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-12">
                                <div class="form-group is-required">
                                    {{ Form::label('name', __('Designação Expedição')) }}
                                    {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group is-required">
                            {{ Form::label('address', __('Morada')) }}
                            {{ Form::text('address', null, ['class' => 'form-control', 'required']) }}
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-3">
                                <div class="form-group is-required">
                                    {{ Form::label('zip_code', __('Código Postal')) }}
                                    {{ Form::text('zip_code', null, ['class' => 'form-control trim', 'required']) }}
                                </div>
                            </div>
                            @if(in_array($appCountry, ['us', 'br']))
                                <div class="col-sm-3 col-md-2">
                                    <div class="form-group is-required">
                                        {{ Form::label('state', 'State') }}
                                        {{ Form::select('state', ['' => ''] + trans('districts_codes.districts.'.$appCountry), null, ['class' => 'form-control select2', 'required']) }}
                                    </div>
                                </div>
                            @endif
                            <div class="{{ in_array($appCountry, ['us', 'br']) ? 'col-sm-4' : 'col-sm-6' }}">
                                <div class="form-group is-required">
                                    {{ Form::label('city', __('Localidade')) }}
                                    {{ Form::text('city', null, ['class' => 'form-control', 'required']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group is-required">
                                    {{ Form::label('country', __('País')) }}
                                    {{ Form::select('country', ['' => ''] + trans('country'), $appCountry, ['class' => 'form-control select2', 'required']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-6">
                                <div class="form-group m-b-0">
                                    {{ Form::label('contact_email', __('E-mail de contacto')) }}
                                    {{ Form::text('contact_email', null, ['class' => 'form-control nospace lowercase email']) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group m-b-0">
                                    {{ Form::label('mobile', __('Telemóvel')) }}
                                    {{ Form::text('mobile', null, ['class' => 'form-control phone']) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group m-b-0">
                                    {{ Form::label('phone', __('Telefone')) }}
                                    {{ Form::text('phone', null, ['class' => 'form-control phone']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6"  style="padding-left: 20px;">
                        <h4 class="form-divider no-border" style="margin-top: 0;  padding: 0">@trans('Dados de Faturação')</h4>
                        <div class="row row-5">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('billing_country', __('País')) }}
                                    {{ Form::select('billing_country', trans('country'), $appCountry, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group {{ $appCountry == 'pt' ? 'is-required' : '' }}">
                                    @if($appCountry == 'pt')
                                        <a href="#" class="pull-right set-cfinal-vat"><small>@trans('NIF Consumidor Final')</small></a>
                                    @endif
                                    {{ Form::label('vat', __('NIF')) }}
                                    <div class="input-group">
                                        {{ Form::text('vat', null, ['class' => 'form-control nospace vat', 'maxlength' => '25', $appCountry == 'pt' ? 'required' : '', 'data-country' => 'billing_country']) }}
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
                                    <div class="text-orange vat-alert" style="display: none" data-toggle="tooltip" title="O NIF indicado já está associado a outro cliente ou potencial cliente. Pode abrir ficha de cliente mesmo assim, contudo apenas é considerado para efeitos de faturação o NIF do primeiro cliente indicado.">
                                        @trans(' NIF já existente') <i class="fas fa-info-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('payment_method', __('Cond. Pgto')) }}
                                    {{ Form::select('payment_method', hasModule('account_wallet') ? ['PRÉ-PAGAMENTO' => ['wallet'=> 'Pré-pagamento'], 'PAGAMENTO MENSAL' => $paymentConditions] : [''=>''] + $paymentConditions , '30d', ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-12">
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
                            <div class="col-sm-6 col-md-6">
                                <div class="form-group">
                                    {{ Form::label('billing_city', __('Localidade')) }}
                                    {{ Form::text('billing_city', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('default_invoice_type', __('Doc. defeito')) }}
                                    {{ Form::select('default_invoice_type', trans('admin/billing.types-list'), 'invoice', ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group m-b-0">
                                    {{ Form::label('billing_email', __('E-mail de faturação')) }}
                                    {{ Form::text('billing_email', null, ['class' => 'form-control nospace lowercase email']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 10px 15px;">
                <button type="button" class="btn btn-default cancel-create-customer">@trans('Cancelar')</button>
                <button type="button" class="btn btn-primary confirm-create-customer" data-loading-text="A criar...">@trans('Criar Cliente')</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>