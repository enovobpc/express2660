<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableWebservicesConfigsAddEndpoint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webservices_configs', function (Blueprint $table) {
            if (!Schema::hasColumn('webservices_configs', 'endpoint')) {
                $table->string('endpoint')
                    ->nullable()
                    ->after('session_id');
            }

            if (!Schema::hasColumn('webservices_configs', 'department')) {
                $table->string('department')
                    ->nullable()
                    ->after('session_id');
            }
        });

        Schema::table('customers_webservices', function (Blueprint $table) {
            if (!Schema::hasColumn('customers_webservices', 'endpoint')) {
                $table->string('endpoint')
                    ->nullable()
                    ->after('session_id');
            }

            if (!Schema::hasColumn('customers_webservices', 'department')) {
                $table->string('department')
                    ->nullable()
                    ->after('session_id');
            }
        });

        Schema::table('customers_webservices', function (Blueprint $table) {
            if (!Schema::hasColumn('customers_webservices', 'endpoint')) {
                $table->string('endpoint')
                    ->nullable()
                    ->after('session_id');
            }

            if (!Schema::hasColumn('customers_webservices', 'department')) {
                $table->string('department')
                    ->nullable()
                    ->after('session_id');
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
        Schema::table('customers_webservices', function (Blueprint $table) {
            if (Schema::hasColumn('customers_webservices', 'department')) {
                $table->dropColumn('department');
            }

            if (Schema::hasColumn('customers_webservices', 'endpoint')) {
                $table->dropColumn('endpoint');
            }
        });

        Schema::table('webservices_configs', function (Blueprint $table) {
            if (Schema::hasColumn('webservices_configs', 'department')) {
                $table->dropColumn('department');
            }

            if (Schema::hasColumn('webservices_configs', 'endpoint')) {
                $table->dropColumn('endpoint');
            }
        });
    }
}
