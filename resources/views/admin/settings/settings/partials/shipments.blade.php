<div class="box no-border">
    <div class="box-body p-t-0">
        <div class="row">
        <div class="col-sm-4">
            <h4 class="section-title">Campos Adicionais</h4>
            <table class="table table-condensed m-b-0">
                {{--<tr>
                    <td>
                        {{ Form::label('shipments_reference2', 'Mostrar campo 2ª Referência', ['class' => 'control-label']) }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Ativa um 2º campo de referência na janela de envio."></i>
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_reference2', 1, Setting::get('shipments_reference2'), ['class' => 'ios'] ) }}</td>
                </tr>--}}
                @if(app_mode_cargo())
                <tr>
                    <td>
                        {{ Form::label('app_mode_adr', 'Incluir opções para gestão ADR', ['class' => 'control-label']) }}
                    </td>
                    <td class="check">{{ Form::checkbox('app_mode_adr', 1, Setting::get('app_mode_adr'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('app_mode_containers', 'Incluir opções para gestão contentores', ['class' => 'control-label']) }}
                    </td>
                    <td class="check">{{ Form::checkbox('app_mode_containers', 1, Setting::get('app_mode_containers'), ['class' => 'ios'] ) }}</td>
                </tr>
                @endif
                <tr>
                    <td>
                        {{ Form::label('shipments_reference3', 'Mostrar campo 3ª Referência', ['class' => 'control-label']) }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Ativa um 3º campo de referência na janela de envio."></i>
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_reference3', 1, Setting::get('shipments_reference3'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipments_obs_sender_recipient', 'Mostrar campo Obs de Recolha e Entrega', ['class' => 'control-label']) }}
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_obs_sender_recipient', 1, Setting::get('shipments_obs_sender_recipient'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipments_requester_name', 'Mostrar campo "Solicitado por"', ['class' => 'control-label']) }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Ativa um novo campo na janela de envio que permite introduzir quem foi a pessoa que solicitou o serviço."></i>
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_requester_name', 1, Setting::get('shipments_requester_name'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipments_vehicles_field_input', 'Inserir livremente matrículas das viaturas', ['class' => 'control-label']) }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Permite colocar livremente a matrícula das viaturas ignorando a necessidade de registo previo das mesmas no sistema"></i>
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_vehicles_field_input', 1, Setting::get('shipments_vehicles_field_input'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipments_show_charge_price', 'Mostrar Reembolso/Cobrança', ['class' => 'control-label']) }}
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_show_charge_price', 1, Setting::get('shipments_show_charge_price'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipments_show_delivery_map_id', 'Mostrar campo Mapa Viagem', ['class' => 'control-label']) }}
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_show_delivery_map_id', 1, Setting::get('shipments_show_delivery_map_id'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipments_show_charge_price', 'Mostrar campo Metros Estrado (LDM)', ['class' => 'control-label']) }}
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_show_ldm', 1, Setting::get('shipments_show_ldm'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('app_rpack', 'Mostrar retorno encomenda (back)', ['class' => 'control-label']) }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Ativa a possibilidade de gerar retorno de encomendas. Esta opção deve estar inativa caso seja utilizada a opção de retorno como encargo adicional."></i>
                    </td>
                    <td class="check">{{ Form::checkbox('app_rpack', 1, Setting::get('app_rpack'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipments_show_assembly', 'Mostrar opção "Montagem"', ['class' => 'control-label']) }}</td>
                    <td class="check">{{ Form::checkbox('shipments_show_assembly', 1, Setting::get('shipments_show_assembly'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipments_show_price_per_ton', 'Mostrar campo "Preço/Ton"', ['class' => 'control-label']) }}</td>
                    <td class="check">{{ Form::checkbox('shipments_show_price_per_ton', 1, Setting::get('shipments_show_price_per_ton'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipments_sender_fields_empty', 'Não preencher automático dados remetente', ['class' => 'control-label']) }}
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_sender_fields_empty', 1, Setting::get('shipments_sender_fields_empty'), ['class' => 'ios'] ) }}</td>
                </tr>
            </table>
            <table class="table table-condensed">
                <tr>
                    <td style="width: 175px">
                        {{ Form::label('shipments_reference2_name', 'Designação do Campo 2ª Ref.', ['class' => 'control-label']) }}
                    </td>
                    <td>{{ Form::text('shipments_reference2_name', Setting::get('shipments_reference2_name'), ['class' =>'form-control']) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipments_reference3_name', 'Designação do Campo 3ª Ref.', ['class' => 'control-label']) }}
                    </td>
                    <td>{{ Form::text('shipments_reference3_name', Setting::get('shipments_reference3_name'), ['class' =>'form-control']) }}</td>
                </tr>
            </table>
            <h4 class="section-title">Webservices</h4>
            <table class="table table-condensed">
                <tr>
                    <td>{{ Form::label('webservices_auto_submit', 'Submeter automático ao gravar', ['class' => 'control-label']) }}</td>
                    <td class="check">{{ Form::checkbox('webservices_auto_submit', 1, Setting::get('webservices_auto_submit'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('hidden_recipient_on_labels', 'Ocultar todos dados remetente nas etiquetas', ['class' => 'control-label']) }}</td>
                    <td class="check">{{ Form::checkbox('hidden_recipient_on_labels', 1, Setting::get('hidden_recipient_on_labels'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('hidden_recipient_addr_on_labels', 'Ocultar só morada remetente nas etiquetas', ['class' => 'control-label']) }}</td>
                    <td class="check">{{ Form::checkbox('hidden_recipient_addr_on_labels', 1, Setting::get('hidden_recipient_addr_on_labels'), ['class' => 'ios'] ) }}</td>
                </tr>
            </table>
            <h4 class="section-title">Parâmetros Estafetagem</h4>
            <table class="table table-condensed m-b-0">
                <tr>
                    <td class="w-65">{{ Form::label('shipments_wainting_time_fractions', 'Tempo Espera - Cobrar a cada', ['class' => 'control-label']) }}</td>
                    <td>
                        <div class="input-group">
                            {{ Form::select('shipments_wainting_time_fractions', ['' => ''] + listNumeric(1, 1),Setting::get('shipments_wainting_time_fractions'), ['class' =>'form-control select2']) }}
                            <span class="input-group-addon">Min</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="w-65">{{ Form::label('shipments_wainting_min_time', 'Tempo Espera - Cobrar a partir de', ['class' => 'control-label']) }}</td>
                    <td>
                        <div class="input-group">
                            {{ Form::text('shipments_wainting_min_time', Setting::get('shipments_wainting_min_time'), ['class' =>'form-control']) }}
                            <span class="input-group-addon">min</span>
                        </div>
                    </td>
                </tr>
            </table>
            <table class="table table-condensed m-b-0" style="border-top: 1px solid #eee">
                <tr>
                    <td>
                        {{ Form::label('shipments_km_calc_auto', 'Calcular KM Automático', ['class' => 'control-label']) }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Quando ativo calcula automáticamente a distancia baseando-se na Google. Caso inativo, usa as tabelas de KM definidas em Configurações > Códigos Postais"></i>
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_km_calc_auto', 1, Setting::get('shipments_km_calc_auto'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipments_km_return_back', 'Calcular KM Ida+Volta', ['class' => 'control-label']) }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Quando ativo, calcula os KM de ida + volta. Caso desativo considera só a distância Origem -> Destino."></i>
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_km_return_back', 1, Setting::get('shipments_km_return_back'), ['class' => 'ios'] ) }}</td>
                </tr>
            </table>
            @if(hasModule('gateway_gps'))
            <h4 class="section-title">Ligação Gestão Frota</h4>
            <table class="table table-condensed">
                <tr>
                    <td style="width: 75px">
                        {{ Form::label('gps_gateway', 'Serviço GPS', ['class' => 'control-label']) }}
                    </td>
                    <td>{{ Form::select('gps_gateway', [''=>''] + trans('admin/global.gps-gateways'), Setting::get('gps_gateway'), ['class' =>'form-control select2']) }}</td>
                </tr>
                <tr>
                    <td style="width: 75px">
                        {{ Form::label('gps_gateway_apikey', 'API Key', ['class' => 'control-label']) }}
                    </td>
                    <td>{{ Form::text('gps_gateway_apikey', Setting::get('gps_gateway_apikey'), ['class' =>'form-control']) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('gps_gateway_username', 'Utilizador', ['class' => 'control-label']) }}
                    </td>
                    <td>{{ Form::text('gps_gateway_username', Setting::get('gps_gateway_username'), ['class' =>'form-control']) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('gps_gateway_subusername', 'Sub-utilizador', ['class' => 'control-label']) }}
                    </td>
                    <td>{{ Form::text('gps_gateway_subusername', Setting::get('gps_gateway_subusername'), ['class' =>'form-control']) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('gps_gateway_password', 'Password', ['class' => 'control-label']) }}
                    </td>
                    <td>{{ Form::text('gps_gateway_password', Setting::get('gps_gateway_password'), ['class' =>'form-control']) }}</td>
                </tr>
            </table>
            @endif
        </div>
        <div class="col-sm-4">
            <h4 class="section-title">Listagem de Envios</h4>
            <table class="table table-condensed m-b-0">
                <tr>
                    <td>{{ Form::label('shipment_list_show_provider_trk', 'Ver tracking fornecedor', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_list_show_provider_trk', 1, Setting::get('shipment_list_show_provider_trk'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipment_list_show_hour', 'Mostrar Hora do Serviço', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_list_show_hour', 1, Setting::get('shipment_list_show_hour'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipments_hide_final_status', 'Ocultar envios entregues ou finalizados', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipments_hide_final_status', 1, Setting::get('shipments_hide_final_status'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipments_show_country_flag', 'Mostrar icon bandeira país', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipments_show_country_flag', 1, Setting::get('shipments_show_country_flag'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipments_hide_scheduled', 'Ocultar envios para datas futuras', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipments_hide_scheduled', 1, Setting::get('shipments_hide_scheduled'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipment_sum_expenses', 'Somar encargos + valor envio', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_sum_expenses', 1, Setting::get('shipment_sum_expenses'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipment_list_show_address', 'Mostrar moradas completas', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_list_show_address', 1, Setting::get('shipment_list_show_address'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipment_list_detail_master', 'Mostrar envios agrupados individualmente', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_list_detail_master', 1, Setting::get('shipment_list_detail_master'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipment_list_show_reference', 'Mostrar coluna dedicada - Referência', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_list_show_reference', 1, Setting::get('shipment_list_show_reference'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipment_list_show_delivery_date', 'Mostrar coluna - Datas Detalhadas', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_list_show_delivery_date', 1, Setting::get('shipment_list_show_delivery_date'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipment_list_order_shipping_date', 'Ordenar data de recolha', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_list_order_shipping_date', 1, Setting::get('shipment_list_order_shipping_date'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipment_list_show_vehicle', 'Mostrar coluna dedicada - Viatura e Rota', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_list_show_vehicle', 1, Setting::get('shipment_list_show_vehicle'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipment_list_show_customer_name', 'Mostrar coluna dedicada - Nome Cliente', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_list_show_customer_name', 1, Setting::get('shipment_list_show_customer_name'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipment_list_show_obs', 'Mostrar coluna dedicada - Observações', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_list_show_obs', 1, Setting::get('shipment_list_show_obs'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipment_list_show_conferred', 'Mostrar coluna - Conferido', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_list_show_conferred', 1, Setting::get('shipment_list_show_conferred'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('delivery_map_status_show_on_map', 'Envios a apresentar no mapa do manifesto de entrega') }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Estados dos envios a apresentar no mapa que contém os envios possíveis de adicionar ao manifesto de entrega."></i>
                        {{ Form::select('delivery_map_status_show_on_map[]', $status, @array_map('intval', Setting::get('delivery_map_status_show_on_map')), ['class' =>'form-control select2', 'multiple']) }}
                    </td>
                </tr>
                <tr>
                    <td>{{ Form::label('shipment_list_pin_pending', 'Afixar envios pendentes topo', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shipment_list_pin_pending', 1, Setting::get('shipment_list_pin_pending'), ['class' => 'ios'] ) }}</td>
                </tr>
            </table>
            <h4 class="section-title">Pesos, Volumes e Limites</h4>
            <table class="table table-condensed m-b-0" style="border-bottom: 1px solid #eee">
                <tr>
                    <td>
                        {{ Form::label('shipments_custom_provider_weight', 'Personalizar peso da etiqueta', ['class' => 'control-label']) }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Esta opção dá-lhe a possibilidade de editar o peso que pretende que seja enviado via webservice. Dessa forma, o peso que sairá na etiqueta será diferente do peso que o cliente inseriu."></i>
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_custom_provider_weight', 1, Setting::get('shipments_custom_provider_weight'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipments_round_up_weight', 'Arredondar pesos ao valor certo', ['class' => 'control-label']) }}
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_round_up_weight', 1, Setting::get('shipments_round_up_weight'), ['class' => 'ios'] ) }}</td>
                </tr>
            </table>
            <table class="table table-condensed">
                <tr>
                    <td>{{ Form::label('show_adr_fields', 'Dimensões - Mostrar campos ADR', ['class' => 'control-label']) }}</td>
                    <td class="w-1">{{ Form::checkbox('show_adr_fields', 1, Setting::get('show_adr_fields'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shp_dimensions_show_price', 'Dimensões - mostrar campo Preço Un.', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shp_dimensions_show_price', 1, Setting::get('shp_dimensions_show_price'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>{{ Form::label('shp_dimensions_show_mounting', 'Dimensões - mostrar campo "Montagem"', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::checkbox('shp_dimensions_show_mounting', 1, Setting::get('shp_dimensions_show_mounting'), ['class' => 'ios'] ) }}</td>
                </tr>
            </table>
        </div>
        <div class="col-sm-4">
            <h4 class="section-title">Criação de Envios</h4>
            <table class="table table-condensed m-b-0" style="border-bottom: 1px solid #eee">
                <tr>
                    <td>{{ Form::label('shipment_default_provider', 'Fornecedor por defeito', ['class' => 'control-label']) }}</td>
                    <td>{{ Form::select('shipment_default_provider', ['' => '', 'economic' => 'Mais barato'] + $providers, Setting::get('shipment_default_provider'), ['class' =>'form-control select2']) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipment_status_after_create', 'Estado por defeito Expedições', ['class' => 'control-label']) }}
                    </td>
                    <td>{{ Form::select('shipment_status_after_create', ['' => 'Aceite (default)'] + $status, Setting::get('shipment_status_after_create'), ['class' =>'form-control select2']) }}</td>
                </tr>
                @if(hasModule('collections'))
                <tr>
                    <td>
                        {{ Form::label('pickup_status_after_create', 'Estado por defeito Recolhas', ['class' => 'control-label']) }}
                    </td>
                    <td>{{ Form::select('pickup_status_after_create', ['' => 'Aceite Central (default)'] + $status, Setting::get('pickup_status_after_create'), ['class' =>'form-control select2']) }}</td>
                </tr>
                @endif
                <tr>
                    <td>
                        {{ Form::label('shipment_schedule_default_status', 'Estado Envios Periódicos', ['class' => 'control-label']) }}
                    </td>
                    <td>{{ Form::select('shipment_schedule_default_status', ['' => 'Pendente (default)'] + $statusPickups, Setting::get('shipment_schedule_default_status'), ['class' =>'form-control select2']) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipment_ref_maxsize', 'Tamanho campo referência', ['class' => 'control-label']) }}
                    </td>
                    <td>{{ Form::select('shipment_ref_maxsize', ['' => '15 caractéres', '25' => '25 caractéres', '50' =>'50 caractéres', '100' => '100 caractéres'], Setting::get('shipment_ref_maxsize'), ['class' =>'form-control select2']) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipment_adicional_addr_mode', 'Moradas adicionais', ['class' => 'control-label']) }}
                        {!! tip('<b>1 Origem + vário destinos</b>: Só é possivel ter um local de origem e criar vários locais de entrega (ou vice-versa).<br/><b>Nova Origem + Destino</b>: Possibilidade de adicionar nova morada de recolha e entrega simultaneamente') !!}
                    </td>
                    <td>{{ Form::select('shipment_adicional_addr_mode', ['udir' => '1 Origem + vário destinos', 'bidir' => 'Nova Origem + Destino', 'pro' => 'Modo Avançado (dinâmico)', 'pro_fixed' => 'Modo avançado (fixo)'], Setting::get('shipment_adicional_addr_mode'), ['class' =>'form-control select2']) }}</td>
                </tr>
            </table>
            <table class="table table-condensed m-0">
                <tr>
                    <td>
                        {{ Form::label('shipment_save_other', 'Mostrar botão Gravar e Adicionar novo', ['class' => 'control-label']) }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Apresenta novo botão de gravação que permite gravar e manter a janela de criação de serviços aberta para permitir criar rapidamente outro serviço"></i>
                    </td>
                    <td class="check">{{ Form::checkbox('shipment_save_other', 1, Setting::get('shipment_save_other'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipment_save_other_fullreset', 'Limpar todos campos ao adicionar novo', ['class' => 'control-label']) }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Se utilizado o botão de gravação + adicionar novo, esta opção faz reset a todos os campos do serviço. Caso esteja inativo, só são limpos os campos do local de carga, descarga e dados da mercadoria."></i>
                    </td>
                    <td class="check">{{ Form::checkbox('shipment_save_other_fullreset', 1, Setting::get('shipment_save_other_fullreset'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipment_default_save_customer', 'Gravar auto novos destinatários', ['class' => 'control-label']) }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Se a opção estiver ativa, ao criar um novo cliente, ficará automáticamente ativa a opção 'Gravar cliente'"></i>
                    </td>
                    <td class="check">{{ Form::checkbox('shipment_default_save_customer', 1, Setting::get('shipment_default_save_customer'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('labels_show_cod', 'Mostrar nas etiquetas/guias valor portes', ['class' => 'control-label']) }}
                    </td>
                    <td class="check">{{ Form::checkbox('labels_show_cod', 1, Setting::get('labels_show_cod'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipments_phone_required', 'Obrigar preenchimento telefone', ['class' => 'control-label']) }}
                    </td>
                    <td class="check">{{ Form::checkbox('shipments_phone_required', 1, Setting::get('shipments_phone_required'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipment_prefill_hour', 'Pré-preencher hora inicio serviço', ['class' => 'control-label']) }}
                    </td>
                    <td class="check">{{ Form::checkbox('shipment_prefill_hour', 1, Setting::get('shipment_prefill_hour'), ['class' => 'ios'] ) }}</td>
                </tr>
                <tr>
                    <td>
                        {{ Form::label('shipment_alert_unpaid_invoices', 'Avisar se existirem faturas em atraso', ['class' => 'control-label']) }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Ao selecionar o cliente do envio será emitido um pop-up de aviso caso o cliente possua faturas em atraso."></i>
                    </td>
                    <td class="check">{{ Form::checkbox('shipment_alert_unpaid_invoices', 1, Setting::get('shipment_alert_unpaid_invoices'), ['class' => 'ios'] ) }}</td>
                </tr>
            </table>
            <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                <tr>
                    <td>
                        {{ Form::label('shipment_alert_payment_condition', 'Avisar condição de pagamento') }}
                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Ao selecionar o cliente do envio será emitido um pop-up de aviso caso a condição de pagamento do cliente for uma das indicadas na caixa de seleção"></i>
                        {{ Form::select('shipment_alert_payment_condition[]', $paymentConditions, empty(Setting::get('shipment_alert_payment_condition')) ? [] : Setting::get('shipment_alert_payment_condition'), ['class' =>'form-control select2', 'multiple']) }}
                    </td>
                </tr>
            </table>
            <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                <tr>
                    <td>
                        {{ Form::label('shipment_max_charge_price', 'Cobrança Máxima') }}
                    </td>
                    <td class="w-115px">
                        <div class="input-group">
                            {{ Form::text('shipment_max_charge_price', Setting::get('shipment_max_charge_price'), ['class' => 'form-control decimal']) }}
                            <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                        </div>
                    </td>
                </tr>
            </table>

            <h4 class="section-title">Gestor de Tarefas</h4>
            <table class="table table-condensed m-0">
                <tr>
                    <td>{{ Form::label('operators_tasks_require_hours', 'Obrigar preencher horário', ['class' => 'control-label']) }}</td>
                    <td class="check">{{ Form::checkbox('operators_tasks_require_hours', 1, Setting::get('operators_tasks_require_hours'), ['class' => 'ios'] ) }}</td>
                </tr>

                <tr>
                    <td>{{ Form::label('operators_tasks_require_service_type', 'Obrigar tipo de serviço', ['class' => 'control-label']) }}</td>
                    <td class="check">{{ Form::checkbox('operators_tasks_require_service_type', 1, Setting::get('operators_tasks_require_service_type'), ['class' => 'ios'] ) }}</td>
                </tr>
            </table>
        </div>
        <div class="col-sm-12">
            <hr/>
            {{ Form::submit('Gravar', array('class' => 'btn btn-primary' ))}}
        </div>
    </div>
    </div>
</div>

