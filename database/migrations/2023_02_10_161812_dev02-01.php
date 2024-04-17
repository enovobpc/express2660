<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Dev0201 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('purchase_invoices', 'payment_method_id')) {
            Schema::table('purchase_invoices', function (Blueprint $table) {
                $table->integer('payment_method_id')
                    ->nullable()
                    ->after('payment_date');
            });
        }

        if (!Schema::hasColumn('purchase_invoices', 'bank_id')) {
            Schema::table('purchase_invoices', function (Blueprint $table) {
                $table->integer('bank_id')
                    ->nullable()
                    ->after('payment_date');
            });
        }

        if (!Schema::hasColumn('shipping_status', 'is_public')) {
            Schema::table('shipping_status', function (Blueprint $table) {
                $table->integer('is_public')
                    ->default(1)
                    ->after('is_visible');
            });
        }

        \DB::statement("UPDATE shipping_status SET is_public=0 WHERE id=45");

        $status = \App\Models\ShippingStatus::find(44);
        if(!$status) {
            \App\Models\ShippingStatus::insert([
                'id' => 44,
                'name' => 'CMR Recebido',
                'name_en' => 'CMR Received',
                'slug' => 'cmr-recebido',
                'tracking_step' => 'delivered',
                'color' => '#16a085',
                'is_public' => true,
            ]);
        }

        $status = \App\Models\ShippingStatus::find(45);
        if(!$status) {
            \App\Models\ShippingStatus::insert([
                'id' => 45,
                'name' => 'Troca',
                'name_en' => 'Change',
                'slug' => 'troca',
                'tracking_step' => 'transport',
                'color' => '#78869c',
                'is_public' => false
            ]);
        } else {
            $status->name           = 'Troca';
            $status->name_en        = 'Change';
            $status->slug           = 'troca';
            $status->tracking_step  = 'transport';
            $status->color          = '#78869c';
            $status->is_public      = false;
            $status->save();
        }

        if(env('DB_DATABASE_LOGISTIC')) {

            Schema::connection('mysql_logistic')->table('reception_orders', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('reception_orders', 'pallets')) {
                    $table->integer('pallets')
                        ->nullable()
                        ->after('obs');
                }
            });

            Schema::connection('mysql_logistic')->table('reception_orders', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('reception_orders', 'boxs')) {
                    $table->integer('boxs')
                        ->nullable()
                        ->after('obs');
                }
            });

            Schema::connection('mysql_logistic')->table('reception_orders', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('reception_orders', 'price')) {
                    $table->integer('price')
                        ->nullable()
                        ->after('obs');
                }
            });
        }

        if (!Schema::hasTable('zip_codes_zones')) {
            Schema::create('zip_codes_zones', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('type')->nullable();
                $table->string('code');
                $table->string('name');
                $table->string('country')->nullable();
                $table->longText('zip_codes')->nullable();
                $table->text('services')->nullable();
                $table->integer('provider_id')->nullable();
                $table->integer('sort')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('users', 'ss_allowance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('ss_allowance')
                    ->after('salary_obs')->nullable();;
            });
        }

        if (!Schema::hasColumn('users', 'christmas_allowance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('christmas_allowance')
                    ->after('salary_obs')->nullable();;
            });
        }

        if (!Schema::hasColumn('users', 'holiday_allowance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('holiday_allowance')
                    ->after('salary_obs')->nullable();;
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
