<?php

$fields = [
    'weight'      => 'Peso',
    'volumes'     => 'Volumes',
    'kms'         => 'Kms',
    'reference'   => 'Referência',
    'reference2'  => 'Referência 2',
    'reference3'  => 'Referência 3',
    'obs'         => 'Observações',
    'total_price' => 'Preço',
    'peage_price' => 'Custo Portagem'
];
?>
<div class="box no-border">
    <div class="box-body p-t-0">
        <div class="row">
            <div class="col-sm-4">
                <h4 class="section-title">Definições Gerais</h4>
                <table class="table table-condensed m-b-0">
                    <tr>
                        <td>
                            <label class="control-label">Ativar o modo básico (Apenas App Web) <i class="fas fa-info-circle" data-toggle="tooltip" title="O modo básico torna o aplicação destinada apenas para entregas removendo todas as opções extra."></i></label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_basic_mode', 1, Setting::get('mobile_app_basic_mode'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">Listar nome remetente e expedidor {!! tip('Na listagem de serviços apresenta os dados do remetente e do expedidor.') !!}</label>
                        </td>
                        <td class="check">{{ Form::checkbox('app_list_show_both', 1, Setting::get('app_list_show_both'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    {{--<tr>
                        <td>
                            <label class="control-label">Listar serviços pelo local de recolha <i class="fas fa-info-circle" data-toggle="tooltip" title="Ative esta opção caso pretenda que a listagem de serviços apresente o local de recolha em vez do local de entrega."></i></label>
                        </td>
                        <td class="check">{{ Form::checkbox('app_list_by_sender', 1, Setting::get('app_list_by_sender'), ['class' => 'ios'] ) }}</td>
                    </tr>--}}
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_show_scheduled', 'Mostrar serviços datas futuras', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_show_scheduled', 1, Setting::get('mobile_app_show_scheduled'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed">
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_location_refresh_secs', 'Intervalo de tempo para rastreio GPS', ['class' => 'control-label']) }}
                            {!! tip('Intervalo de tempo em que o sistema regista a posição do motorista.') !!}
                        </td>
                        <td class="w-50px">{{ Form::select('mobile_app_location_refresh_secs', ['5'=> '5 seg.', '10' => '10 seg.', '15' => '15 seg.', '20' => '20 seg.', '30' => '30 seg.', '60' => '60 seg.', '999999999999' => 'Desligado'], Setting::get('mobile_app_location_refresh_secs'), ['class' => 'select2', hasModule('maps') ? : 'disabled'] ) }}</td>
                    </tr>
                </table>

                <h4 class="section-title">Menus Disponiveis</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_menu_stats', 'Mostrar menu "Estatísticas"', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_menu_stats', 1, Setting::get('mobile_app_menu_stats'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_menu_tasks', 'Mostrar menu "Recolhas Diárias"', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_menu_tasks', 1, Setting::get('mobile_app_menu_tasks'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_menu_customers', 'Mostrar menu "Clientes"', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_menu_customers', 1, Setting::get('mobile_app_menu_customers'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_menu_operators', 'Mostrar menu "Colaboradores"', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_menu_operators', 1, Setting::get('mobile_app_menu_operators'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_menu_traceability', 'Mostrar menu "Rastreabilidade"', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_menu_traceability', 1, Setting::get('mobile_app_menu_traceability'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_menu_traceability_weight', 'Mostrar menu "Controlo Pesos"', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_menu_traceability_weight', 1, Setting::get('mobile_app_menu_traceability_weight'), ['class' => 'ios'] ) }}</td>
                    </tr>

                    <tr>
                        <td>
                            {{ Form::label('mobile_app_menu_timer', 'Mostrar menu "Cronómetro"', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_menu_timer', 1, Setting::get('mobile_app_menu_timer'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    {{--<tr>
                        <td>
                            {{ Form::label('mobile_app_menu_balance', 'Mostrar menu "Contas Correntes"', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_menu_balance', 1, Setting::get('mobile_app_menu_balance'), ['class' => 'ios'] ) }}</td>
                    </tr>--}}

                    @if(hasModule('fleet'))
                        <tr>
                            <td>
                                {{ Form::label('mobile_app_menu_fuel', 'Mostrar menu "Abastecimentos"', ['class' => 'control-label']) }}
                            </td>
                            <td class="check">{{ Form::checkbox('mobile_app_menu_fuel', 1, Setting::get('mobile_app_menu_fuel'), ['class' => 'ios'] ) }}</td>
                        </tr>
                        <tr>
                            <td>
                                {{ Form::label('mobile_app_menu_drive', 'Mostrar menu "Registo Condução"', ['class' => 'control-label']) }}
                            </td>
                            <td class="check">{{ Form::checkbox('mobile_app_menu_drive', 1, Setting::get('mobile_app_menu_drive'), ['class' => 'ios'] ) }}</td>
                        </tr>
                        <tr>
                            <td>
                                {{ Form::label('mobile_app_register_user_logs', 'Mostrar menu "Registo de Horários"', ['class' => 'control-label']) }}
                            </td>
                            <td class="check">{{ Form::checkbox('mobile_app_register_user_logs', 1, Setting::get('mobile_app_register_user_logs'), ['class' => 'ios'] ) }}</td>
                        </tr>
                        <tr>
                            <td>
                                {{ Form::label('mobile_app_menu_checklists', 'Mostrar menu "Listas Controlo"', ['class' => 'control-label']) }}
                            </td>
                            <td class="check">{{ Form::checkbox('mobile_app_menu_checklists', 1, Setting::get('mobile_app_menu_checklists'), ['class' => 'ios'] ) }}</td>
                        </tr>
                    @endif
                    @if(hasModule('equipments'))
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_menu_equipment', 'Mostrar menu "Equipamentos"', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_menu_equipment', 1, Setting::get('mobile_app_menu_equipment'), ['class' => 'ios'] ) }}</td>
                    </tr>
                @endif
                </table>
                <h4 class="section-title">Janelas Horárias</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>
                            <small>Selecione um horário por linha</small><br/>
                            {{ Form::textarea('mobile_app_horaries_list', Setting::get('mobile_app_horaries_list'), ['rows' => '3', 'placeholder' => 'Horários Automáticos', 'class' => 'form-control'] ) }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">
                <h4 class="section-title">Visualização e Gestão de Serviços</h4>
                <table class="table table-condensed m-b-0" style="border-bottom: 1px solid #eee;">
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_autotasks', 'Permitir marcar como "recolhido"', ['class' => 'control-label']) }}
                            {!! tip('No menu de serviço acrescenta o icone para confirmar que o serviço/envio foi recolhido') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_opt_mark_as_collected', 1, Setting::get('mobile_app_opt_mark_as_collected'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">Mostrar dados completos do remetente</label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_details_full_sender', 1, Setting::get('mobile_app_details_full_sender'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">
                                Permitir transfega de cargas
                                {!! tip('Permite ao motorista transferir um serviço ou envio para a aplicação de outro motorista ou de volta para o escritório.') !!}
                            </label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_transfer_shipments', 1, Setting::get('mobile_app_transfer_shipments'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">
                                Permitir agendamento horário
                                {!! tip('Permite ao motorista estabelecer o horário de entrega previsto da mercadoria. Será dispultado um SMS ao destinatário com a hora de entrega.') !!}
                            </label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_opt_schedule_horary', 1, Setting::get('mobile_app_opt_schedule_horary'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">
                                Ocultar aba motorista em "recolhas"
                                {!! tip('Possível visualizar o número de recolhas feitas por dia do motorista') !!}
                            </label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_hide_drivers_tab', 1, Setting::get('mobile_app_hide_drivers_tab'), ['class' => 'ios'] ) }}</td>
                    </tr>
                
                    <tr>
                        <td>
                            <label class="control-label">
                                Permitir visualizar "Observações Internas"
                            </label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_show_internal_obs', 1, Setting::get('mobile_app_show_internal_obs'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_autotasks', 'Permitir editar informação serviço', ['class' => 'control-label']) }}
                            {!! tip('A edição do serviço apenas é possível nos estadso "Entregue" ou "Incidência". Apenas é possível editar as referências do envio.') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_opt_edit_shipment', 1, Setting::get('mobile_app_opt_edit_shipment'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed">
                    <tr>
                        <td style="text-align: left;  padding-top:0">
                            <label class="control-label" style="text-align: left">Campos possíveis editar</label>
                            {{ Form::select('mobile_app_edit_fields[]', $fields, Setting::get('mobile_app_opt_edit_shipment') ? Setting::get('mobile_app_edit_fields') : null, ['class' =>'form-control select2', Setting::get('mobile_app_opt_edit_shipment') ? '' : '', 'multiple']) }}
                        </td>
                    </tr>
                </table>
                <h4 class="section-title">Gravação de Entregas</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>
                            <label class="control-label">Obrigatório preencher receptor <i class="fas fa-info-circle" data-toggle="tooltip" title="Obriga o motorista a registar o nome da pessoa que recebe a encomenda."></i></label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_receiver_required', 1, Setting::get('mobile_app_receiver_required'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_autotasks', 'Permitir gerar serviço de retorno', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_autoreturn', 1, Setting::get('mobile_app_autoreturn'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">Mostrar campo Tempo Entrega</label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_show_wainting_time', 1, Setting::get('mobile_app_show_wainting_time'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_show_vat', 'Mostrar campo NIF ou CC', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_show_vat', 1, Setting::get('mobile_app_show_vat'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">Obrigatório preencher NIF ou CC {!! tip('Obriga o preenchimento do campo NIF ou CC antes da conclusão do serviço.') !!}</label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_vat_required', 1, Setting::get('mobile_app_vat_required'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_receiver_email_show', 'Mostrar campo e-mail receptor', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_receiver_email_show', 1, Setting::get('mobile_app_receiver_email_show'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">Obrigatório preencher e-mail recetor {!! tip('Obriga o preenchimento do campo E-mail antes da conclusão do serviço.') !!}</label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_receiver_email_required', 1, Setting::get('mobile_app_receiver_email_required'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">Obrigatório tirar Fotografia</label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_photo_required', 1, Setting::get('mobile_app_photo_required'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">Permitir Fotografias Múltipas</label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_photo_multiple', 1, Setting::get('mobile_app_photo_multiple'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    {{-- <tr>
                        <td colspan="2">
                            <p>
                                Pode obrigar o preenchimento de uma data ou fotografia diretamente nas configurações dos estados de serviço.
                                <a href="{{ route('admin.tracking.status.index') }}" target="_blank">Gerir Estados</a>
                            </p>
                        </td>
                    </tr> --}}
                </table>

                <h4 class="section-title">Rastreabilidade</h4>
                <table class="table table-condensed m-b-2" style="border-bottom: 1px solid #eee;">
                    <tr>
                        <td>
                            <label class="control-label">Mudar para uma listagem de envios associados ao motorista</label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_traceability_list', 1, Setting::get('mobile_app_traceability_list'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <table >
                    <tr>
                        <td>
                            <label class="control-label" style="text-align: left" >Definir estado(s) para mostrar na listagem</label>
                            {{ Form::select('mobile_app_traceability_state[]', $status, @array_map('intval', Setting::get('mobile_app_traceability_state')), ['class' =>'form-control select2', 'multiple']) }}
                        </td>
                    </tr>
                    </table>
                </table>
                
            </div>
            <div class="col-sm-4">
                <h4 class="section-title">Alertas e Notificações</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_notifications', 'Enviar altertas para os telemóveis', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_notifications', 1, Setting::get('mobile_app_notifications'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">
                                Notificar sempre todos Motoristas
                            </label>
                            {!! tip('Quando um cliente faz um novo envio, são sempre notificados todos os motoristas.') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_notify_all_operators', 1, Setting::get('mobile_app_notify_all_operators'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('shipment_notify_operator', 'Notificar ao criar serviço na área gestão', ['class' => 'control-label']) }}
                            {!! tip('Notifica sempre os motoristas quando um envio for criado por algum funcionário na área de administração') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('shipment_notify_operator', 1, Setting::get('shipment_notify_operator'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <h4 class="section-title">Gestão de Estados</h4>
                <table class="table table-condensed m-0" style="border-bottom: 1px solid #f4f4f4">
                    <tr>
                        <td>
                            <label class="control-label">
                                Permitir a mudança de estado para "Entrada em Armazém"
                            </label>
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_warehouse', 1, Setting::get('mobile_app_warehouse'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">Estado "Visualizado Motorista" ao abrir serviço</label>
                            {!! tip('Quando o estafeta abre o serviço o estado é alterado para lido pelo motorista') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_enable_read_service', 1, Setting::get('mobile_app_enable_read_service'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed">
                    <tr>
                        <td>
                            <label class="control-label">Estado "Recolhido" {!! tip('Estado ao marcar um envio como recolhido.') !!}</label>
                        </td>
                        <td class="w-80px">{{ Form::select('mobile_app_status_pickuped', ['' => 'Recolhido (por defeito)'] + $status, Setting::get('mobile_app_status_pickuped'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label" style="text-align: left">Estado após Recolha {!! tip('Estado após marcar o serviço como recolhido.') !!}</label>
                        </td>
                        <td class="w-80px">{{ Form::select('mobile_app_status_after_pickup', ['' => 'Nenhum'] + $status, Setting::get('mobile_app_status_after_pickup'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label" style="text-align: left">Estado após Leitura {!! tip('Estado após leitura do serviço pelo motorista') !!}</label>
                        </td>
                        <td class="w-80px">{{ Form::select('mobile_app_status_after_read_operator', ['' => 'Lido Motorista (por defeito)'] + $status, Setting::get('mobile_app_status_after_read_operator'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left">
                            <label class="control-label" style="text-align: left">Estados "Pend. Motorista"</label> {!! tip('Estados considerados como "Pendente do Motorista". Ao abrir um serviço nestes estados, o serviço é alterado para o estado "LIDO PELO MOTORISTA"') !!}
                        </td>
                        <td class="w-80px">{{ Form::select('mobile_app_status_pending_operator[]', $status, @array_map('intval', Setting::get('mobile_app_status_pending_operator')), ['class' =>'form-control select2', 'multiple']) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label" style="text-align: left">Estados menu "Entregas"</label> {!! tip('Estados a apresentar no menu "Entregas"') !!}
                        </td>
                        <td class="w-80px">{{ Form::select('mobile_app_status_delivery[]', $status, @array_map('intval', Setting::get('mobile_app_status_delivery')), ['class' =>'form-control select2', 'multiple']) }}</td>
                    </tr>
                </table>

                <h4 class="section-title">Anexos e Downloads</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_download_guide', 'Permitir Download Guia Transporte', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_download_guide', 1, Setting::get('mobile_app_download_guide'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('mobile_app_download_cmr', 'Permitir Download e-CMR', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('mobile_app_download_cmr', 1, Setting::get('mobile_app_download_cmr'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-12">
                {{ Form::submit('Gravar', array('class' => 'btn btn-primary' ))}}
            </div>
        </div>
    </div>
</div>

