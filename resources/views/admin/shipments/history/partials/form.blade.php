<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">@trans('Alterar estado dos envios')</h4>
</div>
<div class="modal-body p-10">
    <div class="row row-5">
        <div class="col-sm-9">
            <div class="form-group form-group-sm status-btn-group">
                {{ Form::label('status_id', 'Estado do pedido', ['class' => 'control-label']) }}
                <div class="clearfix"></div>
                @if(@$shipment->is_collection || Route::currentRouteName() == 'admin.pickups.index')
                    <div class="btn-group">
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="37">Atribuido</button>
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="10">Em recolha</button>
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="14">Realizada</button>
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="18">Falhado</button>
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="9">Incidência</button>
                    </div>
                @elseif(Setting::get('app_mode') == 'courier') {{-- Estafetas --}}
                <div class="btn-group">
                    <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="37">P. Estafeta</button>
                    <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="36">Recolhido</button>
                    <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="3">Transporte</button>
                    <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="5">Entregue</button>
                    <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="9">Incidência</button>
                </div>
                @elseif(Setting::get('app_mode') == 'cargo')
                    <div class="btn-group">
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="37">Atribuido</button>
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="10">Em Recolha</button>
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="17">Em Armazem</button>
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="3">Transporte</button>
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="5">Entregue</button>
                        {{--<button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="9">Incidência</button>--}}
                    </div>
                @else
                    <div class="btn-group">
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="10">Em Recolha</button>
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="3">Transporte</button>
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="4">Distribuição</button>
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="5">Entregue</button>
                        <button class="btn btn-sm btn-default" type="button" data-toogle="select-button" data-id="9">Incidência</button>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group form-group-sm is-required">
                {{ Form::label('status_id', 'Outros Estados', ['class' => 'control-label']) }}
                {{ Form::select('status_id',  ['' => ''] + $status, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('date', 'Data', ['class' => 'control-label']) }}
                <div class="input-group input-group-money">
                    {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('hour', 'Hora', ['class' => 'control-label']) }}
                {{ Form::time('hour', date('H:i'), ['class' => 'form-control hourpicker', 'required']) }}
            </div>
        </div>
        <div class="{{ hasModule('app_apk') ? 'col-sm-5' : 'col-sm-5' }}">
            <div class="form-group is-required">
                @if (@$shipment->is_pickup || Route::currentRouteName() == 'admin.pickups.index')
                {{ Form::label('pickup_operator_id', 'Motorista Recolha', ['class' => 'control-label']) }}
                {{ Form::select('pickup_operator_id', (Auth::user()->isAdmin() ? ['' => '', '-1' => 'Manter motorista atual', '1' => 'Administrador'] + $operators : ['' => '', '-1' => 'Manter operador atual'] + $operators),  NULL, ['class' => 'form-control select2', 'required']) }}
                @else
                {{ Form::label('operator_id', 'Motorista', ['class' => 'control-label']) }}
                {{ Form::select('operator_id', (Auth::user()->isAdmin() ? ['' => '', '-1' => 'Manter motorista atual', '1' => 'Administrador'] + $operators : ['' => '', '-1' => 'Manter operador atual'] + $operators),  NULL, ['class' => 'form-control select2', 'required']) }}
                @endif
            </div>
        </div>
        @if(hasModule('app_apk'))
        <div class="col-sm-2">

            <div class="form-group is-required m-t-15">
                <div class="checkbox pull-left m-t-10 m-b-0">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('notify_operator', 1, true) }}
                        Notificar
                    </label>
                    <i class="fas fa-info-circle" style="position: absolute;right: -15px;top: 4px;font-size: 12px;" data-toggle="tooltip" title="Envia um alerta para o telemóvel do motorista"></i>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="row row-5">
        <div class="form-delivery" style="display: none">
            <div class="col-sm-5">
                <div class="form-group">
                    {{ Form::label('receiver', 'Recebido por', ['class' => 'control-label']) }}
                    {{ Form::text('receiver', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-sm-7">
                <div class="form-group" id="upload">
                    {{ Form::label('attachment', 'Anexar Imagem POD', ['class' => 'control-label']) }}
                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="fas fa-file fileinput-exists"></i>
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">Selecionar</span>
                            <span class="fileinput-exists">Alterar</span>
                            <input type="file" name="attachment" data-file-format="jpeg,jpg,png">
                        </span>
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group m-b-5 is-required hide form-incidence">
                {{ Form::label('incidence_id', 'Motivo da Incidência', ['class' => 'control-label']) }}
                {{ Form::select('incidence_id', ['' => ''] + $incidences, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group m-b-5 is-required hide form-devolution">
                <div class="checkbox pull-left m-t-0">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('devolution', 1, true) }}
                        Ao gravar estado, criar envio de devolução
                    </label>
                </div>
            </div>
        </div>
        @if(in_array(Setting::get('app_mode'), ['cargo', 'freight']))

            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('vehicle', 'Viatura', ['class' => 'control-label']) }}
                    {{-- {{ Form::select('operator_id', (Auth::user()->isAdmin() ? ['' => '', '-1' => 'Manter operador atual', '1' => 'Administrador'] + $operators : ['' => '', '-1' => 'Manter operador atual'] + $operators), null, ['class' => 'form-control select2', 'required']) }} --}}
                    @if(Setting::get('shipments_vehicles_field_input'))
                        {{ Form::text('vehicle', null, ['class' => 'form-control']) }}
                    @else
                        {{ Form::select('vehicle', ['-1' => 'Manter viatura atual'] + ($vehicles ?? []), null, ['class' => 'form-control select2']) }}
                    @endif
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('trailer', 'Reboque', ['class' => 'control-label']) }}
                   @if(Setting::get('shipments_vehicles_field_input'))
                        {{ Form::text('trailer', null, ['class' => 'form-control']) }}
                    @else
                        {{ Form::select('trailer', ['-1' => 'Manter reboque atual'] + ($trailers ?? []), null, ['class' => 'form-control select2']) }}
                    @endif
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('city', 'Localidade/País', ['class' => 'control-label']) }}
                    {{ Form::text('city', null, ['class' => 'form-control']) }}
                </div>
                <span class="loading-distance" style="display: none"><i class="fas fa-spin fa-circle-notch"></i> A calcular tempo rota...</span>
            </div>
        @endif
        <div class="col-sm-12">
            <div class="form-group m-b-5">
                {{ Form::label('obs', 'Observações', ['class' => 'control-label']) }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="row row-5 text-left">
        <div class="col-sm-4">
            @if(!@$shipment->hide_checkbox_notifications)
                <label>Notificar Cliente</label><br/>
                @if(hasModule('history_notifications'))
                <div class="checkbox-inline">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('customer_email', 1, false) }}
                        <i class="fas fa-envelope"></i> E-mail
                    </label>
                </div>
                @else
                    <span data-toggle="tooltip" title="O plano contratado não permite a utilização desta ferramenta.">
                        <div class="checkbox-inline">
                            <label style="padding-left: 0; color: #ccc">
                                {{ Form::checkbox('customer_email', 1, false, ['disabled']) }}
                                <i class="fas fa-envelope"></i> E-mail
                            </label>
                        </div>
                    </span>
                @endif
                @if(hasModule('sms') && hasPermission('sms'))
                    <div class="checkbox-inline m-l-10" >
                        <label>
                            <span data-toggle="popover" data-placement="top" data-content="{!! smsTip(@$countSms) !!}" data-html="true">
                                {{ Form::checkbox('customer_sms', 1) }}
                                <i class="fas fa-mobile-alt"></i> SMS
                            </span>
                        </label>
                    </div>
                @else
                    <span data-toggle="tooltip" title="{{ !hasPermission('sms') ?  'Não tem permissão para envio de SMS' : 'O plano contratado não permite a utilização desta ferramenta.'}}">
                        <div class="checkbox-inline">
                            <label style="padding-left: 0; color: #ccc;">
                                {{ Form::checkbox('sender_sms', 1, false, ['disabled']) }}
                                <i class="fas fa-mobile-alt"></i> SMS
                            </label>
                        </div>
                    </span>
                @endif
            @endif
        </div>
        <div class="col-sm-4">
            @if(!@$shipment->hide_checkbox_notifications)
                <label>Notificar Destinatário</label><br/>
                @if(hasModule('history_notifications'))
                    <div class="checkbox-inline">
                        <label style="padding-left: 0">
                            {{ Form::checkbox('recipient_email', 1, false) }}
                            <i class="fas fa-envelope"></i> E-mail
                        </label>
                    </div>
                @else
                    <span data-toggle="tooltip" title="O plano contratado não permite a utilização desta ferramenta.">
                        <div class="checkbox-inline">
                            <label style="padding-left: 0; color: #ccc">
                                {{ Form::checkbox('customer_email', 1, false, ['disabled']) }}
                                <i class="fas fa-envelope"></i> E-mail
                            </label>
                        </div>
                    </span>
                @endif

                @if(hasModule('sms') && hasPermission('sms'))
                    <div class="checkbox-inline m-l-10" >
                        <label>
                            <span data-toggle="popover" data-placement="top" data-content="{!! smsTip(@$countSms) !!}" data-html="true">
                                {{ Form::checkbox('recipient_sms', 1) }}
                                <i class="fas fa-mobile-alt"></i> SMS
                            </span>
                        </label>
                    </div>
                @else
                    <span data-toggle="tooltip" title="{{ !hasPermission('sms') ?  'Não tem permissão para envio de SMS' : 'O plano contratado não permite a utilização desta ferramenta.'}}">
                        <div class="checkbox-inline">
                            <label style="padding-left: 0; color: #ccc;">
                                {{ Form::checkbox('recipient_sms', 1, false, ['disabled']) }}
                                <i class="fas fa-mobile-alt"></i> SMS
                            </label>
                        </div>
                    </span>
                @endif
            @endif
        </div>
        <div class="col-sm-4 text-right p-t-5">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            <button type="submit" class="btn btn-submit-status btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">Gravar</button>
        </div>
    </div>
</div>
