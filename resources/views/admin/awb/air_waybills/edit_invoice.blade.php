{{ Form::open(['route' => ['admin.air-waybills.invoice.create'], 'method' => 'post', 'class' => 'form-invoice']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Faturar cartas de porte aéreo</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-6">
            <h4 class="text-blue m-t-0 m-b-15">Linhas do Documento</h4>
            <table class="table table-condensed table-hover m-t-10 m-b-0">
                <tr class="bg-gray">
                    <th class="w-1">Ref</th>
                    <th>Descrição</th>
                    <th class="w-55px">IVA</th>
                    <th class="w-100px">Total</th>
                </tr>
                <?php $total = 0; ?>
                @foreach($waybills as $waybill)
                    <?php
                    $total+= $waybill->total_price + $waybill->total_goods_price;
                    ?>
                    <tr>
                        <td class="text-center">
                            {{ Setting::get('invoice_item_waybill_ref') }}
                            {{ Form::hidden('ids[]', $waybill->id) }}
                        </td>
                        <td>{{ Form::text('description[]', '[' . $waybill->date->format('Y-m-d') . '] AWB Nº ' . $waybill->awb_no, ['class' => 'form-control input-sm', 'required']) }}</td>
                        <td>{{ Form::select('tax[]', $taxes, null, ['class' => 'form-control input-sm', 'required']) }}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                {{ Form::text('price[]', number($waybill->total_price + $waybill->total_goods_price), ['class' => 'form-control text-blue bold', 'required']) }}
                                <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>

            <h4 class="text-blue">Resumo total</h4>
            <table class="table table-condensed table-hover m-t-10 m-b-0">
                <tr class="bg-gray">
                    <th class="w-220px"></th>
                    <th>IVA ({{ Setting::get('vat_rate_normal') }}%)</th>
                    <th>IVA (0%)</th>
                    <th>A Faturar</th>
                </tr>
                <tr>
                    <td><b>Total do Documento</b></td>
                    <td>
                        <div class="input-group input-group-sm">
                            {{ Form::text('total_vat', number($total), ['class' => 'form-control text-blue bold', 'required']) }}
                            <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            {{ Form::text('total_no_vat', number(0), ['class' => 'form-control text-blue bold', 'required']) }}
                            <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            {{ Form::text('total_billed', number($total), ['class' => 'form-control text-blue bold', 'required']) }}
                            <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-sm-6">
            <h4 class="text-blue m-t-0 m-b-15">Emissão de Documento de Venda</h4>
            @if(!hasModule('invoices'))
                <div class="row">
                    <div class="col-sm-12">
                        <p class="text-info bold">
                            <i class="fas fa-info-circle"></i> A sua plataforma não possui licença ativa para utilização do módulo de ligação com Software de Faturação Online.
                        </p>
                        <br/>
                        <img src="https://www.keyinvoice.com/images/logo.png">
                        <p>Facturação sempre Online e sempre ligada à sua plataforma de envios.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check"></i> Faturação Online pelo software online Keyinvoice</li>
                            <li><i class="fas fa-check"></i> Emissão de documentos de venda diretamente pela plataforma;</li>
                            <li><i class="fas fa-check"></i> Criação e Atualização de clientes no software de faturação a partir da plataforma;</li>
                            <li><i class="fas fa-check"></i> Disponibilização da conta corrente na área de cliente;</li>
                            <li><i class="fas fa-check"></i> Envio de faturas diretamente via e-mail para o cliente;</li>
                        </ul>
                        <br/>
                        <a href="mailto:geral@enovo.pt" class="btn btn-sm btn-default">Contacte-nos para saber mais.</a>
                        <div class="spacer-30"></div>
                    </div>
                </div>
            @elseif($totalCustomers > 1)
                <h4 class="text-red bold"><i class="fas fa-exclamation-triangle"></i> Não é possível faturar os serviços selecionados.</h4>
                <p>
                    <b>Os serviços que selecionou pertencem a mais do que um cliente.</b>
                    <br/>
                    Para faturar vários serviços de uma só vez estes devem pertencer todos ao mesmo cliente.
                </p>
            @elseif(empty($customer))
                <h4 class="text-red bold"><i class="fas fa-exclamation-triangle"></i> Não é possível faturar os serviços selecionados.</h4>
                <p>
                    <b>Os serviços que selecionou aparentemente já foram faturados <br/>ou o cliente associado não existe.</b>
                </p>
            @else
                <div class="row row-5">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('type', 'Tipo Documento') }}
                            {{ Form::select('type', trans('admin/billing.types-list'), null,['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('api_key', 'Série a Usar') }}
                            {{ Form::select('api_key', $apiKeys, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="spacer-25"></div>
                        <div class="checkbox" style="margin: 5px 0 0 0;">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('draft', 1) }}
                                Criar como rascunho
                            </label>
                        </div>
                    </div>
                </div>
                <div id="invoice-data">
                    <div class="row row-5">
                        <div class="col-sm-3">
                            <div class="form-group is-required">
                                <label for="vat">NIF</label>
                                {{ Form::hidden('customer_id', $customer->id) }}
                                {{ Form::text('vat', $customer->vat, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-sm-9">
                            <div class="form-group">
                                {{ Form::label('name', 'Designação Social') }}
                                {{ Form::text('name', $customer->billing_name, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>
                    <div class="row row-5">
                        <div class="col-sm-4">
                            <div class="form-group is-required">
                                {{ Form::label('docdate', 'Data Documento') }}
                                {{ Form::text('docdate', $docDate, ['class' => 'form-control datepicker', 'required']) }}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{ Form::label('duedate', 'Data Vencimento') }}
                                {{ Form::text('duedate', $docLimitDate, ['class' => 'form-control datepicker']) }}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{ Form::label('docref', 'Referência') }}
                                {{ Form::text('docref', null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group m-b-0">
                        {{ Form::label('obs', 'Observações') }}
                        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 2]) }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($totalCustomers > 1 || empty($customer))
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>
@else
    <div class="modal-footer">
        <div class="pull-left w-70">
            <div class="input-group pull-left m-r-20" style="width: 280px">
                <div class="input-group-addon" data-toggle="tooltip" title="Ative esta opção para enviar e-mail ao cliente.">
                    <i class="fas fa-envelope"></i>
                    {{ Form::checkbox('send_email', 1, false) }}
                </div>
                {{ Form::text('email', $customer->billing_email, ['class' => 'form-control pull-left', 'placeholder' => 'E-mail do cliente']) }}
            </div>
            <div class="text-red pull-left m-l-15 m-t-5 m-b-0 modal-feedback with-100"></div>
        </div>
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A processar...">Gravar</button>
    </div>
@endif
{{ Form::close() }}

<script>
    $('.datepicker').datepicker(Init.datepicker());
    $('.select2').select2(Init.select2());

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-invoice').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTable.draw(); //update datatable
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
                $('#modal-remote-xl').modal('hide');
            } else {
                $('.form-invoice .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> ' + data.feedback);
            }

        }).error(function () {
            $('.form-invoice .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function(){
            $button.button('reset');
        })
    });

    $(document).on('change', '[name="tax[]"],[name="price[]"]', function () {
        var totalTax = totalNoTax = total = 0;
        $('[name="tax[]"]').each(function(index){
            if($(this).val() == '0') {
                totalNoTax+= parseFloat($('[name="price[]"]:eq('+index+')').val());
            } else {
                totalTax+= parseFloat($('[name="price[]"]:eq('+index+')').val());
            }
        })

        total = totalTax + totalNoTax

        $('[name="total_vat"]').val(totalTax.toFixed(2))
        $('[name="total_no_vat"]').val(totalNoTax.toFixed(2))
        $('[name="total_billed"]').val(total.toFixed(2))

    })
</script>