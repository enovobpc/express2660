<div class="row row-10">
    <div class="col-sm-6">
        <h4 class="bold m-t-0 text-blue">1. CONFIGURAÇÃO BASE</h4>
        <div class="row row-5">
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('version', 'Versão Contratada') }}
                    {{ Form::select('version', ['' => ''] + $versionsList, null, ['class' => 'form-control select2', 'required']) }}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('app_mode', 'Modo Aplicação') }}
                    {{ Form::select('app_mode', trans('admin/global.app-modes'), Setting::get('app_mode'), ['class' => 'form-control select2', 'required']) }}
                </div>
            </div>
            <div class="col-sm-4">
                {{ Form::label('app_skin', 'Côr Aplicação') }}
                <table class="table table-condensed m-0 no-border" style="margin-top: -5px">
                    <tr>
                        <td class="w-1">
                            <div class="{{ Setting::get('app_skin') }}">
                                <div class="skin-preview skin-master" data-current-skin="{{ Setting::get('app_skin') }}" style="height: 22px; width: 22px; margin: 6px -3px 0 0;"></div>
                            </div>
                        </td>
                        <td class="w-200px">{{ Form::select('app_skin', trans('admin/global.skins'), Setting::get('app_skin'), ['class' =>'form-control select2']) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <ol>
            <li>Conferir após gravação se há algum módulo adicional a ativar</li>
            <li>Conferir após gravação se há alguma configuração especial</li>
        </ol>
    </div>
    <div class="col-sm-6">
        <h4 class="bold m-t-0 text-blue">2. ACESSO CLIENTE</h4>
        <div class="row row-5">
            <div class="col-sm-4">
                <div class="form-group is-required">
                    {{ Form::label('user_name', 'Nome pessoa') }}
                    {{ Form::text('user_name', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
            <div class="col-sm-5">
                <div class="form-group is-required">
                    {{ Form::label('user_email', 'E-mail') }}
                    {{ Form::text('user_email', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group is-required">
                    {{ Form::label('password', 'Password') }}
                    {{ Form::text('password', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
        </div>
        <ol>
            <li>Usar só o nome do gerente. Não usar "Geral" ou "Nome Empresa"</li>
            <li>Usar o e-mail geral da empresa</li>
            <li>Password: primeironome#NOMECLIENTE (ex: nuno#ASFALTOLARGO)</li>
        </ol>
    </div>
</div>
{{ Form::hidden('delete_photo') }}


