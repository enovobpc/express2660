{{ Form::open($formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Emitir Guia Transporte AT</h4>
</div>
<div class="modal-body">
    <div style="margin: -15px -15px 15px;
    padding: 5px 15px;
    background: #eee;
    border-bottom: 1px solid #ddd;">
        <div class="row">
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="m-b-0 bold">
                            <small style="font-weight: normal">Local Carga</small><br/>
                            {{ $shipment->sender_address }}<br/>
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}<br/>
                            {{ trans('country.'.$shipment->sender_country) }}
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <p class="m-b-0 bold">
                            <small style="font-weight: normal">Local Descarga</small><br/>
                            {{ $shipment->recipient_address }}<br/>
                            {{ $shipment->recipient_zip_code }} {{ $shipment->sender_city }}<br/>
                            {{ trans('country.'.$shipment->recipient_country) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="m-b-0">
                            <small>Dados Cliente</small><br/>
                            NIF: <b>{{ @$shipment->customer->vat }}</b><br/>
                            <b>{{ @$shipment->customer->billing_name }}</b><br/>
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <p class="m-b-0">
                            <small>Data Carga</small><br/>
                            <b>{{ $shipment->shipping_date->format('Y-m-d H:i') }}</b><br/>
                            <small>Data Descarga</small><br/>
                            <b>{{ $shipment->delivery_date->format('Y-m-d H:i') }}</b>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-8">
            <h4 class="m-t-0 text-blue">Artigos do transporte</h4>
            <div style="min-height: 170px; border-radius: 3px; overflow-y: auto">
                <table class="table table-condensed">
                    <tr>
                        <th class="bg-gray">Artigo</th>
                        <th class="bg-gray w-50px">Qtd</th>
                        <th class="bg-gray w-80px">Valor</th>
                        <th class="bg-gray w-80px">IVA</th>
                    </tr>
                    @foreach($lines as $key => $line)
                        <tr>
                            <td style="vertical-align: top; padding: 0; border: none">
                                <div class="input-group input-description w-100" style="position: relative; margin-bottom: -1px">
                                    {{ Form::text('line['.$key.'][description]', @$line['description'], ['class' => 'form-control input-description']) }}
                                </div>
                            </td>
                            <td style="vertical-align: top; padding: 0; border: none">
                                {{ Form::text('line['.$key.'][qty]', @$line['qty'], ['class' => 'form-control text-center input-qty nospace decimal', 'style' => 'margin-bottom: -1px']) }}
                            </td>
                            <td style="vertical-align: top; padding: 0; border: none">
                                <div class="input-group" style="margin-bottom: -1px">
                                    {{ Form::text('line['.$key.'][price]', number(@$line['price']), ['class' => 'form-control input-price nospace decimal', 'style' => 'border-right:0']) }}
                                    <div class="input-group-addon" style="padding: 5px; border-left: 0">{{ Setting::get('app_currency') }}</div>
                                </div>
                            </td>
                            <td style="vertical-align: top; padding: 0;  border: none;">
                                <div class="vat-input" style="margin-bottom: -1px">
                                    {{ Form::select('line['.$key.'][tax_rate]', $vatTaxes, @$exemption, ['class' => 'form-control select2 tax-rate']) }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
<!--            <div class="form-group is-required m-t-15">
                {{ Form::label('obs', 'Observações') }}
                {{ Form::text('obs', $shipment->obs, ['class' => 'form-control']) }}
            </div>-->
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required m-t-8">
                {{ Form::label('reference', 'Referência') }}
                {{ Form::text('reference', 'TRK'.$shipment->tracking_code, ['class' => 'form-control', 'required']) }}
            </div>
            <div class="form-group is-required">
                {{ Form::label('api_key', 'Série') }}
                {{ Form::select('api_key', $apiKeys, null, ['class' => 'form-control select2', 'required']) }}
            </div>
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::label('docdate', 'Data Emissão Doc.') }}
                        <div class="input-group">
                            {{ Form::text('docdate', date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group is-required m-b-0">
                        {{ Form::label('duedate', 'Data Validade Doc.') }}
                        <div class="input-group">
                            {{ Form::text('duedate', $dueDate, ['class' => 'form-control datepicker', 'required']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
        <p class="text-blue m-t-5">
            <i class="fas fa-info-circle"></i> Esta operação é irreversível. Ao emitir, o documento será comunicado à AT.
        </p>
    </div>
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="button"
                class="btn btn-success btn-submit"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A emitir...">
                Emitir Guia AT
        </button>
    </div>
</div>
{{ Form::hidden('doc_type', 'transport-guide') }}
{{ Form::close() }}

<style>,
    .input-qty,
    .input-price,
    .tax-rate {
        border-left: none;
    }
    .input-price,
    td .input-group-addon {
        border-right: none;
    }
</style>
<script>

    $('.modal .datepicker').datepicker(Init.datepicker());
    $('.modal .select2').select2(Init.select2());

    /**
     * Destroy invoice
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.modal .btn-submit').on('click', function(e){
        e.preventDefault();

        var $form = $(this).closest('form');
        var $submitBtn = $(this);


        var hasEmptyValues = false;
        $('.input-price').each(function($q){
            if($(this).val() == '0.00' || $(this).val() == '') {
                hasEmptyValues = true;
            }
        });


        if(hasEmptyValues) {
            $submitBtn.button('reset');
            Growl.error('Não pode emitir a guia porque existem artigos com valor 0,00€.')
        } else {

            $submitBtn.button('loading');

            $.ajax({
                url: $form.attr('action'),
                data: $form.serialize(),
                type: 'POST',
                success: function (data) {
                    if (data.result) {
                        Growl.success(data.feedback);
                        $('#modal-remote-lg').modal('hide');
                        oTable.draw();
                    } else {
                        Growl.error(data.feedback);
                    }
                }
            }).fail(function () {
                Growl.error500();
            }).always(function () {
                $submitBtn.button('reset');
            });
        }
    });


</script>