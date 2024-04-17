<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Branch2023010907 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('delivery_manifests', 'type')) {
            Schema::table('delivery_manifests', function (Blueprint $table) {
                $table->string('type', 2)
                    ->nullable()
                    ->after('code');
            });
        }

        Schema::table('users_workgroups', function (Blueprint $table) {
            if (!Schema::hasColumn('users_workgroups', 'values')) {
                $table->text('values')
                    ->nullable()
                    ->after('name');
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
        //
    }
}
