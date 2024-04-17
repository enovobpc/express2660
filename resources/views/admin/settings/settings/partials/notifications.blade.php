<div class="box no-border">
    <div class="box-body p-t-0">
        <div class="row">
            <div class="col-sm-4">
                <h4 class="section-title">
                    <a href="?tab=customization"
                       data-toggle="tooltip"
                       title="Altere o som da aplicação só para a sua conta sem afetar outros utilizadores."
                       style="margin: -4px;" class="btn btn-xs btn-primary pull-right">
                        Personalizar para mim
                    </a>
                    Som Notificação
                </h4>
                <table class="table table-condensed m-b-0" style="border-bottom: 1px solid #eee">
                    <tr>
                        <td>{{ Form::label('notification_sound', 'Som notificação', ['class' => 'control-label']) }}</td>
                        <td class="w-50px">{{ Form::select('notification_sound', $notificationSounds,  Setting::get('notification_sound') ? Setting::get('notification_sound') : 'notification09', ['class' => 'select2'] ) }}</td>
                        <td class="w-1">
                            <button type="button" class="btn btn-sm btn-default btn-play-notification" data-toggle="tooltip" title="Clique para ouvir o som">
                                <i class="fas fa-volume-up"></i>
                            </button>
                        </td>
                    </tr>
                </table>
                <table class="table table-condensed">
                    <tr>
                        <td>{{ Form::label('disable_notification_sound', 'Desligar som de notificação', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('disable_notification_sound', 1,  Setting::get('disable_notification_sound'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">
                <h4 class="section-title">Mensagens PUSH</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>{{ Form::label('notification_force_disable', 'Desativar alertas de novo envio', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('notification_force_disable', 1,  Setting::get('notification_force_disable'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('notification_push_messages', 'Emitir Mensagens Push', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('notification_push_messages', 1, Setting::get('notification_push_messages'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">
                <h4 class="section-title"><i class="fas fa-qrcode"></i> Código para levantamento {!! tip('Para as entregas nos serviços indicados vai ser enviado por e-mail para o destinatário um código para levantamento de mercadoria.') !!}</h4>
                <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                    <tr>
                        <td>
                            {{ Form::label('shipment_notify_pickup_code_services', 'Enviar código de levantamento nos serviços') }}

                            {{ Form::select('shipment_notify_pickup_code_services[]', $services, empty(Setting::get('shipment_notify_pickup_code_services')) ? null : @array_map('intval', Setting::get('shipment_notify_pickup_code_services')), ['class' =>'form-control select2', 'multiple']) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <h4 class="section-title"><i class="fas fa-euro"></i> Faturação</h4>
                <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                    <tr>
                        <td>
                            {{ Form::label('billing_send_balance_auto', 'Enviar contas correntes automático', ['class' => 'control-label']) }}
                            {!! tip('Ative esta opção para o sistema enviar a conta corrente automáticamente aos seus clientes no dia 1 de cada mês.') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('billing_send_balance_auto', 1, Setting::get('billing_send_balance_auto'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('billing_remember_duedate', 'E-mail de aviso vencimento faturas', ['class' => 'control-label']) }}
                            {!! tip('Esta opção envia um e-mail ao cliente a relembrar a data de vencimento das faturas.') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('billing_remember_duedate', 1, Setting::get('billing_remember_duedate'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                    <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                        <tr>
                            <td>
                                {{ Form::label('billing_remember_duedate_days', 'Enviar e-mail de vencimentos ao faltarem...') }}
                                <div class="input-group">
                                    {{ Form::text('billing_remember_duedate_days', Setting::get('billing_remember_duedate_days'), ['class' =>'form-control', 'placeholder' => '10,5,3,2,1']) }}
                                    <div class="input-group-addon">dias</div>
                                </div>
                            </td>
                        </tr>
                    </table><br>
                </table>
                <h4 class="section-title">Notificações Envio</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>{{ Form::label('shipments_disable_email', 'Enviar email ao criar envio', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('shipments_disable_email', 1,  Setting::get('shipments_disable_email'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('shipments_daily_limit_hour', 'Notificar se o envio/recolha não estiver concluido até às', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Todos os envios/recolhas após a hora indicada, o sistema vai enviar notificar o cliente que não foi entregue no dia."></i>
                        </td>
                        <td>{{ Form::select('shipments_limit_hour_notification', ['' => 'Inativo'] + listHours(), Setting::get('shipments_limit_hour_notification'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('time_window_email', '[Destinatários] Enviar email com janela horária', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Ao notificar os destinatários no manifesto de entrega, incluir a janela horária e a data prevista de entrega."></i>
                        </td>
                        <td class="check">{{ Form::checkbox('time_window_email', 1,  Setting::get('time_window_email'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-8">
                <h4 class="section-title">Notificação automática - Alteração de estados de envio</h4>
                <div class="row">
                    <div class="col-sm-6">
                        <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                            <tr>
                                <td>
                                    {{ Form::label('shipment_sender_notify_status', 'Notificar cliente dos estados') }}
                                    <i class="fas fa-info-circle" data-toggle="tooltip" title="O cliente será informado sempre que os seus envios entrem num dos estados selecionados."></i>
                                    @if(hasModule('history_notifications'))
                                        {{ Form::select('shipment_sender_notify_status[]', $status, empty(Setting::get('shipment_sender_notify_status')) ? null : @array_map('intval', Setting::get('shipment_sender_notify_status')), ['class' =>'form-control select2', 'multiple']) }}
                                    @else
                                        {{ Form::select('shipment_sender_notify_status[]', [],  null, ['class' =>'form-control select2', 'disabled']) }}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                            <tr>
                                <td>
                                    {{ Form::label('shipment_recipient_notify_status', 'Notificar destinatário dos estados') }}
                                    <i class="fas fa-info-circle" data-toggle="tooltip" title="O destinatário será informado sempre que os seus envios entrem num dos estados selecionados."></i>
                                    {{ Form::select('shipment_recipient_notify_status[]', $status, empty(Setting::get('shipment_recipient_notify_status')) ? null : @array_map('intval', Setting::get('shipment_recipient_notify_status')), ['class' =>'form-control select2', 'multiple']) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">

            </div>
            <div class="col-sm-8">
                <h4 class="section-title"><i class="fas fa-mobile-alt"></i> Mensagens SMS</h4>
                <ul class="nav nav-tabs" style="border-bottom: 0">
                    <li role="presentation" class="active">
                        <a href="#sms-registred" role="tab" data-toggle="tab">Envio Registado</a>
                    </li>
                    <li role="presentation">
                        <a href="#sms-delivered" role="tab" data-toggle="tab">Entregue</a>
                    </li>
                    <li role="presentation">
                        <a href="#sms-incidence" role="tab" data-toggle="tab">Incidência</a>
                    </li>
                    <li role="presentation">
                        <a href="#sms-tracking" role="tab" data-toggle="tab">Outros Estados</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="sms-registred">
                        <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                            <tr>
                                <td>
                                    {{ Form::label('sms_text_registered_sender', 'Texto para Remetente') }}
                                    {{ Form::textarea('sms_text_registered_sender', Setting::get('sms_text_registered_sender'), ['class' =>'form-control', 'rows' => 4]) }}
                                </td>
                                <td>
                                    {{ Form::label('sms_text_registered_recipient', 'Texto para o Destinatário') }}
                                    {{ Form::textarea('sms_text_registered_recipient', Setting::get('sms_text_registered_recipient'), ['class' =>'form-control', 'rows' => 4]) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="sms-delivered">
                        <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                            <tr>
                                <td>
                                    {{ Form::label('sms_text_delivered_sender', 'Texto para Remetente') }}
                                    {{ Form::textarea('sms_text_delivered_sender', Setting::get('sms_text_delivered_sender'), ['class' =>'form-control', 'rows' => 4]) }}
                                </td>
                                <td>
                                    {{ Form::label('sms_text_delivered_recipient', 'Texto para o Destinatário') }}
                                    {{ Form::textarea('sms_text_delivered_recipient', Setting::get('sms_text_delivered_recipient'), ['class' =>'form-control', 'rows' => 4]) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="sms-incidence">
                        <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                            <tr>
                                <td>
                                    {{ Form::label('sms_text_incidence_sender', 'Texto para Remetente') }}
                                    {{ Form::textarea('sms_text_incidence_sender', Setting::get('sms_text_incidence_sender'), ['class' =>'form-control', 'rows' => 4]) }}
                                </td>
                                <td>
                                    {{ Form::label('sms_text_incidence_recipient', 'Texto para o Destinatário') }}
                                    {{ Form::textarea('sms_text_incidence_recipient', Setting::get('sms_text_incidence_recipient'), ['class' =>'form-control', 'rows' => 4]) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="sms-tracking">
                        <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                            <tr>
                                <td>
                                    {{ Form::label('sms_text_tracking_sender', 'Texto para Remetente') }}
                                    {{ Form::textarea('sms_text_tracking_sender', Setting::get('sms_text_tracking_sender'), ['class' =>'form-control', 'rows' => 4]) }}
                                </td>
                                <td>
                                    {{ Form::label('sms_text_tracking_recipient', 'Texto para o Destinatário') }}
                                    {{ Form::textarea('sms_text_tracking_recipient', Setting::get('sms_text_tracking_recipient'), ['class' =>'form-control', 'rows' => 4]) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <p class="m-l-5">
                    <b>Códigos de texto</b> - Use os códigos no texto para os substituir pelo respetivo valor.
                </p>
                <div class="col-sm-6">
                    <span class="label label-default">:trk</span> - Substitui pelo código de envio<br>
                    <span class="label label-default">:date</span> - Substitui pela data do último estado<br/>
                    <span class="label label-default">:ddate</span> - Substitui pela data prevista entrega<br/>
                    <span class="label label-default">:dhour</span> - Substitui pela hora prevista entrega<br/>
                    <span class="label label-default">:dshour</span> - Substitui pela hora prevista entrega (Inicio)<br/>
                    <span class="label label-default">:dehour</span> - Substitui pela hora prevista entrega (Fim)<br/>
                    <span class="label label-default">:sender</span> - Substitui pelo nome remetente
                </div>
                <div class="col-sm-6">
                    <span class="label label-default">:recipient</span> - Substitui pelo nome destinatario<br/>
                    <span class="label label-default">:status</span> - Substitui pelo estado do envio<br/>
                    <span class="label label-default">:receiver</span> - Substitui pelo nome do receptor<br/>
                    <span class="label label-default">:incidence</span> - Substitui pelo motivo incidência<br/>
                    <span class="label label-default">:price</span> - Valor a cobrar (só apresenta caso exista)<br/>
                    <span class="label label-default">:ptrk</span> - Substitui pelo TRK do Fornecedor
                    <span class="label label-default">:url</span> - Substitui pelo URL de seguimento
                </div>

                <div class="col-sm-12">
                    <h4 class="section-title"><i class="fas fa-envelope"></i> Mensagens E-mail</h4>
                    {{ Form::label('email_signature', 'Assinatura e-mail') }}
                    {{ Form::textarea('email_signature', Setting::get('email_signature'), ['class' =>'form-control', 'rows' => 4, 'placeholder' => 'Automático pelo sistema']) }}
                    <p class="m-l-5">
                        <b>Códigos de texto</b> - Use os códigos no texto para os substituir pelo respetivo valor.<br/>
                        <span class="label label-default">:name</span> - Substitui pelo nome do utilizador<br/>
                        <span class="label label-default">:email</span> - Substitui pelo email do utilizador<br/>
                        <span class="label label-default">:phone</span> - Substitui pelo telefone do utilizador<br/>
                    </p>
                </div>

            </div>

            <div class="col-sm-12">
                <hr/>
                {{ Form::submit('Gravar', array('class' => 'btn btn-primary' ))}}
            </div>
        </div>
    </div>
</div>

