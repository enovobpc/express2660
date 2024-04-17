@extends(app_email_layout())

@section('content')

    @if(Setting::get('ballance_email_hide_docs_list'))
        <h5>Extrato de Conta Corrente</h5>
        <p>
            Estimado(a) cliente,
            <br/>
            De forma a agilizarmos a comunicação entre Fornecedor/Cliente enviamos-lhe um link para que possa ver a sua Conta Corrente.
        </p>
        <p>
            Neste link pode consultar e efetuar o download de todos os seus documentos emitidos.<br/>
            A informação contida neste link é atualizada automaticamente.
        </p>
        <br/>
        <p style="text-align: center">
            <a href="{{ route('account.public.balance.index', $hash) }}" style="background: #1f2c33 ; padding: 10px; color: #fff;">CONSULTAR CONTA CORRENTE</a>
        </p>
        <br/>
        @if(Setting::get('bank_iban'))
            <hr/>
            <p>
                <b>IBAN para pagamentos: {{ Setting::get('bank_iban') }}</b><br/>
                @if(Setting::get('bank_name'))
                    Banco: {{ Setting::get('bank_name') }}
                @endif
            </p>
        @endif
    @else
        <h5>Extrato de Conta Corrente</h5>
        <p>
            Estimado(a) cliente,
            <br/>
            Junto enviamos o resumo da sua conta corrente até ao momento.
        </p>
        <p>
            Possui atualmente <b>{{ $customer->balance_unpaid_count }} documentos</b> por liquidar num total de <b style="color: red">{{ money($customer->balance_total, Setting::get('app_currency'))  }}</b>.
        </p>
        @if(config('app.source') == 'fozpost')
            <p>
                Agradecemos que proceda à liquidação dos valores vencidos, no <u>prazo máximo de 15 dias</u>.
                <br/>
                Após essa data se os valores não estiverem regularizados iremos proceder ao bloqueio do acesso área cliente e enviar a mesma para contencioso.
                <br/>
                IBAN para pagamentos: PT50.0010.0000.3216.1960.0012.4
            </p>
        @endif
        <table style="width: 100%; border: 1px solid #ddd; font-size: 13px" cellspacing="0" cellpadding="3">
            <tr>
                <th style="background: #dddddd; text-align: left">Data</th>
                <th style="background: #dddddd; text-align: left">Documento</th>
                <th style="background: #dddddd; text-align: left">Referência</th>
                <th style="background: #dddddd; text-align: right">Total</th>
                <th style="background: #dddddd; text-align: right">Pendente</th>
                <th style="background: #dddddd; text-align: left; width: 120px">&nbsp;&nbsp;&nbsp;Vencimento</th>
            </tr>
            <?php $docTotal = $docPending = 0; ?>
            @foreach($invoices as $invoice)
                <?php 
                $docTotal  += $invoice->doc_total; 
                $docPending+= $invoice->doc_pending; 
                
                ?>
                <tr>
                    <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->doc_date }}</td>
                    <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->name }}</td>
                    <td style="border-bottom: 1px solid #dddddd;">{{ $invoice->reference }}</td>
                    <td style="border-bottom: 1px solid #dddddd; text-align: right">{{ money($invoice->doc_total, Setting::get('app_currency')) }}</td>
                    <td style="border-bottom: 1px solid #dddddd; font-weight: bold; text-align: right">{{ money($invoice->doc_pending, Setting::get('app_currency')) }}</td>
                    <td style="border-bottom: 1px solid #dddddd;">
                        @if($invoice->due_date < $today)
                            <div style="color: red; line-height: 12px">
                                &nbsp;&nbsp;&nbsp;{{ $invoice->due_date }}<br/>
                                <small>&nbsp;&nbsp;&nbsp;{{ $invoice->due_date_days_left }} dias atraso</small>
                            </div>
                        @else
                            &nbsp;&nbsp;&nbsp;{{ $invoice->due_date }}
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right">{{ money($docTotal, Setting::get('app_currency')) }}</td>
                <td style="text-align: right"><b>{{ money($docPending, Setting::get('app_currency')) }}</b></td>
            </tr>
        </table>
        <br/>
        <p style="text-align: center">
            <a href="{{ route('account.public.balance.index', $hash) }}" style="background: #1f2c33 ; padding: 10px; color: #fff;">CONSULTAR CONTA CORRENTE</a>
        </p>
        @if(Setting::get('bank_iban'))
            <hr/>
            <p>
                <b>IBAN para pagamentos: {{ Setting::get('bank_iban') }}</b><br/>
                @if(Setting::get('bank_name'))
                    Banco: {{ Setting::get('bank_name') }}
                @endif
            </p>
        @endif
        <p>
            Pode consultar e efetuar o download de todos os seus documentos emitidos na sua área de cliente.
            <br/>
            Aceda aqui para <a href="{{ route('account.login') }}">Iniciar Sessão</a> na sua conta.
        </p>
    @endif
@stop