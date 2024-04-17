<style>
    .box {
        float: left;
        border: 2px solid;
        border-right: 0;
        border-bottom: 0;
    }

    .pt {
        font-size: 10px;
    }

    .en {
        font-style: italic;
        font-size: 10px;
    }

    .pt-small {
        font-size: 8px;
    }

    .input {
        letter-spacing: 0px;
        height: 3.7mm;
        width: 100%;
        border-bottom: 0.5px solid #000;
        padding: 5px 0px;
        margin-bottom: 1px;
        line-height: 1.0;
        font-weight: bold;
        text-transform: uppercase;
    }
</style>
<div style="border-right: 2px solid #000; border-bottom: 2px solid #000; width: 21cm; height: 29cm; font-size: 12px; font-family: Arial; line-height: 1.2">
    <div class="box" style="width: 50%; height: 2.8cm; padding: 5px; text-align: center;">
        <div style="font-weight: bold; font-size: 16px">Autorização de Débito Direto SEPA</div>
        <div class="en" style="margin-bottom: 10px">SEPA Direct Debit Mandate</div>
        <div class="input" style="width: 80%; margin-left: 10%; font-size: 15px">{{ $customer->bank_mandate ? $customer->bank_mandate : $customer->code.'01' }}</div>
        <div class="pt">Referência da autorização (ADD) a completar pelo Credor</div>
        <div class="en">Mandate reference - to be completed by the creditor</div>
    </div>
    <div class="box" style="float: left; width: 48%; height: 3.05cm;">
        <div style="float: left; text-align: center">
            <img src="{{ asset('assets/img/logo/logo_lg.png') }}" style="height: 1.5cm; margin-top: 16px">
            <h2 style="margin: 0;">www.enovo.pt</h2>
        </div>
    </div>
    <div class="box" style="float: left; width: 100%; height: 2.4cm; padding: 3px; font-size: 9px; text-align: justify">
        <p style="padding: 5px 5px 5px 10px">
            Ao subscrever esta autorização, está a autorizar o CREDOR a enviar instruções ao seu Banco para
            debitar a sua conta e o seu Banco a debitar a sua conta, de acordo com as
            instruções do CREDOR.<br/>
            Os seus direitos incluem a possibilidade de exigir do seu Banco o reembolso do montante debitado,
            nos termos e condições acordados com o seu Banco. O reembolso deve ser solicitado até um prazo de
            oito semanas, a contar da data do débito na sua conta.
            Preencha por favor todos os campos assinalados com *. O preenchimento dos campos assinalados
            com ** é da responsabilidade do Credor.<br/>
            <i>
                By signing this mandate form, you authorise the CREDITOR to send instructions to your bank to debit your
                account and your bank to debit your account in accordance with the instructions from CREDITOR.<br/>
                As part of your rights, you are entitled to a refund from your bank under the terms and conditions of
                your agreement with your bank. A refund must be claimed within 8 weeks starting from the date on which
                your account was debited.
                Please complete all the fields marked *. Fields marked with ** must be completed by the Creditor.
            </i>
        </p>
    </div>
    <div class="box" style="float: left; width: 100%; padding: 3px;">
       <div style="float: left; width: 28%; padding-left: 10px">
           <div class="pt" style="font-weight: bold">Identificação do Devedor</div>
           <div class="en">Debtor Identification</div>
       </div>
        <div style="float: left; width: 70%">
            <div>
                <div class="input" style="width: 100%;">{{ $customer->billing_name }}</div>
                <div class="pt-small">* Nome do(s) Devedor(es) / <i>Name of the debtor(s)</i></div>
            </div>
            <div>
                <div class="input" style="width: 100%;">{{ $customer->billing_address }}</div>
                <div class="pt-small">* Nome da rua e número / <i>Street name and number</i></div>
            </div>
            <div>
                <div style="float: left; width: 30%">
                    <div class="input" style="width: 100%;">{{ $customer->billing_zip_code }}</div>
                    <div class="pt-small">* Código Postal / <i>Postal Code(s)</i></div>
                </div>
                <div style="float: left; width: 50%; margin-left: 20%">
                    <div class="input" style="width: 100%;">{{ $customer->billing_city }}</div>
                    <div class="pt-small">* Cidade / <i>City</i></div>
                </div>
            </div>
            <div>
                <div class="input" style="width: 100%;">{{ trans('country.'.$customer->billing_country) }}</div>
                <div class="pt-small">* Country / <i>Country</i></div>
            </div>
            <div>
                <div class="input" style="width: 100%;">{{ $customer->bank_iban }}</div>
                <div class="pt-small">* Número de conta - IBAN / <i>Account number - IBAN</i></div>
            </div>
            <div>
                <div class="input" style="width: 100%;">{{ $customer->bank_swift }}</div>
                <div class="pt-small">* BIC SWIFT / <i>SWIFT BIC</i></div>
            </div>
        </div>
    </div>
    <div class="box" style="float: left; width: 100%; padding: 3px;">
        <div style="float: left; width: 28%; padding-left: 10px">
            <div class="pt" style="font-weight: bold">Identificação do Credor</div>
            <div class="en">Creditor Identification</div>
        </div>
        <div style="float: left; width: 70%">
            <div>
                <div class="input" style="width: 100%;">SOFTVIS - Desenvolvimento de Software, Unipessoal Lda</div>
                <div class="pt-small">** Nome do(s) Credor / <i>Creditor Name</i></div>
            </div>
            <div>
                <div class="input" style="width: 100%;">PT 516 546 333</div>
                <div class="pt-small">** Identificador do Credo / <i>Creditor Identifier</i></div>
            </div>
            <div>
                <div class="input" style="width: 100%;">Rua do Francial, lote 5 Rio de Loba</div>
                <div class="pt-small">** Nome da rua e número / <i>Street name and number</i></div>
            </div>
            <div>
                <div style="float: left; width: 30%">
                    <div class="input" style="width: 100%;">3505-546</div>
                    <div class="pt-small">** Código Postal / <i>Postal Code(s)</i></div>
                </div>
                <div style="float: left; width: 50%; margin-left: 20%">
                    <div class="input" style="width: 100%;">Viseu</div>
                    <div class="pt-small">** Cidade / <i>City</i></div>
                </div>
            </div>
            <div>
                <div class="input" style="width: 100%;">Portugal</div>
                <div class="pt-small">** Country / <i>Country</i></div>
            </div>
        </div>
    </div>
    <div class="box" style="float: left; width: 100%; padding: 3px;">
        <div style="float: left; width: 28%; padding-left: 10px">
            <div class="pt" style="font-weight: bold">Tipos de Pagamento</div>
            <div class="en" style="margin-bottom: 10px">Type of Payment</div>

            <div class="pt" style="font-weight: bold">Local onde está a assinar</div>
            <div class="en" style="margin-bottom: 10px">Location in which you are signing</div>

            <div class="pt" style="font-weight: bold">Assinar aqui por favor</div>
            <div class="en">Location in which you are signing</div>
        </div>
        <div style="float: left; width: 70%">
            <div>
                <div style="float: left; width: 55%">
                    <div class="pt-small" style="width: 85%; float: left; width: 70%; padding-top: 5px; font-size: 10px">* Pagamento recorrente/ <i>Recurrent payment</i></div>
                    <div style="float: left; width: 8%; height: 4mm; border: 1px solid #000; padding: 1px; font-weight: bold; text-align: center">X</div>
                    <div class="pt-small" style="float: left; width: 10%; margin-left: 15px; padding-top: 5px">Ou / <i>Or</i></div>
                </div>
                <div style="float: left; width: 44%">
                    <div class="pt-small" style="float: left; width: 89%; padding-top: 5px; font-size: 10px">* Pagamento pontual/ <i>One-off payment</i></div>
                    <div style="float: left; width: 10%; height: 4mm; border: 1px solid #000;">&nbsp;</div>
                </div>
            </div>
            <div>
                <div style="float: left; width: 40%; margin-bottom: 4px">
                    <div class="input" style="width: 100%;">&nbsp;</div>
                    <div class="pt-small">* Localidade/ <i>Location</i></div>
                </div>
                <div style="float: left; width: 40%; margin-left: 20%">
                    <div class="input" style="width: 100%;">&nbsp;</div>
                    <div class="pt-small">* Data / <i>Date</i></div>
                </div>
            </div>
            <div>
                <div class="input" style="width: 100%; background: #ddd; border: 0.5px solid #000; height: 4.1mm; margin-top: 2px">&nbsp;</div>
                <div class="pt-small">* Assinatura(s) / <i>Signature(s)</i></div>
                <div class="pt-small">
                    Os seus direitos, referentes à autorização acima referida, são explicados em declaração que pode obter no seu Banco.<br/>
                    <i>Your rights regarding the above mandate are explained in a statement that you can obtain from your bank.</i>
                </div>
            </div>
        </div>
    </div>
    <div class="box" style="float: left; width: 100%; padding: 3px;">
        <div style="width: 100%; padding-left: 10px; font-size: 10px">
            Informação detalhada subjacente à relação entre o Credor e o Devedor - apenas para efeitos informativos<br/>
            Details regarding the underlying relatiosship between the Creditor and the Debtor - for information purposes only
        </div>
        <div style="float: left; width: 28%; padding-left: 10px">
            <div class="pt" style="font-weight: bold">Código de Identificação do Devedor</div>
            <div class="en">Debtor Identification Code</div>
        </div>
        <div style="float: left; width: 70%">
            <div>
                <div class="input" style="width: 100%;"></div>
                <div class="pt-small">Número do código, que deseje que o Banco mencione / <i>Write any code number here which you wish to have quoted by your bank</i></div>
            </div>
        </div>
    </div>
    <div class="box" style="float: left; width: 100%; padding: 3px;">
        <div style="float: left; width: 28%; padding-left: 10px">
            <div class="pt" style="font-weight: bold">Pessoa em representação da qual o pagamento é efetuado</div>
            <div class="en">Person on whose behalf payment is made</div>
        </div>
        <div style="float: left; width: 70%">
            <div>
                <div class="input" style="width: 100%;">&nbsp;</div>
                <div class="pt-small">Nome do Devedor representado: se realizar um pagamento no âmbito de um acordo entre o CREDOR e outra pessoa (p.e. quando está
                    a liquidar uma fatura de uma terceira entidade), escreva aqui por favor o nome de outra pessoa. Se está a pagar diretamente por sua
                    conta, não preencha este campo
                    <br/>
                    <i>Name of the Debtor Reference Party: If you are making a payment in respect of an arrangement between CREDITOR and another
                    person (e.g. where you are paying the other person's bill) please write the other person's name here. If you are paying om your own
                    behalf, leave blank.</i>
                </div>
            </div>
            <div>
                <div class="input" style="width: 100%;">&nbsp;</div>
                <div class="pt-small">Código de identificação do Devedor representado / <i>Identification code of the Debtor Reference Party</i></div>
            </div>
        </div>
    </div>
    <div class="box" style="float: left; width: 100%; padding: 3px;">
        <div style="float: left; width: 28%; padding-left: 10px">
            <div class="pt" style="font-weight: bold">Entidade em cujo nome o Credor recebe o pagamento</div>
            <div class="en">Party on whose behalf the Creditor collects the payment</div>
        </div>
        <div style="float: left; width: 70%">
            <div>
                <div class="input" style="width: 100%;">&nbsp;</div>
                <div class="pt-small">Nome do Credor representado: o Credor deve fornecer esta informação, sempre que estiver a efetuar cobranças em representação de
                    outra entidade
                    <br/>
                    <i>Name of the creditor Reference Party: Creditor must complete this section if collecting payment on behalf of another party.</i>
                </div>
            </div>
            <div>
                <div class="input" style="width: 100%;">&nbsp;</div>
                <div class="pt-small">Código de identificação do Credor representado / <i>Identification code of the Creditor Reference Party</i></div>
            </div>
        </div>
    </div>
    <div class="box" style="float: left; width: 100%; padding: 3px;">
        <div style="float: left; width: 28%; padding-left: 10px">
            <div class="pt" style="font-weight: bold">Relativamente ao Contrato</div>
            <div class="en">In respect of the Contract</div>
        </div>
        <div style="float: left; width: 70%">
            <div style="float: left; width: 45%; margin-right: 10%;  text-align: center">
                <div class="input" style="width: 100%;">&nbsp;211201A</div>
                <div class="pt-small">
                    Nº de identificação do Contrato Subjacente<br/>
                    <i>Identification number of the underlying contract.</i>
                </div>
            </div>
            <div style="float: left; width: 45%; margin-left: 10%; text-align: center">
                <div class="input" style="width: 100%;">&nbsp;SOFTWARE ENOVO TMS</div>
                <div class="pt-small">
                    Descrição do Contrato<br/>
                    <i>Description of Contract.</i>
                </div>
            </div>
        </div>
    </div>
</div>