<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePurchasePaymentNotesAddUserId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \App\Models\PurchaseInvoice::where('received_date', '0000-00-00')->update([
            'received_date' => null,
            'payment_until' => null
        ]);

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'total_price_after_pickup')) {
                $table->decimal('total_price_after_pickup', 10,2)
                    ->unsigned()
                    ->nullable()
                    ->after('total_price_when_collecting');
            }

            if (!Schema::hasColumn('shipments', 'refund_method')) {
                $table->string('refund_method', 10)
                    ->nullable()
                    ->after('customer_conferred');
            }

            if (!Schema::hasColumn('shipments', 'devolution_conferred')) {
                $table->boolean('devolution_conferred')
                    ->nullable()
                    ->after('customer_conferred');
            }

            if (!Schema::hasColumn('shipments', 'estimated_delivery_time_min')) {
                $table->timestamp('estimated_delivery_time_min')
                    ->nullable()
                    ->index()
                    ->after('end_hour');
            }

            if (!Schema::hasColumn('shipments', 'estimated_delivery_time_max')) {
                $table->timestamp('estimated_delivery_time_max')
                    ->nullable()
                    ->index()
                    ->after('end_hour');
            }
        });

        Schema::table('webservices_configs', function (Blueprint $table) {
            if (!Schema::hasColumn('webservices_configs', 'agency_id')) {
                $table->boolean('agency_id')
                    ->nullable()
                    ->after('provider_id');
            }
        });

        Schema::table('customers_balance', function (Blueprint $table) {
            if (!Schema::hasColumn('customers_balance', 'receipt_part')) {
                $table->string('receipt_part')
                    ->nullable()
                    ->after('assigned_receipt');
            }


            DB::statement("ALTER TABLE customers_balance MODIFY doc_type VARCHAR(255) COLLATE utf8_unicode_ci");
            DB::statement("ALTER TABLE customers_balance MODIFY doc_id VARCHAR(255) COLLATE utf8_unicode_ci");
            DB::statement("ALTER TABLE customers_balance MODIFY doc_serie VARCHAR(255) COLLATE utf8_unicode_ci");
            DB::statement("ALTER TABLE customers_balance MODIFY doc_serie_id VARCHAR(255) COLLATE utf8_unicode_ci");
        });

        Schema::table('shipping_status', function (Blueprint $table) {

            if (!Schema::hasColumn('shipping_status', 'name_es')) {
                $table->string('name_es')
                    ->nullable()
                    ->after('name_en');
            }

            if (!Schema::hasColumn('shipping_status', 'name_fr')) {
                $table->string('name_fr')
                    ->nullable()
                    ->after('name_en');
            }

            if (!Schema::hasColumn('shipping_status', 'description_es')) {
                $table->string('description_es')
                    ->nullable()
                    ->after('description');
            }

            if (!Schema::hasColumn('shipping_status', 'description_fr')) {
                $table->string('description_fr')
                    ->nullable()
                    ->after('description');
            }

            if (!Schema::hasColumn('shipping_status', 'description_en')) {
                $table->string('description_en')
                    ->nullable()
                    ->after('description');
            }
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_invoices', 'sense')) {
                $table->enum('sense', ['credit', 'debit'])
                    ->default('credit')
                    ->index()
                    ->after('total_unpaid');
            }
        });

        Schema::table('purchase_payment_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_payment_notes', 'user_id')) {
                $table->integer('user_id')
                    ->unsigned()
                    ->nullable()
                    ->index()
                    ->after('total');
            }
        });


        Schema::table('purchase_payment_note_invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('purchase_payment_note_invoices', 'invoice_unpaid')) {
                $table->decimal('invoice_unpaid', 10, 2)
                    ->unsigned()
                    ->nullable()
                    ->after('total');
            }

            if (!Schema::hasColumn('purchase_payment_note_invoices', 'invoice_total')) {
                $table->decimal('invoice_total', 10, 2)
                    ->unsigned()
                    ->nullable()
                    ->after('total');
            }

            if (!Schema::hasColumn('purchase_payment_note_invoices', 'total_pending')) {
                $table->decimal('total_pending', 10, 2)
                    ->unsigned()
                    ->nullable()
                    ->after('total');
            }
        });

        Schema::table('purchase_payment_notes', function (Blueprint $table) {

            if (!Schema::hasColumn('purchase_payment_notes', 'vat_total')) {
                $table->decimal('vat_total', 10,2)
                    ->unsigned()
                    ->nullable()
                    ->after('doc_date');
            }

            if (!Schema::hasColumn('purchase_payment_notes', 'subtotal')) {
                $table->decimal('subtotal', 10,2)
                    ->unsigned()
                    ->nullable()
                    ->after('doc_date');
            }

            if (!Schema::hasColumn('purchase_payment_notes', 'doc_series')) {
                $table->string('doc_series', 10)
                    ->nullable()
                    ->after('code');
            }

            DB::statement("ALTER TABLE purchase_payment_notes MODIFY COLUMN doc_id VARCHAR(10) AFTER code");
            DB::statement("ALTER TABLE purchase_payment_notes MODIFY COLUMN provider_id INT(10) AFTER source");
        });


        \App\Models\PurchaseInvoice::where('doc_type', 'provider-credit-note')->update(['sense' => 'debit']);


        $errorProviders = [];
        $paymentNotes = \App\Models\PurchasePaymentNote::get();
        foreach ($paymentNotes as $paymentNote) {

            try {
                $provider = $paymentNote->provider;

                $code     = explode('/', @$paymentNote->code);
                $docId    = (int) @$code[0];
                $docSerie = @$code[1];

                $paymentNote->doc_id     = $docId;
                $paymentNote->doc_series = $docSerie;
           /*     $paymentNote->subtotal   = @$paymentNote->invoices->sum('subtotal');
                $paymentNote->vat_total  = @$paymentNote->invoices->sum('vat_total');*/
                $paymentNote->save();

                $invoice = \App\Models\PurchaseInvoice::firstOrNew([
                    'doc_type'      => 'payment-note',
                    'doc_id'        => $paymentNote->code,
                    'provider_id'   => $paymentNote->provider_id
                ]);


                $invoice->source            = config('app.source');
                $invoice->doc_type          = 'payment-note';
                $invoice->provider_id       = $paymentNote->provider_id;
                $invoice->sense             = 'debit';
                $invoice->doc_id            = $docId;
                $invoice->doc_series        = $docSerie;
                $invoice->reference         = $paymentNote->code;
                $invoice->doc_date          = $paymentNote->doc_date;
                $invoice->due_date          = $paymentNote->doc_date;
                $invoice->subtotal          = null;
                $invoice->vat_total         = null;
                $invoice->total             = $paymentNote->total;
                $invoice->currency          = Setting::get('app_currency');
                $invoice->billing_code      = @$provider->code;
                $invoice->billing_name      = @$provider->company;
                $invoice->billing_address   = @$provider->billing_address;
                $invoice->billing_zip_code  = @$provider->billing_zip_code;
                $invoice->billing_city      = @$provider->billing_city;
                $invoice->billing_country   = @$provider->billing_country;
                $invoice->vat               = @$provider->vat;
                $invoice->is_settle         = 1;
                $invoice->created_by        = $paymentNote->user_id;
                $invoice->save();
            } catch (\Exception $e) {
                $errorProviders[] = $paymentNote->id . ' | FORN: '. $paymentNote->provider_id. ' ==> '.$paymentNote->billing_name;
                //dd($paymentNote->provider_id);
            }
        }

        //2155,2157,2181
        /*if(!empty($errorProviders)) {
            dd($errorProviders);
        }*/

        if(env('DB_DATABASE_FLEET')) {
            //DB::connection('mysql_logistic')->statement("UPDATE products SET sku = SUBSTRING(barcode, 1, 50)");

            Schema::connection('mysql_fleet')->table('fleet_fuel_log', function (Blueprint $table) {
                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_fuel_log', 'product')) {
                    $table->string('product', 10)
                        ->nullable()
                        ->after('km');
                }
            });

            try {
                \App\Models\FleetGest\Vehicle::where('id', '>', '1')->update(['product' => 'fuel']);
            } catch (\Exception $e) {}

        }

        DB::statement("ALTER TABLE refunds_control CHANGE COLUMN received_method received_method ENUM('money','check','transfer','claimed','portes','vale','settlement','mb','plafound','canceled','mbw') DEFAULT NULL");
        DB::statement("ALTER TABLE refunds_control CHANGE COLUMN payment_method payment_method ENUM('money','check','transfer','claimed','portes','vale','settlement','mb','plafound','canceled','mbw') DEFAULT NULL");
        DB::statement("ALTER TABLE refunds_control CHANGE COLUMN requested_method requested_method ENUM('money','check','transfer','claimed','portes','vale','settlement','mb','plafound','canceled','mbw') DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_payment_notes', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_payment_notes', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
}
