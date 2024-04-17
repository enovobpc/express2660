<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProvidersAddBalanceColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('providers', function (Blueprint $table) {

            if (!Schema::hasColumn('providers', 'balance_total_unpaid')) {
                $table->decimal('balance_total_unpaid',10,2)
                    ->nullable()
                    ->after('percent_total_price_gain');
            }

            if (!Schema::hasColumn('providers', 'is_active')) {
                $table->integer('is_active')
                    ->default(1)
                    ->after('obs');
            }

            if (!Schema::hasColumn('providers', 'balance_count_unpaid')) {
                $table->integer('balance_count_unpaid')
                    ->nullable()
                    ->after('percent_total_price_gain');
            }

            if (!Schema::hasColumn('providers', 'balance_count_expired')) {
                $table->integer('balance_count_expired')
                    ->nullable()
                    ->after('percent_total_price_gain');
            }

            if (!Schema::hasColumn('providers', 'category_id')) {
                $table->integer('category_id')
                    ->nullable()
                    ->after('type');
            }

            if (!Schema::hasColumn('providers', 'category_slug')) {
                $table->string('category_slug', 50)
                    ->nullable()
                    ->after('type');
            }

            if (!Schema::hasColumn('providers', 'iban')) {
                $table->string('iban')
                    ->nullable()
                    ->after('payment_method');
            }
        });

        Schema::dropIfExists('providers_categories');

        if(!Schema::hasTable('providers_categories')) {
            Schema::create('providers_categories', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('name');
                $table->string('slug', 20)->nullable()->index();
                $table->string('color', '8')->nullable();
                $table->boolean('is_static')->default(0);
                $table->integer('sort')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }


        foreach (trans('admin/providers.categories') as $categorySlug => $categoryName) {
            $category = new  \App\Models\ProviderCategory();
            $category->source    = config('app.source');
            $category->name      = $categoryName;
            $category->slug      = $categorySlug;
            $category->color     = trans('admin/providers.categories-colors.' . $categorySlug);
            $category->is_static = 1;
            $category->save();

            \App\Models\Provider::where('category', $categorySlug)->update([
                'category_id'   => $category->id,
                'category_slug' => $category->slug
            ]);
        }

        \App\Models\Provider::whereNull('category')->update([
            'category_id'   => '10',
            'category_slug' => 'others'
        ]);

        \App\Models\Provider::where('payment_method', '')->update([
            'payment_method' => '30d',
        ]);

        Schema::table('providers', function (Blueprint $table) {
            if (Schema::hasColumn('providers', 'category')) {
                $table->dropColumn('category');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('providers', function (Blueprint $table) {
            if (Schema::hasColumn('providers', 'balance_total_unpaid')) {
                $table->dropColumn('balance_total_unpaid');
            }

            if (Schema::hasColumn('providers', 'balance_count_unpaid')) {
                $table->dropColumn('balance_count_unpaid');
            }

            if (Schema::hasColumn('providers', 'balance_count_expired')) {
                $table->dropColumn('balance_count_expired');
            }

            if (Schema::hasColumn('providers', 'category_id')) {
                $table->dropColumn('category_id');
            }

            if (Schema::hasColumn('providers', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });

        Schema::dropIfExists('providers_categories');
    }
}
