<?php

$customerId   = @$row->customer_id;
$customerName = @$row->customer->name;
$customerCode = @$row->customer->code;
$refundName   = @$row->customer->name;
$refundEmail  = @$row->customer->refunds_email ? @$row->customer->refunds_email : @$row->customer->contact_email;
$refundIban   = @$row->customer->iban_refunds;

if(!empty($row->requested_by) && $row->customer_id != $row->requested_by) {
    $customerId   = @$row->requested_by;
    $customerName = @$row->customer->name;
    $customerCode = @$row->requested_customer->code;
    $refundName   = @$row->requested_customer->name;
    $refundEmail  = @$row->requested_customer->refunds_email ? @$row->requested_customer->refunds_email : @$row->requested_customer->contact_email;
    $refundIban   = @$row->requested_customer->iban_refunds;
}

?>
<a href="{{ route('admin.refunds.customers.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg" class="bold">
    {{ money($row->charge_price, Setting::get('app_currency')) }}
</a>

{{--
<div>{{ @$row->customer_id }} / {{ $row->requested_by }}</div>
--}}

<div class="lbl-customer"
     data-toggle="tooltip"
     data-html="true"
     title="Cliente: {{ $customerName }} <hr/> Reembolsar a: {{ $refundName }}"
     data-name="{{ $refundName }}"
     data-id="{{ $customerId }}"
     data-email="{{ $refundEmail }}"
     data-iban="{{ $refundIban }}"
     data-total="{{ $row->charge_price }}">
    <span class="label bg-gray"><i class="fas fa-user"></i> {{ $customerCode }}</span>
</div>
