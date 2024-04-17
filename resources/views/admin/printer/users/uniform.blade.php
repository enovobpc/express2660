<div>
    <br>
    <h1 class="bold" style="text-align: center; font-size: 35px">TERMO DE RESPONSABILIDADE</h1>
    <h4 class="bold m-t-30 m-b-60" style="text-align: center;">(Fardamento)</h4>
</div>

<div style="padding: 0px 90px 0px 90px;  text-align: justify;">
    <span style="font-size: 12px;">
        &emsp;&emsp; <span class="bold underline">{{$user->name}}</span>, 
        funcionário 
        com a categoria Profissional de 
        @if(!empty($user->professional_role))  <span class="bold underline">{{$user->professional_role}}</span>@else <span class="bold underline">N/A</span> @endif,
        ao serviço da empresa
        @if(!empty(Setting::get('company_name')))  {{Setting::get('company_name')}} @else N/A @endif,
        NIPC
        @if(!empty(Setting::get('vat')))  {{trim(Setting::get('vat'))}}@else N/A @endif,
        @if(env('APP_SOURCE') == 'transnos' )
            com sede na Rua do Bouçó, nº 49 e instalações na
            @if(!empty(Setting::get('company_address'))){{trim(Setting::get('company_address'))}}@else N/A @endif, 
            ambas na freguesia de Arões S. Romão, concelho de 
            @if(!empty(Setting::get('company_city'))){{trim(Setting::get('company_city'))}}@else N/A @endif,
        @else
            situada na 
            @if(!empty(Setting::get('company_address'))){{trim(Setting::get('company_address'))}}@else N/A @endif, 
            @if(!empty(Setting::get('company_zip_code'))){{trim(Setting::get('company_zip_code'))}}@else N/A @endif - 
            @if(!empty(Setting::get('company_city'))){{trim(Setting::get('company_city'))}}@else N/A @endif, 
        @endif
        declara que recebeu, nesta data a farda para uso exclusivo em serviço/trabalho.
        <br>
        <br>

        &emsp;&emsp;Descriminação da Farda Recebida:
        <br>
        <ul style="font-size: 12px;">
            @if(isset($uniforms) && !empty($uniforms))
                @foreach($uniforms->equipments as $uniform)
                <li>
                    {{@rtrim($uniform->stock_total, '0.') ?? 'N/A'}} {{@$uniform->name ?? 'N/A'}}
                </li>
                @endforeach
            @endif
        </ul>

        <br>
        <p style="font-size: 12px;">
            &emsp;&emsp; Mais declara que está ciente da obrigatoriedade da utilização da farda para o desempenho das minhas funções.
        </p>

        <br>
        <p style="font-size: 12px;">
            &emsp;&emsp; Compromete-se a:
            <ol type="a" style="font-size: 12px">
                <li>
                    Fazer uso exclusivo na atividade profissional, não podendo usar para fins extraprofissionais;
                </li>
                <li>
                    Usar corretamente a farda de trabalho e com brio, uma vez que é um elemento identificador da empresa;
                </li>
                <li>
                    Zelar pela guarda, conservação e manutenção da farda;
                </li>
                <li>
                    Assumir a responsabilidade da higienização da mesma;
                </li>
                <li>
                    Comunicar à empresa se algum elemento das suas fardas de trabalho se danificar durante e como consequência do trabalho;
                </li>
                <li>
                    Restituir a farda à empresa e devidamente higienizada em caso de:
                </li>
                <ul style="list-style-type: circle;">
                    <li>Necessidade de substituição;</li>
                    <li>Transferência de função ou secção caso não sendo necessário o seu uso;</li>
                    <li>Em quaisquer circunstâncias previstas para a cessação de contrato de trabalho previstas no Código do Trabalho;</li>
                    <li>Em caso de substituição, depois de ter feito o devido uso de dois (2) anos</li>
                    <li>No caso de extravio ou quaisquer outros danos oriundos de uso inadequado  ou falta de cuidados.</li>
                </ul>
            </ol>
        </p>

        <br>
        <p style="font-size: 12px;">
            Mais declara que autoriza a Empresa a descontar da minha remuneração mensal o valor correspondente 
            ao custo integra (conforme o preço de mercado) para a higienização da farda de trabalho, quando esta for 
            devolvida sem a devida higienização e bem como o valor correspondente da peça de fardamento (preço de mercado)
            em caso de substituição por mau uso e fora do estabelecido pela empresa
        </p>
    </span>

    <br>
    <p style="font-size: 12px; margin-left: 60%">
        @if(!empty(Setting::get('company_city'))){{trim(Setting::get('company_city'))}}@else N/A @endif, 
        @if(!empty(Setting::get('company_country'))){{trim(trans('country.' . Setting::get('company_country')))}}@else N/A @endif,
        {{\Carbon\Carbon::now()->format('d/m/Y')}}
        <br>
        O Declarante,
        <br>
        <br>
        ________________________________
    </p>
</div>  