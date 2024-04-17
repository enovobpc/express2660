<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableScheduledNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('scheduled_tasks')) {
            Schema::create('scheduled_tasks', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('action')->index();
                $table->string('target')->index()->nullable();
                $table->integer('target_id')->nullable();
                $table->timestamp('notify_at')->nullable();

                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('scheduled_tasks');
    }
}
