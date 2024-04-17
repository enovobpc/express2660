<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGatewayPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('gateway_payments')) {
            Schema::create('gateway_payments', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('gateway');
                $table->string('target')->index();
                $table->string('target_id');
                $table->string('method')->index();
                $table->decimal('value', 10, 2);
                $table->string('currency', 3)->default('EUR');
                $table->string('reference');
                $table->string('description');

                $table->string('customer_name')->nullable();
                $table->string('customer_address')->nullable();
                $table->string('customer_country')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_phone')->nullable();
                $table->string('customer_vat')->nullable();

                $table->timestamp('expires_at');
                $table->timestamp('paid_at');

                $table->string('status')->default('pending')->nullable();

                $table->string('mb_reference')->nullable();
                $table->string('mb_entity')->nullable();

                $table->string('cc_first_name')->nullable();
                $table->string('cc_last_name')->nullable();
                $table->string('cc_number')->nullable();
                $table->string('cc_cvc')->nullable();
                $table->string('cc_year')->nullable();
                $table->string('cc_month')->nullable();

                $table->string('mbw_phone')->nullable();

                $table->string('transaction_id')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gateway_payments');
    }
}
