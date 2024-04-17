<div class="box no-border">
    <div class="box-body p-t-0">
        <div class="row">
            <div class="col-sm-6">
                <h4 class="section-title">Configuração do e-mail</h4>
                <table class="table table-condensed">
                    <tr>
                        <td class="w-230px">{{ Form::label('budgets_mail', 'E-mail para recepção orçamentos', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('budgets_mail', Setting::get('budgets_mail'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('budgets_mail_password', 'Palavra-passe do e-mail', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('budgets_mail_password', Setting::get('budgets_mail_password'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr style="display: non">
                        <td>{{ Form::label('budgets_mail_host', 'Servidor IMAP', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('budgets_mail_host', Setting::get('budgets_mail_host') ? Setting::get('budgets_mail_host') : 'cp127.webserver.pt', ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr style="display: non">
                        <td>{{ Form::label('budgets_mail_port', 'Porta IMAP', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('budgets_mail_port', '143', ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr style="display: non">
                        <td>{{ Form::label('budgets_mail_encryption', 'Encriptação', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('budgets_mail_encryption', 'tls', ['class' =>'form-control']) }}</td>
                    </tr>
                </table>
                <h4 class="section-title">Resposta pré-definida: Pedidos de Cotação</h4>
                <table class="table table-condensed no-border m-b-0">
                    <td>
                        {{ Form::textarea('budgets_mail_default_answer', Setting::get('budgets_mail_default_answer'), ['class' =>'form-control budget-default-answer', 'id' => 'budget-default-answer', 'rows' => 20]) }}
                    </td>
                </table>
            </div>
            <div class="col-sm-6">
                <h4 class="section-title">Resposta Automática</h4>
                <table class="table table-condensed m-b-0">
                    <tr>
                        <td class="w-70"><span class="m-t-5">Ao receber novo e-mail, responder automáticamente</span></td>
                        <td class="check">{{ Form::checkbox('budgets_mail_autoresponse_active', 1, Setting::get('budgets_mail_autoresponse_active'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                    <tr>
                        <td>{{ Form::label('budgets_mail_notification', 'Quando receber novo pedido, enviar aviso para', ['class' => 'control-label']) }}</td>
                        <td class="w-250px">{{ Form::text('budgets_mail_notification', Setting::get('budgets_mail_notification'), ['class' =>'form-control']) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed no-border m-b-0">
                    <td colspan="2">
                        {{ Form::hidden('budgets_mail_autoresponse_html', 1) }}
                        {{ Form::textarea('budgets_mail_autoresponse', Setting::get('budgets_mail_autoresponse'), ['class' =>'form-control budget-auto-response', 'id' => 'budget-auto-response']) }}
                        <p>
                            <b>Códigos de texto</b><br/>
                            Use os códigos seguintes no texto para que estes sejam substituidos pelas variáveis indicadas.
                            <BR/>
                            <span class="label label-default">:nmCliente</span> - Substitui pelo nome do cliente que solicitou<br/>
                            <span class="label label-default">:numOrcamento</span> - Substitui pelo número de orçamento<br/>
                            <span class="label label-default">:dataHora</span> - Substitui pela data e hora em que o orçamento foi recebido
                        </p>
                    </td>
                </table>
            </div>
        </div>
        <div class="row">
            @if(hasModule('budgets_courier'))
            <div class="col-sm-6">
                <h4 class="section-title">Resposta pré-definida: Cotação Carga Geral</h4>
                <ul class="nav nav-tabs">
                    <li role="presentation" class="active">
                        <a href="#courier-pt" role="tab" data-toggle="tab">Português</a>
                    </li>
                    <li role="presentation">
                        <a href="#courier-en" role="tab" data-toggle="tab">English</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="courier-pt">
                        <table class="table table-condensed no-border m-b-0">
                            <td style="padding: 0; margin-top: -1px">
                                {{ Form::textarea('budgets_courier_mail_default_answer', Setting::get('budgets_courier_mail_default_answer'), ['class' =>'form-control budget-geral-answer', 'id' => 'budget-geral-answer', 'style' => 'border-top: none']) }}
                            </td>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="courier-en">
                        <table class="table table-condensed no-border m-b-0">
                            <td style="padding: 0; margin-top: -1px">
                                {{ Form::textarea('budgets_courier_mail_default_answer_en', Setting::get('budgets_courier_mail_default_answer_en'), ['class' =>'form-control budget-geral-answer', 'id' => 'budget-geral-answer-en', 'style' => 'border-top: none']) }}
                            </td>
                        </table>
                    </div>
                </div>
                <table class="table table-condensed no-border m-b-0">
                    <tr>
                        <td class="w-200px">{{ Form::label('budgets_courier_mail_cc', 'Enviar cópia das respostas para', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('budgets_courier_mail_cc', Setting::get('budgets_courier_mail_cc'), ['class' =>'form-control']) }}</td>
                    </tr>
                </table>
            </div>
            @endif
            @if(hasModule('budgets_animals'))
                <div class="col-sm-6">
                    <h4 class="section-title">Resposta pré-definida: Cotação Animais</h4>
                    <ul class="nav nav-tabs">
                        <li role="presentation" class="active">
                            <a href="#animals-pt" role="tab" data-toggle="tab">Português</a>
                        </li>
                        <li role="presentation">
                            <a href="#animals-en" role="tab" data-toggle="tab">English</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="animals-pt">
                            <table class="table table-condensed no-border m-b-0">
                                <td style="padding: 0; margin-top: -1px">
                                    {{ Form::textarea('budgets_animals_mail_default_answer', Setting::get('budgets_animals_mail_default_answer'), ['class' =>'form-control budget-geral-answer', 'id' => 'budget-animals-answer', 'style' => 'border-top: none']) }}
                                </td>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="animals-en">
                            <table class="table table-condensed no-border m-b-0">
                                <td style="padding: 0; margin-top: -1px">
                                    {{ Form::textarea('budgets_animals_mail_default_answer_en', Setting::get('budgets_animals_mail_default_answer_en'), ['class' =>'form-control budget-geral-answer', 'id' => 'budget-animals-answer-en', 'style' => 'border-top: none']) }}
                                </td>
                            </table>
                        </div>
                    </div>

                    <table class="table table-condensed no-border m-b-0">
                        <tr>
                            <td class="w-200px">{{ Form::label('budgets_animals_mail_cc', 'Enviar cópia das respostas para', ['class' => 'control-label']) }}</td>
                            <td>{{ Form::text('budgets_animals_mail_cc', Setting::get('budgets_animals_mail_cc'), ['class' =>'form-control']) }}</td>
                        </tr>
                    </table>
                </div>
            @endif
            <div class="col-sm-6">
                <h4 class="section-title">Lembrete Automático</h4>
                <table class="table table-condensed m-b-0">
                    <tr>
                        <td class="w-70"><span class="m-t-5">Emitir lembretes automáticos {!! tip('Envia um lembrete ao cliente a solicitar resposta ao pedido de orçamento.') !!}</span></td>
                        <td class="check">{{ Form::checkbox('budgets_mail_reminder_active', 1, Setting::get('budgets_mail_reminder_active'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                    <tr>
                        <td>{{ Form::label('budgets_mail_reminder_days', 'Enviar quando faltarem N dias para fim validade', ['class' => 'control-label']) }}
                        {!! tip('Indique os dias em que deve ser enviada notificação ao cliente. Por ex: 10,5 para enviar 10 dias e 5 dias antes do fim da proposta.') !!}</td>
                        <td class="w-250px">{{ Form::text('budgets_mail_reminder_days', Setting::get('budgets_mail_reminder_days'), ['class' =>'form-control']) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed no-border m-b-0">
                    <td colspan="2">
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active">
                                <a href="#reminder-pt" role="tab" data-toggle="tab">Português</a>
                            </li>
                            <li role="presentation">
                                <a href="#reminder-en" role="tab" data-toggle="tab">English</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="reminder-pt">
                                <table class="table table-condensed no-border m-b-0">
                                    <td style="padding: 0; margin-top: -1px">
                                        {{ Form::textarea('budgets_mail_reminder_html', Setting::get('budgets_mail_reminder_html'), ['class' =>'form-control budget-reminder', 'id' => 'budget-reminder', 'style' => 'border-top: none']) }}
                                    </td>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="reminder-en">
                                <table class="table table-condensed no-border m-b-0">
                                    <td style="padding: 0; margin-top: -1px">
                                        {{ Form::textarea('budgets_mail_reminder_html_en', Setting::get('budgets_mail_reminder_html_en'), ['class' =>'form-control budget-reminder', 'id' => 'budget-reminder-en', 'style' => 'border-top: none']) }}
                                    </td>
                                </table>
                            </div>
                        </div>
                    </td>
                </table>
            </div>
                <div class="col-sm-6">
                    <h4 class="section-title">Cancelamento Automático</h4>
                    <table class="table table-condensed m-b-0">
                        <tr>
                            <td class="w-70"><span class="m-t-5">Cancelar automáticamente se ultrapassar validade proposta</span></td>
                            <td class="check">{{ Form::checkbox('budgets_mail_autocancel_active', 1, Setting::get('budgets_mail_autocancel_active'), ['class' => 'ios'] ) }}</td>
                        </tr>
                    </table>
                    <table class="table table-condensed no-border m-b-0">
                        <td colspan="2">
                            <ul class="nav nav-tabs">
                                <li role="presentation" class="active">
                                    <a href="#cancel-pt" role="tab" data-toggle="tab">Português</a>
                                </li>
                                <li role="presentation">
                                    <a href="#cancel-en" role="tab" data-toggle="tab">English</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="cancel-pt">
                                    <table class="table table-condensed no-border m-b-0">
                                        <td style="padding: 0; margin-top: -1px">
                                            {{ Form::textarea('budgets_mail_autocancel_html', Setting::get('budgets_mail_autocancel_html'), ['class' =>'form-control budget-cancel', 'id' => 'budget-cancel', 'style' => 'border-top: none']) }}
                                        </td>
                                    </table>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="cancel-en">
                                    <table class="table table-condensed no-border m-b-0">
                                        <td style="padding: 0; margin-top: -1px">
                                            {{ Form::textarea('budgets_mail_autocancel_html_en', Setting::get('budgets_mail_autocancel_html_en'), ['class' =>'form-control budget-cancel', 'id' => 'budget-cancel-en', 'style' => 'border-top: none']) }}
                                        </td>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </table>
                </div>
            <div class="col-xs-12">
                <h4 class="section-title">Assinatura do E-mail</h4>
                <table class="table table-condensed no-border m-b-0">
                    <td colspan="2">
                        {{ Form::hidden('budgets_mail_signature_html', 1) }}
                        {{ Form::textarea('budgets_mail_signature', Setting::get('budgets_mail_signature'), ['class' =>'form-control budget-signature', 'id' => 'budget-signature']) }}
                    </td>
                </table>
            </div>
            <div class="col-sm-12">
                <hr/>
                {{ Form::submit('Gravar', array('class' => 'btn btn-primary' ))}}
            </div>
        </div>
    </div>
</div>

