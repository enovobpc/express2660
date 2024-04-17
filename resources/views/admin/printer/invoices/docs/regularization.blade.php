<?php $totalPaid = 0; ?>
<style>
    table td,
    table th {
        padding: 3px;
    }
</style>
@if($invoice->is_deleted)
<div style="width: 100%; height: 14mm; text-align: right; position: absolute; top: -10px; right: 10px; font-size: 50px">
    ANULADO
</div>
@else
<div style="width: 100%; height: 14mm; text-align: right; position: absolute; top: -10px; right: 10px;">
    <p style="margin-bottom: 0">{{ $copyNumber }}</p>
    <h4 style="margin: 3px 0 3px; font-size: 18px; font-weight: bold">{{ transLocale('admin/global.word.regularization_no', $locale) }} {{ strtoupper($invoice->doc_series) }} {{ $invoice->doc_id }}</h4>
    <p style="font-size: 14px; font-weight: bold">{{ transLocale('admin/global.word.doc_date', $locale) }}: {{ $invoice->doc_date }}</p>
</div>
@endif
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


    <div class="guide-content" style="height: 150mm">
        <h4 style="font-weight: bold; font-size: 16px; margin: 15px 0 0 0">
            {{ transLocale('admin/global.word.regularized_qty', $locale, ['total' => money($invoice->doc_total * -1, Setting::get('app_currency'))]) }}
            @if($locale == 'pt')
            ({{ human_money($invoice->doc_total) }})
            @endif
        </h4>

        <div style="margin-top: 20px">
            <h4 style="font-weight: bold; font-size: 16px; margin: 0">{{ transLocale('admin/global.word.related_documents', $locale) }}</h4>
            <p>{{ transLocale('admin/global.word.to_regularizate_documents', $locale) }}</p>
        </div>
        <table style="width: 100%; font-size: 11px;">
            <tr>
                <th style="border-bottom: 1px solid #000; width: 130px;">{{ transLocale('admin/global.word.doc_type', $locale) }}</th>
                <th style="border-bottom: 1px solid #000; ">{{ transLocale('admin/global.word.doc_no', $locale) }}</th>
                <th style="border-bottom: 1px solid #000; width: 80px;">{{ transLocale('admin/global.word.date', $locale) }}</th>
                <th style="border-bottom: 1px solid #000; width: 120px; text-align: right;">{{ transLocale('admin/global.word.doc_value', $locale) }}</th>
                <th style="border-bottom: 1px solid #000; width: 100px; text-align: right;">{{ transLocale('admin/global.word.paid_value', $locale) }}</th>
                <th style="border-bottom: 1px solid #000; width: 100px; text-align: right;">{{ transLocale('admin/global.word.pending_value', $locale) }}</th>
            </tr>
            @if($invoice->lines)
                @foreach($invoice->lines as $line)
                    <?php $totalPaid+=$line->total_price ?>
                    <tr>
                        <td style="padding: 6px 5px">{{ trans('admin/billing.types.' . @$line->assigned_invoice->doc_type) }}</td>
                        <td style="padding: 6px 5px;">{{ $line->description }}</td>
                        <td style="padding: 6px 5px;">{{ @$line->assigned_invoice->doc_date }}</td>
                        <td style="padding: 8px 5px; text-align: right;">{{ money((@$line->assigned_invoice->doc_total), Setting::get('app_currency')) }}</td>
                        <td style="padding: 6px 5px; text-align: right;">{{ money($line->total_price, Setting::get('app_currency')) }}</td>
                        <td style="padding: 6px 5px; text-align: right;">{{ money((@$line->assigned_invoice->doc_total) - $line->total_price, Setting::get('app_currency')) }}</td>
                    </tr>
                @endforeach
            @endif
        </table>
        <h4 style="text-align: right; font-weight: bold">{{ transLocale('admin/global.word.total_regularized', $locale) }}: {{ money(@$totalPaid, Setting::get('app_currency')) }}</h4>
    </div>
    <div style="text-align: center; margin-bottom: 20px; font-size: 11px">* * * Este documento não serve de recibo nem tem validade fiscal * * *</div>

</div>
<div class="fs-6pt" style="padding-left: 0mm; padding-right: 0mm;">
    <div class="pull-left" style="width: 57%"><b style="font-weight: bold">{{ transLocale('admin/global.word.processed_by', $locale) }} {{ app_brand('docsignature') }}</b></div>
    <div class="pull-left text-right" style="width: 42%">{{ transLocale('admin/global.word.issued_on', $locale) }} {{ $invoice->created_at }}</div>
</div>