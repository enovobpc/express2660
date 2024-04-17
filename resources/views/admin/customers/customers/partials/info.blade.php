{{ Form::model($customer, $formOptions) }}
<div class="box no-border">
    <div class="box-body">
        <div class="row row-5">
            <div class="col-sm-8">
                <div class="row row-10">
                    <div class="col-sm-9">
                        @if(Setting::get('show_customers_abbrv'))
                        <div class="col-sm-9 col-lg-4" style="width: 30%">
                            <div class="row row-5">
                                <div class="col-sm-6">
                                    <div class="form-group is-required">
                                        {{ Form::label('code', __('Código')) }}
                                        {{ Form::text('code', null, ['class' => 'form-control nospace uppercase', 'required', 'maxlength' => 8, $vatBlocked ? 'disabled' : '']) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {{ Form::label('code_abbrv', __('Abrv')) }} {!! tip(__('A abreviatura permite identificar de forma mais clara o cliente nas listagens do sistema.')) !!}
                                        {{ Form::text('code_abbrv', null, ['class' => 'form-control nospace uppercase', 'maxlength' => 10]) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                            <div class="col-sm-3 col-lg-2">
                                <div class="form-group is-required">
                                    {{ Form::label('code', __('Código')) }}
                                    {{ Form::text('code', null, ['class' => 'form-control nospace uppercase', 'required', 'maxlength' => 8, $vatBlocked ? 'disabled' : '']) }}
                                </div>
                            </div>
                        @endif
                        <div class="col-sm-9 {{ Setting::get('show_customers_abbrv') ? 'col-lg-8' : 'col-lg-10' }}" style="{{ Setting::get('show_customers_abbrv') ? 'width: 70%' : '' }}">
                            <div class="form-group is-required">
                                {{ Form::label('name', __('Designação para Expedição')) }}
                                {{ Form::text('name', null, ['class' => 'form-control', 'required', $customer->final_consumer ? 'readonly' : '']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-lg-3">
                        <div class="form-group is-required">
                            {{ Form::label('agency_id', __('Agência')) }}
                            {{ Form::select('agency_id', (count($agencies) == 1) ? $agencies : ['' => ''] + $agencies, null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>

                </div>
                <div class="form-group">
                    {{ Form::label('address', __('Morada')) }}
                    {{ Form::text('address', null, ['class' => 'form-control']) }}
                </div>
                <div class="row row-5">
                    <div class="col-sm-3 col-md-2">
                        <div class="form-group">
                            {{ Form::label('zip_code', __('Código Postal')) }}
                            {{ Form::text('zip_code', null, ['class' => 'form-control trim']) }}
                        </div>
                    </div>
                    @if(in_array(Setting::get('app_country'), ['us', 'br']))
                    <div class="col-sm-3 col-md-2">
                        <div class="form-group">
                            {{ Form::label('state', __('State')) }}
                            {{ Form::select('state', ['' => ''] + trans('districts_codes.districts.'.Setting::get('app_country')), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    @endif
                    <div class="{{ in_array(Setting::get('app_country'), ['us', 'br']) ? 'col-sm-5' : 'col-sm-7' }}">
                        <div class="form-group">
                            {{ Form::label('city', __('Localidade')) }}
                            {{ Form::text('city', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('country', __('País')) }}
                            {{ Form::select('country', ['' => ''] + trans('country'), $customer->exists ? null : Setting::get('app_country'), ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('contact_email', __('E-mail de contacto')) }}
                            {{ Form::text('contact_email', null, ['class' => 'form-control nospace lowercase email']) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('mobile', __('Telemóvel')) }}
                            {{ Form::text('mobile', null, ['class' => 'form-control phone']) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('phone', __('Telefone')) }}
                            {{ Form::text('phone', null, ['class' => 'form-control phone']) }}
                        </div>
                    </div>

                </div>

                <h4 class="form-divider">@trans('Dados de Faturação')</h4>
                <div class="row row-5">
                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('billing_country', __('País')) }}
                            {{ Form::select('billing_country', trans('country'), $customer->exists ? null : Setting::get('app_country'), ['class' => 'form-control select2', $vatBlocked ? 'disabled' : '']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group {{ Setting::get('app_country') == 'pt' ? 'is-required' : '' }}">
                            @if(Setting::get('app_country') == 'pt' && !$vatBlocked)
                            <a href="#" class="pull-right set-cfinal-vat"><small>@trans('NIF Consumidor Final')</small></a>
                            @endif
                            {{ Form::label('vat', 'NIF') }}
                            <div class="input-group">
                                @if($vatBlocked)
                                {{ Form::text('vat', null, ['class' => 'form-control', 'disabled', 'data-toggle' => 'tooltip', 'title' => 'Não é possível editar este campo porque já existem faturas associadas.', 'data-placement' => 'left']) }}
                                @else
                                {{ Form::text('vat', null, ['class' => 'form-control nospace vat', 'maxlength' => '25', Setting::get('app_country') == 'pt' ? 'required' : '', 'data-country' => 'billing_country']) }}
                                @endif
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
                                @trans('NIF já existente') <i class="fas fa-info-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="form-group">
                            {{ Form::label('billing_name', __('Designação Social')) }}
                            {{ Form::text('billing_name', $customer->exists ? ($customer->has_billing_info ? null : '') : null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
                <div style="font-size: 13px;position: relative;top: -10px;margin-bottom: -5px; display: none" class="text-red vat-feedback"></div>
                <div class="form-group">
                    {{ Form::label('billing_address', __('Morada')) }}
                    {{ Form::text('billing_address', $customer->exists ? ($customer->has_billing_info ? null : '') : null, ['class' => 'form-control']) }}
                </div>
                <div class="row row-5">
                    <div class="col-sm-3 col-md-2">
                        <div class="form-group">
                            {{ Form::label('billing_zip_code', __('Código Postal')) }}
                            {{ Form::text('billing_zip_code', $customer->exists ? ($customer->has_billing_info ? null : '') : null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-7">
                        <div class="form-group">
                            {{ Form::label('billing_city', __('Localidade')) }}
                            {{ Form::text('billing_city', $customer->exists ? ($customer->has_billing_info ? null : '') : null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('billing_reference', __('Ref. Faturação')) }} {!! tip(__('Utilize este código para colocar uma referência fixa em todas as faturas. Esta referência vai substituir qualquer outra refeência automatizada do sistema.')) !!}
                            {{ Form::text('billing_reference', null, ['class' => 'form-control', 'maxlength' => 50]) }}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('is_particular', __('Tipo')) }}
                            {!! tip(__('Se o cliente for particular, o sistema irá adicionar sempre IVA ao cálculo de preços, faturação e orçamentação')) !!}
                            {{ Form::select('is_particular', ['' => 'Empresa', '1' => 'Particular'], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('responsable', __('Responsável')) }}
                            {{ Form::text('responsable', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('billing_email', __('E-mail de faturação')) }}
                            {{ Form::text('billing_email', $customer->exists ? (!empty($customer->billing_email) ? null : '') : null, ['class' => 'form-control nospace lowercase email', 'placeholder' => 'Manter o mesmo e-mail para comunicações.']) }}
                        </div>
                    </div>
                    @if(config('app.source') == 'rapidix')
                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('billing_code', __('Codigo PHC')) }}
                            {!! tip(__('Código correspondente no programa de faturação.')) !!}
                            {{ Form::text('billing_code', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    @endif

                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('default_invoice_type', __('Doc. por defeito')) }}
                            {{ Form::select('default_invoice_type', trans('admin/billing.types-list'), $customer->exists ? null : 'invoice', ['class' => 'form-control select2']) }}
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('currency', __('Moeda')) }}
                            {{ Form::select('currency', trans('admin/localization.currencies'), $customer->exists ? null : Setting::get('app_currency'), ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-xs-8" style="padding-right: 15px">
                        <div class="form-divider">
                            <h4 class="pull-left">@trans('Condições Pagamento')</h4>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('payment_method', __('Pagamento')) }}
                                    {{ Form::select('payment_method', hasModule('account_wallet') ? ['PRÉ-PAGAMENTO' => ['wallet'=> 'Pgto Automático'], 'PAGAMENTO MENSAL' => $paymentConditions] : [''=>''] + $paymentConditions , $customer->exists ? null : Setting::get('default_customer_payment_method'), ['class' => 'form-control select2']) }}
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('default_payment_method_id', __('Método de Pagamento')) }}
                                    {{ Form::select('default_payment_method_id', ['' => ''] + $paymentMethods, null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>

                            <div class="col-sm-5">
                                <div class="form-group">
                                    @if(hasPermission('banks'))
                                    <small class="pull-right"><a href="{{ route('admin.banks.index', ['tab' => 'banks-institutions'])}}" target="_blank">Gerir Instituições</a></small>
                                    @endif
                                    {{ Form::label('bank_code', __('Banco pagamento')) }}
                                    {!! Form::select('bank_code', $customer->bank_code ? [@$customer->bank_code => @$customer->bank_name] : [], null, ['class' => 'form-control select2', 'data-placeholder' => '']) !!}
                                    {{ Form::hidden('bank_name', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            
                            <div class="col-sm-7">
                                <div class="form-group">
                                    {{ Form::label('bank_iban', __('IBAN débito direto')) }}
                                    {{ Form::text('bank_iban', null, ['class' => 'form-control iban nospace uppercase']) }}
                                </div>
                            </div>
                            <div class="col-xs-5">
                                <div class="row row-5">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            {{ Form::label('bank_swift', __('BIC/SWIFT')) }}
                                            {{ Form::text('bank_swift', null, ['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <small class="pull-right text-blue btn-toggle-mandate-date" data-toggle="tooltip" title="Editar data mandato"><i class="fas fa-calendar-alt"></i></small>
                                            {{ Form::label('bank_mandate', __('Nº Mandato')) }}
                                            {{ Form::text('bank_mandate', null, ['class' => 'form-control']) }}
                                            <div class="btn-create-mandate" style="cursor: pointer;position: absolute;right: 6px;top: 20px; z-index: 1; padding: 8px;">
                                                <i class="fas fa-sync" data-toggle="tooltip" title="Gerar novo mandato"></i>
                                            </div>
                                            {{ Form::text('bank_mandate_date', null, ['class' => 'form-control datepicker', 'style'=>'display:none']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-divider">
                            <h4 class="pull-left">@trans('Reembolsos')</h4>
                        </div>
                        <div class="form-group">
                            {{ Form::label('refunds_email', __('E-mail para reembolsos')) }}
                            {{ Form::text('refunds_email', null, ['class' => 'form-control nospace lowercase email', 'placeholder' => 'Manter o mesmo e-mail para comunicações.']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('iban_refunds', __('IBAN para reembolsos')) }}
                            {{ Form::text('iban_refunds', null, ['class' => 'form-control uppercase iban']) }}
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-divider">
                            <h4 class="pull-left">@trans('Outros dados')</h4>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group m-0">
                            {{ Form::label('website', __('Website/Facebook')) }}
                            {{ Form::url('website', null, ['class' => 'form-control url nospace lowercase']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('other_name', __('Alcunhas/Outros Nomes')) }} {!! tip(__('Indique outros nomes ou alcunhas pelo qual o cliente pode ser pesquisado e encontrado no sistema.')) !!}
                            {{ Form::text('other_name', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            {{ Form::label('obs_shipments', __('Obs por defeito nos serviços')) }} {!! tip(__('Sempre que criar um envio para este cliente, será adicionado nas observações o texto que indicar neste campo.')) !!}
                            {{ Form::textarea('obs_shipments', null, ['class' => 'form-control', 'placeholder' => 'Ex.: Fatura no interior.', 'rows' => 1]) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="p-l-30">
                    <button type="button" class="btn btn-xs btn-default pull-right mark-on-map"><i class="fas fa-map-marker-alt"></i> @trans('Localizar no mapa')</button>
                    <h4 class="m-t-0 bold text-blue">@trans('Localização')</h4>
                    <div id="map" style="width: 100%; height: 220px; margin-bottom: 5px;"></div>
                    {{--<div id="map" style="width: 100%; height: 220px; margin-bottom: 5px; {{ $customer->exists ? 'display: none' : '' }}"></div>--}}
                    {{--@if($customer->map_preview && $customer->map_lat && $customer->map_lng)
                    <div class="customer-map-static">
                        <div class="map-controls">
                            <p class="fs-16">O que pretende fazer?</p>
                            <button type="button" class="btn btn-sm btn-default"
                                data-toggle="modal"
                                data-target="#modal-map-preview"
                                data-map-url="https://www.google.com/maps/embed/v1/place?q={{ $customer->map_lat }},{{ $customer->map_lng }}&zoom=16&key={{ getGoogleMapsApiKey() }}">
                                <i class="fas fa-expand"></i> Ver Mapa
                            </button>
                            <button type="button" class="btn btn-sm btn-default" data-toggle="marker-position">
                                <i class="fas fa-map-marker-alt"></i> Corrigir Posição
                            </button>
                        </div>
                        <img src="{{ asset($customer->map_preview) }}"/>
                    </div>
                    @endif--}}
                    {{ Form::hidden('map_lat', null) }}
                    {{ Form::hidden('map_lng', null) }}
                    <div class="form-group">
                        {{ Form::label('obs', __('Observações')) }}
                        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 4, 'style' => 'min-height:315px']) }}
                    </div>
                    <h4 class="form-divider">@trans('Outras Definições')</h4>

                    <div class="form-group is-required">
                        <a href="{{ route('admin.customers-types.index') }}" data-toggle="modal" data-target="#modal-remote" class="pull-right">
                            @trans('Gerir Categorias')
                        </a>
                        {{ Form::label('type_id', __('Categoria de Cliente')) }} {!! tip("Organize os seus clientes pelo tipo de negócio deles. Pode editar ou adicionar tipos de cliente no menu 'Entidades > Clientes > Ferramentas'.") !!}
                        {{ Form::select('type_id', ['' => ''] + $types, null, ['class' => 'form-control select2', 'required']) }}
                    </div>

                    @if($routes)
                    <div class="row row-5">
                        <div class="col-sm-6">
                            <div class="form-group">
                                {{ Form::label('route_id', __('Rota')) }}
                                {{ Form::select('route_id', ['' => ''] + $routes, null, ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{ Form::label('distance_km', __('Distância')) }}
                                {!! tip(__('Distância do armazém até ao cliente')) !!}
                                <div class="input-group">
                                    {{ Form::text('distance_km', null, ['class' => 'form-control decimal']) }}
                                    <div class="input-group-addon">@trans('km')</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group m-t-25">
                                <label>
                                    {{ Form::checkbox('distance_from_agency', 1, $customer->exists ? null : 1, ['class' => 'form-control decimal']) }}
                                </label>
                                {!! tip(__('Calcular distancia a partir da morada da agência.')) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row row-5">
                        <div class="col-xs-6">
                            @if(Auth::user()->hasRole([config('permissions.role.seller')]))
                                {{ Form::hidden('seller_id', Auth::user()->id) }}
                            @elseif(!Auth::user()->hasRole([config('permissions.role.seller')]))
                                <div class="form-group">
                                    {{ Form::label('seller_id', __('Vendedor')) }}
                                    {{ Form::select('seller_id', ['' => ''] + $sellers, null, ['class' => 'form-control select2']) }}
                                </div>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                {{ Form::label('operator_id', __('Motorista')) }}
                                {{ Form::select('operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                    </div>
                    <div class="row row-5">
                        <div class="col-sm-6">
                            <div class="form-group">
                                {{ Form::label('avg_cost', __('Custo Médio')) }}
                                {!! tip(__('Este é o custo estimado para o cliente fora os serviços (Ex: custos de deslocação). Este valor será contabilizado nas despesas gerais da empresa.')) !!}
                                <div class="input-group">
                                    {{ Form::text('avg_cost', null, ['class' => 'form-control decimal']) }}
                                    <div class="input-group-addon">€</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                {{ Form::label('billing_discount_value', __('Desconto Fixo')) }} {!! tip(__('Utilize este campo para definir uma taxa de desconto a aplicar em todas as faturas do cliente.')) !!}
                                <div class="input-group">
                                    {{ Form::text('billing_discount_value', $customer->exists ? $customer->billing_discount_value : null, ['class' => 'form-control']) }}
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--<div class="row row-5">
                        <div class="col-sm-5">
                            <div class="form-group form-group m-0d">
                                {{ Form::label('time_delivering', 'Tempo Entrega', ['class' => 'control-label']) }}
                                {!! tip(__('Tempo gasto em média por cada entrega deste cliente.')) !!}
                                {{ Form::select('time_delivering', ['' => '1 min', '2' => '2 min','3' => '3 min','4' => '5 min','4' => '5 min', '10' => '10 min'], $customer->time_delivering, ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group form-group m-0">
                                {{ Form::label('time_assembly', 'Tempo Montagem', ['class' => 'control-label']) }}
                                {{ Form::select('time_assembly', ['' => 'Auto'], $customer->time_assembly, ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                    </div>--}}
                </div>
            </div>
        </div>
        <hr/>
        <button type="submit" class="btn btn-primary pull-left" data-loading-text="A gravar...">@trans('Gravar')</button>
        <div class="checkbox m-l-15 pull-left">
            <label style="padding-left: 0; margin-top: -5px; display: block;">
                {{ Form::checkbox('update_billing_software', 1, $customer->exists ? false : true, [hasModule('invoices') ? '' : 'disabled']) }}
                @if(!hasModule('invoices'))
                    <span class="text-muted" data-toggle="tooltip" title="A sua aplicação não está ligada ao programa de faturação.">
                @else
                    <span>
                @endif
                @if($customer->exists)
                    @trans('Ao gravar, atualizar no programa de faturação.')
                @else
                    @trans('Ao gravar, criar cliente no programa de faturação.')
                @endif
                </span>
            </label>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
@if($customer->exists)
{{ Form::hidden('average_weight') }}
{{ Form::select('enabled_services[]', $servicesList, null, ['class' => 'form-control hide', 'multiple' => true]) }}
@endif
{{ Form::close() }}
