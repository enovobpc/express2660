<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Branch2023010602 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('products_payment_method')) {
            Schema::create('products_payment_method', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('method');
                $table->string('slug');
                $table->string('name');
                $table->text('instructions');
                $table->text('filepath');
                $table->text('filename');

                $table->timestamps();
                $table->softDeletes();
            });
        }
        
        try {
            DB::table('products_payment_method')->insert([
                'method' => 'visa',
                'slug' => 'visa',
                'name' => 'Visa',
                'instructions' => 'Paga com cartão de crédito Visa e Mastercard. Os teus pagamentos são sempre seguros e, se surgir algum problema, podes contar com a proteção do teu dinheiro.',
                'filepath' => 'uploads/payment/visa.png',
                'filename' => 'visa.png',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {}

        try {
            DB::table('products_payment_method')->insert([
                'method' => 'mb',
                'slug' => 'mb',
                'name' => 'MB',
                'instructions' => 'Não é necessário o envio de comprovativo, a tua encomenda após pagamento será processada automaticamente. Pagamento possível em qualquer terminal Multibanco ou Home Banking.',
                'filepath' => 'uploads/payment/mb.png',
                'filename' => 'mb.png',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {}

        try {
            DB::table('products_payment_method')->insert([
                'method' => 'mbay',
                'slug' => 'mbay',
                'name' => 'MBWay',
                'instructions' => 'Paga rapidamente através do teu número de telemóvel. Ao escolheres este método vais receber uma notificação no telemóvel. Basta aceitares a notificação para concluires a compra.',
                'filepath' => 'uploads/payment/mbway.png',
                'filename' => 'mbway.png',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {}

        if (!Schema::hasTable('mailing_list')) {
            Schema::create('mailing_list', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('name');
                $table->longText('emails');
                $table->integer('sort');

                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('products_cart')) {
            Schema::create('products_cart', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('ref');
                $table->integer('payment_id');
                $table->decimal('subtotal');
                $table->decimal('vat');
                $table->decimal('final');
                $table->integer('is_final');
                $table->integer('is_finished');
                $table->integer('customer_id');

                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('products_itens_cart')) {
            Schema::create('products_itens_cart', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('cart_id');
                $table->integer('product_id');
                $table->integer('qty');
                $table->decimal('price_unity');
                $table->decimal('vat');
                $table->decimal('subtotal');
                $table->decimal('total');

                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::connection('mysql_fleet')->table('fleet_vehicles', function (Blueprint $table) {
            if (!Schema::connection('mysql_fleet')->hasColumn('fleet_vehicles', 'assistants')) {
                $table->text('assistants')
                    ->nullable()
                    ->after('operator_id');
            }
        });
        
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'product_price')) {
                $table->text('product_price')
                ->nullable()
                ->after('has_prices');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'filepath')) {
                $table->text('filepath')
                ->nullable()
                ->after('unity');
            }

            if (!Schema::hasColumn('products', 'filename')) {
                $table->text('filename')
                ->nullable()
                ->after('unity');
            }

            if (!Schema::hasColumn('products', 'is_cover')) {
                $table->text('is_cover')
                ->nullable()
                ->after('unity');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'hide_products_sales')) {
                $table->dropColumn('hide_products_sales');
            }
        });

        $permission = \App\Models\Permission::where('name', 'mailing_lists')->first();
        if(!$permission) {
            \App\Models\Permission::insert([
                'name'         => 'mailing_lists',
                'display_name' => 'Grupos de E-mail',
                'group'        => 'Configurações',
                'module'       => 'base',
                'created_at'   => date('Y-m-d H:i:s')
            ]);
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
