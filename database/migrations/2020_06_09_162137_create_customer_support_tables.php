<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerSupportTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('customers_support_tickets')) {
            Schema::create('customers_support_tickets', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->integer('customer_id')->unsigned()->nullable();
                $table->integer('user_id')->unsigned()->nullable();
                $table->string('code');
                $table->string('subject')->index();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->longText('message');
                $table->longText('inline_attachments')->nullable();
                $table->string('category');
                $table->string('status')->index()->default('pending');
                $table->integer('shipment_id')->unsigned()->nullable();
                $table->date('date');
                $table->boolean('merged')->default(0);
                $table->text('obs');

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

                $table->foreign('customer_id')
                    ->references('id')
                    ->on('customers')
                    ->onDelete('cascade');

                $table->foreign('shipment_id')
                    ->references('id')
                    ->on('shipments')
                    ->onDelete('cascade');
            });
        }


        if (!Schema::hasTable('customers_support_messages')) {
            Schema::create('customers_support_messages', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('ticket_id')->unsigned()->nullable();
                $table->string('from')->nullable();
                $table->string('from_name')->nullable();
                $table->string('to')->nullable();
                $table->string('to_name')->nullable();
                $table->string('subject')->nullable();
                $table->longText('message')->nullable();
                $table->longText('inline_attachments')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('ticket_id')
                    ->references('id')
                    ->on('customers_support_tickets')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('customers_support_tickets_attachments')) {
            Schema::create('customers_support_tickets_attachments', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('ticket_id')->unsigned()->nullable();
                $table->string('name')->nullable();
                $table->string('filepath')->nullable();
                $table->string('filename')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('ticket_id')
                    ->references('id')
                    ->on('customers_support_tickets')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('customers_support_messages_attachments')) {
            Schema::create('customers_support_messages_attachments', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('message_id')->unsigned()->nullable();
                $table->string('name')->nullable();
                $table->string('filepath')->nullable();
                $table->string('filename')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('message_id')
                    ->references('id')
                    ->on('customers_support_messages')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('customers_support_messages_attachments');
        Schema::dropIfExists('customers_support_tickets_attachments');
        Schema::dropIfExists('customers_support_messages');
        Schema::dropIfExists('customers_support_tickets');
    }
}
