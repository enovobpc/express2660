<div class="box no-border">
    <div class="box-body p-t-0">
        <div class="row">
            <div class="col-sm-4">
                <h4 class="section-title">Papel Timbrado</h4>
                <div class="row row-15">
                    <div class="col-sm-5">
                        <div class="m-t-5 fileinput {{ $pdfBgVertical ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
                            <div class="fileinput-new thumbnail" style="max-height: 205px;">
                                <img src="{{ asset('assets/img/default/pdf_bg_empty.svg') }}" class="w-100">
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-height: 205px;">
                                @if($pdfBgVertical)
                                    <img src="{{ asset($pdfBgVertical) }}?v={{ time() }}" class="w-100">
                                @endif
                            </div>
                            <div>
                                <span class="btn btn-default btn-block btn-sm btn-file">
                                    <span class="fileinput-new">Procurar...</span>
                                    <span class="fileinput-exists"><i class="fas fa-sync-alt"></i> Alterar</span>
                                    <input type="file" name="pdf_bg">
                                </span>
                                <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists btn-remove-bg-img" data-dismiss="fileinput">
                                    <i class="fas fa-close"></i> Remover
                                </a>
                            </div>
                            {{ Form::hidden('delete_pdf_bg') }}
                        </div>
                    </div>
                    <div class="col-sm-7">
                        {{ Form::label('image', 'Papel timbrado', array('class' => 'form-label m-t-10')) }}<br/>
                        <small class="text-muted">
                            Tamanho recomendado: <br/>.PNG, 2480 x 3508 pixeis.
                        </small>
                        <hr style="margin: 10px 0"/>
                        <small>
                            O papel timbrado será colocado como fundo dos documentos que não tenham efeitos fiscais.
                            <br/>
                            Não utilize logótipos nem imagens.
                        </small>
                        <hr style="margin: 10px 0"/>
                        <small>
                            O template dos documentos fiscais não é personalizável.
                        </small>
                        {{--<hr style="margin: 10px 0"/>
                        {{ Form::label('print', 'Impressão automática:', array('class' => 'form-label')) }}<br/>
                        <small class="text-muted">
                            Ao gerar o documento será aberta a janela de impressão.
                        </small>
                        <table class="table table-condensed">
                            <tr>
                                <td>{{ Form::label('open_print_dialog_docs', 'Documentos A4', ['class' => 'control-label']) }}</td>
                                <td class="check">{{ Form::checkbox('open_print_dialog_docs', 1, Setting::get('open_print_dialog_docs'), ['class' => 'ios'] ) }}</td>
                            </tr>
                            <tr>
                                <td>{{ Form::label('open_print_dialog_labels', 'Etiquetas', ['class' => 'control-label']) }}</td>
                                <td class="check">{{ Form::checkbox('open_print_dialog_labels', 1, Setting::get('open_print_dialog_labels'), ['class' => 'ios'] ) }}</td>
                            </tr>
                        </table>--}}
                    </div>
                </div>
                <h4 class="section-title">Impressão automática</h4>
                <table class="table table-condensed m-0">
                    <tr>
                        <td>{{ Form::label('open_print_dialog_docs', 'Imprimir automático documentos A4', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('open_print_dialog_docs', 1, Setting::get('open_print_dialog_docs'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('open_print_dialog_labels', 'Imprimir automático etiquetas', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('open_print_dialog_labels', 1, Setting::get('open_print_dialog_labels'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('invoices_autoprint', 'Imprimir fatura após emissão', ['class' => 'control-label']) }}
                            {!! tip('Abre janela para impressão do documento após a sua emissão') !!}
                        </td>
                        <td class="check">{{ Form::checkbox('invoices_autoprint', 1, Setting::get('invoices_autoprint'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">
                <h4 class="section-title">Guias de Transporte</h4>
                <table class="table table-condensed m-0">
                    <tr>
                        <td>{{ Form::label('shipment_guide_type', 'Design Guias Transporte', ['class' => 'control-label']) }}</td>
                        <td style="width: 200px">{{ Form::select('shipment_guide_type', trans('admin/shipments.guides-types'), Setting::get('shipment_guide_type'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('charging_instructions_model', 'Design Instruções Carga', ['class' => 'control-label']) }}</td>
                        <td style="width: 200px">{{ Form::select('charging_instructions_model', trans('admin/shipments.charging-instructions-types'), Setting::get('charging_instructions_model'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed" style="border-top: 1px solid #eee; border-bottom: 1px solid #eee; margin: 0">
                    <tr>
                        <td>{{ Form::label('guides_fuel_price', 'Preço de referência combustivel', ['class' => 'control-label']) }}</td>
                        <td style="width: 135px">
                            <div class="input-group">
                                {{ Form::text('guides_fuel_price', Setting::get('guides_fuel_price'), ['class' =>'form-control']) }}
                                <span class="input-group-addon">{{ Setting::get('app_currency') }}/Litro</span>
                            </div>
                        </td>
                    </tr>
                </table>
                <table class="table table-condensed">
                    <tr>
                        <td>
                            {{ Form::label('guides_show_conditions', 'Incluir Condições Gerais - Guia Transporte', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Inclui página extra nas guias de transporte com as condições gerais de serviço"></i>
                        </td>
                        <td class="check">{{ Form::checkbox('guides_show_conditions', 1, Setting::get('guides_show_conditions'), ['class' => 'ios'] ) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('shipment_proof_show_conditions', 'Incluir Condições Gerais - Compr. Transporte', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Inclui página extra no documento Comprovativo de transporte com as condições gerais de serviço"></i>
                        </td>
                        <td class="check">{{ Form::checkbox('shipment_proof_show_conditions', 1, Setting::get('shipment_proof_show_conditions'), ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
                <h4 class="section-title">Etiquetas autocolantes</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>{{ Form::label('shipment_label_size', 'Design etiqueta', ['class' => 'control-label']) }}</td>
                        <td style="width: 230px">{{ Form::select('shipment_label_size', trans('admin/shipments.labels-sizes'), Setting::get('shipment_label_size'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                    {{--<tr>
                        <td>{{ Form::label('shipment_label_barcodes', 'Código de barras', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::select('shipment_label_barcodes', ['1' => 'TRK da aplicação', '2' => 'TRK da aplicação + TRK fornecedor'], Setting::get('shipment_label_barcodes'), ['class' =>'form-control select2']) }}</td>
                    </tr>--}}
                    <tr>
                        <td>{{ Form::label('shipment_label_a4', 'Etiquetas A4', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::select('shipment_label_a4', ['' => '- Inativo -', '4' => 'Até 4 por página (A6)', '8' => 'Até 8 por página'], Setting::get('shipment_label_a4'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                </table>
                <h4 class="section-title">Mapas Entrega / Viagem</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>{{ Form::label('trip_summary_mode', 'Mapa de Entregas - serviços por página', ['class' => 'control-label']) }}</td>
                        <td style="width: 70px">{{ Form::select('trip_summary_mode',  ['' => '12', 'xlarge' => '5'], Setting::get('trip_summary_mode'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">
                <h4 class="section-title">Documentos após gravar expedições</h4>
                <table class="table table-condensed m-b-0">
                    <tr>
                        <td>{{ Form::label('shipment_print_default', 'Ao criar na área gestão', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::select('shipment_print_default', trans('admin/shipments.print-options'), Setting::get('shipment_print_default'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('shipment_print_default_customers', 'Ao criar na área cliente', ['class' => 'control-label']) }}</td>
                        <td class="w-150px">{{ Form::select('shipment_print_default_customers', ['' => 'Não imprimir nada'] +  trans('admin/shipments.print-options'), Setting::get('shipment_print_default_customers'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                </table>
                <h4 class="section-title">Importador de Ficheiros</h4>
                <table class="table table-condensed m-b-0">
                    <tr>
                        <td>
                            {{ Form::label('expense1', 'Taxa adicional 1', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Coloque a taxa adicional a que corresponde a esta coluna."></i>
                        </td>
                        <td>{{ Form::select('expense1', ['' => ''] + @$expenses, Setting::get('expense1'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('expense2', 'Taxa adicional 2', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Coloque a taxa adicional a que corresponde a esta coluna."></i>
                        </td>
                        <td>{{ Form::select('expense2', ['' => ''] + @$expenses, Setting::get('expense2'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                    <tr>
                        <td>
                            {{ Form::label('expense3', 'Taxa adicional 3', ['class' => 'control-label']) }}
                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Coloque a taxa adicional a que corresponde a esta coluna."></i>
                        </td>
                        <td>{{ Form::select('expense3', ['' => 'Nenhuma'] + @$expenses, Setting::get('expense3'), ['class' =>'form-control select2']) }}</td>
                    </tr>

                    <tr>
                        <td>
                            {{ Form::label('importer_default_status', 'Estado por defeito', ['class' => 'control-label']) }}
                        </td>
                        <td>{{ Form::select('importer_default_status', ['' => 'Aceite (por defeito)'] + @$status, Setting::get('importer_default_status'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                </table>
                <h4 class="section-title">Exportação Excel - Colunas adicionais</h4>
                <table class="table table-condensed">
                    <tr>
                        <td>{{ Form::label('export_shipping_expenses[]', 'Colunas Taxas Adicionais', ['class' => 'control-label']) }}</td>
                        <td style="width: 230px">
                            {{ Form::selectMultiple('export_shipping_expenses[]', $expenses, @array_map('intval', Setting::get('export_shipping_expenses')), ['class' =>'form-control select2']) }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">

            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr/>
                {{ Form::submit('Gravar', array('class' => 'btn btn-primary' ))}}
            </div>
        </div>
    </div>
</div>

