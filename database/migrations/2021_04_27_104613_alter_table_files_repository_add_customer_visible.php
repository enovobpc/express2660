<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFilesRepositoryAddCustomerVisible extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files_repository', function (Blueprint $table) {
            if (!Schema::hasColumn('files_repository', 'customer_id')) {
                $table->integer('customer_id')
                    ->unsigned()
                    ->nullable()
                    ->index()
                    ->after('source_id');
            }

            if (!Schema::hasColumn('files_repository', 'user_id')) {
                $table->integer('user_id')
                    ->unsigned()
                    ->nullable()
                    ->index()
                    ->after('source_id');
            }

            if (!Schema::hasColumn('files_repository', 'customer_visible')) {
                $table->boolean('customer_visible')
                    ->default(0)
                    ->after('operator_visible');
            }

            if (!Schema::hasColumn('files_repository', 'obs')) {
                $table->text('obs')
                    ->after('count_files');
            }
        });

        DB::statement("ALTER TABLE customers CHANGE COLUMN default_print default_print ENUM('all','labels','guide','cmr', 'labels_a4') DEFAULT NULL");

        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'billing_email')) {
                $table->string('billing_email')
                    ->nullable()
                    ->after('email');
            }
        });

        $providers = \App\Models\Provider::get();
        foreach ($providers as $provider) {
            if(empty($provider->name)) {
                $provider->name = substr($provider->company, 0, 15);
                $provider->save();
            }
        }

        if(env('DB_DATABASE_LOGISTIC')) {

            if (Schema::connection('mysql_logistic')->hasTable('shipping_orders')) {

                Schema::connection('mysql_logistic')->table('shipping_orders', function (Blueprint $table) {

                    if (!Schema::connection('mysql_logistic')->hasColumn('shipping_orders', 'total_weight')) {
                        $table->decimal('total_weight', 10,2)
                            ->nullable()
                            ->after('total_items');
                    }

                    if (!Schema::connection('mysql_logistic')->hasColumn('shipping_orders', 'total_volumes')) {
                        $table->integer('total_volumes')
                            ->nullable()
                            ->after('total_items');
                    }

                    if (!Schema::connection('mysql_logistic')->hasColumn('shipping_orders', 'total_volume')) {
                        $table->decimal('total_volume', 10, 3)
                            ->nullable()
                            ->after('total_items');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files_repository', function (Blueprint $table) {
            if (Schema::hasColumn('files_repository', 'created_by_customer')) {
                $table->dropColumn('created_by_customer');
            }

            if (Schema::hasColumn('files_repository', 'customer_visible')) {
                $table->dropColumn('customer_visible');
            }

            if (Schema::hasColumn('files_repository', 'created_by_customer')) {
                $table->dropColumn('created_by_customer');
            }

            if (Schema::hasColumn('files_repository', 'obs')) {
                $table->dropColumn('obs');
            }
        });
    }
}
