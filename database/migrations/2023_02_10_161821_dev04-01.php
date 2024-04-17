<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Dev0401 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       //ajusted abscences
       Schema::table('users_absences', function(Blueprint $table){
            if(!Schema::hasColumn('users_absences', 'is_adjust')){
                $table->tinyInteger('is_adjust')
                ->default(0)
                ->after('is_meal_subsidy'); 
            };
        });

        Schema::table('users_absences_types', function(Blueprint $table){
            if(!Schema::hasColumn('users_absences_types', 'is_adjust')){
                $table->tinyInteger('is_adjust')
                ->default(0)
                ->after('is_meal_subsidy'); 
            };
        });

        $absence = \App\Models\UserAbsenceType::where('name', 'Acumulação de Férias')->first();

        if(!$absence){
            \App\Models\UserAbsenceType::insert([
                'name'            => 'Acumulação de Férias',
                'periods'         => '["days", "hours"]',
                'is_holiday'      => 1,
                'is_remunerated'  => 1,
                'is_meal_subsidy' => 1,
                'is_adjust'       => 1,
                'created_at'      => date('Y-m-d H:i:s')
            ]);
        }

        //attachaments history images 
        if (!Schema::hasTable('shipments_history_attachaments')) {
           
            Schema::create('shipments_history_attachaments', function (Blueprint $table) {
                
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('shipment_id')->unsigned()->nullable();
                $table->integer('shipment_history_id')->unsigned()->nullable();
                $table->string('name')->nullable();
                $table->string('filename')->nullable();
                $table->string('filepath')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('shipment_id')
                    ->references('id')
                    ->on('shipments');

                $table->foreign('shipment_history_id')
                      ->references('id')
                      ->on('shipments_history');

            });
        }

        //new elements in database [EQUIPMENTS]
        Schema::table('equipments_history', function(Blueprint $table){
            
            if(!Schema::hasColumn('equipments_history', 'ot_code')){
                $table->string('ot_code')
                      ->nullable()
                      ->after('operator_id'); 
            };

            if(!Schema::hasColumn('equipments_history', 'stock_low')){
                $table->integer('stock_low')
                      ->nullable()
                      ->after('ot_code'); 
            };
            
            if(!Schema::hasColumn('equipments_history', 'stock')){
                $table->integer('stock')
                      ->nullable()
                      ->after('stock_low'); 
            };

            if(!Schema::hasColumn('equipments_history', 'obs')){
                $table->string('obs')
                      ->nullable()
                      ->after('stock'); 
            };

        });

        Schema::table('equipments_locations', function(Blueprint $table){
            if(!Schema::hasColumn('equipments_locations', 'operador_id')){
                $table->integer('operador_id')
                      ->nullable()
                      ->after('warehouse_id'); 
            };
        });

        //new element in table fleet_usage_log
        if (env('DB_DATABASE_FLEET')) {

            Schema::connection('mysql_fleet')->table('fleet_usage_log', function(Blueprint $table){
                if(!Schema::connection('mysql_fleet')->hasColumn('fleet_usage_log', 'type')){
                    $table->string('type')
                          ->nullable()
                          ->after('operator_id'); 
                };
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
        //
    }
}
