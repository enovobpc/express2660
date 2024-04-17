<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterServicesAddZipCodesColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'customers')) {
                $table->text('customers')->nullable()->after('week_days');
            }
            if (!Schema::hasColumn('services', 'zip_codes')) {
                $table->text('zip_codes')->nullable()->after('week_days');
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
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'customers')) {
                $table->dropColumn('customers');
            }

            if (Schema::hasColumn('services', 'zip_codes')) {
                $table->dropColumn('zip_codes');
            }
        });
    }
}
