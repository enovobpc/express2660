<div class="box no-border">
    <div class="box-body">
        @if (empty($user->password) && hasModule('human_resources'))
            <div class="alert bg-yellow">
                <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="alert-message">
                    <h4>@trans('Sem acesso à área de gestão.')</h4>
                    <p>@trans('Este colaborador ainda não tem acesso à área de gestão. Complete os dados abaixo e grave de seguida para atribuir uma conta.')</p>
                </div>
            </div>
        @endif

        {{-- @if ($user->exists)
                <h4 class="form-divider">Último acesso</h4>
                <table class='table table-condensed m-0'>
                    <tr>
                        <td>Data</td>
                        <td>
                            {{ $user->last_login ? $user->last_login : 'Nunca' }}
                            @if ($user->last_login)
                                <br/>
                                <small class="italic text-muted">{{ 'Há ' . timeElapsedString($user->last_login) }}</small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>IP</td>
                        <td>{{ $user->ip }}</td>
                    </tr>
                    <tr>
                        <td>País</td>
                        <td class="ip-country"><i class='fas fa-spin fa-circle-notch'></i></td>
                    </tr>
                    <tr>
                        <td>Localidade</td>
                        <td class="ip-city"><i class='fas fa-spin fa-circle-notch'></i></td>
                    </tr>
                    <tr>
                        <td style='width: 80px'>Cód. Postal</td>
                        <td class="ip-postal-code"><i class='fas fa-spin fa-circle-notch'></i></td>
                    </tr>
                    <tr>
                        <td>ISP</td>
                        <td class='ip-isp'><i class='fas fa-spin fa-circle-notch'></i></td>
                    </tr>
                </table>
            @endif --}}

        {{-- @if (Auth::user()->isAdmin())
                <tr>
                    <td>
                        <p class="form-control-static" style="border: none; padding-left: 0;">
                            Utilizador Global <i class="fas fa-info-circle" data-toggle="tooltip" title="Torna este utilizador possível de entrar em qualquer plataforma. Ativar apenas para administradores."></i>
                        </p>
                    </td>
                    <td style="width: 120px">
                        {{ Form::select('source', [config('app.source') => 'Não', '' => 'Sim'], $user->exists ? null : config('app.source'), array('class' =>'form-control select2')) }}
                    </td>
                </tr>
            @endif --}}

        {{ Form::model($user, $loginFormOptions) }}
        <div class="col-sm-2">
            {{ Form::label('image', __('Fotografia'), ['class' => 'form-label']) }}<br />
            <div class="fileinput {{ $user->filepath ? 'fileinput-exists' : 'fileinput-new' }}"
                data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    <img src="{{ asset('assets/img/default/avatar.png') }}" class="img-responsive">
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail">
                    @if ($user->filepath)
                        <img src="{{ asset($user->getCroppa(200, 200)) }}"
                            onerror="this.src = '{{ img_broken(true) }}'" class="img-responsive">
                    @endif
                </div>
                <div>
                    <span class="btn btn-default btn-block btn-sm btn-file">
                        <span class="fileinput-new">@trans('Procurar...')</span>
                        <span class="fileinput-exists"><i class="fas fa-sync-alt"></i> @trans('Alterar')</span>
                        <input type="file" name="image">
                    </span>
                    <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                        <i class="fas fa-close"></i> @trans('Remover')
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-10">
            <div class="row row-5">
                <div class="col-sm-8" style="padding-left: 25px">
                    <h4 class="form-divider no-border" style="margin-top: -8px">@trans('Dados de acesso')</h4>
                    <div class="row row-5">
                        <div class="col-sm-2">
                            <div class="form-group is-required">
                                {{ Form::label('code', __('Código')) }}
                                {{ Form::text('code', null, ['class' => 'form-control', 'maxlength' => 6, 'required']) }}
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                {{ Form::label('code_abbrv', __('Cod. Abrv.')) }}
                                {{ Form::text('code_abbrv', null, ['class' => 'form-control', 'maxlength' => 6]) }}
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group is-required">
                                {{ Form::label('name', __('Nome a apresentar na aplicação'), ['data-content' => '']) }}
                                {{ Form::text('name', !$user->password ? split_name($user->name) : null, ['class' => 'form-control', 'maxlength' => 30, 'required']) }}
                            </div>
                        </div>
                    </div>

                    <div class="row row-5">
                        <div class="{{ hasModule('human_resources') ? 'col-md-12' : 'col-md-9' }}">
                            <div class="form-group is-required">
                                {{ Form::label('email', __('E-mail de acesso')) }}
                                {{ Form::text('email', empty($user->email) ? $user->professional_email : null, ['class' => 'form-control nospace lowercase','required' => true]) }}
                            </div>
                        </div>
                        @if (!hasModule('human_resources'))
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{ Form::label('professional_mobile', __('T. Empresa')) }}
                                    {{ Form::text('professional_mobile', null, ['class' => 'form-control phone']) }}
                                </div>
                            </div>
                        @endif
                    </div>

                    @if (empty($user->password))
                        <div class="form-group is-required m-0">
                            {{ Form::label('password', __('Palavra-passe')) }}
                            <div class="input-group input-group">
                                {{ Form::text('password', str_random(8), ['class' => 'form-control nospace', 'required' => true]) }}
                                <span class="input-group-btn">
                                    <button class="btn btn-default btn-flat" id="random-password" type="button">
                                        <i class="fas fa-sync-alt"></i> @trans('Gerar outra')
                                    </button>
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="row row-5">
                            <div class="col-sm-6">
                                <div class="form-group m-b-0">
                                    {{ Form::label('password', __('Nova palavra-passe')) }}
                                    {{ Form::password('password', ['class' => 'form-control nospace','autofill' => false,'autocomplete' => 'off','placeholder' => 'Vazio para não alterar']) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group m-b-0">
                                    {{ Form::label('password_confirmation', __('Confirmar palavra-passe')) }}
                                    {{ Form::password('password_confirmation', ['class' => 'form-control nospace','autofill' => false,'autocomplete' => 'off']) }}
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <div class="checkbox m-b-15 checkbox-send-password"
                            style="{{ $user->password ? 'display:none' : 'display:block' }}">
                            <label style="padding-left: 0 !important;">
                                {{ Form::checkbox('send_email', 1, $user->password ? false : true) }}
                                <i class="fas fa-envelope"></i> @trans('Ao gravar, enviar email com a palavra-passe')
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4" style="padding-left: 30px">
                    <h4 class="form-divider no-border" style="margin-top: -8px"><i class="fas fa-lock"></i>
                        @trans('Permissões')</h4>
                    <div class="form-group is-required">
                        {{ Form::label('role_id', __('Perfíl da Conta')) }}
                        @if (Auth::user()->perm('admin_roles'))
                            <a href="{{ route('admin.roles.index') }}" class="pull-right m-l-10"
                                target="_blank">@trans('Gerir Perfis')</a>
                        @endif
                        @if (Auth::user()->id == $user->id)
                            {{ Form::select('role_id',[@$user->roles->first()->id => @$user->roles->first()->display_name],@$user->roles->first()->id,['class' => 'form-control select2 roles-selection', 'required', 'disabled']) }}
                        @else
                            {{ Form::select('role_id', ['' => ''] + $roles, @$user->roles->first()->id, ['class' => 'form-control select2 roles-selection','required']) }}
                        @endif
                    </div>

                    {{ Form::label('role_id', __('Agências autorizadas')) }}
                    @if ($agencies->count() >= 5)
                        <div style="max-height: 102px;overflow: scroll;border: 1px solid #ddd;padding: 0 8px;">
                    @endif
                    @foreach ($agencies as $agency)
                        <div class="checkbox m-t-5 m-b-8"
                            style="{{ $agency->source != config('app.source') ? 'display:none' : '' }}">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('agencies[]', $agency->id, null, [Auth::user()->id == $user->id ? 'disabled' : '']) }}
                                <span class="label"
                                    style="background: {{ $agency->color }}">{{ $agency->code }}</span>
                                {{ $agency->print_name }}
                            </label>
                        </div>
                    @endforeach
                    @if ($agencies->count() >= 7)
                </div>
                @endif
            </div>
        </div>
        <div class="row row-5">
            <div class="col-sm-12">
                <div class="col-sm-12" style="padding-left: 25px">
                    <h4 class="form-divider"><i class="fas fa-cog"></i> @trans('Opções da conta')</h4>
                </div>
                <div class="col-sm-4" style="padding-left: 25px">
                    <table class="table table-condensed m-0">
                        <tr>
                            <td>
                                <p class="form-control-static" style="border: none; padding-left: 0; margin: 0">
                                    <i class="fas fa-fw fa-check-circle"></i> @trans('Colaborador ativo')
                                </p>
                            </td>
                            <td class="check">
                                {{ Form::checkbox('active', 1, $user->exists ? null : 1, ['class' => 'ios',Auth::user()->id == $user->id ? 'disabled' : '']) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="form-control-static" style="border: none; padding-left: 0; margin: 0">
                                    <i class="fas fa-fw fa-laptop"></i> @trans('Entrar na Área Gestão')
                                </p>
                            </td>
                            <td class="check">
                                {{ Form::checkbox('login_admin', 1, $user->exists ? null : 1, ['class' => 'ios',$user->is_operator || Auth::user()->id == $user->id || !$user->active ? 'disabled' : '']) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="form-control-static" style="border: none; padding-left: 0; margin: 0">
                                    <i class="fas fa-fw fa-mobile-alt"></i> @trans('Entrar na App Motorista')
                                    {!! tip(__('Esta opção considera o utilizador como motorista.')) !!}
                                </p>
                            </td>
                            <td class="check">
                                {{ Form::checkbox('login_app', 1, $user->login_app && !$user->active ? false : null, ['class' => 'ios',!$user->active || Auth::user()->id == $user->id ? 'disabled' : '']) }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-4" style="padding-left: 25px">
                    <table class="table table-condensed m-0">
                        <tr>
                            <td>
                                <p class="form-control-static" style="border: none; padding-left: 0; margin: 0">
                                    @trans('Pode editar preços dos serviços')
                                </p>
                            </td>
                            <td class="check">
                                {{ Form::checkbox('allowed_actions[edit_prices]',1,$user->is_operator ? 0 : @$user->allowed_actions['edit_prices'],[Auth::user()->id == $user->id ? 'disabled' : '', 'class' => 'ios', $user->is_operator ? 'disabled' : '']) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="form-control-static" style="border: none; padding-left: 0; margin: 0">
                                    @trans('Pode editar serviços bloqueados')
                                </p>
                            </td>
                            <td class="check">
                                {{ Form::checkbox('allowed_actions[edit_blocked]',1,$user->is_operator ? 0 : @$user->allowed_actions['edit_blocked'],[Auth::user()->id == $user->id ? 'disabled' : '', 'class' => 'ios', $user->is_operator ? 'disabled' : '']) }}
                            </td>
                        </tr>
                        @if (hasModule('budgets'))
                            <tr>
                                <td>
                                    <p class="form-control-static" style="border: none; padding-left: 0; margin: 0">
                                        @trans('Pode ver orçamento a fornecedores')
                                    </p>
                                </td>
                                <td class="check">
                                    {{ Form::checkbox('allowed_actions[show_budget_providers]',1,$user->is_operator ? 0 : @$user->allowed_actions['show_budget_providers'],[Auth::user()->id == $user->id ? 'disabled' : '', 'class' => 'ios', $user->is_operator ? 'disabled' : '']) }}
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
                <div class="col-sm-4" style="padding-left: 25px;">
                    <div class="row row-5" style="margin-top: -20px;">
                        <div class="col-sm-12">
                            <div class="form-group">
                                {{ Form::label('locale', __('Idioma')) }}
                                {{ Form::select('locale', trans('locales'), $user->password ? null : Setting::get('app_locale'), ['class' => 'form-control select2country']) }}
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group">
                                {{ Form::label('provider_id', __('Associar Fornec.')) }}
                                {!! tip(__('Se este motorista é um subcontratado e não pertence aos funcionários da sua empresa, escolha qual o fornecedor a que ele pertence.')) !!}
                                {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                {{ Form::label('comission_percent', __('Comissão')) }}
                                @if (hasModule('prospection') || Setting::get('app_mode') == 'courier')
                                    <div class="input-group">
                                        {{ Form::text('comission_percent', null, ['class' => 'form-control decimal', 'maxlength' => 5]) }}
                                        <div class="input-group-addon">%</div>
                                    </div>
                                @else
                                    <div class="input-group" data-toggle="tooltip"
                                        title="@trans('Não possui o módulo de gestão comercial ativo.')">
                                        {{ Form::text('cpct', null, ['class' => 'form-control', 'disabled']) }}
                                        <div class="input-group-addon">%</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <hr />
        @if (!$user->isAdmin())
            {{ Form::hidden('source', config('app.source')) }}
        @endif
        <button class="btn btn-primary">@trans('Gravar')</button>
    </div>
    @if (Auth::user()->id == $user->id)
        {{ Form::hidden('active', 0) }}
        {{ Form::hidden('location_enabled') }}
    @endif
    {{ Form::hidden('delete_photo') }}
    {{ Form::close() }}
</div>
</div>
