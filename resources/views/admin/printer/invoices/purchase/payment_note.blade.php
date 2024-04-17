<?php
    $totalPaid = 0
?>
<style>
    table td,
    table th {
        padding: 3px;
    }
</style>
<div class="invoice" style="width: 210mm; font-size: 10pt; height: 260mm">
    <div class="guide-content" style="height: 5cm">
        <div class="guide-row" style="padding-top: 5mm;">
            <div class="fs-9px lh-1-2" style="width: 45%; height: 14mm; float: left; text-align: left;">
                <p style="margin-top: 20px; line-height: 1.5">
                    <b>{{ Setting::get('company_name') }}</b><br/>
                    {{ Setting::get('company_address') }}<br/>
                    {{ Setting::get('company_zip_code') }} {{ Setting::get('company_city') }}<br/>
                    {{ trans('country.' . Setting::get('company_country')) }}
                </p>
                <p>
                    N/ Contribuinte: {{ Setting::get('vat') }}
                </p>
            </div>
            <div class="fs-9px lh-1-2" style="width: 55%; float: left;">
                <div style="text-align: right; margin-top: -40px; margin-bottom: 30px">
                    <p style="font-size: 12px">{{ $copyNumber }}</p>
                    <h4 style="margin: 0; color: #777; font-weight: bold; font-size: 16px;">
                        <b>Documento Interno</b><br>
                    </h4>
                    <h4 style="margin: 5px 0 3px; font-size: 18px; font-weight: bold">Nota de Pagamento {{ $paymentNote->code }}</h4>
                </div>
                <div style="width: 100mm; height: 45mm; margin-right: 50px; border: 1px solid #999; padding: 0 20px; border-radius: 10px">
                    <p style="margin-top: 20px; line-height: 19px; font-size: 13px">
                        <b style="font-weight: bold">{{ $paymentNote->billing_name }}</b><br>
                        {{ $paymentNote->invoices->first()->invoice->billing_address }}
                        <br/>
                        {{ $paymentNote->invoices->first()->invoice->billing_zip_code }} {{ $paymentNote->invoices->first()->invoice->billing_city }}
                        <br/>
                        {{ trans('country.' . $paymentNote->invoices->first()->invoice->billing_country) }}
                    </p>
                    <p style="font-size: 12px; line-height: 18px">
                        @if(@$paymentNote->invoices->first()->invoice->vat)
                            <strong style="font-weight: bold">Contribuinte:</strong> {{ @$paymentNote->invoices->first()->invoice->vat }}<br/>
                        @endif
                        @if(@$paymentNote->invoices->first()->invoice->billing_code)
                            <strong style="font-weight: bold">Código de Fornecedor:</strong> {{ @$paymentNote->invoices->first()->invoice->billing_code }}<br/>
                        @endif
                        @if(@$paymentNote->invoices->first()->invoice->billing_phone)
                            <strong style="font-weight: bold">Telefone:</strong> {{ @$paymentNote->invoices->first()->invoice->billing_phone }}<br/>
                        @endif
                        @if(@$paymentNote->invoices->first()->invoice->billing_email)
                            <strong style="font-weight: bold">E-mail:</strong> {{ @$paymentNote->invoices->first()->invoice->billing_email }}<br/>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
   <div class="guide-content">
        <div class="guide-row" style="padding-top: 5mm;">
            <div class="fs-9px lh-1-2" style="width: 46%; height: 14mm; float: left;">
                <p>
                </p>
            </div>
            <div class="fs-9px lh-1-2" style="width: 54%; height: 14mm; float: left;">
                <div style="border-bottom: 1px solid #000; font-weight: bold">
                    <div style="float: left; width: 25mm">
                        Data
                    </div>
                    <div style="float: left; width: 45%">
                        N./Ref.
                    </div>
                    <div style="float: left; text-align: right; width: 5%">
                        Pág.
                    </div>
                </div>
                <div style="margin-top: 4px">
                    <div style="float: left; width: 44%">
                        {{ $paymentNote->doc_date }}
                    </div>
                    <div style="float: left; width: 45%">
                        {{ $paymentNote->reference }}&nbsp;&nbsp;
                    </div>
                    <div style="float: left; text-align: right; width: 10%;">
                        1/1
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="guide-content" style="height: 150mm">
        <h4 style="font-weight: bold; font-size: 16px; margin: 15px 0 0 0">
            @if($paymentNote->total > 0.00)
            Efetuamos o pagamento de {{ money($paymentNote->total, Setting::get('app_currency')) }} ({{ human_money($paymentNote->total) }})
            @else
            Confirmamos o recebimento de {{ money($paymentNote->total * -1, Setting::get('app_currency')) }} ({{ human_money($paymentNote->total) }})
            @endif
        </h4>
        @if(!$paymentNote->payment_methods->isEmpty())
        <div style="margin-top: 20px">
            <h4 style="font-weight: bold; font-size: 16px; margin: 0">Meios de pagamento utilizados</h4>
        </div>
        <table style="width: 100%; font-size: 11px;">
            <tr>
                <th style="border-bottom: 1px solid #000; width: 80px">Data</th>
                <th style="border-bottom: 1px solid #000; width: 180px;">Método</th>
                <th style="border-bottom: 1px solid #000; width: 80px;">Valor</th>
                <th style="border-bottom: 1px solid #000; width: 180px;">Banco</th>
                <th style="border-bottom: 1px solid #000;">Observações</th>
            </tr>
            @foreach($paymentNote->payment_methods as $line)
            <tr>
                <td>{{ $line->date }}</td>
                <td>{{ @$line->payment_method->name }}</td>
                <td>{{ money($line->total, Setting::get('app_currency')) }}</td>
                <td>{{ @$line->bankInfo->name }}</td>
                <td>{{ $line->obs }}</td>
            </tr>
            @endforeach
        </table>
        @endif
        <div style="margin-top: 20px">
            <h4 style="font-weight: bold; font-size: 16px; margin: 0">Documentos relacionados</h4>
            <p>
                Para pagamento do(s) seguinte(s) documento(s):
            </p>
        </div>
        <table style="width: 100%; font-size: 11px;">
            <tr>
                <th style="border-bottom: 1px solid #000; ">Tipo Doc.</th>
                <th style="border-bottom: 1px solid #000; width: 130px;">Nº Documento</th>
                <th style="border-bottom: 1px solid #000; width: 80px;">Data</th>
                <th style="border-bottom: 1px solid #000; width: 120px; text-align: right;">Valor Documento</th>
                <th style="border-bottom: 1px solid #000; width: 100px; text-align: right;">Valor Pago</th>
                <th style="border-bottom: 1px solid #000; width: 100px; text-align: right;">Valor Pendente</th>
            </tr>
            @if($paymentNote->invoices)
                @foreach($paymentNote->invoices as $line)
                    <?php
                    $totalPaid+= $line->total
                    ?>
                    <tr>
                        <td style="padding: 6px 5px">{{ trans('admin/billing.types.' . @$line->invoice->doc_type) }}</td>
                        <td style="padding: 6px 5px;">{{ $line->invoice->reference }}</td>
                        <td style="padding: 6px 5px;">{{ $line->invoice->doc_date }}</td>
                        <td style="padding: 8px 5px; text-align: right;">{{ money(@$line->invoice->total, Setting::get('app_currency')) }}</td>
                        <td style="padding: 6px 5px; text-align: right;">{{ money($line->total, Setting::get('app_currency')) }}</td>
                        <td style="padding: 6px 5px; text-align: right;">{{ money(@$line->total_pending, Setting::get('app_currency')) }}</td>
                    </tr>
                @endforeach
            @endif
        </table>
        @if($paymentNote->discount > 0.00)
            <?php $totalPaid = $totalPaid - $paymentNote->discount ?>
            <h4 style="text-align: right; font-weight: bold; font-size: 14px">
                Total: {{ money($paymentNote->total + $paymentNote->discount, Setting::get('app_currency')) }}
            </h4>
            <h4 style="text-align: right; font-weight: bold; font-size: 14px">
                Desconto Financeiro: {{ money($paymentNote->discount, Setting::get('app_currency')) }}
            </h4>
        @endif
        <h4 style="text-align: right; font-weight: bold">
            @if($paymentNote->total > 0.00)
            Total Pago: {{ money($totalPaid, Setting::get('app_currency')) }}
            @else
            Total Recebido: {{ money($totalPaid * -1, Setting::get('app_currency')) }}
            @endif
        </h4>
    </div>
    <div class="text-center fs-10 w-100">
        * * * ESTE DOCUMENTO NÃO TEM VALOR CONTABILÍSTICO * * *
    </div>
</div>
<div class="fs-6pt" style="padding-left: 10mm; padding-right: 10mm;">
    <div class="pull-left" style="width: 57%"><b style="font-weight: bold">{{ app_brand('docsignature') }}</b></div>
    <div class="pull-left text-right" style="width: 42%">Emitido por: {{ @$paymentNote->user->name ? @$paymentNote->user->name : Auth::user()->name }}</div>
</div>