<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCpanelEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('cpanel_emails')) {
            Schema::create('cpanel_emails', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('email')->index();
                $table->string('password');
                $table->integer('quota')->nullable();
                $table->integer('usage')->nullable();
                $table->boolean('login_suspended')->nullable()->default(0);
                $table->boolean('incoming_suspended')->nullable()->default(0);
                $table->boolean('outgoing_suspended')->nullable()->default(0);
                $table->boolean('forwarding_active')->nullable()->default(0);
                $table->boolean('autoresponder_active')->nullable()->default(0);
                $table->integer('created_by')->unsigned()->index()->nullable();
                $table->integer('deleted_by')->unsigned()->index()->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        //adicionar na tabela de purchase invoices coluna data recebimento
        Schema::table('invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('invoices', 'is_hidden')) {
                $table->boolean('is_hidden')
                    ->nullable()
                    ->after('is_particular');
            }
        });

        //adicionar na tabela de purchase invoices coluna data recebimento
        Schema::table('purchase_invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('purchase_invoices', 'payment_until')) {
                $table->date('payment_until')
                    ->nullable()
                    ->after('due_date');
            }

            if (!Schema::hasColumn('purchase_invoices', 'received_date')) {
                $table->date('received_date')
                    ->nullable()
                    ->after('due_date');
            }
        });

        //adicionar na tabela de purchase invoices coluna data recebimento
        Schema::table('notices', function (Blueprint $table) {

            if (!Schema::hasColumn('notices', 'source_id')) {
                $table->string('source_id')
                    ->nullable()
                    ->after('sources');
            }

            if (!Schema::hasColumn('notices', 'source_type')) {
                $table->string('source_type')
                    ->nullable()
                    ->after('sources');
            }

            if (!Schema::hasColumn('notices', 'level')) {
                $table->string('level', 25)
                    ->default('warning')
                    ->after('sources');
            }
        });

        //atualiza as notas de crédito a fornecedor para ficarem com sinal negativo
        $invoices = \App\Models\PurchaseInvoice::where('doc_type', 'provider-credit-note')->get();
        foreach ($invoices as $invoice) {
            $invoice->subtotal      = $invoice->subtotal * -1;
            $invoice->vat_total     = $invoice->vat_total * -1;
            $invoice->total         = $invoice->total * -1;
            $invoice->total_unpaid  = $invoice->total_unpaid * -1;
            $invoice->is_settle     = 0;
            $invoice->save();
        }

        $method = new \App\Models\WebserviceMethod();
        $method->method   = 'correos_express';
        $method->name     = 'Correos Express';
        $method->sources  = '["'.config('app.source').'"]';
        $method->enabled  = 1;
        $method->save();


        if(env('DB_DATABASE_LOGISTIC')) {
            DB::connection('mysql_logistic')->statement("ALTER TABLE products CHANGE COLUMN unity unity ENUM('unity','box','meter','palet', 'liter') DEFAULT NULL");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('cpanel_emails');

        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_invoices', 'received_date')) {
                $table->dropColumn('received_date');
            }

            if (Schema::hasColumn('purchase_invoices', 'payment_until')) {
                $table->dropColumn('payment_until');
            }
        });
    }
}
