{{ Form::open(['route' => ['admin.refunds.customers.selected.update'], 'method' => 'post', 'files' => true]) }}
<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Editar reembolsos selecionados</h4>
</div>
<div class="modal-body">
    @if(@$customer)
        <div class="modal-alert bg-gray-light" style="display: none">
            <h4 class="pull-left">
                <small>IBAN Reembolso:</small>
                <span class="iban-lbl fw-500">{{ @$customer->iban ? $customer->iban : 'Não definido na ficha de cliente.' }}</span>
                <span class="iban-nospaces hide">{{ nospace(@$customer->iban) }}</span>
            </h4>
            <button type="button" class="btn btn-xs btn-default btn-iban-copy m-l-10"
                    data-toggle="copy-clipboard"
                    data-target="#modal-remote-lg .iban-nospaces"
                    data-feedback="IBAN copiado para a área de transferência.">
                <i class="fas fa-copy"></i> Copiar IBAN
            </button>
            <button type="button" class="btn btn-xs btn-default btn-iban-edit">
                <i class="fas fa-pencil-alt"></i> Editar IBAN
            </button>
            <div class="input-group input-group-sm input-iban-edit" style="width: 315px; float: left; margin: -4px 0 -10px 5px; display: none">
                {{ Form::text('iban', @$customer->iban, ['class' => 'form-control iban-input iban uppercase']) }}
                <span class="input-group-btn">
                <button class="btn btn-primary btn-iban-save" type="button">Gravar</button>
                <button class="btn btn-default btn-iban-cancel" type="button"><i class="fas fa-times"></i></button>
            </span>
            </div>
            <div class="clearfix"></div>
        </div>
    @else
    <div class="modal-alert bg-gray-light" style="display: none">
        <h4 class="pull-left">
            <small>IBAN Reembolso:</small>
            <span class="iban-lbl fw-500"></span>
            <span class="iban-nospaces hide"></span>
        </h4>
        <button type="button" class="btn btn-xs btn-default btn-iban-copy m-l-10"
                data-toggle="copy-clipboard"
                data-target=".iban-nospaces"
                data-feedback="IBAN copiado para a área de transferência.">
            <i class="fas fa-copy"></i> Copiar IBAN
        </button>
        <button type="button" class="btn btn-xs btn-default btn-iban-edit">
            <i class="fas fa-pencil-alt"></i> Editar IBAN
        </button>
        <div class="input-group input-group-sm input-iban-edit" style="width: 315px; float: left; margin: -4px 0 -10px 5px; display: none">
            {{ Form::text('iban', null, ['class' => 'form-control iban-input iban']) }}
            <span class="input-group-btn">
                            <button class="btn btn-primary btn-iban-save" type="button">Gravar</button>
                            <button class="btn btn-default btn-iban-cancel" type="button"><i class="fas fa-times"></i></button>
                        </span>
        </div>
        <div class="clearfix"></div>
    </div>
    @endif
    <div class="row row-5" style="    margin: -14px -15px 15px -15px;
    border-bottom: 1px solid #ddd;
    padding: 0 15px 10px;">
        <div class="col-sm-9">
            <h3 class="m-0"><small>Reembolso a: </small><br/>
                <span class="refund-name">{{ @$customer->name }}</span>
            </h3>
        </div>
        {{--<div class="col-sm-1 text-center">
            <h3 class="m-0"><small>Guias </small><br/>1</h3>
        </div>--}}
        <div class="col-sm-3 text-right">
            <h3 class="m-0"><small>Total (<span class="refund-counter">{{ @$count }}</span> envios) </small><br/><b class="text-blue"><span class="refund-total">{{ @$total ? money($total) : '' }}</span>{{ Setting::get('app_currency') }}</b></h3>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-6" style="padding-right: 30px;">
            <h4 class="text-uppercase m-t-0 fs-14 text-blue">Recebimento</h4>
            <div class="row row-5">
                <div class="col-sm-7">
                    <div class="form-group">
                        {{ Form::label('received_method', 'Forma de recebimento') }}
                        {{ Form::select('received_method', ['' => '- Não alterar -'] + trans('admin/refunds.payment-methods-list'), null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('received_date', 'Data') }}
                        <div class="input-group">
                            {{ Form::text('received_date', null, ['class' => 'form-control datepicker nospace', 'autocomplete' => 'field-1']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
            <h4 class="text-uppercase m-t-10 fs-14 text-blue">Reembolso ao Cliente</h4>
            <div class="row row-5">
                <div class="col-sm-7">
                    <div class="form-group">
                        {{ Form::label('payment_method', 'Forma de reembolso') }}
                        {{ Form::select('payment_method', ['' => '- Não alterar -'] + trans('admin/refunds.payment-methods-list'), null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('payment_date', 'Data') }}
                        <div class="input-group">
                            {{ Form::text('payment_date', null, ['class' => 'form-control datepicker nospace', 'autocomplete' => 'field-1']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-{{ (config('app.source') === 'invictacargo') ? '7' : '12' }}">
                    <div class="form-group m-b-0" id="upload">
                        {{ Form::label('attachment', 'Anexar comprovativo de reembolso', ['class' => 'control-label']) }}
                        <div class="fileinput fileinput-new input-group m-b-0" data-provides="fileinput">
                            <div class="form-control" data-trigger="fileinput">
                                <i class="fas fa-file fileinput-exists"></i>
                                <span class="fileinput-filename"></span>
                            </div>
                            <span class="input-group-addon btn btn-default btn-file">
                                <span class="fileinput-new">Selecionar</span>
                                <span class="fileinput-exists">Alterar</span>
                                <input type="file" name="attachment" data-file-format="jpeg,jpg,png,pdf">
                            </span>
                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                        </div>
                    </div>
                </div>

                @if (config('app.source') === 'invictacargo')
                    <div class="col-sm-5">
                        <div class="form-group">
                            {{ Form::label('operator', 'Motorista') }}
                            {{ Form::select('operator', ['' => ''] + $operators, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-sm-6">
            <h4 class="text-uppercase m-t-0 fs-14 text-blue">Notas e Observações</h4>
            <div class="form-group">
                {{ Form::label('customer_obs', 'Observações visiveis ao Cliente') }}
                {{ Form::textarea('customer_obs', null, ['class' => 'form-control', 'rows' => 5]) }}
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('obs', 'Observações Internas') }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options w-75">
        <div class="input-group input-email pull-left m-r-20" style="width: 280px" data-toggle="tooltip" title="">
            <div class="overflow"></div>
            <div class="input-group-addon" data-toggle="tooltip" title="Ative esta opção para enviar e-mail ao cliente.">
                <i class="fas fa-envelope"></i>
                {{ Form::checkbox('send_email', 1, false) }}
            </div>
            {{ Form::text('email', @$customer->refunds_email, ['class' => 'form-control pull-left email nospace lowercase', 'placeholder' => 'E-mail do cliente']) }}
        </div>
        <div class="pull-left">
            <p style="margin: 6px 8px 0 0;"><b>Imprimir</b></p>
        </div>
        <div class="checkbox">
            <label>
                {{ Form::checkbox('print_proof', 1) }}
                Comprovativo
            </label>
        </div>
        <div class="checkbox" style="margin-left: -8px; margin-top: 10px">
            <label>
                {{ Form::checkbox('print_summary', 1) }}
                Resumo
            </label>
        </div>
    </div>

    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar</button>
    </div>
</div>
{{ Form::hidden('save_iban') }}
{{ Form::hidden('multiple_customers') }}
@if(@$ids)
    {{ Form::hidden('ids', $ids) }}
@endif
{{ Form::close() }}
@if(@$ids)
    <script>
        $('.select2').select2(Init.select2())
        $('.datepicker').datepicker(Init.datepicker())
    </script>
@endif