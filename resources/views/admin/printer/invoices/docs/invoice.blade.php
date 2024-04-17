<style>
    table td,
    table th {
        padding: 3px;
    }
</style>
<div style="width: 100%; height: 14mm; text-align: right; position: absolute; top: -10px; right: 10px;">
    <p style="margin-bottom: 0">{{ $copyNumber }}</p>
    <h4 style="margin: 3px 0 3px; font-size: 18px; font-weight: bold">{{ transLocale('admin/global.word.invoice_no', $locale) }} {{ strtoupper($invoice->doc_series) }} {{ $invoice->doc_id }}</h4>
    <p style="font-size: 14px; font-weight: bold">{{ transLocale('admin/global.word.doc_date', $locale) }}: {{ $invoice->doc_date }}</p>
</div>
<div class="invoice" style="width: 210mm; font-size: 10pt; height: 261mm;">
    <div class="guide-content" style="height: 5cm">
        <div class="guide-row" style="padding-top: 0mm;">
            <div class="fs-7px lh-1-2" style="width: 50%; height: 14mm; float: left;">
                <p style="margin-top: 10px; margin-bottom: 4px; line-height: 14px; font-size: 11px;">
                    <b style="font-weight: bold; font-size: 13px; margin-bottom: 4px">{{ Setting::get('company_name') }}</b><br>
                </p>
                <p style="line-height: 14px; font-size: 11px;">
                    {{ Setting::get('company_address') }}
                    <br/>
                    {{ Setting::get('company_zip_code') }} {{ Setting::get('company_city') }}
                    <br/>
                    {{ trans('country.' . Setting::get('company_country')) }}
                </p>
                <p style="font-size: 11px; line-height: 16px">
                    <strong style="font-weight: bold">{{ transLocale('admin/global.word.tin', $locale) }}:</strong> {{ Setting::get('vat') }}<br/>
                    @if(Setting::get('company_phone'))
                        <strong style="font-weight: bold">{{ transLocale('admin/global.word.phone', $locale) }}:</strong> {{ Setting::get('company_phone') }}<br/>
                    @endif
                    @if(Setting::get('company_email'))
                        <strong style="font-weight: bold">{{ transLocale('admin/global.word.email', $locale) }}:</strong> {{ Setting::get('company_email') }}<br/>
                    @endif
                    @if(Setting::get('company_website'))
                        <strong style="font-weight: bold">Website:</strong> {{ Setting::get('company_website') }}<br/>
                    @endif

                    @if(Setting::get('company_capital'))
                        <strong>{{ transLocale('admin/global.word.social_capital', $locale) }}:</strong> {{ Setting::get('company_capital') }}<br/>
                    @endif
                    <br/>
                    @if(Setting::get('bank_iban'))
                        <strong style="font-weight: bold">IBAN:</strong> {{ Setting::get('bank_name') }} / {{ Setting::get('bank_iban') }}<br/>
                    @else
                        &nbsp;
                    @endif
                </p>
            </div>
            <div class="fs-9px lh-1-2" style="margin-top: 40px; width: 40%; height: 30mm; float: left; padding: 15px; border: 1px solid #999; border-radius: 5px">
                <p style="line-height: 19px; font-size: 13px">
                    <b style="font-weight: bold; margin-bottom: 5px">{{ $invoice->billing_name }}</b>
                </p>
                <p style="line-height: 19px; font-size: 13px">
                    {{ $invoice->billing_address }}
                    <br/>
                    {{ $invoice->billing_zip_code }} {{ $invoice->billing_city }}
                    <br/>
                    {{ trans('country.' . $invoice->billing_country) }}
                </p>
               {{-- <p style="font-size: 12px; line-height: 18px">
                    @if(@$invoice->vat)
                        <strong style="font-weight: bold">Contribuinte:</strong> {{ @$invoice->vat }}<br/>
                    @endif
                </p>--}}
            </div>
        </div>
    </div>
    <div class="guide-content">
        <div class="guide-row" style="padding-top: 5mm;">
            <div class="lh-1-2" style="width: 100%; height: 14mm; float: left; font-size: 11.5px">
                <div style="font-weight: bold; border: 1px solid #ccc; padding: 3px; border-radius: 3px; background: #ccc">
                    <div style="float: left; width: 100px;">
                        {{ transLocale('admin/global.word.document', $locale) }}
                    </div>
                    <div style="float: left; width: 100px;">
                        {{ transLocale('admin/global.word.customer_tin', $locale) }}
                    </div>
                    <div style="float: left; width: 60px;">
                        {{ transLocale('admin/global.word.customer', $locale) }}
                    </div>
                    <div style="float: left; width: 270px;">
                        {{ transLocale('admin/global.word.doc_ref', $locale) }}
                    </div>
                    <div style="float: left; width: 85px;">
                        {{ transLocale('admin/global.word.payment_condition', $locale) }}
                    </div>
                    <div style="float: left; width: 80px;">
                        {{ transLocale('admin/global.word.due_date', $locale) }}
                    </div>
                    <div style="float: left; text-align: right; width: 30px;">
                        {{ transLocale('admin/global.word.pag', $locale) }}.
                    </div>
                </div>
                <div style="margin-top: 4px; padding-left: 4px">
                    <div style="float: left; width: 100px">
                        <span style="font-weight: bold">{{ $invoice->doc_series }} {{ $invoice->doc_id }}</span>
                    </div>
                    <div style="float: left; width: 100px">
                        {{ $invoice->vat }}
                    </div>
                    <div style="float: left; width: 60px;">
                        {{ $invoice->billing_code }}
                    </div>
                    <div style="float: left; width: 270px;">
                        {{ $invoice->reference }}&nbsp;
                    </div>
                    <div style="float: left; width: 85px">
                        &nbsp;
                    </div>
                    <div style="float: left; width: 80px">
                        {{ $invoice->due_date }}
                    </div>
                    <div style="float: left; text-align: right; width: 30px;">
                        1/1
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="guide-content" style="height: 120mm;">
        <table style="width: 100%; font-size: 11px; margin-top: 0px; line-height: 14px">
            <tr>
                <th style="border-bottom: 1px solid #999; width: 60px;">{{ transLocale('admin/global.word.item_ref', $locale) }}</th>
                <th style="border-bottom: 1px solid #999">{{ transLocale('admin/global.word.designation', $locale) }}</th>
                <th style="border-bottom: 1px solid #999; width: 40px; text-align: right;">{{ transLocale('admin/global.word.qty', $locale) }}</th>
                <th style="border-bottom: 1px solid #999; width: 40px; text-align: right;">{{ transLocale('admin/global.word.uni', $locale) }}</th>
                <th style="border-bottom: 1px solid #999; width: 90px; text-align: right;">{{ transLocale('admin/global.word.uni_price', $locale) }}</th>
                <th style="border-bottom: 1px solid #999; width: 70px; text-align: right;">{{ transLocale('admin/global.word.discount', $locale) }}</th>
                <th style="border-bottom: 1px solid #999; width: 70px; text-align: right;">{{ transLocale('admin/global.word.tax', $locale) }}</th>
                <th style="border-bottom: 1px solid #999; width: 95px; text-align: right;">{{ transLocale('admin/global.word.total_net', $locale) }}</th>
            </tr>
            @foreach($invoice->lines as $line)
            <tr>
                <td style="padding: 2px">{{ $line->reference }}</td>
                <td style="padding: 2px">{{ $line->description }}</td>
                <td style="padding: 2px; text-align: right;">{{ $line->qty }}</td>
                <td style="padding: 2px; text-align: right;">Un</td>
                <td style="padding: 2px; text-align: right;">{{ money($line->total_price, $invoice->currency) }}</td>
                <td style="padding: 2px; text-align: right;">{{ $line->discount ? money($line->discount, '%') : '' }}</td>
                <td style="padding: 2px; text-align: right;">{{ money($line->tax_rate, '%') }}</td>
                <td style="padding: 2px; text-align: right;">{{ money($line->subtotal, $invoice->currency) }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    {{--<div style="text-align: center; margin-bottom: 20px; font-size: 11px">* * * Este documento não serve de fatura nem tem validade fiscal * * *</div>--}}

    <div class="guide-content" style="border-top: 1px solid #999; padding-top: 10px">
        <div class="guide-row">
            <div class="lh-1-2" style="width: 430px; height: 24mm; margin-bottom: -10px; float: left;">
                <h4 style="margin: 0; font-size: 15px; font-weight: bold; padding-left: 2px">{{ transLocale('admin/global.word.taxes_summary', $locale) }}</h4>
                <table style="width: 100%; font-size: 11px">
                    <tr>
                       <th style="border-bottom: 1px solid #999">{{ transLocale('admin/global.word.designation', $locale) }}</th>
                       <th style="border-bottom: 1px solid #999; text-align: right; width: 30px">{{ transLocale('admin/global.word.value', $locale) }}</th>
                       <th style="border-bottom: 1px solid #999; text-align: right; width: 90px">{{ transLocale('admin/global.word.incidence', $locale) }}</th>
                       <th style="border-bottom: 1px solid #999; text-align: right; width: 90px">{{ transLocale('admin/global.word.total', $locale) }}</th>
                    </tr>
                    @foreach($taxesNormal as $taxValue => $taxItems)
                        <?php
                        $taxIncidence = $taxItems->sum('subtotal');
                        $taxTotal     = $taxIncidence * ($taxValue / 100);
                        ?>
                    <tr>
                        <td style="padding: 1px 0">{{ transLocale('admin/global.word.vat_tax', $locale) }} {{ $taxValue }}%</td>
                        <td style="text-align: right; padding: 1px 0">{{ money($taxValue) }}%</td>
                        <td style="text-align: right; padding: 1px 0">{{ money($taxIncidence, Setting::get('app_currency')) }}</td>
                        <td style="text-align: right; padding: 1px 0">{{ money($taxTotal, Setting::get('app_currency')) }}</td>
                    </tr>
                    @endforeach
                    @foreach($taxesExempt as $taxReason => $taxItems)
                        <?php

                            $taxIncidence = $taxItems->sum('subtotal');
                            $taxTotal     = 0;
                        ?>
                        <tr>
                            <td style="padding: 1px 0">{{ transLocale('admin/global.word.tax_exempt', $locale) }} ({{ $taxReason }})</td>
                            <td style="text-align: right; padding: 1px 0">0,00%</td>
                            <td style="text-align: right; padding: 1px 0">{{ money($taxIncidence, Setting::get('app_currency')) }}</td>
                            <td style="text-align: right; padding: 1px 0">{{ money($taxTotal, Setting::get('app_currency')) }}</td>
                        </tr>
                    @endforeach
                </table>
                <p style="font-size: 11px; margin-top: 15px">
                    {{ $invoice->obs }}
                </p>
            </div>
            <div class="lh-1-2" style="width: 270px; height: 14mm; float: right">
                <div>
                    <table style="width: 100%; font-size: 12px">
                        <tr>
                            <th class="text-right">{{ transLocale('admin/global.word.total_net', $locale) }}</th>
                            <td class="text-right" style="font-weight: bold">{{ money($invoice->doc_subtotal, Setting::get('app_currency')) }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">{{ transLocale('admin/global.word.total_discount', $locale) }}</th>
                            <td class="text-right" style="font-weight: bold">{{ money($invoice->doc_discount, Setting::get('app_currency')) }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">{{ transLocale('admin/global.word.total_taxes', $locale) }}</th>
                            <td class="text-right" style="font-weight: bold">{{ money($invoice->doc_vat, Setting::get('app_currency')) }}</td>
                        </tr>
                        <tr>
                            <th class="text-right" style="font-size: 18px">{{ transLocale('admin/global.word.total_pay', $locale) }}</th>
                            <td class="text-right" style="font-size: 18px; font-weight: bold; width: 120px">{{ money($invoice->doc_total, Setting::get('app_currency')) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{--
    <div class="guide-content" style="margin-top: 15px; border-top: 1px solid #999; padding-top: 10px">
        <div class="lh-1-2" style="width: 40%; height: 10mm; float: left">
            <div style="float: left; width: 50px; margin-right: 5px;">
                <img src="{{ asset('assets/img/default/mb.svg') }}" style="height: 50px"/>
            </div>
            <div style="float: left; width: 210px">
                <span style="font-weight: bold; font-size: 14px;">Pagamento Multibanco</span><br/>
                <table style="width: 100%; font-size: 11px; line-height: 11px; margin-top: 5px">
                    <tr>
                        <th style="padding: 0">Entidade</th>
                        <th style="padding: 0">Referência</th>
                        <th style="padding: 0">Valor</th>
                    </tr>
                    <tr>
                        <td style="padding: 0">00000</td>
                        <td style="padding: 0">00000</td>
                        <td style="padding: 0">{{ money($invoice->doc_total, Setting::get('app_currency')) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="lh-1-2" style="width: 60%; height: 10mm; float: left">
            <div style="font-weight: bold; font-size: 14px;">Pagamento Transferência</div>
            <div style="font-size: 11px; margin-top: 5px">
                {{ Setting::get('bank_name') }}<br/> {{ Setting::get('bank_iban') }}
            </div>
        </div>
    </div>--}}
</div>
<div class="fs-6pt" style="padding-left: 0mm; padding-right: 0mm;">
    <div class="pull-left" style="width: 57%"><b style="font-weight: bold">{{ transLocale('admin/global.word.processed_by', $locale) }} {{ app_brand('docsignature') }}</b></div>
    <div class="pull-left text-right" style="width: 42%">{{ transLocale('admin/global.word.issued_on', $locale) }} {{ $invoice->created_at }}</div>
</div>