<style>
    .box {
        float: left;
        border-top: 2px solid #000;
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
<div style="float: left; width: 50%; height: 1.5cm; padding: 5px; text-align: left;">
    <div style="font-weight: bold; font-size: 20px">Autorização de Débito Direto SEPA</div>
    <div class="en" style="margin-bottom: 10px">SEPA Direct Debit Mandate</div>
</div>
<div style="float: left; width: 48%; height: 1.5cm;">
    <div style="float: left; text-align: center">
        <div style="text-align: right; line-height: 16px">
            <div style="font-size: 12px">N.º Mandato / Autorização</div>
            <div style="font-size: 20px; font-weight: bold; letter-spacing: 3px">{{ $customer->bank_mandate ? $customer->bank_mandate : '_______' }}</div>
        </div>
    </div>
</div>

<div style="float: left; width: 100%; height: 1.5cm; padding: 3px; font-size: 9px; line-height: 11px; text-align: justify">
    <p style="padding: 0">
        Ao subscrever esta autorização ou ao assinar o contrato subjecente a esta autorização, está a autorizar o CREDOR a enviar instruções ao seu Banco para
        debitar a sua conta e o seu Banco a debitar a sua conta, de acordo com as
        instruções do CREDOR. Os seus direitos incluem a possibilidade de exigir do seu Banco o reembolso do montante debitado,
        nos termos e condições acordados com o seu Banco. O reembolso deve ser solicitado até um prazo de
        oito semanas, a contar da data do débito na sua conta.<br/>
        Em caso de devolução, insuficiência de saldo ou impossibilidade de cobrança será cobrado o custo administrativo de cinquenta euros mais IVA e implicará a suspensão
        imediata e sem aviso de todos os serviços associados ao contrato.
        <br/>
        Preencha por favor todos os campos assinalados com *. O preenchimento dos campos assinalados
        com ** é da responsabilidade do Credor.
    </p>
</div>
<div style="border-right: 2px solid #000; border-left: 2px solid #000; border-bottom: 2px solid #000; width: 21cm; height: 10cm; font-size: 12px; font-family: Arial; line-height: 1.2">
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
                <div class="input" style="width: 100%;">{{ @$customer->agency->company->name }}</div>
                <div class="pt-small">** Nome do(s) Credor / <i>Creditor Name</i></div>
            </div>
            <div>
                <div class="input" style="width: 100%;">PT_____________</div>
                <div class="pt-small">** Identificador do Credo / <i>Creditor Identifier</i></div>
            </div>
            <div>
                <div class="input" style="width: 100%;">{{ @$customer->agency->company->address }}</div>
                <div class="pt-small">** Nome da rua e número / <i>Street name and number</i></div>
            </div>
            <div>
                <div style="float: left; width: 30%">
                    <div class="input" style="width: 100%;">{{ @$customer->agency->company->zip_code }}</div>
                    <div class="pt-small">** Código Postal / <i>Postal Code(s)</i></div>
                </div>
                <div style="float: left; width: 50%; margin-left: 20%">
                    <div class="input" style="width: 100%;">{{ @$customer->agency->company->city }}</div>
                    <div class="pt-small">** Cidade / <i>City</i></div>
                </div>
            </div>
            <div>
                <div class="input" style="width: 100%;">{{ trans('country.'.@$customer->agency->company->country) }}</div>
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
                <div style="float: left; width: 100%">
                    <div class="input" style="width: 100%;">Pagamento Recorrente</div>
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
                    Os seus direitos, referentes à autorização acima referida, são explicados em declaração que pode obter no seu Banco.
                </div>
            </div>
        </div>
    </div>
<!--<div class="box" style="float: left; width: 100%; padding: 3px;">
        <div style="float: left; width: 28%; padding-left: 10px">
            <div class="pt" style="font-weight: bold">Código de Identificação do Devedor</div>
            <div class="en">Debtor Identification Code</div>
        </div>
        <div style="float: left; width: 70%">
            <div>
                <div class="input" style="width: 100%;">{{ $customer->code }}</div>
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
                </div>
            </div>
            <div>
                <div class="input" style="width: 100%;">&nbsp;</div>
                <div class="pt-small">Código de identificação do Devedor representado / <i>Identification code of the Debtor Reference Party</i></div>
            </div>
        </div>
    </div>-->
<!--    <div class="box" style="float: left; width: 100%; padding: 3px;">
        <div style="float: left; width: 28%; padding-left: 10px">
            <div class="pt" style="font-weight: bold">Entidade em cujo nome o Credor recebe o pagamento</div>
        </div>
        <div style="float: left; width: 70%">
            <div>
                <div class="input" style="width: 100%;">&nbsp;</div>
                <div class="pt-small">
                    Nome do Credor representado: o Credor deve fornecer esta informação, sempre que estiver a efetuar cobranças em representação de
                    outra entidade
                </div>
            </div>
            <div>
                <div class="input" style="width: 100%;">&nbsp;</div>
                <div class="pt-small">Código de identificação do Credor representado</div>
            </div>
        </div>
    </div>-->
    <div class="box" style="float: left; width: 100%; padding: 3px 3px 0 3px;">
        <div style="float: left; width: 28%; padding-left: 10px">
            <div class="pt" style="font-weight: bold">Relativamente ao Contrato</div>
            <div class="en">In respect of the Contract</div>
        </div>
        <div style="float: left; width: 70%">
            <div style="float: left; width: 45%; margin-right: 10%;  text-align: center">
                <div class="input" style="width: 100%;">&nbsp;{{ @$proposal->code ? @$proposal->code : (@$customer->contract_code ? @$customer->contract_code : @$customer->code) }}</div>
                <div class="pt-small">
                    Nº de identificação do Contrato ou Cliente Subjacente<br/>
                    <i>Identification number of the underlying contract.</i>
                </div>
            </div>
            <div style="float: left; width: 45%; margin-left: 10%; text-align: center">
                <div class="input" style="width: 100%;">&nbsp;SERVIÇOS CORREIO, TRANSPORTE E LOGISTICA</div>
                <div class="pt-small">
                    Descrição do Contrato<br/>
                    <i>Description of Contract.</i>
                </div>
            </div>
        </div>
    </div>
</div>
