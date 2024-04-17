<?php
$currency = Setting::get('app_currency');
?>
<style>
    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table td {
        padding: 5px;
        font-size: 11px;
    }

    .table th {
        padding: 5px;
        font-size: 12px;
        font-weight: bold;
        color: white;
        background: rgba(56, 55, 55, 0.705)  
    }

    .table {
        border: 0.5px solid #555;
    }

    

    .table th {
        border: 0.5px solid #555;
    }
</style>
<div style="margin-bottom: 5px; margin-top: -20px; margin-left: -10px">
    <table class="table table-striped">
        <tr>
            <td>
                <span style="font-weight: bold; font-size: 13px">@trans('Descrição serviço:')</span>  <span style="font-size: 12px">{{@$maintenance->title}}</span>
            </td>
            <td>
                <span style="font-weight: bold; font-size: 13px">@trans('Viatura:')</span>  <span style="font-size: 12px"> {{@$maintenance->vehicle->license_plate}}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span style="font-weight: bold; font-size: 13px">@trans('Data:')</span>  <span style="font-size: 12px">{{ \Carbon\Carbon::parse(@$maintenance->date)->format('Y-m-d') }}</span>
            </td>
            <td>
                <span style="font-weight: bold; font-size: 13px">@trans('Km Viatura:')</span>  <span style="font-size: 12px"> {{@$maintenance->km}}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span style="font-weight: bold; font-size: 13px">@trans('Fornecedor:')</span>  <span style="font-size: 12px"> {{@$maintenance->provider->name}}</span>
            </td>
            <td>
                <span style="font-weight: bold; font-size: 13px">@trans('Operador:')</span>  <span style="font-size: 12px"> {{@$maintenance->operator->name}}</span>
            </td>
        </tr>
    </table>
</div>

<div>
    <h3>
        @trans('Peças Associadas')
    </h3>
</div>

<div style="margin-bottom: 5px; margin-left: -10px">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>
                    @trans('Designação')
                </th>
                <th>
                    @trans('Referência')  
                 </th>
                 <th>
                    @trans('Marca')  
                 </th>
                 <th>
                    @trans('Categoria')  
                 </th>
                 <th style="width:20px">
                    @trans('Quantidade')
                 </th>
                 <th style="width:75px">
                    @trans('Preço Custo')
                 </th>
            </tr>
        </thead>
        <tbody>
            @foreach($maintenance->parts as $part)
                <tr>
                    <td>
                        {{ $part->product->name }}
                    </td>
                    <td>
                        {{ $part->product->reference }}
                    </td>
                    <td>
                        {{ @$part->product->brand->name }}
                    </td>
                    <td>
                        {{ trans('admin/fleet.parts.categories.' . @$part->category) }}
                    </td>
                    <td>
                        {{ $part->qty }}
                    </td>
                    <td>
                        {{ money($part->price ?? 0.00) }} {{ $currency }}  
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="row" style="padding-top: 5mm; margin-left:75%">
    <div class="fs-10pt p-5 m-r-20" style="border-bottom: 1px solid #ddd; background: #f2f2f2;">
        @trans('TOTAL DOCUMENTO')
    </div>
    <table style="float: right; orientation: right">
        <tr>
            <td>
                <span style="font-size:12px; ">
                    @trans('Valor liquido:')'
                </span>
            </td>
            <td>
                <span style="font-size:12px;">
                    {{money($maintenance->total) ?? 0.00}} {{$currency}}  
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <span style="font-size:12px; ">
                    @trans('Valor Iva:')'
                </span>
            </td>
            <td>
                <span style="font-size:12px; ">
                    {{money($maintenance->total * 0.23) ?? 0.00}} {{$currency}}  
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <span style="font-size:14px; font-weight: bold ">
                    @trans('Valor Total:')'
                </span>
            </td>
            <td>
                <span style="font-size:14px; font-weight: bold ">
                    {{money($maintenance->total *1.23) ?? 0.00}} {{$currency}}  
                </span>
            </td>
        </tr>
    </table>
</div>
<div class="text-center fs-10 w-100 m-b-50">
    @trans('* * * ESTE DOCUMENTO NÃO SERVE DE FATURA * * *')'
</div>
<div class="fs-6pt" style="padding-left: 10mm; padding-right: 10mm;">
    <div class="pull-left" style="width: 57%"><b style="font-weight: bold">{{ app_brand('docsignature') }}</b></div>
    <div class="pull-left text-right" style="width: 42%">@trans('Emitido por:')' {{ Auth::user()->name }}</div>
</div>
