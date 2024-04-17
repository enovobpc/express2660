<div class="box p-0">
    <div class="box-body p-t-0">
        <div class="row">
            <div class="col-sm-4">
                <h4 class="section-title">Definições Gerais</h4>
                <table class="table table-condensed m-0">
                    <tr>
                        <td class="w-200px">{{ Form::label('billing_method', 'Formato de Faturação', ['class' => 'control-label']) }}</td>
                        <td>
                            {{ Form::select('billing_method', trans('admin/billing.billing-methods'), Setting::get('billing_method'), ['class' =>'form-control select2']) }}
                        </td>
                    </tr>
                    {{-- <tr>
                        <td class="w-200px">{{ Form::label('billing_items_method', 'Formato artigos faturação', ['class' => 'control-label']) }}</td>
                        <td>
                            {{ Form::select('billing_items_method', ['' => 'Agrupar por tipo destino', 'services' => 'Agrupar tipo serviço + destino'], Setting::get('billing_items_method'), ['class' =>'form-control select2']) }}
                        </td>
                    </tr> --}}
                    <tr>
                        <td class="w-200px">{{ Form::label('shipments_billing_date', 'Data para faturação envios', ['class' => 'control-label']) }}</td>
                        <td>
                            {{ Form::select('shipments_billing_date', ['' => 'Data de Criação', 'delivery' => 'Data da Entrega'], Setting::get('shipments_billing_date'), ['class' =>'form-control select2']) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="w-200px">{{ Form::label('billing_method', 'Estado envios após faturação', ['class' => 'control-label']) }}</td>
                        <td>
                            {{ Form::select('shipments_status_after_billing', ['' => 'Manter o mesmo']+ $status, Setting::get('shipments_status_after_billing'), ['class' =>'form-control select2']) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="w-200px">{{ Form::label('saft_day', 'Dia limite para envio SAF-T', ['class' => 'control-label']) }}</td>
                        <td>
                            <div class="input-group">
                                {{ Form::text('saft_day', Setting::get('saft_day'), ['class' =>'form-control number', 'maxlength' => 2]) }}
                                <div class="input-group-addon">de cada mês</div>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                    <tr>
                        <td>{{ Form::label('saft_send_auto', 'Enviar SAF-T automáticamente', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('saft_send_auto', 1, Setting::get('saft_send_auto'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed" style="border-top: 1px solid #eee">
                    <tr>
                        <td>
                            {{ Form::label('billing_store_invoices', 'Arquivar faturas em sistema', ['class' => 'control-label']) }}
                            {!! tip('Grava em servidor o PDF das faturas. Evita dessa forma chamadas adicionais ao software de faturação e permite guardar sempre os documentos originais em vez da 2ª via.') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('billing_store_invoices', 1, Setting::get('billing_store_invoices'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('block_shipments_after_billing', 'Bloquear envios depois de fechar o mês', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('block_shipments_after_billing', 1, Setting::get('block_shipments_after_billing'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('billing_force_today', 'Faturar sempre na data de hoje', ['class' => 'control-label']) }} {!! tip('Ative esta opção caso pretenda que a faturação mensal seja sempre emitida com a data do próprio dia. Caso inativo, o sistema irá assumir o dia 30/31 de cada mês.') !!}</td>
                        <td class="check">{{ Form::checkbox('billing_force_today', 1, Setting::get('billing_force_today'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('billing_auto_hide_zero', 'Marcar como faturados automaticamente', ['class' => 'control-label']) }}
                            {!! tip('Esta opção marca os clientes automáticamente como faturados caso a faturação total seja 0,00€ ou caso todos os seus envios estejam já faturados.') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('billing_auto_hide_zero', 1, Setting::get('billing_auto_hide_zero'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('billing_auto_hide_zero', 'Conta Corrente - Ocultar listagem e-mail', ['class' => 'control-label']) }}
                            {!! tip('Permite que o e-mail de conta corrente inclua a lista de documentos pendentes') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('ballance_email_hide_docs_list', 1, Setting::get('ballance_email_hide_docs_list'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('billing_show_cred_deb_column', 'Conta Corrente - Ver Coluna Créd./Debto', ['class' => 'control-label']) }}
                            {!! tip('Mostra na lista de conta corrente a coluna crédito e débito em vez da coluna total') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('billing_show_cred_deb_column', 1, Setting::get('billing_show_cred_deb_column'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('billing_allow_negative_stock', 'Permitir venda de Artigos com stock negativo', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('billing_allow_negative_stock', 1, Setting::get('billing_allow_negative_stock'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('default_customer_payment_method', 'Pagamento por Defeito', ['class' => 'control-label']) }}
                            {!! tip('Pagamento por defeito ao criar uma nova ficha de cliente') !!}
                        </td>
                        <td class="w-120px">{{ Form::select('default_customer_payment_method', hasModule('account_wallet') ? ['PRÉ-PAGAMENTO' => ['wallet'=> 'Pgto Automático'], 'PAGAMENTO MENSAL' => $paymentConditions] : [''=>''] + $paymentConditions, Setting::get('default_customer_payment_method'), ['class' => 'form-control select2']) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">
                @if(Auth::user()->isAdmin())
                    <h4 class="section-title">Software AT</h4>
                    <table class="table table-condensed">
                        <tr>
                            <td>
                                {{ Form::select('invoice_software', ['KeyInvoice' => 'Keyinvoice', 'EnovoTms' => 'Nativo', 'SageX3' => 'Sage X3'], Setting::get('invoice_software'), ['class' =>'form-control select2']) }}
                            </td>
                        </tr>
                    </table>
                    <table class="table table-condensed">
                        <tr>
                            <td class="w-75px">{{ Form::label('invoice_apikey', 'Licença 01', ['class' => 'control-label']) }}</td>
                            <td>
                                <div class="row row-0">
                                    <div class="col-sm-3" style="margin-right: -1px">
                                        {{ Form::text('invoice_apikey_name', Setting::get('invoice_apikey_name'), ['class' =>'form-control', 'placeholder' => 'Descrição']) }}
                                    </div>
                                    <div class="col-sm-9">
                                        {{ Form::select('invoice_apikey_agencies[]', $agencies, @array_map('intval', Setting::get('invoice_apikey_agencies')), ['class' =>'form-control select2', 'multiple', 'data-placeholder' => 'Todas Agência(s)']) }}
                                    </div>
                                    <div class="col-sm-12">
                                        {{ Form::text('invoice_apikey', Setting::get('invoice_apikey'), ['class' =>'form-control input-xs', 'style' => 'margin-top:-3px', 'placeholder' => 'Chave da API']) }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                @else
                    <div style="display: none">
                        {{ Form::select('invoice_software', ['KeyInvoice' => 'Keyinvoice', 'EnovoTms' => 'Nativo', 'SageX3' => 'Sage X3'], Setting::get('invoice_software'), ['class' =>'form-control select2']) }}
                    </div>
                @endif
                <h4 class="section-title">Ficheiros de Resumo</h4>
                {{ Form::hidden('billing_customers_pdf_position', 'v') }}
                <table class="table table-condensed m-0" style="border-bottom: 1px solid #eee">
                    <tr>
                        <td>
                            {{ Form::label('billing_show_vat', 'Mostrar percentagem de IVA', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Por baixo do preço liquido é indicada a percentagem de IVA correspondente."></i>
                        </td>
                        <td class="check">{{ Form::checkbox('billing_show_vat', 1, Setting::get('billing_show_vat'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('billing_customers_pdf_provider_trk', 'Mostrar tracking do fornecedor', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Adiciona no resumo de envios a colunca com o código e informação do expedidor"></i>
                        </td>
                        <td class="check">{{ Form::checkbox('billing_customers_pdf_provider_trk', 1, Setting::get('billing_customers_pdf_provider_trk'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('billing_customers_excel_expenses', 'Incluir detalhe encargos no ficheiro Excel', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Adiciona no resumo excel linhas referentes aos encargos do envio."></i>
                        </td>
                        <td class="check">{{ Form::checkbox('billing_customers_excel_expenses', 1, Setting::get('billing_customers_excel_expenses'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('billing_attach_excel', 'Enviar sempre por e-mail resumo Excel', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('billing_attach_excel', 1, Setting::get('billing_attach_excel'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed m-0">
                    <tr>
                        <td>{{ Form::label('billing_ignored_services[]', 'Serviços a ignorar das listagens', ['class' => 'control-label']) }}</td>
                        <td class="w-150px">
                            {{ Form::select('billing_ignored_services[]', $services, @array_map('intval', Setting::get('billing_ignored_services')),['class' =>'form-control select2', 'multiple']) }}
                        </td>
                    </tr>
                </table>
                <h4 class="section-title">Envio de e-mails</h4>
                <table class="table table-condensed m-0" style="border-bottom: 1px solid #eee">
                    <tr>
                        <td>{{ Form::label('accountant_email', 'E-mail envio SAF-T', ['class' => 'control-label']) }}</td>
                        <td>
                            {{ Form::text('accountant_email', Setting::get('accountant_email'), ['class' =>'form-control input-sm email']) }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('billing_email_cc', 'Cópia e-mails faturação', ['class' => 'control-label']) }}</td>
                        <td>
                            {{ Form::text('billing_email_cc', Setting::get('billing_email_cc'), ['class' =>'form-control']) }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('refunds_email_cc', 'Cópia e-mails reembolsos', ['class' => 'control-label']) }}</td>
                        <td>
                            {{ Form::text('refunds_email_cc', Setting::get('refunds_email_cc'), ['class' =>'form-control']) }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">
                <h4 class="section-title">Cálculo de Preços</h4>
                <table class="table table-condensed m-b-0" style="border-bottom: 1px solid #eee;">
                    <tr>
                        <td>{{ Form::label('shipment_use_final_consumer_table', 'Calcular preços PVP', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('shipment_use_final_consumer_table', 1, Setting::get('shipment_use_final_consumer_table'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('shipments_average_weight', 'Calcular peso médio', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Se esta opção estiver ativa, poderá ativar para cada cliente se os envios que o cliente faz se baseiam em peso médio. O cálculo do peso médio corresponde á formula: Peso/Nº Volumes"></i>
                        </td>
                        <td class="check">{{ Form::checkbox('shipments_average_weight', 1, Setting::get('shipments_average_weight'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('shipment_price_per_package_line', 'Calcular preço por tipo pacote', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Se esta opção estiver ativa, o sistema permitirá o cálculo de preços individualizadamente por cada linha de pacote"></i>
                        </td>
                        <td class="check">{{ Form::checkbox('shipment_price_per_package_line', 1, Setting::get('shipment_price_per_package_line'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('shipment_nocalc_kg_adic_zero', 'Não calcular preço se KG adicional = 0', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Caso o peso ultrapasse os KG adicionais e não exista preço para os KG adicionais, o sistema assumirá o preço total como 0.00. Ocorre quando o peso máximo do serviço é o definido na tabela de preços."></i>
                        </td>
                        <td class="check">{{ Form::checkbox('shipment_nocalc_kg_adic_zero', 1, Setting::get('shipment_nocalc_kg_adic_zero'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed m-b-0" style="border-bottom: 1px solid #eee;">
                    <tr>
                        <td>{{ Form::label('app_money_decimals', 'Número casas decimais preços', ['class' => 'control-label']) }}</td>
                        <td class="w-65px">{{ Form::select('app_money_decimals', ['2' => '2', '3' => '3'], Setting::get('app_money_decimals'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed">
                    <tr>
                        <td>{{ Form::label('cod_prices_table', 'Cálculo Portes Destino', ['class' => 'control-label']) }}</td>
                        <td class="w-65px">{{ Form::select('cod_prices_table', ['' => 'Usar tabela do cliente'] + $pricesTables, Setting::get('cod_prices_table'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                </table>
                <h4 class="section-title">Taxa de Seguro</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>{{ Form::label('fuel_tax', 'Percentagem a aplicar na fatura', ['class' => 'control-label']) }}</td>
                        <td class="w-100px">
                            <div class="input-group">
                                {{ Form::text('insurance_tax', Setting::get('insurance_tax'), ['class' =>'form-control decimal']) }}
                                <span class="input-group-addon" id="basic-addon2">%</span>
                            </div>
                        </td>
                    </tr>
                </table>
                <h4 class="section-title"><i class="fas fa-gas-pump"></i> Taxa de Combustível</h4>
                <table class="table table-condensed m-0" style="border-bottom: 1px solid #eee;">
                    <tr>
                        <td>{{ Form::label('fuel_tax', 'Ativar Taxa combustível/Taxa por defeito', ['class' => 'control-label']) }}</td>
                        <td class="w-100px">
                            <div class="input-group">
                                {{ Form::text('fuel_tax', Setting::get('fuel_tax'), ['class' =>'form-control decimal']) }}
                                <span class="input-group-addon" id="basic-addon2">%</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('fuel_tax_budgets', 'Percentagem a aplicar nos orçamentos', ['class' => 'control-label']) }}</td>
                        <td class="w-100px">
                            <div class="input-group">
                                {{ Form::text('fuel_tax_budgets', Setting::get('fuel_tax_budgets'), ['class' =>'form-control decimal']) }}
                                <span class="input-group-addon" id="basic-addon2">%</span>
                            </div>
                        </td>
                    </tr>
                </table>
                <table class="table table-condensed m-b-0" style="border-bottom: 1px solid #eee;">
                    <tr>
                        <td class="vertical-align-middle">
                            {{ Form::label('fuel_tax_invoice_detail', 'Descriminar taxas na fatura') }}
                            {!! tip('Se opção ativa, será adicionada na fatura uma linha para cada taxa de combustível. Se inativo, será cobrado junto com o valor dos envios.') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('fuel_tax_invoice_detail', 1, Setting::get('fuel_tax_invoice_detail'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <p class="text-blue">
                    <i class="fas fa-info-circle"></i> Pode configurar agendar as taxas de combustível por semana ou mês no menu "Taxas Adicionais".
                    <a href="{{ route('admin.expenses.index',['tab' => 'fuel'] ) }}" class="btn btn-xs btn-default"><i class="fas fa-cog"></i> Configurar</a>
                </p>
                <h4 class="section-title">Escalões por defeito tabelas de preço</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>
                            {{ Form::label('default_weights', 'Pesos por defeito (separados por vírgula)') }}
                            {{ Form::text('default_weights', Setting::get('default_weights'), ['class' =>'form-control']) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <h4 class="section-title">Comentários nas faturas</h4>
                <table class="table table-condensed m-b-0">
                    <td class="w-160px vertical-align-middle">
                        <label>Inserir Observações Automáticas {!! tip('O sistema irá adicionar automáticamente observações nas faturas.') !!}</label>
                    </td>
                    <td class="check w-40px">{{ Form::checkbox('invoices_obs_auto', 1, Setting::get('invoices_obs_auto'), ['class' => 'ios'] ) }}</td>
                    <td class="w-100px">{{ Form::label('invoice_obs_allowance_percent', 'Ajudas Custo Observações') }}</td>
                    <td class="w-80px">
                        <div class="input-group">
                            {{ Form::text('invoice_obs_allowance_percent', Setting::get('invoice_obs_allowance_percent'), ['class' =>'form-control decimal']) }}
                            <div class="input-group-addon">%</div>
                        </div>
                        
                    </td>
                    <td></td>
                </table>
                <table class="table table-condensed">
                    <tr>
                        <td class="w-100px">{{ Form::label('invoice_obs', 'Observações Fatura Mensal ou Parcial', ['class' => 'control-label']) }}</td>
                        <td>
                            {{ Form::textarea('invoice_obs', Setting::get('invoice_obs'), ['class' =>'form-control', 'rows' => 2]) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="w-100px">{{ Form::label('invoice_shipment_obs', 'Observações ao Faturar Serviço Indivual', ['class' => 'control-label']) }}</td>
                        <td>
                            {{ Form::textarea('invoice_shipment_obs', Setting::get('invoice_shipment_obs'), ['class' =>'form-control', 'rows' => 2]) }}
                            <small><span  class="label label-default">:month</span> Mês atual</small>
                            <small><span  class="label label-default">:year</span> Ano atual</small>
                            <small><span  class="label label-default">:period</span> Perído Faturação</small>
                            <small><span  class="label label-default">:allowanceprice</span> Preço ajuda custo</small>
                            <small><span  class="label label-default">:allowancepercent</span> % ajuda custo</small>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('invoice_footer_obs', 'Obs. Rodapé', ['class' => 'control-label']) }}</td>
                        <td colspan="3">{{ Form::textarea('invoice_footer_obs', Setting::get('invoice_footer_obs'), ['class' =>'form-control', 'rows' => 2]) }}</td>
                    </tr>
                </table>
                <h4 class="section-title" style="position: relative;padding: 15px 0 0 10px;">
                    Artigos a faturar
                    <div class="input-sm" style="position: relative; width: 260px; top: -12px;right: 0;float: right;">
                        {{ Form::select('invoice_items_method', ['' => 'Agrupar por tipo destino', 'services' => 'Agrupar tipo serviço + destino'], Setting::get('invoice_items_method'), ['class' =>'form-control select2']) }}
                    </div>
                </h4>

                @if(empty(Setting::get('invoice_items_method')))
                <table class="table table-condensed">
                    <tr>
                        <th class="w-190px" style="border: none">Artigo na aplicação</th>
                        <th style="border: none">Artigo correspondente no programa de faturação</th>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoice_item_nacional_ref', 'Envios Nacionais', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Aplica-se a Taxa de IVA Normal. Este artigo refere-se a todos os envios nacionais."></i>
                        </td>
                        <td>
                            {{ Form::select('invoice_item_nacional_ref',  ['' => ''] + $billingProducts, Setting::get('invoice_item_nacional_ref'), ['class' =>'form-control select2']) }}
                            {{ Form::hidden('invoice_item_nacional_desc', @$billingProducts[Setting::get('invoice_item_nacional_ref')]) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoice_item_import_ref', 'Importações', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Aplica-se a Taxa de IVA Normal. Este artigo refere-se a todos os envios que sejam importações."></i>
                        </td>
                        <td>
                            {{ Form::select('invoice_item_import_ref',  ['' => ''] + $billingProducts, Setting::get('invoice_item_import_ref'),  ['class' =>'form-control select2']) }}
                            {{ Form::hidden('invoice_item_import_desc', @$billingProducts[Setting::get('invoice_item_import_ref')]) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoice_item_import_ref', 'Envios Espanha', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Aplica-se a Taxa de IVA Isenta. Este artigo refere-se a todos os envios que tenham sido enviados para Espanha."></i>
                        </td>
                        <td>
                            {{ Form::select('invoice_item_spain_ref',  ['' => ''] + $billingProducts, Setting::get('invoice_item_spain_ref'),  ['class' =>'form-control select2']) }}
                            {{ Form::hidden('invoice_item_spain_desc', @$billingProducts[Setting::get('invoice_item_spain_ref')]) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoice_item_internacional_ref', 'Envios Internacionais', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Aplica-se a Taxa de IVA Isenta. Este artigo refere-se a todos os envios que tenham sido enviados para qualquer parte do mundo (excepto espanha)."></i>
                        </td>
                        <td>
                            {{ Form::select('invoice_item_internacional_ref',  ['' => ''] + $billingProducts, Setting::get('invoice_item_internacional_ref'),  ['class' =>'form-control select2']) }}
                            {{ Form::hidden('invoice_item_internacional_desc', @$billingProducts[Setting::get('invoice_item_internacional_ref')]) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoice_item_courier_ref', 'Serviço Estafetagem', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Aplica-se a Taxa de IVA Normal. Este artigo refere-se a todos os envios nacionais."></i>
                        </td>
                        <td>
                            {{ Form::select('invoice_item_courier_ref',  ['' => ''] + $billingProducts, Setting::get('invoice_item_courier_ref'), ['class' =>'form-control select2']) }}
                            {{ Form::hidden('invoice_item_courier_desc', @$billingProducts[Setting::get('invoice_item_courier_ref')]) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoice_item_express_service_ref', 'Serviços Expresso', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Aplica-se a Taxa de IVA Normal. Este artigo destina-se a faturar todos os serviços expressos."></i>
                        </td>
                        <td>
                            {{ Form::select('invoice_item_express_service_ref',  ['' => ''] + $billingProducts, Setting::get('invoice_item_express_service_ref'),  ['class' =>'form-control select2']) }}
                            {{ Form::hidden('invoice_item_express_service_desc', @$billingProducts[Setting::get('invoice_item_express_service_ref')]) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoice_item_covenants_ref', 'Avenças Mensais', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Aplica-se a Taxa de IVA Normal. Este artigo destina-se a faturar avenças mensais."></i>
                        </td>
                        <td>
                            {{ Form::select('invoice_item_covenants_ref',  ['' => ''] + $billingProducts, Setting::get('invoice_item_covenants_ref'),  ['class' =>'form-control select2']) }}
                            {{ Form::hidden('invoice_item_covenants_desc', @$billingProducts[Setting::get('invoice_item_covenants_ref')]) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoice_item_mail_ref', 'Serviços envio correio', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Aplica-se a Taxa de IVA 0%. Este artigo destina-se a faturar serviços de emissão de correio CTT."></i>
                        </td>
                        <td>
                            {{ Form::select('invoice_item_mail_ref',  ['' => ''] + $billingProducts, Setting::get('invoice_item_mail_ref'),  ['class' =>'form-control select2']) }}
                            {{ Form::hidden('invoice_item_mail_desc', @$billingProducts[Setting::get('invoice_item_mail_ref')]) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoice_item_products_ref', 'Venda de Artigos', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Aplica-se a Taxa de IVA Normal. Este artigo destina-se a faturar todas as vendas de artigos ou produtos."></i>
                        </td>
                        <td>
                            {{ Form::select('invoice_item_products_ref',  ['' => ''] + $billingProducts, Setting::get('invoice_item_products_ref'),  ['class' =>'form-control select2']) }}
                            {{ Form::hidden('invoice_item_products_desc', @$billingProducts[Setting::get('invoice_item_products_ref')]) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoice_item_fuel_ref', 'Taxa de Combústivel', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="A taxa de iva é aplica automáticamente. Este artigo destina-se a faturar as taxas de combustível caso existam."></i>
                        </td>
                        <td>
                            {{ Form::select('invoice_item_fuel_ref',  ['' => ''] + $billingProducts, Setting::get('invoice_item_fuel_ref'),  ['class' =>'form-control select2']) }}
                            {{ Form::hidden('invoice_item_fuel_desc', @$billingProducts[Setting::get('invoice_item_fuel_ref')]) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoice_item_insurance_ref', 'Taxa de Seguro', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="A taxa de iva é aplica automáticamente. Este artigo destina-se a faturar as taxas de seguro quando aplicável a todos os envios."></i>
                        </td>
                        <td>
                            {{ Form::select('invoice_item_insurance_ref',  ['' => ''] + $billingProducts, Setting::get('invoice_item_insurance_ref'),  ['class' =>'form-control select2']) }}
                            {{ Form::hidden('invoice_item_insurance_desc', @$billingProducts[Setting::get('invoice_item_insurance_ref')]) }}
                        </td>
                    </tr>
                    @if(hasModule('waybills'))
                        <tr>
                            <td>Cartas de Porte Aéreo</td>
                            <td>
                                {{ Form::select('invoice_item_waybill_ref',  ['' => ''] + $billingProducts, Setting::get('invoice_item_waybill_ref'),  ['class' =>'form-control select2']) }}
                                {{ Form::hidden('invoice_item_waybill_desc', @$billingProducts[Setting::get('invoice_item_waybill_ref')]) }}
                            </td>
                        </tr>
                    @endif
                </table>
                @else
                <table class="table table-condensed table-hover table-invoice-items">
                    <tr>
                        <th class="bg-gray" style="border: none">Serviço</th>
                        <th class="bg-gray" style="border: none" colspan="2">Artigo correspondente no programa de faturação</th>
                    </tr>
                    <?php $invoiceItems = Setting::get('invoice_item') ?>
                    @foreach ($services as $serviceId => $serviceName)
                    <tr>
                        <td class="w-150px" style="vertical-align: middle">{{ $serviceName }}</td>
                        <td class="w-40">
                            <table class="w-100 input-sm">
                                <tr>
                                    <td class="w-80px">
                                        Nacional<br/>
                                        <small class="italic text-muted">NAC{{$serviceId}}</small>
                                    </td>
                                    <td>{{ Form::select('invoice_item[NAC'.$serviceId.']',  ['' => ''] + $billingProducts, @$invoiceItems['NAC'.$serviceId], ['class' =>'form-control select2']) }}</td>
                                </tr>
                                <tr>
                                    <td class="w-80px">
                                        Espanha<br/>
                                        <small class="italic text-muted">ESP{{$serviceId}}</small>
                                    </td>
                                    <td>{{ Form::select('invoice_item[ESP'.$serviceId.']',  ['' => ''] + $billingProducts, @$invoiceItems['ESP'.$serviceId], ['class' =>'form-control select2']) }}</td>
                                </tr>
                            </table>
                        </td>
                        <td class="w-40">
                            <table class="w-100">
                                <tr>
                                    <td class="w-85px">
                                        Exportação<br/>
                                        <small class="italic text-muted">EXP{{$serviceId}}</small>
                                    </td>
                                    <td>{{ Form::select('invoice_item[EXP'.$serviceId.']',  ['' => ''] + $billingProducts, @$invoiceItems['EXP'.$serviceId], ['class' =>'form-control select2']) }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        Importação<br/>
                                        <small class="italic text-muted">IMP{{$serviceId}}</small>
                                    </td>
                                    <td>{{ Form::select('invoice_item[IMP'.$serviceId.']',  ['' => ''] + $billingProducts, @$invoiceItems['IMP'.$serviceId], ['class' =>'form-control select2']) }}</td>
                                </tr>
                                
                            </table>
                        </td>
                    </tr>
                    @endforeach
                </table>
                <style>
                    .table-invoice-items td {
                        border-bottom: 1px solid #ccc
                    }
                
                    .table-invoice-items table td {
                        border-bottom: 1px solid transparent
                    }
                
                    .table-invoice-items table td:first-child {
                        text-align: right;
                        padding-right: 8px;
                        line-height: 12px;
                    }

                    .table-invoice-items table td small {
                        font-size: 80%
                    }
                </style>
                @endif
            </div>
            <div class="col-sm-4">
                <h4 class="section-title">Faturação Individual Serviços/Envios</h4>
                <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                    <tr>
                        <td>
                            {{ Form::label('invoice_use_shipment_ref', 'Usar referência do cliente na fatura', ['class' => 'control-label']) }}
                            {!! tip('Ao faturar é usada no campo referência da fatura a referência do cliente.') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('invoice_use_shipment_ref', 1, Setting::get('invoice_use_shipment_ref'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoice_include_shipment_expenses', 'Descriminar na fatura encargos do envio', ['class' => 'control-label']) }}
                            {!! tip('Ao faturar é são faturadas individualmente os encargos dos envios.') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('invoice_include_shipment_expenses', 1, Setting::get('invoice_include_shipment_expenses'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <h4 class="section-title">Reembolsos</h4>
                <table class="table table-condensed m-0" style="border-top: 1px solid #eee">
                    <tr>
                        <td>
                            {{ Form::label('refunds_request_mode', 'Permitir aos clientes solicitarem reembolso', ['class' => 'control-label']) }}
                            {!! tip('Esta opção permite que o cliente solicite o reembolso de um ou mais envios com método de pagamento escolhido pelo cliente.') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('refunds_request_mode', 1, Setting::get('refunds_request_mode'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('refunds_show_shipment_ref', 'Mostrar coluna Referência', ['class' => 'control-label']) }}
                        </td>
                        <td class="check">{{ Form::checkbox('refunds_show_shipment_ref', 1, Setting::get('refunds_show_shipment_ref'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>

                <h4 class="section-title">Pagamentos automáticos</h4>
                <table class="table table-condensed">
                    <tr>
                        <td colspan="2">
                            {{ Form::label('wallet_payment_methods[]', 'Métodos de pagamento disponíveis', ['class' => 'control-label']) }}
                            {{ Form::select('wallet_payment_methods[]', ['mb' => 'Multibanco', 'mbway' => 'MB Way', 'visa' => 'Visa/Mastercard'], Setting::get('wallet_payment_methods'), ['class' =>'form-control select2', 'multiple']) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            {{ Form::label('wallet_amounts', 'Valores de carregamento de conta (separado por vírgula)', ['class' => 'control-label']) }}
                            {{ Form::text('wallet_amounts', Setting::get('wallet_amounts') ? Setting::get('wallet_amounts') : '50,100,250,500', ['class' =>'form-control']) }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('wallet_min_amount', 'Valor mínimo de saldo em conta', ['class' => 'control-label']) }}</td>
                        <td class="w-100px">
                            <div class="input-group">
                                {{ Form::text('wallet_min_amount', Setting::get('wallet_min_amount'), ['class' =>'form-control decimal']) }}
                                <span class="input-group-addon" id="basic-addon2">{{ Setting::get('app_currency') }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                {{ Form::submit('Gravar', array('class' => 'btn btn-primary' ))}}
            </div>
        </div>
    </div>
</div>
