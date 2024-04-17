<style>
    table td,
    table th {
        padding: 3px;
    }
</style>
<div class="invoice" style="width: 210mm; font-size: 10pt; height: 260mm">
    <div class="guide-content" style="height: 5cm">
        <div class="guide-row" style="padding-top: 5mm;">
            <div class="fs-9px lh-1-2" style="width: 50%; height: 14mm; float: left;">
                <p style="margin-top: 20px; line-height: 19px; font-size: 13px">
                    <b style="font-weight: bold">{{ $invoice->billing_name }}</b><br>
                    {{ $invoice->billing_address }}
                    <br/>
                    {{ $invoice->billing_zip_code }} {{ $invoice->billing_city }}
                    <br/>
                    {{ trans('country.' . $invoice->billing_country) }}
                </p>
                <p style="font-size: 12px; line-height: 18px">
                    @if(@$invoice->vat)
                        <strong style="font-weight: bold">Contribuinte:</strong> {{ @$invoice->vat }}<br/>
                    @endif
                    @if(@$invoice->billing_code)
                        <strong style="font-weight: bold">Código de Fornecedor:</strong> {{ @$invoice->billing_code }}<br/>
                    @endif
                    @if(@$invoice->billing_phone)
                        <strong style="font-weight: bold">Telefone:</strong> {{ @$shipment->billing_phone }}<br/>
                    @endif
                    @if(@$invoice->billing_email)
                        <strong style="font-weight: bold">E-mail:</strong> {{ @$shipment->billing_email }}<br/>
                    @endif
                        <strong style="font-weight: bold">IBAN Pagamentos:</strong> {{ strtoupper(@$invoice->provider->iban) }}<br/>
                    @if($invoice->doc_type == 'provider-invoice-receipt')
                        @if(@$invoice->bank_id)
                            <strong style="font-weight: bold">Banco:</strong> {{ @$invoice->bank->name }}<br/>
                        @endif
                        @if(@$invoice->payment_method_id)
                            <strong style="font-weight: bold">Método de Pagamento:</strong> {{ @$invoice->payment->name }}<br/>
                        @endif
                    @endif
                </p>
            </div>
            <div class="fs-9px lh-1-2" style="width: 50%; height: 14mm; float: left; text-align: right;">
                <p>{{ @$copyNumber }}</p>
                <h4 style="margin: 0; color: #777; font-weight: bold; font-size: 20px;">
                    <b>Documento Interno</b><br>
                </h4>
                {{--<h4 style="margin: 5px 0 3px; font-size: 20px; font-weight: bold">Fatura Fornecedor Nº FF {{ strtoupper($invoice->reference) }}</h4>--}}
                @if($invoice->doc_type == 'order')
                    <h4 style="margin: 5px 0 3px; font-size: 20px; font-weight: bold">Encomenda Nº {{ $invoice->reference }}</h4>
                @else
                    <h4 style="margin: 5px 0 3px; font-size: 20px; font-weight: bold">Fatura Fornecedor Nº {{ $invoice->code }}</h4>
                @endif
                <p style="font-size: 16px; font-weight: bold">Data Emissão: {{ $invoice->doc_date }}</p>
            </div>
        </div>
    </div>
    <div class="guide-content">
        <div class="guide-row" style="padding-top: 5mm;">
            <div class="fs-9px lh-1-2" style="width: 55%; height: 14mm; float: left;">
                <p>
                </p>
            </div>
            <div class="fs-9px lh-1-2" style="width: 45%; height: 14mm; float: left;">
                <div style="border-bottom: 1px solid #000; font-weight: bold">
                    <div style="float: left; width: 44%">
                        Ref.ª Fornecedor
                    </div>
                    <div style="float: left; width: 45%">
                        &nbsp;
                    </div>
                    <div style="float: left; text-align: right; width: 10%">
                        Pág.
                    </div>
                </div>
                <div style="margin-top: 4px">
                    <div style="float: left; width: 44%">
                        {{ $invoice->reference }}
                    </div>
                    <div style="float: left; width: 45%">
                        &nbsp;
                    </div>
                    <div style="float: left; text-align: right; width: 10%;">
                        1/1
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="guide-content" style="height: 150mm">
        <table style="width: 100%; font-size: 12px; margin-top: 50px">
            <tr>
                <th style="border-bottom: 1px solid #000; width: 90px;">Ref.ª Artigo</th>
                <th style="border-bottom: 1px solid #000">Designação</th>
                <th style="border-bottom: 1px solid #000; width: 40px; text-align: right;">Qtd.</th>
                <th style="border-bottom: 1px solid #000; width: 40px; text-align: right;">Uni.</th>
                <th style="border-bottom: 1px solid #000; width: 90px; text-align: right;">PVP Unit.</th>
                <th style="border-bottom: 1px solid #000; width: 70px; text-align: right;">Desconto</th>
                <th style="border-bottom: 1px solid #000; width: 70px; text-align: right;">Imposto</th>
                <th style="border-bottom: 1px solid #000; width: 95px; text-align: right;">Total Liquido</th>
            </tr>
            @if($invoice->lines->isEmpty())
                <tr>
                    <td style="padding: 8px 5px"></td>
                    <td style="padding: 8px 5px"></td>
                    <td style="padding: 8px 5px; text-align: right;"></td>
                    <td style="padding: 8px 5px; text-align: right;">Uni</td>
                    <td style="padding: 8px 5px; text-align: right;">{{ money($invoice->total, $invoice->currency) }}</td>
                    <td style="padding: 8px 5px; text-align: right;">{{ $invoice->discount ? money($invoice->discount, '%') : '' }}</td>
                    <td style="padding: 8px 5px; text-align: right;"></td>
                    <td style="padding: 8px 5px; text-align: right;">{{ money($invoice->subtotal, $invoice->currency) }}</td>
                </tr>
            @else
                @foreach($invoice->lines as $line)
                <tr>
                    <td style="padding: 8px 5px">{{ $line->reference }}</td>
                    <td style="padding: 8px 5px">{{ $line->description }}</td>
                    <td style="padding: 8px 5px; text-align: right;">{{ $line->qty }}</td>
                    <td style="padding: 8px 5px; text-align: right;">Uni</td>
                    <td style="padding: 8px 5px; text-align: right;">{{ money($line->total_price, $invoice->currency) }}</td>
                    <td style="padding: 8px 5px; text-align: right;">{{ $line->discount ? money($line->discount, '%') : '' }}</td>
                    <td style="padding: 8px 5px; text-align: right;">{{ money($line->tax_rate, '%') }}</td>
                    <td style="padding: 8px 5px; text-align: right;">{{ money($line->subtotal, $invoice->currency) }}</td>
                </tr>
                @endforeach
            @endif
        </table>
        @if($invoice->obs)
        <p style="margin-top: 10px">{{ $invoice->obs }}</p>
        @endif
    </div>
    @if($invoice->doc_type != 'order')
    <div class="text-center fs-10 w-100">
        * * * ESTE DOCUMENTO NÃO TEM VALOR CONTABILÍSTICO NEM SERVE DE FATURA * * *
    </div>
    @endif
</div>
<div class="fs-6pt" style="padding-left: 10mm; padding-right: 10mm;">
    <div class="pull-left" style="width: 57%"><b style="font-weight: bold">{{ app_brand('docsignature') }}</b></div>
    <div class="pull-left text-right" style="width: 42%">Emitido por: {{ Auth::user()->name }}</div>
</div>