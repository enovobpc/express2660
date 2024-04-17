<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInvoicesScheduled extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('invoices_scheduled')) {
            Schema::create('invoices_scheduled', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->integer('invoice_id')->unsigned()->index();
                $table->integer('repeat_every')->index();
                $table->string('frequency', 10)->nullable();
                $table->string('repeat', 10)->nullable();
                $table->string('month_days')->nullable();
                $table->string('weekdays')->nullable();
                $table->integer('end_repetitions')->nullable();
                $table->date('end_date')->nullable();
                $table->integer('count_repetitions')->nullable();
                $table->boolean('send_email')->default(1);
                $table->boolean('is_draft')->default(0);
                $table->string('last_schedule')->nullable();
                $table->boolean('finished')->default(0);

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('invoice_id')
                    ->references('id')
                    ->on('invoices');
            });
        }

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'is_scheduled')) {
                $table->boolean('is_scheduled')
                    ->default(0)
                    ->after('is_settle');
            }
        });

        Schema::table('purchase_payment_note_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_payment_note_methods', 'bank')) {
                $table->string('bank', 50)
                    ->nullable()
                    ->after('method');
            }
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'is_scheduled')) {
                $table->dropColumn('is_scheduled');
            }
        });

        Schema::dropIfExists('invoices_scheduled');
    }
}
