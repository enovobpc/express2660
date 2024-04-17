<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInvoicesSeries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('invoices_series')) {

            Schema::create('invoices_series', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('doc_type')->index();
                $table->string('code')->index();
                $table->string('name');
                $table->string('serie_id', 4)->index();
                $table->string('api_key');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        $uniqueKeys = \App\Models\Invoice::filterSource()
            ->whereNotNull('api_key')
            ->groupBy('api_key')
            ->pluck('api_key', 'api_key')
            ->toArray();

        foreach ($uniqueKeys as $apiKey) {

            $invoice = new \App\Models\InvoiceGateway\KeyInvoice\Document($apiKey);

            $docsTypes = ['invoice', 'invoice-receipt', 'simplified-invoice', 'credit-note'];

            foreach ($docsTypes as $docType) {
                $serieDetails = $invoice->getCurrentSerie($docType);

                if($serieDetails) {
                    $invoiceSerie = new \App\Models\InvoiceSerie();
                    $invoiceSerie->source   = config('app.source');
                    $invoiceSerie->doc_type = $docType;
                    $invoiceSerie->code     = $serieDetails['code'];
                    $invoiceSerie->name     = $serieDetails['name'];
                    $invoiceSerie->serie_id = $serieDetails['id'];
                    $invoiceSerie->api_key  = $apiKey;
                    $invoiceSerie->save();
                }
            }
        }


        $allSeries = \App\Models\InvoiceSerie::get();
        foreach ($allSeries as $invoiceSerie) {
            \App\Models\Invoice::where('api_key', $invoiceSerie->api_key)
                ->where('doc_type', $invoiceSerie->doc_type)
                ->update([
                    'doc_series'    => $invoiceSerie->code,
                    'doc_series_id' => $invoiceSerie->serie_id
                ]);
        }

        \App\Models\Invoice::whereIn('doc_type', ['invoice-receipt', 'simplified-invoice'])->update(['is_settle' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices_series');
    }
}
