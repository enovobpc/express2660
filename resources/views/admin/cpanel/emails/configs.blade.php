<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Configurações Outlook')</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('email', __()'Selecione uma conta de e-mail') }}
                {{ Form::select('email', $emailAccounts, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default m-0">
                <div class="panel-heading">
                    <p class="panel-title bold"><i class="fas fa-sign-in-alt"></i> @trans('Recepção')</p>
                </div>
                <table class="table manual_settings_table">
                    <tr>
                        <td class="w-100px bold">@trans('Username')</td>
                        <td class="config-email">{{ $email->email }}</td>
                    </tr>
                    <tr>
                        <td class="bold">@trans('Password')</td>
                        @if(Auth::user()->isAdmin())
                            <td class="config-password"><i>{{ $email->password }}</i></td>
                        @else
                            <td><i>@trans('Password do e-mail')</i></td>
                        @endif
                    </tr>
                    <tr>
                        <td class="bold">@trans('Servidor')</td>
                        <td>{{ @$configs['inbox_host'] }}</td>
                    </tr>
                    <tr>
                        <td>
                            <b>@trans('Porta')</b><br/>
                            <small>@trans('Ligação Segura')</small>
                        </td>
                        <td>
                            <ul class="list-unstyled">
                                <li class="text-info">
                                    <span class="label label-info">IMAP</span> {{ @$configs['inbox_port'] }}
                                </li>
                                <li class="text-info">
                                    <span class="label label-info">POP3</span> 995
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>@trans('Porta')</b><br/>
                            <small>@trans('Ligação Normal')</small>
                        </td>
                        <td>
                            <ul class="list-unstyled">
                                <li class="text-muted">
                                    <span class="label label-default">IMAP</span> {{ @$configs['inbox_insecure_port'] }}
                                </li>
                                <li class="text-muted">
                                    <span class="label label-default">POP3</span> 110
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default m-0">
                <div class="panel-heading">
                    <p class="panel-title bold"><i class="fas fa-sign-out-alt"></i> @trans('Envio')</p>
                </div>
                <table class="table manual_settings_table">
                    <tr>
                        <td class="bold w-100px">@trans('Username')</td>
                        <td class="config-email">{{ $email->email }}</td>
                    </tr>
                    <tr>
                        <td class="bold">@trans('Password')</td>
                        @if(Auth::user()->isAdmin())
                        <td class="config-password"><i>{{ $email->password }}</i></td>
                        @else
                        <td><i>@trans('Password do e-mail')</i></td>
                        @endif
                    </tr>
                    <tr>
                        <td class="bold">@trans('Servidor')</td>
                        <td>{{ @$configs['smtp_host'] }}</td>
                    </tr>
                    <tr>
                        <td>
                            <b>@trans('Porta')</b><br/>
                            <small>@trans('Ligação Segura')</small>
                        </td>
                        <td>
                            <ul class="list-unstyled">
                                <li class="text-info">
                                    <span class="label label-info">SMTP</span> {{ @$configs['smtp_port'] }}
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>@trans('Porta')</b><br/>
                            <small>@trans('Ligação Normal')</small>
                        </td>
                        <td>
                            <ul class="list-unstyled">
                                <li class="text-muted">
                                    <span class="label label-default">SMTP</span> {{ @$configs['smtp_insecure_port'] }}
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
</div>

<script>
    $('.select2').select2(Init.select2());

    $('[name="email"]').on('change', function(){
        var password = $(this).find('option:selected').html();
        $('.config-email').html(password)
        $('.config-password').html($(this).val())
    })
</script>