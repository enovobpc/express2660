<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanningCargoTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('cargo_planning_events_types')) {
            Schema::create('cargo_planning_events_types', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('title')->index();
                $table->string('color', 8);
                $table->string('icon');
                $table->integer('sort')->index();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        if(!Schema::hasTable('cargo_planning_events')) {
            Schema::create('cargo_planning_events', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('type_id')->nullable();
                $table->string('resource')->nullable();
                $table->string('title');
                $table->timestamp('start_date')->nullable()->index();
                $table->timestamp('end_date')->nullable()->index();
                $table->string('color', 8);
                $table->string('icon');
                $table->text('obs');

                $table->timestamps();
                $table->softDeletes();
            });
        }

        try {
            DB::table('cargo_planning_events_types')->insert([
                [
                    'id'     => 1,
                    'source' => config('app.source'),
                    'title'  => 'Viagem',
                    'color'  => '#abdcff',
                    'icon'   => 'fas fa-truck',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id'     => 2,
                    'source' => config('app.source'),
                    'title'  => 'Viagem s/ Reboque',
                    'color'  => '#abdcff',
                    'icon'   => 'fas fa-truck-pickup',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id'     => 3,
                    'source' => config('app.source'),
                    'title'  => 'Em Manutenção',
                    'color'  => '#f4c300',
                    'icon'   => 'fas fa-wrench',
                    'created_at' => date('Y-m-d H:i:s')
                ]]
            );
        } catch (\Exception $e) {}

        try {
            DB::table('permissions')->insert([
                'name' => 'cargo_planning',
                'display_name' => 'Planeamento de Cargas',
                'module' => 'cargo_planning',
                'group' => 'Envios e Recolhas',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cargo_planning_events');
        Schema::dropIfExists('cargo_planning_events_types');
    }
}
