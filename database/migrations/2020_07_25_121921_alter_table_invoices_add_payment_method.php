<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableInvoicesAddPaymentMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method', 25)
                    ->nullable()
                    ->after('due_date');
            }

            if (!Schema::hasColumn('invoices', 'payment_condition')) {
                $table->string('payment_condition', 25)
                    ->nullable()
                    ->after('due_date');
            }
        });

        Schema::table('purchase_invoices_types', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_invoices_types', 'is_static')) {
                $table->boolean('is_static')
                    ->default(0)
                    ->after('name');
            }
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_invoices', 'ignore_stats')) {
                $table->boolean('ignore_stats')
                    ->default(0)
                    ->after('is_settle');
            }
        });


        /**
         * Insert types
         */
        $types = [
            'Subcontratação Transportes',
            'Abastecimentos',
            'Oficinas e Manutenções',
            'Seguros e Finanças',
            'Alugueres e Rentings',
            'Custos Administrativos',
            'Ordenados',
            'Água, Luz e Gás',
            'Despesas Informáticas'
        ];

        foreach ($types as $key => $type) {
            $row = new \App\Models\PurchaseInvoiceType();
            $row->name = $type;
            $row->sort = $key + 1;
            $row->save();
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'payment_method')) {
                $table->dropColumn('payment_method');
            }

            if (Schema::hasColumn('invoices', 'payment_condition')) {
                $table->dropColumn('payment_condition');
            }
        });

        Schema::table('purchase_invoices_types', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_invoices_types', 'is_static')) {
                $table->dropColumn('is_static');
            }
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_invoices', 'ignore_stats')) {
                $table->dropColumn('ignore_stats');
            }
        });
    }
}
