<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NormalizeInvoicesBillingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('customers_billing', function (Blueprint $table) {
            if (!Schema::hasColumn('customers_billing', 'invoice_doc_id')) {
                $table->string('invoice_doc_id')
                    ->nullable()
                    ->after('invoice_id');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'invoice_doc_id')) {
                $table->string('invoice_doc_id')
                    ->nullable()
                    ->after('invoice_id');
            }
        });


        //MIGRA CAMPOS BASE DE DADOS
        \App\Models\CustomerBilling::whereNotNull('invoice_id')
            ->update([
                "invoice_doc_id" => DB::raw("invoice_id"),
            ]);

        \App\Models\Shipment::whereNotNull('invoice_id')
            ->update([
                "invoice_doc_id" => DB::raw("invoice_id"),
            ]);

        \App\Models\CustomerBilling::whereNotNull('invoice_id')
            ->update([
                "invoice_id" => null,
            ]);

        \App\Models\Shipment::whereNotNull('invoice_id')
            ->update([
                "invoice_id" => null,
            ]);
/*
       $customersBilling = \App\Models\CustomerBilling::whereNotNull('invoice_doc_id')
           ->whereNull('invoice_id')
           ->where('invoice_type', '<>', 'nodoc')
           ->get(['id', 'invoice_id', 'invoice_doc_id', 'invoice_type', 'api_key']);
        foreach ($customersBilling as $billing) {

            //find Invoice
            $invoiceRow = \App\Models\Invoice::where('doc_id', $billing->invoice_doc_id)
                ->where('doc_type', $billing->invoice_type)
                ->where('api_key', $billing->api_key)
                ->first(['id']);

            if(@$invoiceRow->id) {
                $billing->invoice_id = @$invoiceRow->id;
                $billing->save();
            }
        }*/

        //MIGRA SHIPMENTS
        /*$shipmentsInvoices = \App\Models\Shipment::whereNotNull('invoice_doc_id')
            ->whereNull('invoice_id')
            ->where('invoice_type', '<>', 'nodoc')
            ->get(['id', 'invoice_id', 'invoice_doc_id', 'invoice_type', 'invoice_key']);

        foreach ($shipmentsInvoices as $shipment) {

            //find Invoice
            $invoiceRow = \App\Models\Invoice::where('doc_id', $shipment->invoice_doc_id)
                ->where('doc_type', $shipment->invoice_type)
                ->where('api_key', $shipment->invoice_key)
                ->first(['id']);

            if(@$invoiceRow->id) {
                $shipment->invoice_id = @$invoiceRow->id;
                $shipment->save();
            }
        }*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        //MIGRA
        $customersBilling = \App\Models\CustomerBilling::whereNotNull('invoice_doc_id')->get();
        foreach ($customersBilling as $billing) {
            $billing->invoice_id = @$billing->invoice_doc_id;
            $billing->save();
        }

        //MIGRA SHIPMENTS
        $shipmentsInvoices = \App\Models\CustomerBilling::whereNotNull('invoice_doc_id')->get();
        foreach ($shipmentsInvoices as $shipment) {
            $shipment->invoice_id = $shipment->invoice_doc_id;
            $shipment->save();
        }

        Schema::table('customers_billing', function (Blueprint $table) {
            if (Schema::hasColumn('customers_billing', 'invoice_doc_id')) {
                $table->dropColumn('invoice_doc_id');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'invoice_doc_id')) {
                $table->dropColumn('invoice_doc_id');
            }
        });
    }
}
