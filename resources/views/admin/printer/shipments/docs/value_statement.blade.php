<style>
    table td,
    table th {
        padding: 3px;
    }
    div {
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
    }
</style>

<div style="width: 180mm; padding: 9.5mm; padding-top: 5mm; font-size: 10pt; height: 230mm; margin-left: 15px">
    <div style="text-align: center;">
        <h1 style="font-size: 25px; margin-bottom: 0">Declaração de Valores</h1>
        <h1 style="font-size: 19px; margin-top: 0">de sujeitos não passívos de IVA</h1>
        <p style="font-size: 14px">(Conforme Decreto – Lei nº 147/2003 de 11 de Julho)</p>
    </div>
    <div style="height: 40px">&nbsp;</div>

    <div style="line-height: 18px; text-align: justify">
        <p>
            Eu, <b style="font-weight: bold">{{ @$shipment->customer->billing_name }}</b>,
            com o contribuínte <b style="font-weight: bold">{{ @$shipment->customer->vat ? @$shipment->customer->vat : '---' }}</b>,
            e morada fiscal em <b style="font-weight: bold">{{ @$shipment->customer->billing_address }} {{ @$shipment->customer->billing_zip_code }} {{ @$shipment->customer->billing_city }}</b>,
            declaro que:
        </p>
        {{--<h2 style="font-weight: ; font-size: 14px; margin-bottom: 5px">Declaro que:</h2>--}}
        <p>
            A presente expedição é uma transação <b style="text-decoration: underline">não passiva de IVA</b>, não tendo a transportadora
            qualquer responsabilidade sobre os conteúdos da mesma;
        </p>
        <p>
            Os volumes/objetos que se fazem acompanhar, não fazem parte de uma transacção comercial;
        </p>
        <p>
            Para que não existam dúvidas da proveniência e do destino, declaro que os dados constantes nesta declaração são
            verdadeiros e acrescento de seguida os dados do destino.
        </p>
        <table style="width: 100%; font-size: 9.5pt; margin-left: 30px; margin-top: 10px; margin-bottom: 5px;">
            <tr>
                <td style="width: 100px; padding-bottom: 0">Destinatário:</td>
                <td style="padding-bottom: 0">
                    {{ $shipment->recipient_name }}
                    @if($shipment->recipient_attn)
                        (A/C: {{ $shipment->recipient_attn }})
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 0">Morada:</td>
                <td style="padding-bottom: 0">{{ $shipment->recipient_address }} {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</td>
            </tr>
            <tr>
                <td style="padding-bottom: 0">Contacto:</td>
                <td style="padding-bottom: 0">{{ $shipment->recipient_phone }}</td>
            </tr>
            <tr>
                <td style="padding-bottom: 0">Contribuinte:</td>
                <td style="padding-bottom: 0">{{ $shipment->recipient_vat ? $shipment->recipient_vat : '-/-' }}</td>
            </tr>
        </table>
        <p>
            <div style="line-height: 22px; margin-bottom: 15px; text-align: left">
                Descrição do Conteúdo:  ___________________________________________________________________
                _______________________________________________________________________________________
                _______________________________________________________________________________________
            </div>
            Valor Estimativo / Estatístico: &nbsp;&nbsp;&nbsp;{{ money($shipment->charge_price) }} Euros. (Sem valor comercial)
        </p>
    </div>
    <br/>
    <div style="text-align: justify">
        <h2 style="font-weight: bold; font-size: 14px">Protecção de Dados Pessoais</h2>
        <p style="font-size: 12px; line-height: 17px">
            A {{ $shipment->agency->company }}, com o NIF {{ $shipment->agency->vat }}, pretende assegurar que o tratamento de
            tais dados obedece às regras estabelecidas no Regulamento (UE) 2016/679 de 27 de Abril de 2016.
            Para este efeito foi aprovada a Política de Privacidade da empresa, estabelecendo as principais regras
            observadas pela {{ $shipment->agency->company }}, no que diz respeito ao tratamento de dados pessoais.
        </p>
        <p style="font-size: 12px">
            A recolha e tratamento dos dados pessoais fornecidos à {{ $shipment->agency->company }}, assim como o exercício dos
            direitos dos seus titulares relativamente a estes dados, regem-se pela Política de Privacidade, que tomei
            conhecimento e concordo, nos termos e na medida em que sejam aplicáveis aos titulares dos dados.
            Desta forma dou o meu consentimento expresso no que tange à utilização dos meus dados pessoais, assim como dos
            dados por mim fornecido de terceiros, no respeito estrito da legislação em vigor, bem como declaro ter conhecimento
            das condições de tratamento de dados pessoais em vigor na {{ $shipment->agency->company }}, cuja cópia me foi
            facultada e / ou disponibilizada em {{ env('APP_URL') }}/avisos-legais/politica-privacidade.
            Tomei conhecimento e concordo com o acima exposto.
        </p>
    </div>
    <br/>
    <br/>
    <div style="text-align: center">
        _____ de ___________________ de ________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_________________________________________<br/>
        <span style="font-size: 10px">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Assinatura do declarante
        </span>
    </div>


</div>



{{--
<div class="fs-6pt" style="padding-left: 10mm; padding-right: 10mm;">
    <div class="pull-left" style="width: 57%"><b style="font-weight: bold">ENOVO TMS - Software Transportes e Logísitica | tms.enovo.pt</b></div>
    <div class="pull-left text-right" style="width: 42%">Emitido por: {{ Auth::user()->name }}</div>
</div>--}}
