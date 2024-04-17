<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGatewayPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('gateway_payments');

        if(!Schema::hasTable('gateway_payments')) {
            Schema::create('gateway_payments', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('gateway');
                $table->string('code', 15)->index();
                $table->string('customer_id')->index()->nullable();
                $table->string('target')->index()->nullable();
                $table->string('target_id')->index()->nullable();
                $table->string('method')->index();
                $table->decimal('value', 10, 2);
                $table->enum('sense', ['debit', 'credit'])->default('debit');
                $table->string('currency', 3)->default('EUR');
                $table->string('reference');
                $table->string('description');

                $table->string('customer_name')->nullable();
                $table->string('customer_address')->nullable();
                $table->string('customer_country')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_phone')->nullable();
                $table->string('customer_vat')->nullable();

                $table->timestamp('expires_at')->nullable();
                $table->timestamp('paid_at')->nullable();

                $table->string('status')->default('pending')->nullable();

                $table->string('mb_reference')->nullable();
                $table->string('mb_entity')->nullable();

                $table->string('cc_first_name')->nullable();
                $table->string('cc_last_name')->nullable();
                $table->string('cc_number')->nullable();
                $table->string('cc_cvc')->nullable();
                $table->string('cc_year')->nullable();
                $table->string('cc_month')->nullable();

                $table->string('mbway_phone')->nullable();

                $table->string('transaction_id')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'wallet_balance')) {
                $table->decimal('wallet_balance', 10,2)->nullable()->after('balance_divergence');
            }
        });

        try {
            $exists = DB::table('permissions')->where('name', 'gateway_payments')->first();
            if(!$exists) {
                DB::table('permissions')->insert([
                    'name' => 'gateway_payments',
                    'display_name' => 'Gerir Pagamentos MB/Visa',
                    'module' => 'gateway_payments',
                    'group' => 'Pagamentos MB/Visa',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'wallet_balance')) {
                $table->dropColumn('wallet_balance');
            }
        });

        Schema::dropIfExists('gateway_payments');
    }
}
