<?php
$uncheck = '<img style="height: 10px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAAdgAAAHYBTnsmCAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAABXSURBVDiN7cyhDkBgGIXh5zczbsOmEBT3fwWKDdFtMEmgSV8TvO2E8yR3HUqxDiwJDSasQaBFn6PAhiEIzCiy4OnVD/zAN4AcJ2qMwW+NMz2jRRUEdqwX7wwLPZa20SQAAAAASUVORK5CYII=">';
$check   = '<img style="height: 10px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAAWQAAAFkBqp2phgAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAADvSURBVDiNpdMxSoNBEAXg76mFKAhaaKOHEKwsJFWIjXiGlB7CUwh2tvZWegWxC0asBSGFjYWFCGPzR341AX98MLDs7HvzZmcX+hijOsYY/TSLO1zohiH2NGq9qtIl0EMtdKwqyTdOJ4Ek2xgl2e0skGQdN5jgvpNAkmVc4QPHVfX+SyDJZpLzJCs/yIu4xA4Oq+q1nW87WMUA10nWWvtnOMCgqp5nOfwaY1PlEbfYwCnesD9vjN8EmsQWRnhqej7q9A6qatIkJzipqqtZtqdYmtlT1UuS/fZtz8PcMf6FPHXwgGGSv5xvY9hw//edPwHFiKJUsrqN6wAAAABJRU5ErkJggg==">';
?>
<style>
    div {
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
    }
    p {
        margin-bottom: 5px;
        letter-spacing: 0;
    }
</style>

<div style="width: 180mm; padding: 7mm; padding-top: -2mm;margin-top:-10px; font-size: 10pt; height: 230mm">
    <div style="text-align: center;">
        <h1 style="font-size: 23px; margin-bottom: 0; color: black">
            DECLARAÇÃO DE ACTIVIDADE<br/>
            <small style="color: black">(REGULAMENTO (CE) Nº. 561/2006 OU AETR)</small>
        </h1>
        <p style="font-size: 12px; margin-top: 12px; font-style: italic">Preencher (texto dactilografado) e assinar antes de cada viagem.<br/>Conservar juntamente com os registos originais do aparelho de controlo, sempre que necessário.</p>
        <h5 style="font-weight: bold; margin-bottom: 5px">AS FALSAS DECLARAÇÕES CONSTITUEM UMA INFRACÇÃO</h5>
    </div>
    <div style="line-height: 18px; text-align: justify; border:2px solid black; padding-left: 2mm">
        <p style="font-weight: bold; font-size:12px">Parte a preencher pela empresa</p>
        <p><small style="font-size:10px">(1)&emsp;&nbsp;&nbsp; Nome da Empresa:</small> {{ @$company->name }}</p>
        <p><small style="font-size:10px">(2)&emsp;&nbsp;&nbsp; Morada, código postal, localidade, país:</small> {{ @$company->address }}, {{ @$company->zip_code }}, {{ @$company->city }}, {{ trans('country.'.@$company->country) }}</p>
        <p><small style="font-size:10px">(3)&emsp;&nbsp;&nbsp; Numero de telefone (incluindo o prefixo internacional):</small> {{ @$company->phone }}</p>
        <p><small style="font-size:10px">(4)&emsp;&nbsp;&nbsp; Número de fax (incluindo o prefixo internacional):</small> {{ @$company->fax }}</p>
        <p><small style="font-size:10px">(5)&emsp;&nbsp;&nbsp; Endereço de correio electrónico:</small> {{ @$company->email }}</p>

        <p style="font-weight: bold; font-size:12px">Eu, abaixo assinado:</p>
        <p><small style="font-size:10px">(6)&emsp;&nbsp;&nbsp; Apelido e nome:</small> {{ $manager->name }}</p>
        <p><small style="font-size:10px">(7)&emsp;&nbsp;&nbsp; Funções na empresa:</small> {{ $manager->professional_role }}</p>

        <p style="font-weight: bold; font-size:12px">declaro que o condutor:</p>
        <p><small style="font-size:10px">(8)&emsp;&nbsp;&nbsp; Apelido e nome:</small> {{ $operator->name }}</p>
        <p><small style="font-size:10px">(9)&emsp;&nbsp;&nbsp; Data de nascimento (dia/mês/ano):</small> {{ $operator->birthdate}}</p>
        <p><small style="font-size:10px">(10)&emsp; Número de carta de condução, de bilhete de identidade ou passaporte:</small> {{ $operator->id_card }}</p>
        <p><small style="font-size:10px">(11)&emsp; que começou a trabalhar na empresa em (dia/mês/ano)</small> {{ $operator->admission_date }}</p>

        <p style="font-weight: bold; font-size:12px">no período:</p>
        <p><small style="font-size:10px">(12)&emsp; de (hora/dia/mês/ano):</small> {{ $startDate }}</p>
        <p><small style="font-size:10px">(13)&emsp; até (hora/dia/mês/ano):</small> {{ $endDate }}</p>
        <?php $i = 14 ?>
        @foreach(trans('admin/shipments.inactivity-reasons') as $key => $title)
        <p><small style="font-size:10px">({{ $i }})&emsp;{!! in_array($key, $reasons) ? $check : $uncheck !!}  {{ $title }}</small></p>
        <?php $i++ ?>
        @endforeach
        <p><small style="font-size:10px">(20)&emsp; Localidade:</small> {{ $company->city }} &emsp;&emsp;<small style="font-size:10px">Data:</small> {{ $docDate }}</small> </p>
        <p>
            <div style="line-height: 21px; margin-bottom: 0px; text-align: left; font-size:10px">
                Assinatura Declarante:  _____________________________________________________________________________________________________<br/>
            </div>
        </p>
    </div>
    <div style="text-align: justify">
        <p style="font-size: 10px; line-height: 14px">(21)&emsp; Eu, abaixo assinado, o condutor, confirmo que, no período acima mencionado, não conduzi nenhum veículo abrangido pela âmbito de aplicação do Regulamento (CE) nº. 561/2006 ou pela AETR.</p>

        <p style="font-size: 10px; line-height: 10px"><br/>(22)&emsp; Localidade:&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; Data:</p>
        <p style="font-size: 10px; line-height: 30px; margin-top: 15px">Assinatura do condutor:_______________________________________________________________________________________________________</p>

        <p style="font-size: 7px; line-height: 3px">1&emsp; A versão eletrónica e pronta a imprimir do presente formulário está disponivel no seguinte endereço: http://ec.europa.eu.</p>
        <p style="font-size: 7px; line-height: 3px">2&emsp; Acordo Europeu relativo ao Trabalho das Tripulações de Veículos que Efectuam Transportes Rodoviários Internacionais.</p>
    </div>
</div>