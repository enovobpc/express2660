@if (!hasModule('account'))
    @include('admin.partials.denied_message')
@else
    <div class="box no-border">
        <div class="box-body">
            @if (empty($customer->password))
                <div class="alert bg-yellow">
                    <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="alert-message">
                        <h4>@trans('Sem acesso à área de cliente.')</h4>
                        <p>@trans('Este cliente ainda não tem acesso à área de cliente. Complete os dados abaixo e grave de
                            seguida para atribuir uma conta.')</p>
                    </div>
                </div>
            @endif

            {{ Form::model($customer, ['route' => ['admin.customers.login', $customer->id], 'files' => true, 'class' => 'form-account-login']) }}
            <div class="col-sm-2 col-md-3 col-lg-2">
                {{ Form::label('image', __('Imagem/Logótipo'), ['class' => 'form-label']) }}<br />
                <div class="fileinput {{ $customer->filepath ? 'fileinput-exists' : 'fileinput-new' }}"
                    data-provides="fileinput">
                    <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                        <img src="{{ asset('assets/img/default/default.thumb.png') }}">
                    </div>
                    <div class="fileinput-preview fileinput-exists thumbnail"
                        style="max-width: 150px; max-height: 150px;">
                        @if ($customer->filepath)
                            <img src="{{ asset($customer->getCroppa(200)) }}"
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
                <h4 class="form-divider">@trans('Último acesso')</h4>
                <p>
                    {{ $customer->last_login ? $customer->last_login : 'Nunca' }}
                    @if ($customer->last_login)
                        <br />
                        <small
                            class="italic text-muted">{{ 'Há ' . timeElapsedString($customer->last_login) }}</small>
                    @endif
                </p>
                @if ($customer->last_login)
                    <p>
                        <?php
                        $html = "<div class='text-center'>" . $customer->last_login . '</div>';
                        $html .= "<table class='table table-condensed m-0'>";
                        $html .= '<tr>';
                        $html .= '<td>País</td>';
                        $html .= "<td class='ip-country'><i class='fas fa-spin fa-circle-notch'></i></td>";
                        $html .= '</tr><tr>';
                        $html .= '<td>Localidade</td>';
                        $html .= "<td class='ip-city'><i class='fas fa-spin fa-circle-notch'></i></td>";
                        $html .= '</tr><tr>';
                        $html .= "<td style='width: 80px'>Cód. Postal</td>";
                        $html .= "<td class='ip-postal-code'><i class='fas fa-spin fa-circle-notch'></i></td>";
                        $html .= '</tr><tr>';
                        $html .= '<td>ISP</td>';
                        $html .= "<td class='ip-isp'><i class='fas fa-spin fa-circle-notch'></i></td>";
                        $html .= '</tr>';
                        $html .= '</table>';
                        ?>
                    <div class="cursor-pointer ip-localtion-popup" data-ip="{{ $customer->ip }}"
                        data-time="{{ $customer->last_login }}" data-loaded="0" data-html="true" data-toggle="popover"
                        data-trigger="hover" data-placement="left" data-title="Histórico do último acesso"
                        data-content="{!! $html !!}" data-placement="top">
                        @trans('IP:') {{ $customer->ip }} <i class="fas fa-external-link-square-alt"></i>
                    </div>
                    </p>
                @endif


                {{-- <table class='table table-condensed m-0'>
                    <tr>
                        <td>Data</td>
                        <td>
                            {{ $customer->last_login ? $customer->last_login : 'Nunca' }}
                            @if ($customer->last_login)
                                <br/>
                                <small class="italic text-muted">{{ 'Há ' . timeElapsedString($customer->last_login) }}</small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>IP</td>
                        <td>{{ $customer->ip }}</td>
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
                </table> --}}
            </div>
            <div class="col-sm-10 col-md-9 col-lg-10">
                <div class="row row-5">
                    <div class="col-sm-6 col-lg-6">
                        <div class="form-group is-required">
                            {{ Form::label('display_name', __('Nome da área de cliente')) }}
                            {{ Form::text('display_name', empty($customer->display_name) ? trim(substr($customer->name, 0, 20)) : null, ['class' => 'form-control','required' => true,'maxlength' => 20]) }}
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-12">
                                <div class="form-group is-required">
                                    {{ Form::label('email', __('E-mail')) }}
                                    {{ Form::text('email', empty($customer->email) ? $customer->contact_email : null, ['class' => 'form-control email nospace lowercase','required' => true]) }}
                                </div>
                            </div>
                            @if (empty($customer->password))
                                <div class="col-sm-12">
                                    <div class="form-group is-required m-b-5">
                                        {{ Form::label('password', __('Palavra-passe')) }}
                                        <div class="input-group input-group">
                                            {{ Form::text('password', str_random(8), ['class' => 'form-control nospace', 'required' => true]) }}
                                            <span class="input-group-btn">
                                                <button class="btn btn-default btn-flat" id="random-password"
                                                    type="button">
                                                    <i class="fas fa-sync-alt"></i> @trans('Gerar outra')
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-sm-6">
                                    <div class="form-group m-b-0">
                                        {{ Form::label('password', __('Nova palavra-passe')) }}
                                        {{ Form::password('password', ['class' => 'form-control nospace','autocomplete' => 'off','placeholder' => 'Deixar vazio para não alterar']) }}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group m-b-0">
                                        {{ Form::label('password_confirmation', __('Confirmar palavra-passe')) }}
                                        {{ Form::password('password_confirmation', ['class' => 'form-control nospace', 'autocomplete' => 'off']) }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <div class="checkbox m-b-15 checkbox-send-password"
                                style="{{ $customer->password ? 'display:none' : 'display:block' }}">
                                <label style="padding-left: 0 !important;">
                                    {{ Form::checkbox('send_email', 1, $customer->password ? false : true) }}
                                    <i class="fas fa-envelope"></i> @trans('Ao gravar, enviar email com a palavra-passe')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-5 col-lg-offset-1">
                        <h4 class="form-divider no-border" style="margin-top: 0">@trans('Opções de acesso')</h4>
                        <table class="table table-condensed m-0">
                            <tr>
                                <td>
                                    <p class="form-control-static" style="border: none; padding-left: 0;">
                                        @trans('Bloquear acesso à área de cliente') {!! tip(__('Impede o cliente de iniciar sessão na área de cliente.')) !!}
                                    </p>
                                </td>
                                <td class="check">
                                    {{ Form::checkbox('active', 1, $customer->active ? 0 : 1, ['class' => 'ios']) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="form-control-static" style="border: none; padding-left: 0;">
                                        @trans('Ocultar envios anteriores à Área de Cliente') {!! tip(__('Esta opção impede que o cliente veja o histórico de envios e faturação anterior à data em que lhe foi dado acesso à área de cliente.')) !!}
                                    </p>
                                </td>
                                <td class="check">
                                    {{ Form::checkbox('hide_old_shipments', 1, $customer->password ? null : 1, ['class' => 'ios']) }}
                                </td>
                            </tr>
                        </table>
                        <table class="table table-condensed m-0">
                            <tr style="{{ Setting::get('customers_choose_providers') ? '' : 'display:non' }}">
                                <td>
                                    <p class="form-control-static"
                                        style="border: none; padding-left: 0; margin: -5px 0 0 0;">
                                        @trans('Permitir escolher o fornecedor')
                                        {!! tip(__('Ative esta opção apenas se o cliente tiver contrato exclusivo com um fornecedor. Esta opção permite ao cliente selecionar o fornecedor pelo qual enviar.')) !!}
                                    </p>
                                </td>
                                <td class="w-240px">
                                    {{ Form::selectMultiple('enabled_providers[]',$providersList,!empty($customer->enabled_providers) ? implode($customer->enabled_providers, ',') : null,['class' => 'form-control select2', 'multiple' => true, 'data-placeholder' => 'Escolher Automático']) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if (!empty(@$servicesList))
                    <h4 class="form-divider">Serviços contratados {!! tip(__('Esta é a lista de serviços que o cliente pode escolher e orçamentar.')) !!}</h4>
                    <a href="#" class="m-l-15 select-all-services">@trans('Marcar/Desmarcar Todos')</a>
                    <div class="row row-5 services-list">
                        <?php array_cut($servicesList, 4, true); ?>
                        @foreach ($servicesList as $groupItems)
                            <div class="col-sm-3">
                                @foreach ($groupItems as $id => $service)
                                    <div class="checkbox m-t-5 m-b-8">
                                        <label style="padding-left: 0">
                                            {{ Form::checkbox('enabled_services[]', $id, null, ['class' => 'row-service']) }}
                                            {{ $service }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-sm-12">
                <hr />
                {{ Form::hidden('delete_photo') }}
                <button class="btn btn-primary">@trans('Gravar')</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endif
