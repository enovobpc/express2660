<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickupPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pickup_points')) {
            Schema::create('pickup_points', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('code', '25');
                $table->unsignedInteger('provider_id');

                $table->string('type', '25')->nullable();
                $table->string('name')->nullable();
                $table->string('address')->nullable();
                $table->string('zip_code')->nullable();
                $table->string('city')->nullable();
                $table->string('country')->nullable();
                $table->string('latitude')->nullable();
                $table->string('longitude')->nullable();

                $table->string('email')->nullable();
                $table->string('phone', '15')->nullable();
                $table->string('mobile', '15')->nullable();
                $table->text('horary')->nullable();

                $table->boolean('is_active')->nullable()->default(0);
                $table->boolean('delivery_saturday')->nullable()->default(0);
                $table->boolean('delivery_sunday')->nullable()->default(0);

                $table->timestamps();
                $table->softDeletes();


                $table->foreign('provider_id')->references('id')->on('providers');
            });
        }

        $permission = \App\Models\Permission::where('name', 'pickup_points')->first();
        if (!$permission) {
            $permission = new \App\Models\Permission();
            $permission->name         = 'pickup_points';
            $permission->display_name = 'Gestão Pickup Points';
            $permission->group        = 'Entidades';
            $permission->module       = 'admin';
            $permission->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pickup_points');
    }
}
