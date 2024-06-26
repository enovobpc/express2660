<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            if (!Schema::hasColumn('routes', 'type')) {
                $table->string('type', 25)
                    ->enum(['delivery', 'pickup'])
                    ->nullable()
                    ->after('source');
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
        Schema::table('routes', function (Blueprint $table) {
            if (Schema::hasColumn('routes', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
}
