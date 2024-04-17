<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('pack_types')) {
            Schema::create('pack_types', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('code')->index();
                $table->string('name');
                $table->smallInteger('sort');
                $table->timestamps();
                $table->softDeletes();
            });

            \App\Models\PackType::insert([
                [
                    'source' => config('app.source'),
                    'code'   => 'box',
                    'name'   => 'Caixa',
                    'sort'   => '1',
                ],
                [
                    'source' => config('app.source'),
                    'code' => 'pal',
                    'name' => 'Palete',
                    'sort' => '2',
                ],
                [
                    'source' => config('app.source'),
                    'code' => 'env',
                    'name' => 'Envelope',
                    'sort' => '3',
                ]
            ]);
        }

        Schema::table('shipments', function (Blueprint $table) {

            if (Schema::hasColumn('shipments', 'width')) {
                $table->dropColumn('width');
            }

            if (Schema::hasColumn('shipments', 'height')) {
                $table->dropColumn('height');
            }

            if (Schema::hasColumn('shipments', 'length')) {
                $table->dropColumn('length');
            }

            if (!Schema::hasColumn('shipments', 'devolution_conferred')) {
                $table->boolean('devolution_conferred')
                    ->default(0)
                    ->after('customer_conferred');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'enabled_packages')) {
                $table->string('enabled_packages')
                    ->nullable()
                    ->after('enabled_providers');
            }

            if (!Schema::hasColumn('customers', 'monthly_plafound')) {
                $table->decimal('monthly_plafound', 10, 2)
                    ->nullable()
                    ->after('unpaid_invoices_credit');
            }

            if (!Schema::hasColumn('customers', 'shipping_services_notify')) {
                $table->string('shipping_services_notify')
                    ->nullable()
                    ->after('sms_enabled');
            }

            if (!Schema::hasColumn('customers', 'customer_sms_text')) {
                $table->string('customer_sms_text')
                    ->nullable()
                    ->after('sms_enabled');
            }
        });


        \DB::statement("ALTER TABLE shipments MODIFY COLUMN packaging_type VARCHAR(50) AFTER hours");
        \DB::statement("UPDATE shipments SET packaging_type = null");

        $exists = \App\Models\Permission::where('name', 'pack_types')->first();
        if(!$exists) {
            \App\Models\Permission::insert([
                'name' => 'pack_types',
                'display_name' => 'Gerir Tipos Embalagem',
                'group' => 'Envios e Recolhas',
                'module' => 'base'
            ]);
        }

        $exists = \App\Models\Permission::where('name', 'devolutions')->first();
        if(!$exists) {
            \App\Models\Permission::insert([
                'name' => 'devolutions',
                'display_name' => 'Gestão de Devoluções',
                'group' => 'Controlo Financeiro',
                'module' => 'base'
            ]);
        }



        /*\DB::statement("ALTER TABLE shipments MODIFY COLUMN pickup_operator_id INT(10) AFTER service_id");
        \DB::statement("ALTER TABLE shipments MODIFY COLUMN operator_id INT(10) AFTER service_id");
        \DB::statement("ALTER TABLE shipments MODIFY COLUMN dispatcher_id INT(10) AFTER service_id");*/


       /* \DB::statement("ALTER TABLE shipments MODIFY COLUMN created_by_customer BOOLEAN AFTER last_provider_sender_agency");
        \DB::statement("ALTER TABLE shipments MODIFY COLUMN is_collection BOOLEAN AFTER last_provider_sender_agency");
        \DB::statement("ALTER TABLE shipments MODIFY COLUMN is_import BOOLEAN AFTER last_provider_sender_agency");
        \DB::statement("ALTER TABLE shipments MODIFY COLUMN is_blocked BOOLEAN AFTER last_provider_sender_agency");
        \DB::statement("ALTER TABLE shipments MODIFY COLUMN is_closed BOOLEAN AFTER last_provider_sender_agency");
        \DB::statement("ALTER TABLE shipments MODIFY COLUMN price_fixed BOOLEAN AFTER last_provider_sender_agency");
        \DB::statement("ALTER TABLE shipments MODIFY COLUMN payment_at_recipient BOOLEAN AFTER last_provider_sender_agency");
        \DB::statement("ALTER TABLE shipments MODIFY COLUMN is_scheduled BOOLEAN AFTER last_provider_sender_agency");
        \DB::statement("ALTER TABLE shipments MODIFY COLUMN is_printed BOOLEAN AFTER last_provider_sender_agency");*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pack_types');
    }
}
