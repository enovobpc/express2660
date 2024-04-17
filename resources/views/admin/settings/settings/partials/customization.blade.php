<div class="box no-border">
    <div class="box-body p-t-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="alert bg-purple">
                    <h4 class="bold text-uppercase"><i class="fas fa-paint-brush"></i> Personalização</h4>
                    <p>
                        As configurações que aqui fizer terão efeitos apenas na sua conta pessoal ({{ Auth::user()->name }}).
                        <br/>
                        Nenhuma alteração irá afetar outros utilizadores ou configurações principais do sistema.
                    </p>
                </div>
            </div>
            <div class="col-sm-4">
                <h4 class="section-title">Som Notificação</h4>
                <table class="table table-condensed m-b-0" style="border-bottom: 1px solid #eee">
                    <tr>
                        <td>{{ Form::label('customization_notification_sound', 'Som notificação', ['class' => 'control-label']) }}</td>
                        <td class="w-50px">{{ Form::select('customization_notification_sound', $notificationSounds,   @Auth::user()->settings['notification_sound'] ?  @Auth::user()->settings['notification_sound'] : 'notification09', ['class' => 'select2'] ) }}</td>
                        <td class="w-1">
                            <button type="button" class="btn btn-sm btn-default btn-play-notification" data-toggle="tooltip" title="Clique para ouvir o som">
                                <i class="fas fa-volume-up"></i>
                            </button>
                        </td>
                    </tr>
                </table>
                <table class="table table-condensed">
                    <tr>
                        <td>{{ Form::label('customization_disable_notification_sound', 'Desligar som de notificação', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('customization_disable_notification_sound', 1,  @Auth::user()->settings['disable_notification_sound'], ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">
                <h4 class="section-title">Personalização de Estilo</h4>
                <table class="table table-condensed m-0">
                    <tr>
                        <td>{{ Form::label('customization_app_skin', 'Côr da aplicação', ['class' => 'control-label']) }}</td>
                        <td class="w-1">
                            <div class="{{ app_skin() }}">
                            <div class="skin-preview skin-master" data-current-skin="{{ @Auth::user()->settings['app_skin'] }}" style="height: 22px; width: 22px; margin: 6px -3px 0 0;"></div>
                            </div>
                        </td>
                        </td>
                        <td class="w-200px">{{ Form::select('customization_app_skin', [''=>'- Igual à definição geral -'] + trans('admin/global.skins'), @Auth::user()->settings['app_skin'], ['class' =>'form-control select2']) }}</td>
                    </tr>
                </table>
                <table class="w-100">
                    <tr>
                        <td>{{ Form::label('customization_fixed_menu', 'Fixar menu lateral (só para mim)', ['class' => 'control-label']) }}</td>
                        <td class="check">{{ Form::checkbox('customization_fixed_menu', 1, @Auth::user()->settings['fixed_menu'], ['class' => 'ios'] ) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-12">
                <h4 class="section-title"><i class="fas fa-envelope"></i> Mensagens E-mail</h4>
                {{ Form::label('email_signature', 'Assinatura e-mail personalizada') }}
                {{ Form::textarea('customization_email_signature', @Auth::user()->settings['email_signature'], ['class' =>'form-control', 'rows' => 4, 'placeholder' => 'Automático pelo sistema']) }}
                <p class="m-l-5">
                    <b>Códigos de texto</b> - Use os códigos no texto para os substituir pelo respetivo valor.<br/>
                    <span class="label label-default">:name</span> - Substitui pelo nome do utilizador<br/>
                    <span class="label label-default">:email</span> - Substitui pelo email do utilizador<br/>
                    <span class="label label-default">:phone</span> - Substitui pelo telefone do utilizador<br/>
                </p>
            </div>
            <div class="col-sm-12">
                <hr/>
                {{ Form::submit('Gravar', array('class' => 'btn btn-primary' ))}}
            </div>
        </div>
    </div>
</div>

