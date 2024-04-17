{{ Form::model($provider, $formOptions) }}
<div class="box no-border">
    <div class="box-body">
        <div class="row row-5">
            <div class="col-sm-9">
                <div class="row row-5">
                    <div class="col-sm-5">
                        <div class="row row-5">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('code', __('Código')) }}
                                    {{ Form::text('code', null, ['class' => 'form-control nospace uppercase', 'maxlength' => 5]) }}
                                </div>
                            </div>
                            <div class="col-sm-9">
                                <div class="form-group is-required">
                                    {{ Form::label('name', __('Nome no sistema')) }}
                                    {!! tip(__('Este é o nome que será apresentado em todas as listagens do sistema.')) !!}
                                    {{ Form::text('name', str_limit(@$provider->name, 15, ''), ['class' => 'form-control','maxlength' => 15,'required']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group is-required">
                            {{ Form::label('category_id', __('Categoria')) }}
                            {{ Form::select('category_id', ['' => ''] + $categories, null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox m-l-0 m-t-26 m-b-0">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('type', 'carrier', $provider->type == 'carrier' ? true : false) }}
                                @trans('É Transportador')
                            </label>
                            {!! tip(__('Ative esta opção para que o fornecedor apareça na lista de fornecedores da janela de criação de envios')) !!}
                        </div>
                    </div>
                </div>
                <div class="row row-5 carrier-options"
                    style="{{ $provider->type == 'carrier' ? 'display:block' : 'display:none' }}">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('webservice_method', __('Rede associada')) }}
                            {{ Form::select('webservice_method', ['' => ''] + $webserviceMethods, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="form-group p-l-0 m-b-0">
                            {{ Form::label('color', __('Identificador')) }}<br />
                            {{ Form::select('color', $colors) }}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="checkbox m-l-0 m-t-0">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('autodetect_agencies') }}
                                @trans('Detectar Agência Destino Automáticamente')
                            </label>
                            {!! tip(__('Se ativar esta opção, quando selecionar este fornecedor, o sistema irá selecionar automáticamente as agências de destino associadas.')) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-b-0">
                            {{ Form::label('operation_zip_codes', __('Códigos postais área atuação')) }}
                            {{ Form::textarea('operation_zip_codes', null, ['class' => 'form-control', 'rows' => 3]) }}
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-12">
                        <h4 class="form-divider">@trans('Dados de Faturação')</h4>
                        <div class="row row-5">
                            <div class="col-sm-3 col-md-2 col-lg-2">
                                <div class="form-group">
                                    {{ Form::label('country', __('País')) }}
                                    {{ Form::select('country', trans('country'), $provider->exists ? null : Setting::get('app_country'), ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-3">
                                <div class="form-group m-0">
                                    {{ Form::label('vat', __('NIF')) }}
                                    <div class="input-group">
                                        {{ Form::text('vat', null, ['class' => 'form-control nospace vat','maxlength' => '15','data-country' => 'country']) }}
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-validate-nif"
                                                data-toggle="modal" data-target="#modal-vat-validation"
                                                data-href="{{ coreUrl('helper/vat/info') }}"
                                                data-vv-country="#country" data-vv-name="#company"
                                                data-vv-address="#address" data-vv-zip-code="#zip_code"
                                                data-vv-city="#city" data-vv-phone="#phone" data-vv-mobile="#mobile">
                                                @trans('Validar')
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-orange vat-alert" style="display: none" data-toggle="tooltip"
                                    title="O NIF indicado já está associado a outro cliente ou potencial cliente. Pode abrir ficha de cliente mesmo assim, contudo apenas é considerado para efeitos de faturação o NIF do primeiro cliente indicado.">
                                    @trans('NIF já existente') <i class="fas fa-info-circle"></i>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="form-group">
                                    {{ Form::label('company', __('Designação Social')) }}
                                    {{ Form::text('company', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                        <div style="font-size: 13px;position: relative;top: -10px;margin-bottom: -5px; display: none"
                            class="text-red vat-feedback"></div>
                        <div class="form-group">
                            {{ Form::label('address', __('Morada')) }}
                            {{ Form::text('address', null, ['class' => 'form-control']) }}
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-3 col-md-3 col-lg-2">
                                <div class="form-group">
                                    {{ Form::label('zip_code', __('Código Postal')) }}
                                    {{ Form::text('zip_code', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <div class="form-group">
                                    {{ Form::label('city', __('Localidade')) }}
                                    {{ Form::text('city', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('email', __('E-mail Contacto')) }}
                                    {{ Form::text('email', null, ['class' => 'form-control nospace email']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('phone', __('Telefone')) }}
                                    {{ Form::text('phone', null, ['class' => 'form-control phone']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('mobile', __('Telemóvel')) }}
                                    {{ Form::text('mobile', $provider->mobile ?? null, ['class' => 'form-control phone']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('attn', __('Pessoa Contacto')) }}
                                    {{ Form::text('attn', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    {{ Form::label('payment_method', __('Pagamento')) }}
                                    {{ Form::select('payment_method', ['' => ''] + $paymentConditions, null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-user"></i> @trans('Cliente Associado') {!! tip(__('Se este fornecedor é também cliente, associe ambas as fichas para ativar o encontro de contas.')) !!}
                                    </label>
                                    {{ Form::select('assigned_customer_id',empty($customer) ? [] : $customer->pluck('name', 'id'),empty($customer) ? null : $customer->id,['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>
                                        @trans('Agência Faturação') {!! tip(__('Escolhe a agência para aparece nas faturas deste fornecedor. Por padrão vai buscar a informação geral do sistema.')) !!}
                                    </label>
                                    {{ Form::select('agency_id',empty($agencies)? []: $agencies->pluck('name', 'id')->prepend('', '')->toArray(),null,['class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                        <h4 class="form-divider">@trans('Dados Pagamento')</h4>
                        <div class="row row-5">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('billing_email', __('E-mail Pagamentos')) }}
                                    {{ Form::text('billing_email', null, ['class' => 'form-control nospace email']) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group m-b-0">
                                    {{ Form::label('bank_iban', __('IBAN para pagamentos')) }}
                                    {{ Form::text('bank_iban', null, ['class' => 'form-control iban']) }}
                                </div>
                            </div>
                        </div>

                        @if ($provider->type == 'carrier')
                            <div class="form-divider">
                                <h4 class="pull-left">@trans('Enviar Resumo diário')</h4>
                                @if ($provider->daily_report)
                                    <div class="badge-status status-success">
                                        <i class="fas fa-circle"></i> @trans('ATIVO')
                                    </div>
                                @else
                                    <div class="badge-status">
                                        <i class="fas fa-circle"></i> @trans('INATIVO')
                                    </div>
                                @endif
                                <div class="clearfix"></div>
                            </div>
                            <div class="m-b-10">
                                <small class="text-muted italic">@trans('Enviar diáriamente ao cliente o resumo e estado atual
                                    de todos os envios que sofreram alterações durante o dia.')</small>
                            </div>
                            <div class="row row-5">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-email pull-left">
                                        <div class="input-group-addon" data-toggle="tooltip"
                                            title="Ative esta opção para enviar e-mail ao cliente.">
                                            <i class="fas fa-envelope"></i>
                                            {{ Form::checkbox('daily_report') }}
                                        </div>
                                        {{ Form::text('daily_report_email', null, ['class' => 'form-control pull-left nospace lowercase','placeholder' => 'E-mail para notificação']) }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <label>@trans('Disponibilizar fornecedor para')</label>
                <div class="row row-5">
                    <div class="col-xs-12">
                        @if ($agencies->count() >= 6)
                            <div style="max-height: 155px;overflow: scroll;border: 1px solid #ddd;padding: 0 8px;">
                        @endif
                        @foreach ($agencies as $agency)
                            <div class="checkbox m-t-5 m-b-8">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('agencies[]', $agency->id, null) }}
                                    <span class="label"
                                        style="background: {{ $agency->color }}">{{ $agency->code }}</span>
                                    {{ $agency->print_name }}
                                </label>
                            </div>
                        @endforeach
                        @if ($agencies->count() >= 6)
                    </div>
                    @endif
                </div>
            </div>
            <h4 class="form-divider">@trans('Outros Dados')</h4>
            <div class="form-group is-required">
                {{ Form::label('locale', __('Preferência de Idioma')) }}
                {{ Form::select('locale', trans('admin/localization.locales'), null, ['class' => 'form-control select2']) }}
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('obs', __('Observações')) }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 17, 'style' => 'min-height:132px']) }}
            </div>
        </div>
    </div>
    <hr />
    <button type="submit" class="btn btn-primary pull-left">@trans('Gravar')</button>
    <div class="checkbox m-l-15 pull-left">
        <label style="padding-left: 0; margin-top: -5px; display: block;">
            {{ Form::checkbox('update_billing_software', 1, $provider->exists ? false : true, [hasModule('invoices') ? '' : 'disabled']) }}
            @if (!hasModule('invoices'))
                <span class="text-muted" data-toggle="tooltip"
                    title="A sua aplicação não está ligada ao programa de faturação.">
                @else
                    <span>
            @endif
            @if ($provider->exists)
                @trans('Ao gravar, atualizar no programa de faturação.')
            @else
                @trans('Ao gravar, criar fornecedor no programa de faturação.')
            @endif
            </span>
        </label>
    </div>
    <div class="clearfix"></div>
</div>
</div>
{{ Form::close() }}
