<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsersExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('users_expenses')) {
            Schema::create('users_expenses', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->integer('user_id')->unsigned()->index();
                $table->integer('provider_id')->unsigned()->index();
                $table->integer('type_id')->unsigned()->index()->nullable();
                $table->string('description');
                $table->decimal('total', 10, 2);
                $table->date('date')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->text('obs');
                $table->boolean('is_fixed');
                $table->integer('purchase_invoice_id')->unsigned()->index()->nullable();
                $table->integer('created_by')->unsigned()->index()->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

                $table->foreign('provider_id')
                    ->references('id')
                    ->on('providers')
                    ->onDelete('cascade');

                $table->foreign('type_id')
                    ->references('id')
                    ->on('purchase_invoices_types')
                    ->onDelete('cascade');

                $table->foreign('purchase_invoice_id')
                    ->references('id')
                    ->on('purchase_invoices')
                    ->onDelete('cascade');

                $table->foreign('created_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }

        try {
            DB::table('permissions')->insert([
                'name' => 'users_expenses',
                'display_name' => 'Gestão Despesas - Motoristas',
                'module' => 'human_resources',
                'group' => 'Entidades',
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
        Schema::dropIfExists('users_expenses');
    }
}
