<?php

use App\Models\Bank;
use App\Models\BankInstitution;
use App\Models\FleetGest\TyrePosition;
use App\Models\Invoice;
use App\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

class Dev0101 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('files_repository', function (Blueprint $table) {

            if (!Schema::hasColumn('files_repository', 'type_id')) {
                $table->string('type_id', 100)
                    ->nullable()
                    ->after('customer_id');
            }
        });
        
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'allowance_value_internacional')) {
                $table->decimal('allowance_value_internacional', 10, 2)
                    ->nullable()
                    ->after('salary_obs');
            }

            if (!Schema::hasColumn('users', 'allowance_value_spain')) {
                $table->decimal('allowance_value_spain', 10, 2)
                    ->nullable()
                    ->after('salary_obs');
            }

            if (!Schema::hasColumn('users', 'allowance_value_nacional')) {
                $table->decimal('allowance_value_nacional', 10, 2)
                    ->nullable()
                    ->after('salary_obs');
            }
        });

        Schema::table('invoices_lines', function (Blueprint $table) {

            if (!Schema::hasColumn('invoices_lines', 'tax_rate_id')) {
                $table->integer('tax_rate_id')
                    ->unsigned()
                    ->nullable()
                    ->after('tax_rate');
            }

            if (!Schema::hasColumn('invoices_lines', 'billing_code')) {
                $table->string('billing_code')
                    ->nullable()
                    ->after('exemption_reason_code');
            }
        });

        $company = null;
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('name');
                $table->string('display_name');
                $table->string('vat');
                $table->string('address')->nullable();
                $table->string('zip_code')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('phone')->nullable();
                $table->string('mobile')->nullable();
                $table->string('email')->nullable();
                $table->string('website')->nullable();
                $table->string('capital')->nullable();
                $table->string('conservatory')->nullable();
                $table->string('charter')->nullable();

                $table->string('filehost')->nullable();
                $table->string('filepath')->nullable();
                $table->string('filename')->nullable();
                $table->string('filepath_black')->nullable();
                $table->string('filename_black')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });

            $company = \App\Models\Company::find(1);
            if(empty($company)) {
                $company = new \App\Models\Company();
                $company->source    = config('app.source');
                $company->name      = Setting::get('company_name');
                $company->display_name = Setting::get('company_name');
                $company->vat       = Setting::get('vat');
                $company->address   = Setting::get('company_address');
                $company->zip_code  = Setting::get('company_zip_code');
                $company->city      = Setting::get('company_city');
                $company->country   = Setting::get('company_country');
                $company->phone     = Setting::get('company_phone');
                $company->mobile    = Setting::get('company_mobile');
                $company->email     = Setting::get('company_email');
                $company->website   = Setting::get('company_website');
                $company->capital   = Setting::get('company_capital');
                $company->charter   = Setting::get('company_permit');
                $company->conservatory = Setting::get('company_conservatory');
                $company->save();
            }
        }

        Schema::table('agencies', function (Blueprint $table) {
            if (!Schema::hasColumn('agencies', 'company_id')) {
                $table->integer('company_id')
                    ->unsigned()
                    ->nullable()
                    ->after('source');

                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies');
            }
        });
        \DB::statement("UPDATE agencies SET company_id=1 WHERE company_id IS NULL");

        if (!Schema::hasColumn('customers_recipients', 'always_cod')) {
            Schema::table('customers_recipients', function (Blueprint $table) {
                $table->boolean('always_cod')
                    ->default(0)
                    ->after('obs');
            });
        }

        Schema::table('billing_zones', function (Blueprint $table) {

            if (!Schema::hasColumn('billing_zones', 'matrix')) {
                $table->text('matrix')
                    ->nullable()
                    ->after('customers');
            }

            if (!Schema::hasColumn('billing_zones', 'pack_types')) {
                $table->text('pack_types')
                    ->nullable()
                    ->after('customers');
            }
        });

        \DB::statement("ALTER TABLE purchase_payment_notes CHANGE COLUMN subtotal subtotal DECIMAL(10,2) NULL");
        \DB::statement("ALTER TABLE purchase_payment_notes CHANGE COLUMN vat_total vat_total DECIMAL(10,2) NULL");
        \DB::statement("ALTER TABLE billing_zones CHANGE COLUMN unity unity VARCHAR(50) DEFAULT 'country'");
        \DB::statement("UPDATE permissions SET name='mailing_lists', display_name='Listas de E-mail' WHERE name='mailing-list'");

        if (!Schema::hasColumn('shipments_history', 'trailer')) {
            Schema::table('shipments_history', function (Blueprint $table) {
                $table->string('trailer')
                    ->nullable()
                    ->after('vehicle');
            });
        }

        Schema::table('shipments', function (Blueprint $table) {

             //adiciona receiver
            if (!Schema::hasColumn('shipments', 'receiver_phone')) {
                $table->string('receiver_phone')
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'receiver_vat')) {
                $table->string('receiver_vat')
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'receiver_country')) {
                $table->string('receiver_country', 5)
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'receiver_city')) {
                $table->string('receiver_city')
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'receiver_zip_code')) {
                $table->string('receiver_zip_code')
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'receiver_address')) {
                $table->string('receiver_address')
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'receiver_name')) {
                $table->string('receiver_name')
                    ->nullable()
                    ->after('recipient_email');
            }

            //adiciona shipper
            if (!Schema::hasColumn('shipments', 'shipper_phone')) {
                $table->string('shipper_phone')
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'shipper_vat')) {
                $table->string('shipper_vat')
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'shipper_country')) {
                $table->string('shipper_country', 5)
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'shipper_city')) {
                $table->string('shipper_city')
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'shipper_zip_code')) {
                $table->string('shipper_zip_code')
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'shipper_address')) {
                $table->string('shipper_address')
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'shipper_name')) {
                $table->string('shipper_name')
                    ->nullable()
                    ->after('recipient_email');
            }


            if (!Schema::hasColumn('shipments', 'closed_at')) {
                $table->timestamp('closed_at')
                    ->nullable()
                    ->after('delivered_date');
            }

            if (!Schema::hasColumn('shipments', 'has_sku')) {
                $table->boolean('has_sku')
                    ->default(0)
                    ->nullable()
                    ->after('has_assembly');
            }

            if (!Schema::hasColumn('shipments', 'at_code')) {
                $table->string('at_code')
                    ->nullable()
                    ->after('incoterm');
            }

            if (!Schema::hasColumn('shipments', 'conferred_original_cost')) {
                $table->decimal('conferred_original_cost', 10,2)
                    ->nullable()
                    ->comment('Preço Custo antes da conferencia por ficheiro')
                    ->after('operator_conferred_at');
            }

            if (!Schema::hasColumn('shipments', 'sender_longitude')) {
                $table->string('sender_longitude')
                    ->nullable()
                    ->after('sender_vat');
            }
        
            if (!Schema::hasColumn('shipments', 'sender_latitude')) {
                $table->string('sender_latitude')
                    ->nullable()
                    ->after('sender_vat');
            }

            if (!Schema::hasColumn('shipments', 'recipient_longitude')) {
                $table->string('recipient_longitude')
                    ->nullable()
                    ->after('recipient_email');
            }
        
            if (!Schema::hasColumn('shipments', 'recipient_latitude')) {
                $table->string('recipient_latitude')
                    ->nullable()
                    ->after('recipient_email');
            }

            if (!Schema::hasColumn('shipments', 'typology_id')) {
                $table->integer('typology_id')
                    ->unsigned()
                    ->nullable()
                    ->after('type');
            }

        });
        \DB::statement("ALTER TABLE shipments CHANGE COLUMN is_collection is_collection BOOLEAN DEFAULT 0");
        \DB::statement("UPDATE shipments SET is_collection=0 WHERE is_collection IS NULL");
        \DB::statement("UPDATE shipments SET recipient_latitude=map_lat, recipient_longitude=map_lng");

        Schema::table('delivery_manifests', function (Blueprint $table) {

            if (!Schema::hasColumn('delivery_manifests', 'type')) {
                $table->string('type', 5)
                    ->nullable()
                    ->after('code');
            }

            if (!Schema::hasColumn('delivery_manifests', 'kms_empty')) {
                $table->decimal('kms_empty', 10,2)
                    ->nullable()
                    ->after('kms');
            }

            if (!Schema::hasColumn('delivery_manifests', 'keywords')) {
                $table->text('keywords')
                    ->nullable()
                    ->after('obs');
            }

            if (!Schema::hasColumn('delivery_manifests', 'end_date')) {
                $table->date('end_date')
                    ->nullable()
                    ->after('delivery_date');
            }

            if (!Schema::hasColumn('delivery_manifests', 'start_date')) {
                $table->date('start_date')
                    ->nullable()
                    ->after('delivery_date');
            }

            if (!Schema::hasColumn('delivery_manifests', 'is_route_optimized')) {
                $table->boolean('is_route_optimized')
                    ->default(0)
                    ->after('is_internacional');
            }
        });
        \DB::statement("UPDATE delivery_manifests SET start_date=pickup_date, end_date=delivery_date");


        Schema::table('delivery_manifests', function (Blueprint $table) {


            if (!Schema::hasColumn('delivery_manifests', 'cost_vat_rate_id')) {
                $table->string('cost_vat_rate_id', 5)
                    ->nullable()
                    ->after('keywords');
            }

            if (!Schema::hasColumn('delivery_manifests', 'cost_vat_rate')) {
                $table->decimal('cost_vat_rate', 10, 2)
                    ->nullable()
                    ->after('keywords');
            }


            if (!Schema::hasColumn('delivery_manifests', 'cost_expenses_price')) {
                $table->decimal('cost_expenses_price', 10, 2)
                    ->nullable()
                    ->after('keywords');
            }

            if (!Schema::hasColumn('delivery_manifests', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)
                    ->nullable()
                    ->after('keywords');
            }

            if (!Schema::hasColumn('delivery_manifests', 'sort')) {
                 $table->integer('sort')
                     ->unsigned()
                     ->after('obs');
             }

             if (!Schema::hasColumn('delivery_manifests', 'children_type')) {
                 $table->integer('children_id')
                     ->unsigned()
                     ->nullable()
                     ->after('type');
             }

             if (!Schema::hasColumn('delivery_manifests', 'children_code')) {
                 $table->string('children_code')
                     ->nullable()
                     ->after('type');
             }

             if (!Schema::hasColumn('delivery_manifests', 'children_type')) {
                 $table->string('children_type', 1)
                     ->nullable()
                     ->after('type');
             }

             if (!Schema::hasColumn('delivery_manifests', 'parent_id')) {
                 $table->integer('parent_id')
                     ->unsigned()
                     ->nullable()
                     ->after('type');
             }

             if (!Schema::hasColumn('delivery_manifests', 'parent_code')) {
                 $table->string('parent_code')
                     ->nullable()
                     ->after('type');
             }
         });

        Schema::table('delivery_manifests', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_manifests', 'parent_delivery_manifest_code')) {
                \DB::statement('update delivery_manifests set parent_code = parent_delivery_manifest_code');

                $table->dropColumn('parent_delivery_manifest_code');
            }

            if (Schema::hasColumn('delivery_manifests', 'parent_delivery_manifest_code')) {
                \DB::statement('update delivery_manifests set parent_id = parent_delivery_manifest_id');
                $table->dropColumn('parent_delivery_manifest_id');
            }

            if (Schema::hasColumn('delivery_manifests', 'is_return')) {
                \DB::statement('update delivery_manifests set type = "R" where is_return=1');
                $table->dropColumn('is_return');
            }
        });

        if (env('DB_DATABASE_FLEET')) {

            if (!Schema::connection('mysql_fleet')->hasTable('fleet_tyres_positions')) {
                Schema::connection('mysql_fleet')->create('fleet_tyres_positions', function (Blueprint $table) {
                    $table->engine = 'InnoDB';
    
                    $table->increments('id');
                    $table->string('source')->index();
                    $table->string('name');
                    $table->integer('sort');
    
                    $table->timestamps();
                    $table->softDeletes();
                });

                $datetime =  date('Y-m-d H:i:s');
                $arr = [
                    [
                        'id' => 1,
                        'source' => config('app.source'),
                        'name' => 'Frontais',
                        'sort' => 1,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 2,
                        'source' => config('app.source'),
                        'name' => 'Traseiros',
                        'sort' => 2,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 3,
                        'source' => config('app.source'),
                        'name' => 'Frontal Direito',
                        'sort' => 3,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 4,
                        'source' => config('app.source'),
                        'name' => 'Frontal Esquerdo',
                        'sort' => 4,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 5,
                        'source' => config('app.source'),
                        'name' => 'Traseiro Direito',
                        'sort' => 5,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 6,
                        'source' => config('app.source'),
                        'name' => 'Traseiro Esquerdo',
                        'sort' => 6,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 7,
                        'source' => config('app.source'),
                        'name' => '1º Eixo',
                        'sort' => 7,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 8,
                        'source' => config('app.source'),
                        'name' => '1º Eixo Direito',
                        'sort' => 8,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 9,
                        'source' => config('app.source'),
                        'name' => '1º Eixo Esquerdo',
                        'sort' => 9,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 10,
                        'source' => config('app.source'),
                        'name' => '2º Eixo',
                        'sort' => 10,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 11,
                        'source' => config('app.source'),
                        'name' => '2º Eixo Direito',
                        'sort' => 11,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 12,
                        'source' => config('app.source'),
                        'name' => '2º Eixo Esquerdo',
                        'sort' => 12,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 13,
                        'source' => config('app.source'),
                        'name' => '3º Eixo',
                        'sort' => 13,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 14,
                        'source' => config('app.source'),
                        'name' => '3º Eixo Direito',
                        'sort' => 14,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ],
                    [
                        'id' => 15,
                        'source' => config('app.source'),
                        'name' => '3º Eixo Esquerdo',
                        'sort' => 15,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ]
                ];
                
                TyrePosition::insert($arr);
             
            }

            if (!Schema::connection('mysql_fleet')->hasTable('fleet_tyres')) {
                Schema::connection('mysql_fleet')->create('fleet_tyres', function (Blueprint $table) {
                    $table->engine = 'InnoDB';
    
                    $table->increments('id');
                    $table->string('source')->index();
                    $table->integer('vehicle_id')->unsigned();
                    $table->integer('operator_id')->unsigned()->nullable();
                    $table->integer('provider_id')->unsigned()->nullable();
                    $table->integer('position_id')->unsigned();
                    $table->string('reference');
                    $table->string('size')->nullable();
                    $table->string('brand')->nullable();
                    $table->string('model')->nullable();
                    $table->date('date');
                    $table->date('end_date')->nullable();
                    $table->integer('kms')->nullable();
                    $table->integer('end_kms')->nullable();
                    $table->decimal('duration_days', 10, 2)->nullable();
                    $table->decimal('duration_kms', 10, 2)->nullable();
                    $table->decimal('depth', 10, 2)->nullable();
                    $table->decimal('total', 10, 2)->nullable();
                    $table->text('measurements')->nullable(); //aferições
                    $table->text('obs')->nullable();

                    $table->string('filehost')->nullable();
                    $table->string('filepath')->nullable();
                    $table->string('filename')->nullable();
                    $table->string('filepath_black')->nullable();
                    $table->string('filename_black')->nullable();
    
                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('vehicle_id')
                        ->references('id')
                        ->on('fleet_vehicles');

                    $table->foreign('position_id')
                        ->references('id')
                        ->on('fleet_tyres_positions');
                });
            }

            


            Schema::connection('mysql_fleet')->table('fleet_maintenance_assigned_parts', function (Blueprint $table) {
                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_maintenance_assigned_parts', 'qty')) {
                    $table->integer('qty')
                        ->default(1)
                        ->nullable()
                        ->after('part_id');
                }
            });

            Schema::connection('mysql_fleet')->table('fleet_parts', function (Blueprint $table) {
                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_parts', 'model_name')) {
                    $table->string('model_name')
                        ->nullable()
                        ->after('brand_name');
                }
            });

            Schema::connection('mysql_fleet')->table('fleet_vehicles', function (Blueprint $table) {
                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_vehicles', 'assistants')) {
                    $table->text('assistants')
                        ->nullable()
                        ->after('operator_id');
                }
            });
        }


        if (!Schema::hasColumn('customers', 'company_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->integer('company_id')
                    ->unsigned()
                    ->nullable()
                    ->after('source');

                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies');
            });
        }

        
        Schema::table('customers', function (Blueprint $table) {

            if (!Schema::hasColumn('customers', 'bank_code')) {
                $table->string('bank_code')
                    ->nullable()
                    ->after('iban_refunds');
            }

            if (!Schema::hasColumn('customers', 'bank_mandate_date')) {
                $table->date('bank_mandate_date')
                    ->nullable()
                    ->after('bank_mandate');
            }
        });
       

        \DB::statement("UPDATE customers SET company_id=(select company_id from agencies where id=agency_id)");

        //atualiza logotipos da empresa e agencias
        $agency  = \App\Models\Agency::first();

        if($agency) {
            $updateArr = [
                'filehost' => $agency->filehost,
                'filepath' => $agency->filepath,
                'filename' => $agency->filename,
                'filepath_black' => $agency->filepath_black,
                'filename_black' => $agency->filename_black,
            ];

            \App\Models\Company::where('id','>',1)->update($updateArr);
            \App\Models\Agency::where('id','>',1)->update($updateArr);
        }


        if (!Schema::hasTable('billing_vat_rates')) {
            Schema::create('billing_vat_rates', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->integer('company_id')->unsigned()->nullable();
                $table->string('code')->index();
                $table->string('name');
                $table->string('name_abrv')->index();
                $table->string('class')->default('iva');
                $table->string('subclass')->default('nor');
                $table->string('zone')->default('pt');
                $table->decimal('value', 10, 2)->nullable();
                $table->string('exemption_reason')->nullable();
                $table->string('billing_code')->nullable();
                $table->boolean('is_sales')->default(1);
                $table->boolean('is_purchases')->default(0);
                $table->boolean('is_default')->default(0);
                $table->boolean('is_active')->default(1);
                $table->integer('sort')->unsigned();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies');
            });

            $vatRate = \App\Models\Billing\VatRate::where('code', '23')->first();
            if(empty($vatRate)) {
                $vatRate = new \App\Models\Billing\VatRate();
                $vatRate->source    = config('app.source');
                $vatRate->company_id= 1;
                $vatRate->code      = '23';
                $vatRate->name_abrv = '23%';
                $vatRate->name      = 'Taxa Normal 23%';
                $vatRate->class     = 'iva';
                $vatRate->subclass  = 'nor';
                $vatRate->zone      = 'pt';
                $vatRate->value     = 23;
                $vatRate->is_default   = 1;
                $vatRate->is_purchases = 1;
                $vatRate->billing_code = 1;
                $vatRate->sort = 1;
                $vatRate->save();
            }

            $vatRate = \App\Models\Billing\VatRate::where('code', 'PT13')->first();
            if(empty($vatRate)) {
                $vatRate = new \App\Models\Billing\VatRate();
                $vatRate->source    = config('app.source');
                $vatRate->company_id= 1;
                $vatRate->code      = '13';
                $vatRate->name_abrv = '13%';
                $vatRate->name      = 'Taxa Intermédia 13%';
                $vatRate->class     = 'iva';
                $vatRate->subclass  = 'int';
                $vatRate->zone      = 'pt';
                $vatRate->value     = 13;
                $vatRate->is_purchases = 1;
                $vatRate->sort = 2;
                $vatRate->save();
            }

            $vatRate = \App\Models\Billing\VatRate::where('code', 'PT6')->first();
            if(empty($vatRate)) {
                $vatRate = new \App\Models\Billing\VatRate();
                $vatRate->source    = config('app.source');
                $vatRate->company_id= 1;
                $vatRate->code      = '6';
                $vatRate->name_abrv = '6%';
                $vatRate->name      = 'Taxa Reduzida 6%';
                $vatRate->class     = 'iva';
                $vatRate->subclass  = 'red';
                $vatRate->zone      = 'pt';
                $vatRate->value     = 6;
                $vatRate->is_purchases = 1;
                $vatRate->sort = 3;
                $vatRate->save();
            }

            $vatRate = \App\Models\Billing\VatRate::where('code', 'M01')->first();
            if(empty($vatRate)) {
                $vatRate = new \App\Models\Billing\VatRate();
                $vatRate->source    = config('app.source');
                $vatRate->company_id= 1;
                $vatRate->code      = 'M01';
                $vatRate->name_abrv = '0% (M01)';
                $vatRate->name      = 'Isento Artigo 16.º n.º 6 do CIVA';
                $vatRate->class     = 'iva';
                $vatRate->subclass  = 'ise';
                $vatRate->zone      = 'pt';
                $vatRate->value     = 0;
                $vatRate->exemption_reason = 'M01';
                $vatRate->is_purchases = 1;
                $vatRate->billing_code = Setting::get('exemption_reason_m01');
                $vatRate->is_active = Setting::get('exemption_reason_m01') ? 1 : 0;
                $vatRate->sort = 3;
                $vatRate->save();
            }

            $vatRate = \App\Models\Billing\VatRate::where('code', 'M05')->first();
            if(empty($vatRate)) {
                $vatRate = new \App\Models\Billing\VatRate();
                $vatRate->source    = config('app.source');
                $vatRate->company_id= 1;
                $vatRate->code      = 'M05';
                $vatRate->name_abrv = '0% (M05)';
                $vatRate->name      = 'Isento Artigo 14.º do CIVA ';
                $vatRate->class     = 'iva';
                $vatRate->subclass  = 'ise';
                $vatRate->zone      = 'pt';
                $vatRate->value     = 0;
                $vatRate->exemption_reason = 'M05';
                $vatRate->is_purchases = 1;
                $vatRate->billing_code = Setting::get('exemption_reason_m05');
                $vatRate->is_active = Setting::get('exemption_reason_m05') ? 1 : 0;
                $vatRate->save();
            }

            $vatRate = \App\Models\Billing\VatRate::where('code', 'M40')->first();
            if(empty($vatRate)) {
                $vatRate = new \App\Models\Billing\VatRate();
                $vatRate->source    = config('app.source');
                $vatRate->company_id= 1;
                $vatRate->code      = 'M40';
                $vatRate->name_abrv = '0% (M40)';
                $vatRate->name      = 'IVA - Autoliquidação';
                $vatRate->class     = 'iva';
                $vatRate->subclass  = 'ise';
                $vatRate->zone      = 'pt';
                $vatRate->value     = 0;
                $vatRate->exemption_reason = 'M40';
                $vatRate->is_purchases = 1;
                $vatRate->billing_code = (Setting::get('exemption_reason_m08') ? Setting::get('exemption_reason_m08') : Setting::get('exemption_reason_m40')) ? 1 : 0;
                $vatRate->is_active = (Setting::get('exemption_reason_m08') ? Setting::get('exemption_reason_m08') : Setting::get('exemption_reason_m40')) ? 1 : 0;
                $vatRate->save();
            }

            $vatRate = \App\Models\Billing\VatRate::where('code', 'M99')->first();
            if(empty($vatRate)) {
                $vatRate = new \App\Models\Billing\VatRate();
                $vatRate->source    = config('app.source');
                $vatRate->company_id= 1;
                $vatRate->code      = 'M99';
                $vatRate->name_abrv = '0% (M99)';
                $vatRate->name      = 'Não sujeito; não tributado';
                $vatRate->class     = 'iva';
                $vatRate->subclass  = 'ise';
                $vatRate->zone      = 'pt';
                $vatRate->value     = 0;
                $vatRate->exemption_reason = 'M99';
                $vatRate->is_purchases = 1;
                $vatRate->billing_code = Setting::get('exemption_reason_m99');
                $vatRate->is_active = Setting::get('exemption_reason_m99') ? 1 : 0;
                $vatRate->save();
            }
        }

        if (!Schema::hasTable('billing_api_keys')) {
            Schema::create('billing_api_keys', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->integer('company_id')->unsigned()->nullable();
                $table->string('name');
                $table->string('token')->nullable();
                $table->string('username')->nullable();
                $table->string('password')->nullable();
                $table->boolean('is_active')->default(1)->nullable();
                $table->boolean('is_default')->default(0)->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->integer('sort')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies');
            });
        }

        //migra chaves API
        for ($i = 1; $i <= 60; $i++) {

            if ($i == 1) {
                $varName = 'invoice_apikey';
                $arrKey  = Setting::get('invoice_apikey');
                $arrVal  = Setting::get('invoice_apikey_name');
            } else {
                $varName = 'invoice_apikey_' . ($i < 10 ? '0' . $i : $i);
                $arrKey  = Setting::get($varName);
                $arrVal  = Setting::get($varName . '_name');
            }


            if (!empty($arrKey) && @$company) {
                $apiKey = \App\Models\Billing\ApiKey::where('token', $arrKey)->first();

                if(!$apiKey) {
                    $apiKey = new \App\Models\Billing\ApiKey();
                    $apiKey->source = config('app.source');
                    $apiKey->start_date = '2023-01-01';
                    $apiKey->end_date   = '2023-12-31';
                    $apiKey->token = $arrKey;
                    $apiKey->name  = $arrVal;
                    $apiKey->company_id = 1;
                    $apiKey->is_active = 1;
                    $apiKey->is_default = $varName == 'invoice_apikey' ? true : false;
                    $apiKey->save();
                }
            }
        }

        Schema::table('safts', function (Blueprint $table) {
            if (!Schema::hasColumn('safts', 'company_id')) {
                $table->integer('company_id')
                    ->unsigned()
                    ->nullable()
                    ->after('source');
                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies');
            }
        });
        \DB::statement("UPDATE safts SET company_id=1 WHERE company_id IS NULL");

        Schema::dropIfExists('products_payment_method');
        Schema::dropIfExists('products_itens_cart');
        Schema::dropIfExists('products_cart');


        $permission = \App\Models\Permission::find(70);
        if($permission) {
            $permission->name         = 'show_cost_prices';
            $permission->display_name = 'Ver preços custo';
            $permission->save();

            try {
                //adiciona esta opção ao perfil gerência e administração e financeiro
                \DB::statement("INSERT INTO permission_role VALUES (70, 2)");
                \DB::statement("INSERT INTO permission_role VALUES (70, 4)");
                \DB::statement("INSERT INTO permission_role VALUES (70, 8)");
            } catch (\Exception $e) {}
        }

        if (!Schema::hasTable('invoices_divergences')) {
            Schema::create('invoices_divergences', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('invoice_id')->nullable();
                $table->string('doc_id')->nullable();
                $table->string('doc_series')->nullable();
                $table->string('doc_type')->nullable();
                $table->string('vat')->nullable();
                $table->string('invoice_vat')->nullable();
                $table->string('customer_name')->nullable();
                $table->date('date')->nullable();
                $table->decimal('total', 10, 2)->nullable();
                $table->decimal('invoice_doc_total', 10, 2)->nullable();
                $table->boolean('invoice_settle')->default(0);
                $table->boolean('has_divergence')->default(0);
                $table->string('obs')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('mailing_list')) {
            Schema::rename('mailing_list', 'mailing_lists');
        }


        $permission = \App\Models\Permission::where('name', 'vat_rates')->first();
        if(!$permission) {
            $permission = new Permission();
            $permission->name         = 'vat_rates';
            $permission->display_name = 'Gerir Taxas IVA';
            $permission->group        = 'Faturação';
            $permission->module       = 'invoices';
            $permission->save();

            $permId = @$permission->id;

            try {
                //adiciona esta opção ao perfil gerência e administração e financeiro
                \DB::statement("INSERT INTO permission_role VALUES (".$permId.", 2)");
                \DB::statement("INSERT INTO permission_role VALUES (".$permId.", 4)");
                \DB::statement("INSERT INTO permission_role VALUES (".$permId.", 8)");
            } catch (\Exception $e) {}
        }



        Schema::table('purchase_payment_note_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_payment_note_methods', 'bank_id')) {
                $table->integer('bank_id')
                    ->unsigned()
                    ->nullable()
                    ->after('method');

                $table->foreign('bank_id')
                    ->references('id')
                    ->on('banks');
            }

            if (!Schema::hasColumn('purchase_payment_note_methods', 'payment_method_id')) {
                $table->integer('payment_method_id')
                    ->unsigned()
                    ->nullable()
                    ->after('method');

                $table->foreign('payment_method_id')
                    ->references('id')
                    ->on('payment_methods');
            }
        });


        Schema::table('banks', function (Blueprint $table) {
            if (!Schema::hasColumn('banks', 'bank_institution_id')) {
                $table->string('bank_institution_id', 8)
                    ->nullable()
                    ->after('titular_vat');
            }

            if (!Schema::hasColumn('banks', 'old_bank_code')) {
                $table->string('old_bank_code')
                    ->nullable()
                    ->after('bank_code');
            }

            if (!Schema::hasColumn('banks', 'company_id')) {
                $table->integer('company_id')
                    ->unsigned()
                    ->nullable()
                    ->after('source');

                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies');
            }
        });


        //corrige notas de pagamento
       $paymentNotesBanks = \App\Models\PurchasePaymentNoteMethod::where(function($q){
                $q->whereNotNull('bank');
                $q->where('bank', '<>', '');
            })
            ->groupBy('bank')
            ->pluck('bank', 'bank')
            ->toArray();

        foreach($paymentNotesBanks as $bankOldCode) {
        
            $bank = \App\Models\Bank::where('bank_code', $bankOldCode)->first();

            if($bank) {
                \App\Models\PurchasePaymentNoteMethod::withTrashed()
                ->where('bank', $bankOldCode)->update([
                    'bank_id' => $bank->id
                ]);
            }
        }


        //corrige clientes
       $customersBanks = \App\Models\Customer::where(function($q){
                $q->whereNotNull('bank_code');
                $q->where('bank_code', '<>', '');
            })
            ->groupBy('bank_code')
            ->pluck('bank_code', 'bank_code')
            ->toArray();

        foreach($customersBanks as $bankOldCode) {

            $bank = \App\Models\BankInstitution::where('old_code', $bankOldCode)->first();

            if($bank) {
                \App\Models\Customer::withTrashed()
                ->where('bank_code', $bankOldCode)->update([
                    'bank_code' => $bank->code
                ]);
            }
        }

        //Corrige tabela Banks
        $banks = \App\Models\Bank::get();
        foreach($banks as $bank) {
            if(substr($bank->bank_code, 0, 2) != 'PT' && strlen($bank->bank_code) != 6) {
                $bankInstitution = \App\Models\BankInstitution::where('old_code', $bank->bank_code)->first();
                if($bankInstitution) {
                    $bank->update([
                        'company_id'          => $bank->company_id ? $bank->company_id : 1,
                        'bank_institution_id' => @$bankInstitution->code,
                        'bank_code'           => @$bankInstitution->bank_code,
                        'old_bank_code'       => $bank->bank_code
                    ]);
                }
            }
        }

        //Corrige metodo pagamento na tabela payment_methods
        $paymentMethods = \App\Models\PurchasePaymentNoteMethod::where(function($q){
                $q->whereNotNull('method');
                $q->where('method', '<>', '');
            })
            ->groupBy('method')
            ->pluck('method', 'method')
            ->toArray();

        foreach($paymentMethods as $oldMethod) {

            $method = \App\Models\PaymentMethod::where('code', $oldMethod)->first();

            if($method) {
                \App\Models\PurchasePaymentNoteMethod::withTrashed()
                ->where('method', $oldMethod)->update([
                    'payment_method_id' => $method->id
                ]);
            }
        }

        Schema::table('sepa_payments', function (Blueprint $table) {

            if (!Schema::hasColumn('sepa_payments', 'company_vat')) {
                $table->string('company_vat')
                    ->nullable()
                    ->after('company');
            }

            if (!Schema::hasColumn('sepa_payments', 'company_id')) {
                $table->integer('company_id')
                    ->unsigned()
                    ->nullable()
                    ->after('name');

                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies');
            }

        });

        Schema::table('sepa_payments_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('sepa_payments_transactions', 'company_vat')) {
                $table->string('company_vat', 35)
                    ->nullable()
                    ->after('company_name');
            }
        });
        
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'is_reversed')) {
                $table->boolean('is_reversed')
                    ->default(0)
                    ->after('is_deleted');
            }

            if (!Schema::hasColumn('invoices', 'doc_total_credit')) {
                $table->decimal('doc_total_credit', 10,2)
                    ->nullable()
                    ->after('fuel_tax');
            }

            if (!Schema::hasColumn('invoices', 'doc_total_debit')) {
                $table->decimal('doc_total_debit', 10,2)
                    ->nullable()
                    ->after('fuel_tax');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'payment_bank_id')) {
                $table->integer('payment_bank_id')
                    ->nullable()
                    ->unsigned()
                    ->after('payment_method');
            }

            if (!Schema::hasColumn('invoices', 'customer_balance')) {
                $table->decimal('customer_balance', 10,2)
                    ->nullable()
                    ->after('doc_total_credit');
            }

            if (!Schema::hasColumn('invoices', 'doc_total_balance')) {
                $table->decimal('doc_total_balance', 10,2)
                    ->nullable()
                    ->after('doc_total_credit');
            }

            if (!Schema::hasColumn('invoices', 'sort')) {
                $table->string('sort', 20)
                    ->nullable()
                    ->index()
                    ->after('created_by');
            }

            if (Schema::hasColumn('invoices', 'balance')) {
                $table->dropColumn('balance');
            }
        });


        Schema::table('customers', function (Blueprint $table) {

            if (!Schema::hasColumn('customers', 'balance_total')) {
                $table->decimal('balance_total', 10, 2)
                    ->default(0)
                    ->index()
                    ->after('balance_divergence');
            }

            if (!Schema::hasColumn('customers', 'balance_total_credit')) {
                $table->decimal('balance_total_credit', 10, 2)
                    ->default(0)
                    ->index()
                    ->after('balance_divergence');
            }

            if (!Schema::hasColumn('customers', 'balance_total_debit')) {
                $table->decimal('balance_total_debit', 10, 2)
                    ->default(0)
                    ->index()
                    ->after('balance_divergence');
            }

            if (!Schema::hasColumn('customers', 'balance_expired_count')) {
                $table->integer('balance_expired_count')
                    ->default(0)
                    ->after('balance_divergence');
            }

            if (!Schema::hasColumn('customers', 'balance_unpaid_count')) {
                $table->integer('balance_unpaid_count')
                    ->default(0)
                    ->after('balance_divergence');
            }
        });

        //converte notas de credito e recibos em sinal negativo
        $invoices = Invoice::whereIn('doc_type', ['credit-note', 'receipt', 'regularization'])->get();

        foreach($invoices as $invoice) {
            if($invoice->doc_total > 0.00) {
                $invoice->doc_total = $invoice->doc_total * -1;
            }

            if($invoice->doc_vat > 0.00) {
                $invoice->doc_vat = $invoice->doc_vat * -1;
            }

            if($invoice->doc_subtotal > 0.00) {
                $invoice->doc_subtotal = $invoice->doc_subtotal * -1;
            }

            if($invoice->doc_total_pending > 0.00) {
                $invoice->doc_total_pending = $invoice->doc_total_pending * -1;
            }

            if($invoice->total > 0.00) {
                $invoice->total = $invoice->total * -1;
            }

            if($invoice->total_vat > 0.00) {
                $invoice->total_vat = $invoice->total_vat * -1;
            }

            if($invoice->total_no_vat > 0.00) {
                $invoice->total_no_vat = $invoice->total_no_vat * -1;
            }

            $invoice->save();
        }
              
        
        \DB::statement('update invoices set doc_total_debit  = doc_total where doc_type in ("invoice", "invoice-receipt", "simplified-invoice", "proforma-invoice", "internal-doc", "nodoc", "debit-note")');
        \DB::statement('update invoices set doc_total_credit = doc_total where doc_type in ("credit-note", "regularization", "receipt")');
        \DB::statement('update invoices set doc_total_credit = (doc_total*-1) where doc_type in ("invoice-receipt", "simplified-invoice")');

        /* $permission = \App\Models\Permission::where('', 70)->first();
        if($permission) {
            $permission->name         = 'show_cost_prices';
            $permission->display_name = 'Ver preços custo';
            $permission->save();

            try {
                //adiciona esta opção ao perfil gerência e administração e financeiro
                \DB::statement("INSERT INTO permission_role VALUES (70, 2)");
                \DB::statement("INSERT INTO permission_role VALUES (70, 4)");
                \DB::statement("INSERT INTO permission_role VALUES (70, 8)");
            } catch (\Exception $e) {}
        } */

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'marker_icon')) {
                $table->string('marker_icon', 255)
                    ->nullable()
                    ->after('filename');
            }
        });

        Schema::table('refunds_control', function (Blueprint $table) {
            if (!Schema::hasColumn('refunds_control', 'submited_at')) {
                $table->timestamp('submited_at')
                    ->nullable()
                    ->after('paid');
            }
        });

        if (!Schema::connection('mysql_logs')->hasTable('sended_emails')) {
            Schema::connection('mysql_logs')->create('sended_emails', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('subject')->nullable();
                $table->string('from')->nullable();
                $table->string('to')->nullable();
                $table->string('cc')->nullable();
                $table->string('bcc')->nullable();
                $table->longText('message')->nullable();
                $table->text('headers');
                $table->string('message_id');
                $table->longText('attached_docs')->nullable();
                $table->longText('attached_files')->nullable();
                $table->boolean('is_draft')->default(0);
                $table->boolean('sended_by')->nullable();
                $table->timestamp('sended_at')->nullable();
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!File::exists(public_path('uploads/tmp_files'))) {
            File::makeDirectory(public_path('uploads/tmp_files'));
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
