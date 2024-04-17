<div class="row row-10">
    <div class="col-sm-2">
        <img src="{{ asset('assets/img/default/logo/logo.svg') }}" class="w-100 m-t-20"/>
    </div>
    <div class="col-sm-4">
        <h4 class="m-t-10 m-b-3 bold">Versão {{ $version }}</h4>
        <span>{{ $versionDate }}</span>
    </div>
    <div class="col-sm-6">
        <div class="row row-5">
            <div class="col-sm-5">
                <div class="form-group">
                    {{ Form::label('plan_version', 'Plano Contratado') }}
                    {{ Form::select('plan_version', ['' => $planVersion] + $versionsList, null, ['class' => 'form-control select2']) }}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group is-required">
                    {{ Form::label('license', 'Licença') }}
                    {{ Form::select('license', ['1' => 'Ativa', '0' => 'Suspensa'], $licenseStatus, ['class' => 'form-control select2', 'required']) }}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    {{ Form::label('quota', 'Espaço') }}
                    {{ Form::select('quota', $spaces, \App\Models\CacheSetting::get('quota'), ['class' => 'form-control select2']) }}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {{ Form::label('emails_total', 'Contas E-mail') }}
                    {{ Form::select('emails_total', $emails, \App\Models\CacheSetting::get('emails_total'), ['class' => 'form-control select2', 'placeholder' => 'Ilimitado']) }}
                </div>
            </div>
        </div>
    </div>
</div>
<hr style="margin-top: 0; margin-bottom: 10px"/>
<div class="row row-10">
    <div class="col-sm-6">
        <h4 class="section-title text-blue"><i class="fas fa-bug"></i> Registo de Erros</h4>
        <table class="table table-condensed">
            <tr>
                <td>{{ Form::label('debug_mode', 'Debug da Aplicação', ['class' => 'control-label']) }}</td>
                <td class="check">{{ Form::checkbox('debug_mode', 1, Setting::get('debug_mode'), ['class' => 'ios'] ) }}</td>
            </tr>
            <tr>
                <td>{{ Form::label('api_debug_mode', 'Debug chamadas API', ['class' => 'control-label']) }}</td>
                <td class="check">{{ Form::checkbox('api_debug_mode', 1, Setting::get('api_debug_mode'), ['class' => 'ios'] ) }}</td>
            </tr>
            <tr>
                <td>{{ Form::label('error_log_email_active', 'Enviar log por e-mail', ['class' => 'control-label']) }}</td>
                <td class="check">{{ Form::checkbox('error_log_email_active', 1, Setting::get('error_log_email_active'), ['class' => 'ios'] ) }}</td>
            </tr>
            <tr>
                <td style="vertical-align: middle">{{ Form::label('error_log_email', 'E-mail para notificação', ['class' => 'control-label']) }}</td>
                <td class="w-45">{{ Form::text('error_log_email', Setting::get('error_log_email'), ['class' =>'form-control input-sm']) }}</td>
            </tr>
            <tr>
                <td style="vertical-align: middle">{{ Form::label('debug_ignore_ip', 'Ignorar debug para os IP\'s', ['class' => 'control-label']) }}</td>
                <td>{{ Form::text('debug_ignore_ip', Setting::get('debug_ignore_ip', client_ip()), ['class' =>'form-control input-sm', 'placeholder' => client_ip()]) }}</td>
            </tr>
        </table>
    </div>
    <div class="col-sm-6">
        <h4 class="section-title text-blue"><i class="fas fa-wrench"></i> Sistema em Manutenção</h4>
        <table class="table table-condensed">
            <tr>
                <td>{{ Form::label('maintenance_mode', 'Sistema em Manutenção', ['class' => 'control-label']) }}</td>
                <td class="check">{{ Form::checkbox('maintenance_mode', 1, Setting::get('maintenance_mode'), ['class' => 'ios'] ) }}</td>
            </tr>
            <tr>
                <td style="vertical-align: middle">{{ Form::label('maintenance_time', 'Tempo previsto manutenção', ['class' => 'control-label']) }}</td>
                <td class="w-30">
                    <div class="input-group">
                        {{ Form::text('maintenance_time', Setting::get('maintenance_time'), ['class' =>'form-control input-sm']) }}
                        <div class="input-group-addon">min</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: middle">{{ Form::label('maintenance_ignore_ip', 'Ignorar para os IP\'s', ['class' => 'control-label']) }}</td>
                <td>{{ Form::text('maintenance_ignore_ip', Setting::get('maintenance_ignore_ip', client_ip()), ['class' =>'form-control input-sm', 'placeholder' => client_ip()]) }}</td>
            </tr>
        </table>
        <h4 class="section-title text-blue"><i class="fas fa-tachometer-alt"></i> Cache de sistema</h4>
        <table class="table table-condensed">
            <tr>
                <td style="vertical-align: middle">
                 Core Settings: <i class="settings-last-sync">{{ \App\Models\CacheSetting::getLastUpdate() }}</i>
                </td>
                <td style="vertical-align: middle">
                    <button type="button" class="btn btn-xs btn-default pull-right btn-sync-settings" data-loading-text="<i class='fas fa-spin fa-sync-alt'></i> A sincronizar..." style="margin: -2px -5px -3px; padding: 0 5px;">
                        <i class="fas fa-sync-alt"></i> Sincronizar Agora
                    </button>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: middle">
                    <label for="cache_size" class="control-label">
                        <i class="fas fa-spin fa-circle-notch"></i> A calcular espaço...
                    </label>
                </td>
                <td style="vertical-align: middle">
                    <button type="button" class="btn btn-xs btn-default pull-right btn-clean-storage" style="margin: -2px -5px -3px; padding: 0 5px;">
                        <i class="fas fa-broom"></i> Limpar Dados
                    </button>
                </td>
            </tr>
           
        </table>
    </div>
</div>