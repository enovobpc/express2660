<div>
    <br>
    <h1 class="bold" style="text-align: center; font-size: 35px">DECLARAÇÃO</h1>
    <h4 class="bold m-t-30 m-b-60" style="text-align: center;">(Aquisição de Equipamentos)</h4>
</div>

<div style="padding: 0px 90px 0px 90px;  text-align: justify;">
    <span style="font-size: 13px;">
        &emsp;&emsp;Eu, <span class="bold underline">{{@$user->fullname ?? 'N/A'}}</span>, portador do número de identificação 
        <span class="bold underline">{{@$cc->card_no ?? 'N/A'}}</span>, e NIF <span class="bold underline">{{@$user->vat ?? 'N/A'}}</span>, com residência em <span class="bold underline">{{ @$user->address ?? 'N/A' }}, {{ @$user->zip_code ?? 'N/A' }}, {{ @$user->city ?? 'N/A' }}, </span>
        declaro que recebi a quantia no valor de 
        <span class="bold underline"> {{$equipmentPrice}}{{Setting::get('app_currency') ?? '€'}} </span>
        para a aquisição única e exclusiva de um <span class="bold underline">equipamento eletrónico móvel (Smartphone)</span> para
        uso reservado à empresa <span class="bold underline">{{@Setting::get('company_name') ?? 'N/A'}}</span>
        <p style="font-size: 13px;">
            &emsp;&emsp; Como tal declaro que tomei conhecimento das seguintes características que o mesmo terá que respeitar:
        </p>
        <p>
            <ul style="font-size: 13px;">
                @foreach ($equipmentInfo as $info)
                    <li>
                        {{$info}}
                    </li>
                @endforeach
            </ul>
        </p>
        <p style="font-size: 13px;">
            &emsp;&emsp;Após a data de assinatura da declaração reconheço que, no prazo de <span class="bold underline"> 1(um) mês</span>, apresento o mesmo à empresa para prova e instalação da aplicação.            
        </p>
        <p style="font-size: 13px;">
            &emsp;&emsp;Mais declaro que me comprometo a que o equipamento a adquirir tenha uma validade
            mínima de <span class="bold underline">2 anos (dois anos)</span> desde a data deste documento.
        </p>
        <p style="font-size: 13px;">
            &emsp;&emsp;Declaro também que não será imputado à empresa <span class="bold underline">{{@Setting::get('company_name') ?? 'N/A'}}</span>
            qualquer valor de reparação ou compra de novo equipamento, sendo que, a conservação do
            mesmo é de única responsabilidade minha.
        </p>
        <p style="font-size: 13px;">
            &emsp;&emsp;Caso o vinculo laboral termine/cesse comprometo-me à devolução do valor inicialmente entregue.
        </p>
        
    </span>
    <div class="m-t-50" style="text-align: right; font-size: 14px">
        <p class="m-b-15">Fafe, Portugal, 18/04/2023</p>
        <p class="m-b-15">O Declarante,</p>
        <p class="m-b-15">____________________________________</p>
    </div>
</div>