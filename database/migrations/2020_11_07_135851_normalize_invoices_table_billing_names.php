<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NormalizeInvoicesTableBillingNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $invoices = \App\Models\Invoice::with('customer')
            ->whereNull('billing_name')
            ->orWhere('billing_name', '')
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->billing_code      = @$invoice->customer->code;
            $invoice->billing_name      = @$invoice->customer->billing_name;
            $invoice->billing_address   = @$invoice->customer->billing_address;
            $invoice->billing_zip_code  = @$invoice->customer->billing_zip_code;
            $invoice->billing_city      = @$invoice->customer->billing_city;
            $invoice->billing_country   = @$invoice->customer->billing_country;
            $invoice->billing_email     = @$invoice->customer->billing_email;
            $invoice->vat               = @$invoice->customer->vat;
            $invoice->save();
        }

        \App\Models\WebserviceMethod::where('method', 'quickbox')->update([
            'method'  => 'enovo_tms',
            'name'    => 'ENOVO TMS',
            'enabled' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
