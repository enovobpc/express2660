<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Branch2021110503 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('events_manager')) {
            Schema::create('events_manager', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');

                $table->string('type', '25')->nullable();
                $table->string('name')->nullable();

                $table->unsignedInteger('customer_id')->nullable();
                $table->text('horary')->nullable();
                $table->text('observation')->nullable();

                $table->boolean('is_active')->default(0);
                $table->boolean('is_draft')->default(1);

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('customer_id')->references('id')->on('customers');
            });
        }

        $permission = \App\Models\Permission::where('name', 'events_management')->exists();
        if (!$permission) {
            $permission = new \App\Models\Permission();
            $permission->name         = 'events_management';
            $permission->display_name = 'Gestão de Eventos';
            $permission->group        = 'Módulo de Logística';
            $permission->module       = 'admin';
            $permission->save();
        }

        if (!Schema::hasTable('event_products_lines')) {
            Schema::create('event_products_lines', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');

                $table->unsignedInteger('event_manager_id')->nullable();
                $table->unsignedInteger('product_id')->nullable();
                $table->unsignedInteger('location_id')->nullable();

                $table->string('name')->nullable();
                $table->integer('qty')->nullable();
                $table->integer('qty_satisfied')->nullable();

                // Baseado no Shipping_orders_lines
                $table->decimal('price', 10, 2)->nullable();
                $table->string('barcode', 255)->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('event_manager_id')->references('id')->on('events_manager');
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
        Schema::dropIfExists('event_products_lines');
        Schema::dropIfExists('events_manager');
    }
}
