<tr style="display: {{ ($i >= $rowsVisible && !empty($billing->lines)) || (empty($billing->lines) && $i > 0) ? 'none' : '' }}">
    <td style="vertical-align: top">
        <div class="billing-remove-row">
            <i class="fas fa-times text-red"></i>
        </div>
        <div class="input-group input-group-sm input-description w-100" style="position: relative">
            <span class="input-group-addon label-reference">{{ @$ref }}</span>
            {{ Form::hidden('line['.$key.'][billing_product_id]', @$billingProductId, ['class' => 'input-id']) }}
            {{ Form::hidden('line['.$key.'][reference]', @$ref, ['class' => 'input-reference']) }}
            {{ Form::text('line['.$key.'][description]', @$desc, ['class' => 'form-control search-product']) }}
        </div>
    </td>
    <td style="vertical-align: top">
        {{ Form::text('line['.$key.'][qty]', $qty, ['class' => 'form-control input-sm text-center input-qty nospace decimal']) }}
    </td>
    <td style="vertical-align: top">
        <div class="input-group input-group-sm">
            {{ Form::text('line['.$key.'][total_price]', number(@$price), ['class' => 'form-control input-price nospace decimal']) }}
            <div class="input-group-addon" style="padding: 5px">{{ Setting::get('app_currency') }}</div>
        </div>
    </td>
    <td style="vertical-align: top">
        <div class="input-group input-group-sm">
            {{ Form::text('line['.$key.'][discount]', number(@$discount), ['class' => 'form-control input-discount nospace decimal']) }}
            <div class="input-group-addon" style="padding: 5px">%</div>
        </div>
    </td>
    <td style="vertical-align: top">
        <div class="input-group input-group-sm">
            {{ Form::text('line['.$key.'][subtotal]', number(@$subtotal), ['class' => 'form-control text-blue bold p-l-5 input-subtotal nospace decimal', 'readonly']) }}
            <div class="input-group-addon text-blue bold" style="padding: 5px">{{ Setting::get('app_currency') }}</div>
        </div>
    </td>
    <td class="vertical-align-top input-sm">
        <div class="vat-input">
            {{ Form::select('line['.$key.'][tax_rate]', $vatTaxes, @$exemption, ['class' => 'form-control select2 tax-rate']) }}
        </div>
    </td>
</tr>


