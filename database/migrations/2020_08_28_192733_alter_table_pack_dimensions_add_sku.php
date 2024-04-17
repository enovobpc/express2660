<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePackDimensionsAddSku extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments_packs_dimensions', function (Blueprint $table) {

            if (!Schema::hasColumn('shipments_packs_dimensions', 'validity')) {
                $table->string('validity')
                    ->nullable()
                    ->after('optional_fields');
            }

            if (!Schema::hasColumn('shipments_packs_dimensions', 'lote')) {
                $table->string('lote')
                    ->nullable()
                    ->after('optional_fields');
            }

            if (!Schema::hasColumn('shipments_packs_dimensions', 'serial_no')) {
                $table->string('serial_no')
                    ->nullable()
                    ->after('optional_fields');
            }

            if (!Schema::hasColumn('shipments_packs_dimensions', 'sku')) {
                $table->string('sku')
                    ->nullable()
                    ->after('optional_fields');
            }

            if (!Schema::hasColumn('shipments_packs_dimensions', 'product_id')) {
                $table->integer('product_id')
                    ->nullable()
                    ->after('optional_fields');
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
        Schema::table('shipments_packs_dimensions', function (Blueprint $table) {
            if (Schema::hasColumn('shipments_packs_dimensions', 'product_id')) {
                $table->dropColumn('product_id');
            }

            if (Schema::hasColumn('shipments_packs_dimensions', 'sku')) {
                $table->dropColumn('sku');
            }

            if (Schema::hasColumn('shipments_packs_dimensions', 'serial_no')) {
                $table->dropColumn('serial_no');
            }

            if (Schema::hasColumn('shipments_packs_dimensions', 'lote')) {
                $table->dropColumn('lote');
            }

            if (Schema::hasColumn('shipments_packs_dimensions', 'validity')) {
                $table->dropColumn('validity');
            }
        });
    }
}
