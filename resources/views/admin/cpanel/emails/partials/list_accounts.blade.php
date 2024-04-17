<div class="row">
    <div class="col-sm-12">
        <div style="font-family: Arial">
            <p style="font-weight: bold; font-size: 15px">@trans('ACESSO CONTAS E-MAIL')</p>
            <p>@trans('URL acesso:')' <a href="{{ request()->getHttpHost().'/webmail' }}">{{ request()->getHttpHost().'/webmail' }}</a></p>
            <table style="width: 100%">
                @foreach($emailAccounts as $emailAccount)
                    <tr>
                        <td>
                            <hr style="margin: 4px 0"/>
                            @trans('E-mail:') {{ $emailAccount->email }}<br/>
                            @trans('Password:') {{ $emailAccount->password }}<br/>
                        </td>
                    </tr>
                @endforeach
            </table>
            <hr/>
            <p style="font-weight: bold; font-size: 15px">@trans('CONFIGURAÇÃO OUTLOOK/TELEMÓVEL')</p>
            <p>
                @trans('Servidor Envio:')' mail.{{ request()->getHttpHost() }} (@trans('Porta') 465)<br/>
                @trans('Servidor Recepção:')' mail.{{ request()->getHttpHost() }} (@trans('Porta') 993)
            </p>
        </div>
    </div>
</div>