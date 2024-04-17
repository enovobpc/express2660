<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableUsersAddOperatorLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'login_app')) {
                $table->boolean('login_app')
                    ->default(0)
                    ->after('filename');
            }

            if (!Schema::hasColumn('users', 'login_admin')) {
                $table->boolean('login_admin')
                    ->default(0)
                    ->after('filename');
            }

            if (!Schema::hasColumn('users', 'agency_id')) {
                $table->integer('agency_id')
                    ->nullable()
                    ->unsigned()
                    ->after('professional_obs');
            }

            if (!Schema::hasColumn('users', 'admission_date')) {
                $table->date('admission_date')
                    ->nullable()
                    ->after('professional_obs');
            }
        });

        $users = \App\Models\User::get();

        foreach ($users as $user) {
            if(empty($user->password)) {
                $user->login_app   = 0;
                $user->login_admin = 0;
            } else if($user->is_operator) {
                $user->login_app = 1;
                $user->login_admin = 0;
            } else {
                $user->login_app   = 0;
                $user->login_admin = 1;
            }

            $user->save();
        }

        //apaga roles sem atribuição
        $rolesInUsage =\Illuminate\Support\Facades\DB::table('assigned_roles')->groupBy('role_id')->pluck('role_id')->toArray();
        \App\Models\Role::whereNotNull('source')->whereNotIn('id', $rolesInUsage)->delete();

        $exists = \App\Models\Role::whereId(7)->first();
        if(!$exists) {
            $role = \App\Models\Role::Insert([
                'id' => '7',
                'name' => 'balcao',
                'display_name' => 'Balcão',
                'sort' => 7,
                'is_static' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $exists = \App\Models\Role::whereId(8)->first();
        if(!$exists) {
            $role = \App\Models\Role::Insert([
                'id' => '8',
                'name' => 'financeiro',
                'display_name' => 'Financeiro',
                'sort' => 4,
                'is_static' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        \App\Models\Role::where('name', 'acesso-a-licenca')->delete();

        $permissions = [12,19,35,56,70,79,91,102];
        $role = \App\Models\Role::where('name', 'balcao')->first();
        if($role) {
            $role->perms()->sync($permissions);
        }

        $permissions = [12,13,19,20,26,30,31,32,34,35,37,40,52,57,65,68,69,78,82,83,85,86,100,102,112,123,124];
        $financeiro = \App\Models\Role::where('name', 'financeiro')->first();
        if($financeiro) {
            $financeiro->perms()->sync($permissions);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'login_admin')) {
                $table->dropColumn('login_admin');
            }

            if (Schema::hasColumn('users', 'login_app')) {
                $table->dropColumn('login_app');
            }

            if (Schema::hasColumn('users', 'agency_id')) {
                $table->dropColumn('agency_id');
            }

            if (Schema::hasColumn('users', 'admission_date')) {
                $table->dropColumn('admission_date');
            }
        });
    }
}
