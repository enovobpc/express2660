<div class="box no-border">
    <div class="box-body">
        {{ Form::model($customer, ['route'=> ['admin.customers.update', $customer->id, 'save' => 'settings'], 'method' => 'PUT']) }}
        <div class="col-sm-5">
            @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'admin_settings'))
                <h4 class="form-divider no-border" style="border-top: none; margin-top: 0">@trans('Bloqueio da conta')</h4>
                <table class="table table-condensed" style="border-top: 1px solid #eee">
                    <tr>
                        <td>
                            <p class="form-control-static" style="border: none; padding-left: 0;">
                                @trans('Bloqueio por faturação em atraso após')
                            </p>
                        </td>
                        <td class="w-110px">
                            <div class="input-group">
                                {{ Form::text('unpaid_invoices_limit', null, array('class' =>'form-control int', 'placeholder' => Setting::get('customers_unpaid_invoices_limit'))) }}
                                <span class="input-group-addon">@trans('dias')</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="form-control-static" style="border: none; padding-left: 0;">
                                @trans('Bloqueio por Limite de Crédito após ultrapassar')
                            </p>
                        </td>
                        <td class="w-110px">
                            <div class="input-group">
                                {{ Form::text('unpaid_invoices_credit', null, array('class' =>'form-control decimal')) }}
                                <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="form-control-static" style="border: none; padding-left: 0;">
                                @trans('Plafound máximo') {{ Setting::get('billing_method') == '30d' ? 'mensal' : 'quinzenal' }}
                                {!! tip(__('Bloqueia o sistema após o cliente atingir mensalmente serviços no valor indicado')) !!}
                            </p>
                        </td>
                        <td class="w-110px">
                            <div class="input-group">
                                {{ Form::text('monthly_plafound', null, array('class' =>'form-control decimal')) }}
                                <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="form-control-static m-0" style="border: none; padding-left: 0;">
                                @trans('Ignorar da faturação massiva') {!! tip(__('Exclui este cliente da faturação massiva, impedindo que seja faturado quando utilizada a funcionalidade de faturação massiva.')) !!}
                            </p>
                        </td>
                        <td class="check">{{ Form::checkbox('ignore_mass_billing', 1, $customer->ignore_mass_billing, ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
            @endif

            <h4 class="form-divider" style="border-top: none; margin-top: 0">@trans('Opções e Menus da Área de cliente')</h4>
            <table class="table table-condensed">
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Preferência de Idioma')
                        </p>
                    </td>
                    <td class="w-190px">{{ Form::select('locale', trans('admin/localization.locales'), null, ['class' => 'form-control select2']) }}</td>
                </tr>
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Mostrar coluna referência')
                        </p>
                    </td>
                    <td class="w-120px">{{ Form::select('show_reference', ['' => '- Usar Definição Geral -', '1'=> 'Sim', '2'=>'Não'], null, ['class' =>'form-control select2']) }}</td>
                </tr>
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Ocultar Preços e Faturação')
                        </p>
                    </td>
                    <td>{{ Form::select('hide_billing', ['' => __('- Usar Definição Geral -'), '2' => __('Ocultar'), '1' => __('Mostrar')], null, ['class' =>'form-control select2']) }}</td>
                </tr>
                @if(hasModule('collections'))
                <tr>
                    <td>
                        <p class="form-control-static m-0" style="border: none; padding-left: 0;">
                            @trans('Ocultar menu "Pedidos Recolha"')' {!! tip(__('Esta opção oculta o menu "Pedidos de Recolha" na área do cliente.')) !!}
                        </p>
                    </td>
                    <td class="check">{{ Form::checkbox('hide_menu_pickups', 1, @$customer->settings['hide_menu_pickups'], ['class' => 'ios'] ) }}</td>
                </tr>
                @endif
                <tr>
                    <td>
                        <p class="form-control-static m-0" style="border: none; padding-left: 0;">
                            @trans('Ocultar botão Orçamentar') {!! tip(__('Esta opção oculta o botão "Orçamentar" na área do cliente.')) !!}
                        </p>
                    </td>
                    <td class="check">{{ Form::checkbox('hide_budget_btn', 1, $customer->password ? null : 0, ['class' => 'ios'] ) }}</td>
                </tr>
                @if(hasModule('incidences'))
                    <tr>
                        <td>
                            <p class="form-control-static m-0" style="border: none; padding-left: 0;">
                                @trans('Ocultar menu "Incidências"')' {!! tip(__('Impede o cliente de visualizar o menu e gerir incidências.')) !!}
                            </p>
                        </td>
                        <td class="check">{{ Form::checkbox('hide_incidences_menu', 1, @$customer->settings['hide_incidences_menu'], ['class' => 'ios'] ) }}</td>
                    </tr>
                @endif
                @if(hasModule('products'))
                    <tr>
                        <td>
                            <p class="form-control-static m-0" style="border: none; padding-left: 0;">
                                @trans('Ocultar menu "Venda de Productos"')
                            </p>
                        </td>
                        <td class="check">{{ Form::checkbox('hide_products_sales', 1, @$customer->settings['hide_products_sales'], ['class' => 'ios'] ) }}</td>
                    </tr>

                    <tr>
                        <td>
                            <p class="form-control-static m-0" style="border: none; padding-left: 0;">
                                @trans('Ocultar Botão De Criação Envios')
                            </p>
                        </td>
                        <td class="check">{{ Form::checkbox('hide_btn_shipments', 1, @$customer->settings['hide_btn_shipments'], ['class' => 'ios'] ) }}</td>
                    </tr>
                @endif
                @if(hasModule('shipment_attachments'))
                    <tr>
                        <td>
                            <p class="form-control-static m-0" style="border: none; padding-left: 0;">
                                @trans('Mostrar separador anexos') {!! tip(__('Permite ao cliente visualizar o separador de anexos do envio.')) !!}
                            </p>
                        </td>
                        <td class="check">{{ Form::checkbox('show_shipment_attachments', 1, @$customer->settings['show_shipment_attachments'], ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <p class="form-control-static m-0" style="border: none; padding-left: 0;">
                                @trans('Permitir carregamento de anexos') {!! tip(__('Permite ao cliente carregar novos anexos para o envio.')) !!}
                            </p>
                        </td>
                        <td class="check">{{ Form::checkbox('upload_shipment_attachments', 1, @$customer->settings['upload_shipment_attachments'], ['class' => 'ios'] ) }}</td>
                    </tr>
                @endif
            </table>
            <h4 class="form-divider" style="border-top: none; margin-top: 0">@trans('Criação de Envios')</h4>
            <table class="table table-condensed m-0">
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Ao criar envios, imprimir')
                        </p>
                    </td>
                    <td style="width: 90px">
                        {{ Form::select('default_print', ['' => __('- Usar Definição Geral -')] + trans('admin/shipments.print-options'), $customer->default_print, ['class' => 'form-control select2']) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Formato Etiqueta')
                        </p>
                    </td>
                    <td>{{ Form::select('label_template', trans('admin/shipments.labels-sizes')+['' => '- Usar Definição Geral -'], @$customer->settings['label_template'], ['class' =>'form-control select2']) }}</td>
                </tr>
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Serviço ativo por defeito')
                        </p>
                    </td>
                    <td style="width: 90px">
                        {{ Form::select('default_service', ['' => __('- Usar Definição Geral -')] + $servicesList, null, ['class' => 'form-control select2']) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Hora limite expedição diária') {!! tip(__('Todos os envios após a hora indicada, são transferidos para o dia seguinte.')) !!}
                        </p>
                    </td>
                    <td>{{ Form::select('shipments_daily_limit_hour', ['' => __('- Usar Definição Geral -')] + listHours(), null, ['class' =>'form-control select2']) }}</td>
                </tr>
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Tipo embalagens disponíveis')
                        </p>
                    </td>
                    <td>{{ Form::select('enabled_packages[]', $packTypes, null, ['class' =>'form-control select2', 'multiple', 'data-placeholder' => 'Todos']) }}</td>
                </tr>
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Autorizar Pontos Pickup') {!! tip(__('Escolha os fornecedores dos quais o cliente poderá escolher pontos pickup. O envio será automáticamente atribuido ao fornecedor do ponto pickup associado.')) !!}
                        </p>
                    </td>
                    <td>
                        @if(hasModule('pudos'))
                            {{ Form::select('enabled_pudo_providers[]', $pudoProviders, array_map('intval', $customer->enabled_pudo_providers ? $customer->enabled_pudo_providers : []), ['class' =>'form-control select2', 'multiple']) }}
                        @else
                        <span data-toggle="tooltip" title="Módulo não incluido na sua licença.">
                            {{ Form::select('enabled_pudo_providers[]', [], '', ['class' =>'form-control select2', 'disabled']) }}
                        </span>
                        @endif
                    </td>
                </tr>
            </table>
            <table class="table table-condensed m-0">
                <tr>
                    <td>
                        <p class="form-control-static m-0" style="border: none; padding-left: 0;">
                            @trans('Ativar sempre portes no destino') {!! tip(__('Ative esta opção se todos os envios do cliente sejam pagos pelo destinatário.')) !!}
                        </p>
                    </td>
                    <td class="check">{{ Form::checkbox('always_cod', 1, $customer->always_cod, ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Permitir o envio de mensagens SMS')
                        </p>
                    </td>
                    <td class="check">{{ Form::checkbox('sms_enabled', 1, $customer->sms_enabled, ['class' => 'ios'] ) }}</td>
                </tr>
            </table>
        </div>
        <div class="col-sm-7" style="padding-left: 50px">
            <div class="form-divider no-border" style="border-top: none; margin-top: 0">
                <h4 class="pull-left">@trans('Enviar Resumo diário')</h4>
                @if($customer->daily_report)
                    <div class="badge-status status-success">
                        <i class="fas fa-circle"></i> @trans('ATIVO')
                    </div>
                @else
                    <div class="badge-status">
                        <i class="fas fa-circle"></i> @trans('INATIVO')
                    </div>
                @endif
                {!! tip(__('Enviar diáriamente ao cliente o resumo e estado atual de todos os envios que sofreram alterações durante o dia.')) !!}
                <div class="clearfix"></div>
            </div>
            <div class="m-b-10"></div>
            <div class="row row-5">
                <div class="col-sm-12">
                    <div class="input-group input-group-email pull-left">
                        <div class="input-group-addon" data-toggle="tooltip" title="Ative esta opção para enviar e-mail ao cliente.">
                            <i class="fas fa-envelope"></i>
                            {{ Form::checkbox('daily_report') }}
                        </div>
                        {{ Form::text('daily_report_email', null, ['class' => 'form-control pull-left nospace lowercase email', 'placeholder' => 'E-mail para notificação']) }}
                    </div>
                </div>
            </div>
            <h4 class="form-divider"><i class="fas fa-bell"></i> @trans('Notificações Automáticas')</h4>
            <table class="table table-condensed m-b-0">
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Enviar notificações de alteração de estados')
                        </p>
                    </td>
                    <td>
                        {{ Form::select('shipping_status_notify_method', ['email' => 'Apenas E-mail', 'sms' => 'Apenas SMS', 'both' => 'E-mail e SMS'], null, ['class' => 'form-control select2']) }}
                    </td>
                </tr>
            </table>
            <table class="table table-condensed m-0">
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Notificar Cliente nos Estados')
                            {!! tip(__('Notifique automáticamente o cliente quando um envio é entregue, tem incidência ou se encontra em outro estado à sua escolha.')) !!}
                        </p>
                    </td>
                    <td class="w-300px">
                        @if(hasModule('history_notifications'))
                            {{ Form::selectMultiple('shipping_status_notify[]', ['-1' => __('- Não notificar -')] + $statusList, null, ['class' =>'form-control select2', 'data-placeholder' => '- Usar a Definição Geral -']) }}
                        @else
                            <span data-toggle="tooltip" title="Módulo não incluído na sua licença.">
                            {{ Form::text('shipping_status_notify', '', ['class' =>'form-control', 'disabled']) }}
                            </span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Notificar Destinatário nos estados')
                            {!! tip(__('Notifique automáticamente os destinatários deste cliente quando um envio é entregue, tem incidência ou se encontra em outro estado à sua escolha.')) !!}
                        </p>
                    </td>
                    <td class="w-200px">
                        @if(hasModule('history_notifications'))
                            {{ Form::selectMultiple('shipping_status_notify_recipient[]', ['-1' => '- Não notificar -'] + $statusList, null, ['class' =>'form-control select2', 'data-placeholder' => '- Usar a Definição Geral -']) }}
                        @else
                            <span data-toggle="tooltip" title="Módulo não incluído na sua licença.">
                            {{ Form::text('shipping_status_notify_recipient', '', ['class' =>'form-control', 'disabled']) }}
                            </span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            @trans('Notificar só nos seguintes serviços:')
                            {!! tip(__('Escolha quais os serviços nos quais enviar ou não uma notificação.')) !!}
                        </p>
                    </td>
                    <td class="w-200px">
                        {{ Form::selectMultiple('shipping_services_notify[]', $servicesList, null, ['class' =>'form-control select2', 'data-placeholder' => 'Todos']) }}
                    </td>
                </tr>
            </table>
            <h4 class="form-divider"><i class="fas fa-mobile-alt"></i> @trans('Textos das mensagens SMS')</h4>
            <ul class="nav nav-tabs" style="border-bottom: 0">
                <li role="presentation" class="active">
                    <a href="#sms-registred" role="tab" data-toggle="tab">@trans('Envio Registado')</a>
                </li>
                <li role="presentation">
                    <a href="#sms-delivered" role="tab" data-toggle="tab">@trans('Entregue')</a>
                </li>
                <li role="presentation">
                    <a href="#sms-incidence" role="tab" data-toggle="tab">@trans('Incidência')</a>
                </li>
                <li role="presentation">
                    <a href="#sms-tracking" role="tab" data-toggle="tab">@trans('Outros Estados')</a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="sms-registred">
                    <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                        <tr>
                            <td>
                                {{ Form::label('customer_sms_text[sms_text_registered_sender]', __('Texto para Remetente')) }}
                                {{ Form::textarea('customer_sms_text[sms_text_registered_sender]', null, ['class' =>'form-control', 'rows' => 4]) }}
                            </td>
                            <td>
                                {{ Form::label('customer_sms_text[sms_text_registered_recipient]', __('Texto para o Destinatário')) }}
                                {{ Form::textarea('customer_sms_text[sms_text_registered_recipient]', null, ['class' =>'form-control', 'rows' => 4]) }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane" id="sms-delivered">
                    <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                        <tr>
                            <td>
                                {{ Form::label('customer_sms_text[sms_text_delivered_sender]', __('Texto para Remetente')) }}
                                {{ Form::textarea('customer_sms_text[sms_text_delivered_sender]', null, ['class' =>'form-control', 'rows' => 4]) }}
                            </td>
                            <td>
                                {{ Form::label('customer_sms_text[sms_text_delivered_recipient]', __('Texto para o Destinatário')) }}
                                {{ Form::textarea('customer_sms_text[sms_text_delivered_recipient]', null, ['class' =>'form-control', 'rows' => 4]) }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane" id="sms-incidence">
                    <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                        <tr>
                            <td>
                                {{ Form::label('customer_sms_text[sms_text_incidence_sender]', __('Texto para Remetente')) }}
                                {{ Form::textarea('customer_sms_text[sms_text_incidence_sender]', null, ['class' =>'form-control', 'rows' => 4]) }}
                            </td>
                            <td>
                                {{ Form::label('customer_sms_text[sms_text_incidence_recipient]', __('Texto para o Destinatário')) }}
                                {{ Form::textarea('customer_sms_text[sms_text_incidence_recipient]', null, ['class' =>'form-control', 'rows' => 4]) }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane" id="sms-tracking">
                    <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                        <tr>
                            <td>
                                {{ Form::label('customer_sms_text[sms_text_tracking_sender]', __('Texto para Remetente')) }}
                                {{ Form::textarea('customer_sms_text[sms_text_tracking_sender]', null, ['class' =>'form-control', 'rows' => 4]) }}
                            </td>
                            <td>
                                {{ Form::label('customer_sms_text[sms_text_tracking_recipient]', __('Texto para o Destinatário')) }}
                                {{ Form::textarea('customer_sms_text[sms_text_tracking_recipient]', null, ['class' =>'form-control', 'rows' => 4]) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <p class="m-l-5">
                <b>@trans('Códigos de texto')</b> - @trans('Use os códigos no texto para os substituir pelo respetivo valor.')'
            <div class="col-sm-6">
                <span class="label label-default">:trk</span> - @trans('Substitui pelo código de envio')<br>
                <span class="label label-default">:price</span> - @trans('Valor a cobrar (só apresenta caso exista)')<br/>
                <span class="label label-default">:date</span> - @trans('Substitui pela data do último estado')<br/>
                <span class="label label-default">:sender</span> - @trans('Substitui pelo nome remetente')<br>
                <span class="label label-default">:recipient</span> - @trans('Substitui pelo nome destinatario')
            </div>
            <div class="col-sm-6">
                <span class="label label-default">:status</span> - @trans('Substitui pelo estado do envio')<br/>
                <span class="label label-default">:receiver</span> - @trans('Substitui pelo nome do receptor')<br/>
                <span class="label label-default">:incidence</span> - @trans('Substitui pelo motivo incidência')<br/>
                <span class="label label-default">:url</span> - @trans('Substitui pelo URL de seguimento')
            </div>
            </p>
            @if(hasModule('logistic'))
            <h4 class="form-divider" style="border-top: none; margin-top: 30px">@trans('Logística e Stocks')</h4>
            <table class="table table-condensed m-0">
                <tr>
                    <td>
                        <p class="form-control-static m-0" style="border: none; padding-left: 0;">
                            @trans('Ocultar menu logística')
                        </p>
                    </td>
                    <td class="check">{{ Form::checkbox('logistic_hide_menu', 1, @$customer->settings['logistic_hide_menu'], ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        <p class="form-control-static m-0" style="border: none; padding-left: 0;">
                            @trans('Não mostrar produtos sem stock')
                        </p>
                    </td>
                    <td class="check">{{ Form::checkbox('logistic_stock_only_available', 1, @$customer->settings['logistic_stock_only_available'], ['class' => 'ios'] ) }}</td>
                </tr>
            </table>
            @endif
        </div>
        <div class="col-sm-12">
            <hr/>
            <button class="btn btn-primary">@trans('Gravar')</button>
        </div>
        {{ Form::close() }}
    </div>
</div>