<?php

use App\Models\BankInstitution;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Branch2021111801 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE services CHANGE COLUMN unity unity ENUM('weight','volume','internacional','m3','pallet','km','hours','services','ldm','advalor','costpercent') DEFAULT NULL");

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'apk_version')) {
                $table->string('apk_version', 12)
                    ->nullable()
                    ->after('location_last_update');
            }
        });

        Schema::table('invoices_scheduled', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices_scheduled', 'start_date')) {
                $table->date('start_date')
                    ->nullable()
                    ->after('end_repetitions');
            }
        });

        if (!Schema::hasTable('banks')) {
            Schema::create('banks', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('name', 35);
                $table->string('titular_name', 35);
                $table->string('titular_vat');
                $table->string('bank_code')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('bank_iban')->nullable();
                $table->string('bank_swift')->nullable();
                $table->string('credor_code')->nullable();
                $table->text('obs');
                $table->boolean('is_active')->default(1);
                $table->smallInteger('sort')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'sepa_payment_id')) {
                $table->unsignedInteger('sepa_payment_id')
                    ->nullable()
                    ->after('assigned_invoice_id');
            }
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_invoices', 'sepa_payment_id')) {
                $table->unsignedInteger('sepa_payment_id')
                    ->nullable()
                    ->after('api_key');
            }
        });


        if (!Schema::hasTable('sepa_payments')) {
            Schema::create('sepa_payments', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->unsignedInteger('bank_id')->index();
                $table->enum('type', ['dd','trf'])->default('dd');
                $table->string('code', 35);
                $table->string('name', 35);
                $table->string('company', 35)->nullable();
                $table->string('bank_name', 35)->nullable();
                $table->string('bank_iban')->nullable();
                $table->string('bank_swift')->nullable();
                $table->string('credor_code')->nullable();
                $table->string('bank_operation_code')->nullable();
                $table->integer('transactions_count');
                $table->decimal('transactions_total', 10, 2);
                $table->string('status', 25)->default('editing');
                $table->boolean('has_errors')->default(0);
                $table->boolean('errors_processed')->default(0);
                $table->string('error_code', 4)->nullable();
                $table->string('error_msg')->nullable();
                $table->text('obs');

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('bank_id')
                    ->references('id')
                    ->on('banks');
            });
        }

        if (!Schema::hasTable('sepa_payments_groups')) {
            Schema::create('sepa_payments_groups', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->unsignedInteger('payment_id')->index();
                $table->string('code', 35);
                $table->string('service_type', 5);
                $table->string('sequence_type');
                $table->date('processing_date');
                $table->string('category');
                $table->string('company', 35);
                $table->unsignedInteger('bank_id')->nullable();
                $table->string('bank_name', 35)->nullable();
                $table->string('bank_iban')->nullable();
                $table->string('bank_swift')->nullable();
                $table->string('credor_code')->nullable();
                $table->integer('transactions_count');
                $table->decimal('transactions_total', 10, 2);
                $table->string('status', 25)->default('pending');
                $table->string('error_code', 4)->nullable();
                $table->string('error_msg')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('payment_id')
                    ->references('id')
                    ->on('sepa_payments');

                $table->foreign('bank_id')
                    ->references('id')
                    ->on('banks');
            });
        }

        if (!Schema::hasTable('sepa_payments_transactions')) {
            Schema::create('sepa_payments_transactions', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->unsignedInteger('payment_id')->index();
                $table->unsignedInteger('group_id')->index();
                $table->unsignedInteger('invoice_id')->index()->nullable();
                $table->unsignedInteger('purchase_invoice_id')->index()->nullable();
                $table->unsignedInteger('customer_id')->index()->nullable();
                $table->unsignedInteger('provider_id')->index()->nullable();
                $table->string('reference', 35);
                $table->decimal('amount', 10, 2);
                $table->string('mandate_code', 35)->nullable();
                $table->date('mandate_date')->nullable();
                $table->string('transaction_code', 35)->nullable();
                $table->string('company_code', 35)->nullable();
                $table->string('company_name', 35)->nullable();
                $table->unsignedInteger('bank_id')->nullable();
                $table->string('bank_name', 35)->nullable();
                $table->string('bank_iban')->nullable();
                $table->string('bank_swift')->nullable();
                $table->string('credor_code')->nullable();
                $table->text('obs', 140);
                $table->string('status', 25)->default('pending');
                $table->string('error_code', 4)->nullable();
                $table->string('error_msg')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('payment_id')
                    ->references('id')
                    ->on('sepa_payments');

                $table->foreign('group_id')
                    ->references('id')
                    ->on('sepa_payments_groups');

                $table->foreign('invoice_id')
                    ->references('id')
                    ->on('invoices');

                $table->foreign('purchase_invoice_id')
                    ->references('id')
                    ->on('purchase_invoices');

                $table->foreign('customer_id')
                    ->references('id')
                    ->on('customers');

                $table->foreign('provider_id')
                    ->references('id')
                    ->on('providers');

                $table->foreign('bank_id')
                    ->references('id')
                    ->on('banks');
            });
        }

        Schema::table('invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('invoices', 'sepa_payment_id')) {
                $table->unsignedInteger('sepa_payment_id')
                    ->nullable()
                    ->after('assigned_invoice_id');

                $table->foreign('sepa_payment_id')
                    ->references('id')
                    ->on('sepa_payments');
            }

            if (!Schema::hasColumn('invoices', 'payment_date')) {
                $table->date('payment_date')
                    ->nullable()
                    ->after('payment_method');
            }

            if (!Schema::hasColumn('invoices', 'doc_after_payment')) {
                $table->string('doc_after_payment')
                    ->nullable()
                    ->after('settle_obs');
            }

            if (!Schema::hasColumn('invoices', 'internal_code')) {
                $table->string('internal_code', 12)
                    ->nullable()
                    ->after('customer_id');
            }

            if (!Schema::hasColumn('invoices', 'paypal_account')) {
                $table->string('paypal_account', 50)
                    ->nullable()
                    ->after('obs');
            }

            if (!Schema::hasColumn('invoices', 'mbw_phone')) {
                $table->string('mbw_phone', 9)
                    ->nullable()
                    ->after('obs');
            }

            if (!Schema::hasColumn('invoices', 'mb_reference')) {
                $table->string('mb_reference', 9)
                    ->nullable()
                    ->after('obs');
            }

            if (!Schema::hasColumn('invoices', 'mb_entity')) {
                $table->string('mb_entity', 5)
                    ->nullable()
                    ->after('obs');
            }

        });

        Schema::table('invoices_scheduled', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices_scheduled', 'year_days')) {
                $table->string('year_days', 500)
                    ->nullable()
                    ->after('month_days');
            }

            if (!Schema::hasColumn('invoices_scheduled', 'paypal_active')) {
                $table->boolean('paypal_active')
                    ->default(0)
                    ->after('is_draft');
            }


            if (!Schema::hasColumn('invoices_scheduled', 'mbw_active')) {
                $table->boolean('mbw_active')
                    ->default(0)
                    ->after('is_draft');
            }

            if (!Schema::hasColumn('invoices_scheduled', 'mb_active')) {
                $table->boolean('mb_active')
                    ->default(0)
                    ->after('is_draft');
            }
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_invoices', 'sepa_payment_id')) {
                $table->unsignedInteger('sepa_payment_id')
                    ->nullable()
                    ->after('api_key');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'dimensions_required')) {
                $table->boolean('dimensions_required')
                    ->default(0)
                    ->after('allow_kms');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'ignore_mass_billing')) {
                $table->boolean('ignore_mass_billing')
                    ->default(0)
                    ->after('sms_enabled');
            }

            if (!Schema::hasColumn('customers', 'bank_mandate')) {
                $table->string('bank_mandate', 25)
                    ->nullable()
                    ->after('iban_refunds');
            }

            if (!Schema::hasColumn('customers', 'bank_swift')) {
                $table->string('bank_swift')
                    ->nullable()
                    ->after('iban_refunds');
            }

            if (!Schema::hasColumn('customers', 'bank_iban')) {
                $table->string('bank_iban')
                    ->nullable()
                    ->after('iban_refunds');
            }

            if (!Schema::hasColumn('customers', 'bank_name')) {
                $table->string('bank_name')
                    ->nullable()
                    ->after('iban_refunds');
            }
        });

        Schema::table('providers', function (Blueprint $table) {

            if (!Schema::hasColumn('providers', 'bank_mandate')) {
                $table->string('bank_mandate')
                    ->nullable()
                    ->after('iban');
            }

            if (!Schema::hasColumn('providers', 'bank_swift')) {
                $table->string('bank_swift')
                    ->nullable()
                    ->after('iban');
            }

            if (!Schema::hasColumn('providers', 'bank_iban')) {
                $table->string('bank_iban')
                    ->nullable()
                    ->after('iban');
            }

            if (!Schema::hasColumn('providers', 'bank_name')) {
                $table->string('bank_name')
                    ->nullable()
                    ->after('iban');
            }
        });

        Schema::table('providers', function (Blueprint $table) {
            if (Schema::hasColumn('providers', 'iban')) {
                DB::statement("UPDATE providers SET bank_iban = iban");
                $table->dropColumn('iban');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {

            if (!Schema::hasColumn('shipments', 'provider_weight')) {
                $table->decimal('provider_weight', 8, 2)
                    ->nullable()
                    ->after('customer_weight');
            }

            if (!Schema::hasColumn('shipments', 'price_kg_unity')) {
                $table->string('price_kg_unity', 2)
                    ->nullable()
                    ->after('zone');
            }

            if (!Schema::hasColumn('shipments', 'price_kg')) {
                $table->decimal('price_kg', 10, 2)
                    ->nullable()
                    ->after('zone');
            }
        });


        Schema::table('payment_conditions', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_conditions', 'is_active')) {
                $table->boolean('is_active')
                    ->default(1)
                    ->after('software_code');
            }
        });

        if (!Schema::hasTable('payment_methods')) {
            Schema::create('payment_methods', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('code', 10);
                $table->string('name', 35);
                $table->boolean('is_active')->default(1);
                $table->smallInteger('sort')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });


            $now = date('Y-m-d H:i:s');
            $i = 1;
            $paymentMethods = [
                'transfer'   => 'Transferência',
                'money'      => 'Numerário',
                'check'      => 'Cheque',
                'mb'         => 'Multibanco',
                'dd'         => 'Débito Direto',
                'settlement' => 'Acerto de Contas',
                'confirming' => 'Confirming',
                'mbw'        => 'MB Way',
            ];
            foreach ($paymentMethods as $code => $name) {
                \App\Models\PaymentMethod::insert([
                    'id'         => $i,
                    'source'     => config('app.source'),
                    'code'       => $code,
                    'name'       => $name,
                    'is_active'  => 1,
                    'sort'       => $i,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $i++;
            }
        }


        if (!Schema::hasTable('sepa_payments')) {
            Schema::create('sepa_payments', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->unsignedInteger('bank_id')->index();
                $table->enum('type', ['dd','trf'])->default('dd');
                $table->string('code', 35);
                $table->string('name', 35);
                $table->string('company', 35)->nullable();
                $table->string('bank_name', 35)->nullable();
                $table->string('bank_iban')->nullable();
                $table->string('bank_swift')->nullable();
                $table->string('credor_code')->nullable();
                $table->string('bank_operation_code')->nullable();
                $table->integer('transactions_count');
                $table->decimal('transactions_total', 10, 2);
                $table->string('status', 25)->default('pending');
                $table->boolean('has_errors')->default(0);
                $table->boolean('errors_processed')->default(0);
                $table->string('error_code', 4)->nullable();
                $table->string('error_msg')->nullable();
                $table->text('obs');

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('bank_id')
                    ->references('id')
                    ->on('banks');
            });
        }

        if (!Schema::hasTable('sepa_payments_groups')) {
            Schema::create('sepa_payments_groups', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->unsignedInteger('payment_id')->index();
                $table->string('code', 35);
                $table->string('service_type', 5);
                $table->string('sequence_type');
                $table->date('processing_date');
                $table->string('category');
                $table->string('company', 35);
                $table->unsignedInteger('bank_id')->nullable();
                $table->string('bank_name', 35)->nullable();
                $table->string('bank_iban')->nullable();
                $table->string('bank_swift')->nullable();
                $table->string('credor_code')->nullable();
                $table->integer('transactions_count');
                $table->decimal('transactions_total', 10, 2);
                $table->string('status', 25)->default('pending');
                $table->string('error_code', 4)->nullable();
                $table->string('error_msg')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('payment_id')
                    ->references('id')
                    ->on('sepa_payments');

                $table->foreign('bank_id')
                    ->references('id')
                    ->on('banks');
            });
        }

        if (!Schema::hasTable('sepa_payments_transactions')) {
            Schema::create('sepa_payments_transactions', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->unsignedInteger('payment_id')->index();
                $table->unsignedInteger('group_id')->index();
                $table->unsignedInteger('invoice_id')->index()->nullable();
                $table->unsignedInteger('purchase_invoice_id')->index()->nullable();
                $table->unsignedInteger('customer_id')->index()->nullable();
                $table->unsignedInteger('provider_id')->index()->nullable();
                $table->string('reference', 35);
                $table->decimal('amount', 10, 2);
                $table->string('mandate_code', 35)->nullable();
                $table->date('mandate_date')->nullable();
                $table->string('transaction_code', 35)->nullable();
                $table->string('company_code', 35)->nullable();
                $table->string('company_name', 35)->nullable();
                $table->unsignedInteger('bank_id')->nullable();
                $table->string('bank_name', 35)->nullable();
                $table->string('bank_iban')->nullable();
                $table->string('bank_swift')->nullable();
                $table->string('credor_code')->nullable();
                $table->text('obs', 140);
                $table->string('status', 25)->default('pending');
                $table->string('error_code', 4)->nullable();
                $table->string('error_msg')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('payment_id')
                    ->references('id')
                    ->on('sepa_payments');

                $table->foreign('group_id')
                    ->references('id')
                    ->on('sepa_payments_groups');

                $table->foreign('invoice_id')
                    ->references('id')
                    ->on('invoices');

                $table->foreign('purchase_invoice_id')
                    ->references('id')
                    ->on('purchase_invoices');

                $table->foreign('customer_id')
                    ->references('id')
                    ->on('customers');

                $table->foreign('provider_id')
                    ->references('id')
                    ->on('providers');

                $table->foreign('bank_id')
                    ->references('id')
                    ->on('banks');
            });
        }

        $permission = \App\Models\Permission::where('name', 'sepa_transfers')->first();
        if (!$permission) {
            $permission = new \App\Models\Permission();
            $permission->name         = 'sepa_transfers';
            $permission->display_name = 'Transferências SEPA';
            $permission->group        = 'Controlo Financeiro';
            $permission->module       = 'base';
            $permission->save();
        }


 
        
        anlutro\LaravelSettings\Facade::set('tracking_auto_sync', true); 
        anlutro\LaravelSettings\Facade::save();

        
        $bankCode = anlutro\LaravelSettings\Facade::get('bank_name');
        $bank = \App\Models\Bank::where('name', $bankCode)->first();
        if (!$bank) {
            $bank = new \App\Models\Bank();
            $bank->name       = anlutro\LaravelSettings\Facade::get('bank_name');
            $bank->bank_name  = anlutro\LaravelSettings\Facade::get('bank_name');
            $bank->bank_code  = $bankCode;
            $bank->bank_iban  = anlutro\LaravelSettings\Facade::get('bank_iban');
            $bank->bank_swift = anlutro\LaravelSettings\Facade::get('bank_swift');
            $bank->save();
        }

        for ($i = 1; $i <= 5; $i++) {
            $oldBankCode = anlutro\LaravelSettings\Facade::get('bank_' . $i);
            if ($oldBankCode) {

                $bk = BankInstitution::where('old_code', $bankCode)->first();
                
                $bank = \App\Models\Bank::where('bank_code', $bk->code)->first();
                if (!$bank) {
                    $bank = new \App\Models\Bank();
                    $bank->name       = $bk->bank_name;
                    $bank->bank_name  = $bk->bank_name;
                    $bank->bank_code  = $bk->code;
                    $bank->bank_iban  = null;
                    $bank->bank_swift = $bk->bank_swift;
                    $bank->save();
                }
            }
        }

        $permission = \App\Models\Permission::where('name', 'banks')->first();
        if (!$permission) {
            $permission = new \App\Models\Permission();
            $permission->name         = 'banks';
            $permission->display_name = 'Gestão de Bancos';
            $permission->group        = 'Configurações';
            $permission->module       = 'base';
            $permission->save();
        }

        $permission = \App\Models\Permission::where('name', 'payment_conditions')->first();
        if (!$permission) {
            $permission = new \App\Models\Permission();
            $permission->name         = 'payment_conditions';
            $permission->display_name = 'Gestão de Condições Pagamento';
            $permission->group        = 'Configurações';
            $permission->module       = 'base';
            $permission->save();
        }

        $permission = \App\Models\Permission::where('name', 'payment_methods')->first();
        if (!$permission) {
            $permission = new \App\Models\Permission();
            $permission->name         = 'payment_methods';
            $permission->display_name = 'Gestão de Formas Pagamento';
            $permission->group        = 'Configurações';
            $permission->module       = 'base';
            $permission->save();
        }

        $permission = \App\Models\Permission::where('name', 'sepa_transfers')->first();
        if (!$permission) {
            $permission = new \App\Models\Permission();
            $permission->name         = 'sepa_transfers';
            $permission->display_name = 'Transferências SEPA';
            $permission->group        = 'Controlo Financeiro';
            $permission->module       = 'base';
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
        //
    }
}
