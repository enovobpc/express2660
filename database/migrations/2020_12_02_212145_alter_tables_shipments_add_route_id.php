<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablesShipmentsAddRouteId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'route_id')) {
                $table->string('route_id')
                    ->nullable()
                    ->after('operator_id');
            }
        });

        Schema::table('calendar_events', function (Blueprint $table) {
            if (!Schema::hasColumn('calendar_events', 'type')) {
                $table->string('type')
                    ->nullable()
                    ->index()
                    ->after('end');
            }

            if (!Schema::hasColumn('calendar_events', 'customer_id')) {
                $table->integer('customer_id')
                    ->nullable()
                    ->index()
                    ->after('end');
            }
        });


        if(!Schema::connection('mysql_logs')->hasTable('login_logs')) {
            Schema::connection('mysql_logs')->table('login_logs', function (Blueprint $table) {
                Schema::connection('mysql_logs')->create('login_logs', function (Blueprint $table) {

                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('source')->index();
                    $table->string('target')->index();
                    $table->string('user_id')->index();
                    $table->boolean('remember');
                    $table->string('ip');
                    $table->timestamps();
                    $table->softDeletes();
                });
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
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'route_id')) {
                $table->dropColumn('route_id');
            }
        });

        Schema::table('calendar_events', function (Blueprint $table) {
            if (Schema::hasColumn('calendar_events', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('calendar_events', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
        });

        Schema::connection('mysql_logs')->dropIfExists('login_logs');
    }
}
