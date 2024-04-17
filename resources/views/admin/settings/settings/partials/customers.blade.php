<div class="box no-border">
    <div class="box-body p-t-0">
        <div class="row">
            <div class="col-sm-4">
                <h4 class="section-title">Configurações Gerais</h4>
                <table class="table table-condensed">

                    <tr>
                        <td>
                            {{ Form::label('show_customers_abbrv', 'Mostrar campo "abreviatura"', ['class' => 'control-label']) }}
                            {!! tip('Este campo permite criar um código de abreviatura para o cliente para que seja mais fácil identificar o cliente no sistema.') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('show_customers_abbrv', 1, Setting::get('show_customers_abbrv'), ['class' => 'ios'] ) }}</td>
                    </tr>

                    <tr>
                        <td>{{ Form::label('customers_hide_payment_at_recipient', 'Ocultar opção portes no destino', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('customers_hide_payment_at_recipient', 1, Setting::get('customers_hide_payment_at_recipient'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('customers_show_charge_price', 'Mostrar opção reembolso/cobrança', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('customers_show_charge_price', 1, Setting::get('customers_show_charge_price'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('shipments_reference2_visible', 'Mostrar 2ª Referência ao cliente', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('shipments_reference2_visible', 1, Setting::get('shipments_reference2_visible'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('shipments_reference3_visible', 'Mostrar 3ª Referência ao cliente', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('shipments_reference3_visible', 1, Setting::get('shipments_reference3_visible'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('customers_choose_providers', 'Permitir escolher o fornecedor', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('customers_choose_providers', 1, Setting::get('customers_choose_providers'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('customers_list_show_wallet', 'Mostrar coluna Saldo', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('customers_list_show_wallet', 1, Setting::get('customers_list_show_wallet'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('show_customers_reference', 'Mostrar coluna Referência', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('show_customers_reference', 1, Setting::get('show_customers_reference'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('customers_list_only_active', 'Listar só clientes ativos', ['class' => 'control-label']) }} {!! tip('Esta opção ativa por defeito o filtro de cliente ativo na listagem de clientes. A qualquer momento é possível consultar todos os clientes desativando o filtro da listagem.') !!}</td>
                        <td class="check">{{ Form::checkbox('customers_list_only_active', 1, Setting::get('customers_list_only_active'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customers_unpaid_invoices_limit', 'Bloqueio por faturação em atraso', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Impede a criação de novos envios se o cliente tiver documentos por liquidar à mais do que um determinado número de dias. O cliente poderá aceder à área de cliente mas não conseguirá criar envios."></i>
                        </td>
                        <td class="w-115px">
                            <div class="input-group">
                                {{ Form::text('customers_unpaid_invoices_limit', Setting::get('customers_unpaid_invoices_limit'), ['class' =>'form-control']) }}
                                <span class="input-group-addon" id="basic-addon2">Dias</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('alert_max_days_without_shipments', 'Alerta de tempo sem ativade', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="O sistema irá idêntificar os clientes que já não façam qualquer envio à mais do que o número de dias indicados."></i>
                        </td>
                        <td>
                            <div class="input-group">
                                {{ Form::text('alert_max_days_without_shipments', Setting::get('alert_max_days_without_shipments'), ['class' =>'form-control']) }}
                                <span class="input-group-addon" id="basic-addon2">Dias</span>
                            </div>
                        </td>
                    </tr>
                </table>
                <h4 class="section-title">Seguimento de envios no site</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>{{ Form::label('tracktrace_show_signature', 'Mostrar nome e assinatura de entrega', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('tracktrace_show_signature', 1, Setting::get('tracktrace_show_signature'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('tracking_email_active', 'Permitir envio de e-mail para seguimento', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('tracking_email_active', 1, Setting::get('tracking_email_active'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('tracking_location_active', 'Permitir consultar localização de entrega', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('tracking_location_active', 1, Setting::get('tracking_location_active'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('tracking_show_provider_trk', 'Permitir consultar código fornecedor', ['class' => 'control-label']) }}
                            {!! tip('Ative esta opção para permitir que seja apresentado o fornecedor por onde seguiu o envio e o respetivo número de envio.') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('tracking_show_provider_trk', 1, Setting::get('tracking_show_provider_trk'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('tracking_show_delivery_date', 'Permitir consultar previsão entrega', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('tracking_show_delivery_date', 1, Setting::get('tracking_show_delivery_date'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('tracking_show_delivery_agency', 'Mostrar contactos agência entrega', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('tracking_show_delivery_agency', 1, Setting::get('tracking_show_delivery_agency'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('tracking_auto_sync', 'Sincronizar estados ao consultar', ['class' => 'control-label']) }} {!! tip('Quando um cliente consultar o estado de um do envio no site público, o sistema vai sincronizar e atualizar todos os estados de acordo com o fornecedor.') !!}</td>
                        <td class="check">{{ Form::checkbox('tracking_auto_sync', 1, Setting::get('tracking_auto_sync'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('change_info_shipment', 'Reagendar entrega', ['class' => 'control-label']) }} {!! tip('Permitir que qualquer pessoa com o número do tracking possa alterar ou reagendar um envio') !!}</td>
                        <td class="check">{{ Form::checkbox('change_info_shipment', 1, Setting::get('change_info_shipment'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('tracking_show_operator_name', 'Mostrar nome motorista (só com login)', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('tracking_show_operator_name', 1, Setting::get('tracking_show_operator_name'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('tracking_show_operator_phone', 'Mostrar contacto motorista (só com login)', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('tracking_show_operator_phone', 1, Setting::get('tracking_show_operator_phone'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
            </div>

            <div class="col-sm-4">
                <h4 class="section-title">Criação de Envios</h4>
                <table class="table table-condensed m-0">
                    <tr>
                        <td>
                            {{ Form::label('customer_shipment_phone_required', 'Obrigar preenchimento do telefone', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Força a que o cliente insira sempre um telefone no campo remetente e destinatário"></i>
                        </td>
                        <td class="check">{{ Form::checkbox('customer_shipment_phone_required', 1, Setting::get('customer_shipment_phone_required'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customer_recipient_vat_required', 'Obrigar preenchimento NIF', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Obriga o preenchimento do NIF nos dados do remetente e destinatário."></i>
                        </td>
                        <td class="check">{{ Form::checkbox('customer_recipient_vat_required', 1, Setting::get('customer_recipient_vat_required'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customer_account_email_required', 'Obrigar preenchimento do E-MAIL', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Obriga o preenchimento do email na criação de um envio."></i>
                        </td>
                        <td class="check">{{ Form::checkbox('customer_account_email_required', 1, Setting::get('customer_account_email_required'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customer_shipment_without_pickup', 'Mostrar campo "leva à agência', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Ativa o campo para permitir ao cliente indicar que deixa os volumes na agência, sem necessidade de recolha pelo motorista. "></i>
                        </td>
                        <td class="check">{{ Form::checkbox('customer_shipment_without_pickup', 1, Setting::get('customer_shipment_without_pickup'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('customers_shipment_hours', 'Mostrar campo hora do serviço', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('customers_shipment_hours', 1, Setting::get('customers_shipment_hours'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('customers_shipment_hours_fill', 'Pré preencher hora do serviço', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('customers_shipment_hours_fill', 1, Setting::get('customers_shipment_hours_fill'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('customers_shipment_hours_required', 'Obrigar preenchimento hora do serviço', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('customers_shipment_hours_required', 1, Setting::get('customers_shipment_hours_required'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customers_show_webservice_errors', 'Mostrar erros dos webservices', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Caso a submissão via webservice dê erro, o cliente será informado da mensagem de erro. Caso a opção esteja inativa, o cliente não será notificado de erros de submissão."></i>
                        </td>
                        <td class="check">{{ Form::checkbox('customers_show_webservice_errors', 1, Setting::get('customers_show_webservice_errors'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customers_preview_shipment_price', 'Mostrar pré-visualização de preço', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Apresenta na janela de criação do envio o preço previsto a pagar pelo transporte."></i>
                        </td>
                        <td class="check">{{ Form::checkbox('customers_preview_shipment_price', 1, Setting::get('customers_preview_shipment_price'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customers_force_default_provider', 'Forçar a assumir o fornecedor por defeito', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Força a que todos os envios criados na área de cliente assumam sempre o fornecedor {{ @$providers[Setting::get('shipment_default_provider')] }}"></i>
                        </td>
                        <td class="check">{{ Form::checkbox('customers_force_default_provider', 1, Setting::get('customers_force_default_provider'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customer_block_provider_labels', 'Impedir impressão etiquetas fornecedor', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Força a que na área de cliente seja sempre impressa a etiqueta da {{ Setting::get('company_name') }}"></i>
                        </td>
                        <td class="check">{{ Form::checkbox('customer_block_provider_labels', 1, Setting::get('customer_block_provider_labels'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    @if(!in_array(Setting::get('app_mode'), ['cargo', 'freight']))
                    <tr>
                        <td>
                            {{ Form::label('customers_show_cmr', 'Permitir imprimir CMR', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('customers_show_cmr', 1, Setting::get('customers_show_cmr'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>
                            {{ Form::label('customer_show_provider_trk', 'Mostrar TRK Fornecedor', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('customer_show_provider_trk', 1, Setting::get('customer_show_provider_trk'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customers_show_delivery_date', 'Mostrar coluna data de entrega', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Apresenta na listagem a informação sobre a data e hora prevista de entrega."></i>
                        </td>
                        <td class="check">{{ Form::checkbox('customers_show_delivery_date', 1, Setting::get('customers_show_delivery_date'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customer_show_adr_fields', 'Mostrar campos ADR', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('customer_show_adr_fields', 1, Setting::get('customer_show_adr_fields'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('account_toggle_sender', 'Permitir trocar campos Local Rec. e Dest.', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Ativa na janela de envio/recolha a opção para trocar os campos 'Local Recolha' e 'Local de Entrega'"></i>
                        </td>
                        <td class="check">
                            {{ Form::checkbox('account_toggle_sender', 1, Setting::get('account_toggle_sender'), ['class' => 'ios'] ) }}
                        </td>
                    </tr>
                    @if(hasModule('shipment_attachments'))
                    <tr>
                        <td>
                            {{ Form::label('show_shipment_attachments', 'Permitir ver anexos na área cliente', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('show_shipment_attachments', 1, Setting::get('show_shipment_attachments'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>
                            {{ Form::label('show_shipment_assembly', 'Permitir ver informação de montagem', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('show_shipment_assembly', 1, Setting::get('show_shipment_assembly'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customer_block_edit_sender_address', 'Impedir edição morada recolha', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('customer_block_edit_sender_address', 1, Setting::get('customer_block_edit_sender_address'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                    <tr>
                        <td>
                            {{ Form::label('customers_default_service', 'Serviço ativo por defeito', ['class' => 'control-label']) }}
                            {!! tip('Estabelece o serviço escolhido como serviço sempre pré-selecionado na criação do envio.') !!}
                        </td>
                        <td>{{ Form::select('customers_default_service', ['' => 'Nenhum'] + $services, Setting::get('customers_default_service'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('shipments_daily_limit_hour', 'Hora limite recolha', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Todos os envios após a hora indicada, são transferidos para o dia seguinte. O cliente será informado na janela de criação do envio."></i>
                        </td>
                        <td>{{ Form::select('shipments_daily_limit_hour', ['' => 'Inativo'] + listHours(), Setting::get('shipments_daily_limit_hour'), ['class' =>'form-control select2']) }}</td>
                    </tr>

                </table>
                <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                    <tr>
                        <td>
                            {{ Form::label('customers_allow_edit_after_webservice', 'Permitir editar após submissão webservice', ['class' => 'control-label']) }}
                            {!! tip('Permite editar o envio mesmo após a submissão ao webservice') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('customers_allow_edit_after_webservice', 1, Setting::get('customers_allow_edit_after_webservice'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customers_allow_delete_after_webservice', 'Permitir eliminar após submissão', ['class' => 'control-label']) }}
                            {!! tip('Permite eliminar o envio mesmo após a submissão ao webservice') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('customers_allow_delete_after_webservice', 1, Setting::get('customers_allow_delete_after_webservice'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                    <tr>
                        <td>
                            {{ Form::label('services_can_delete', 'Permitir editar ou eliminar envios nos estados') }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="O cliente poderá editar ou eliminar envios se estes estiverem em um dos estados listados."></i>
                            {{ Form::select('services_can_delete[]', $status, empty(Setting::get('services_can_delete')) ? []: @array_map('intval', Setting::get('services_can_delete')), ['class' =>'form-control select2', 'multiple']) }}
                        </td>
                    </tr>
                </table>
                <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                    <tr>
                        <td>
                            {{ Form::label('customers_shipment_default_save_customer', 'Não gravar auto novos destinatários') }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Se a opção estiver ativa, ao criar um novo envio, não ficará automáticamente ativa a opção 'Gravar novo endereço'"></i>
                            <td class="check">{{ Form::checkbox('customers_shipment_default_save_customer', 1, Setting::get('customers_shipment_default_save_customer'), ['class' => 'ios'] ) }}</td>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">
                <h4 class="section-title">Numeração de Clientes</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>{{ Form::label('customers_code_autoincrement', 'Numeração automática', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('customers_code_autoincrement', 1, Setting::get('customers_code_autoincrement'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customers_use_empty_codes', 'Preencher falhas de numeração', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Se a numeração dos clientes não for sequencial, esta opção irá detectar os códigos não usados e atribui esses códigos a novos clientes."></i>
                        </td>
                        <td class="check">{{ Form::checkbox('customers_use_empty_codes', 1, Setting::get('customers_use_empty_codes'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customers_code_prefix', 'Prefixo de numeração automática', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Se os códigos de cliente começam por um prefixo (Por ex: K-342, Z342), indique-o para que o sistema o assuma automáticamente. "></i>
                        </td>
                        <td class="w-100px">
                            {{ Form::text('customers_code_prefix', Setting::get('customers_code_prefix'), ['class' =>'form-control uppercase nospace']) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('customers_code_pad_left', 'Maximo de zeros à esquerda', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Acrescenta zeros à esquerda do numero de cliente. Ex: 032"></i>
                        </td>
                        <td class="w-100px">
                            {{ Form::select('customers_code_pad_left', ['' => '', '2' => '1 (Ex: 08)', '3' => '2  (Ex: 006)', '4' => '3 (Ex: 0004)', '5' => '4 (Ex: 00003)'], Setting::get('customers_code_pad_left'), ['class' =>'form-control select2']) }}
                        </td>
                    </tr>
                </table>
                <h4 class="section-title">Controlo de Reembolsos</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>
                            {{ Form::label('refunds_control_customers_hide_received_column', 'Ocultar coluna "Recebido"', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Oculta a coluna que informa quando o reembolso foi recebido na agência."></i>
                        </td>
                        <td class="check">
                            {{ Form::checkbox('refunds_control_customers_hide_received_column', 1, Setting::get('refunds_control_customers_hide_received_column'), ['class' => 'ios'] ) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('refunds_control_customers_hide_paid_column', 'Ocultar coluna  "Pago"', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Oculta a coluna que informa quando o reembolso foi pago ao cliente."></i>
                        </td>
                        <td class="check">{{ Form::checkbox('refunds_control_customers_hide_paid_column', 1, Setting::get('refunds_control_customers_hide_paid_column'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <h4 class="section-title">Menus Área Cliente</h4>
                <table class="table table-condensed m-b-0">
                    <tr>
                        <td>
                            {{ Form::label('account_shipments_menuname', 'Título do menu "Envios"', ['class' => 'control-label']) }}
                        </td>
                        <td class="w-170px">
                            {{ Form::text('account_shipments_menuname', Setting::get('account_shipments_menuname'), ['class' =>'form-control', 'placeholder' => trans('account/global.menu.shipments')]) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('account_pickups_menuname', 'Título do menu "Recolhas"', ['class' => 'control-label']) }}
                        </td>
                        <td class="w-170px">
                            {{ Form::text('account_pickups_menuname', Setting::get('account_pickups_menuname'), ['class' =>'form-control', 'placeholder' => trans('account/global.menu.pickups')]) }}
                        </td>
                    </tr>
                </table>
                <table class="table table-condensed">
                    <tr>
                        <td>{{ Form::label('customers_hide_billing', 'Ocultar preços e faturação', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('customers_hide_billing', 1, Setting::get('customers_hide_billing'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('show_customers_ballance', 'Mostrar conta corrente', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('show_customers_ballance', 1, Setting::get('show_customers_ballance'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('customers_show_support_phone', 'Mostrar número de Apoio ao Cliente', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('customers_show_support_phone', 1, Setting::get('customers_show_support_phone'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('account_edit_details', 'Permitir editar dados pessoais', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">
                            {{ Form::checkbox('account_edit_details', 1, Setting::get('account_edit_details'), ['class' => 'ios'] ) }}
                        </td>
                    </tr>
                    @if(hasModule('incidences'))
                    <tr>
                        <td>
                            {{ Form::label('account_show_incidences', 'Permitir ver e gerir Incidências', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Ativa o menu para consulta e gestão de incidências."></i>
                        </td>
                        <td class="check">
                            {{ Form::checkbox('account_show_incidences', 1, Setting::get('account_show_incidences'), ['class' => 'ios'] ) }}
                        </td>
                    </tr>
                    @endif
                    @if(hasModule('logistic'))
                        <tr>
                            <td>{{ Form::label('show_customers_logistic', 'Motrar menu logística', ['class' => 'control-label']) }}</td>
                            <td class="check">{{ Form::checkbox('show_customers_logistic', 1, Setting::get('show_customers_logistic'), ['class' => 'ios'] ) }}</td>
                        </tr>
                    @endif
                </table>
            </table>
                <h4 class="section-title">Registo de Novos Clientes</h4>
                @if(!hasModule('account_signup'))
                <div style="width: 100%; opacity: 0.4;"
                     data-toggle="tooltip"
                     title="Módulo não incluido no seu plano. Permita que novos clientes se registem na área de cliente.">
                @else
                <div>
                @endif
                <table class="table table-condensed m-b-0">
                    <tr>
                        <td>
                            {{ Form::label('account_signup', 'Permitir o registo livre de novos clientes', ['class' => 'control-label']) }}
                            {!! tip('Ao ativar esta opção, permite que na página inicial qualquer pessoa se possa registar no site e abrir uma conta de cliente') !!}
                        </td>
                        <td class="check">
                            {{ Form::checkbox('account_signup', 1, Setting::get('account_signup'), ['class' => 'ios'] ) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('account_signup_validate', 'Novos registos requerem validação', ['class' => 'control-label']) }}
                            {!! tip('Novos registos são identificados e requerem validação de um gerente ou administrativo.') !!}
                        </td>
                        <td class="check">
                            {{ Form::checkbox('account_signup_validate', 1, Setting::get('account_signup_validate'), ['class' => 'ios'] ) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('account_signup_fast', 'Ativar registo rápido', ['class' => 'control-label']) }}
                            {!! tip('Ao ativar esta opção, vai ser pedido ao cliente apenas o nome, e-mail e password no ato de registo.') !!}
                        </td>
                        <td class="check">
                            {{ Form::checkbox('account_signup_fast', 1, Setting::get('account_signup_fast'), ['class' => 'ios'] ) }}
                        </td>
                    </tr>
                </table>
                <table class="table table-condensed m-b-0">
                    <tr>
                        <td>
                            {{ Form::label('account_signup_agency', 'Agência por defeito', ['class' => 'control-label']) }}
                        </td>
                        <td class="w-170px">
                            {{ Form::select('account_signup_agency', [''=>''] + $agencies, Setting::get('account_signup_agency'), ['class' =>'form-control select2']) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('account_signup_type', 'Tipo de cliente por defeito', ['class' => 'control-label']) }}
                        </td>
                        <td class="w-170px">
                            {{ Form::select('account_signup_type', [''=>''] + $customersTypes, Setting::get('account_signup_type'), ['class' =>'form-control select2']) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('account_signup_prices_table', 'Tabela de preços por defeito', ['class' => 'control-label']) }}
                        </td>
                        <td class="w-170px">
                            {{ Form::select('account_signup_prices_table', [''=>''] + $pricesTables, Setting::get('account_signup_prices_table'), ['class' =>'form-control select2']) }}
                        </td>
                    </tr>
                </table>
                    <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                        <tr>
                            <td>
                                {{ Form::label('account_signup_services', 'Serviços disponíveis') }}
                                {!! tip('Selecione quais os serviços disponíveis por defeito quando se cria área de cliente.') !!}
                                {{ Form::select('account_signup_services[]', $services, @array_map('intval', Setting::get('account_signup_services')), ['class' =>'form-control select2', 'multiple']) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="col-sm-12">
                {{ Form::submit('Gravar', array('class' => 'btn btn-primary' ))}}
            </div>
        </div>
    </div>
</div>
