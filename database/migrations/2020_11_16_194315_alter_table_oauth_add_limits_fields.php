<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOauthAddLimitsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            if (!Schema::hasColumn('oauth_clients', 'last_call')) {
                $table->timestamp('last_call')
                    ->nullable()
                    ->after('revoked');
            }

            if (!Schema::hasColumn('oauth_clients', 'daily_counter')) {
                $table->integer('daily_counter')
                    ->default(0)
                    ->after('revoked');
            }

            if (!Schema::hasColumn('oauth_clients', 'daily_limit')) {
                $table->integer('daily_limit')
                    ->default(100)
                    ->nullable()
                    ->after('revoked');
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
        Schema::table('oauth_clients', function (Blueprint $table) {
            if (Schema::hasColumn('oauth_clients', 'last_call')) {
                $table->dropColumn('last_call');
            }

            if (Schema::hasColumn('oauth_clients', 'daily_limit')) {
                $table->dropColumn('daily_limit');
            }

            if (Schema::hasColumn('oauth_clients', 'daily_counter')) {
                $table->dropColumn('daily_counter');
            }
        });
    }
}
