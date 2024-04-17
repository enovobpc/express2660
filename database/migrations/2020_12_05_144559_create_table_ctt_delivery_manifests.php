<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCttDeliveryManifests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('ctt_delivery_manifests')) {
            Schema::create('ctt_delivery_manifests', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('title')->nullable();
                $table->integer('customer_id')->unsigned()->index();
                $table->string('filepath')->nullable();
                $table->string('filename')->nullable();
                $table->string('pickup_trk', 30)->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('customer_id')
                    ->references('id')
                    ->on('customers');
            });
        }

        Schema::table('users_expenses', function (Blueprint $table) {

            if (Schema::hasColumn('users_expenses', 'purchase_invoice_id')) {
                $table->dropForeign('users_expenses_purchase_invoice_id_foreign');
                $table->dropColumn('purchase_invoice_id');
            }

            if (!Schema::hasColumn('users_expenses', 'assigned_invoice_id')) {
                $table->integer('assigned_invoice_id')
                    ->unsigned()
                    ->index()
                    ->nullable();

                $table->foreign('assigned_invoice_id')
                    ->references('id')
                    ->on('purchase_invoices')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('ctt_delivery_manifests');
    }
}
