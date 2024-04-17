<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name'         => 'administrator',
            'display_name' => 'Administrador',
            'created_at'   => new DateTime,
            'updated_at'   => new DateTime
        ]);
        
        DB::table('permissions')->insert([
            [
                'name'         => 'admin_users',
                'display_name' => 'Administração - Utilizadores',
                'created_at'   => new DateTime,
                'updated_at'   => new DateTime
            ],
            [
                'name'         => 'admin_roles',
                'display_name' => 'Administração - Perfis e permissões',
                'created_at'   => new DateTime,
                'updated_at'   => new DateTime
            ]
        ]);
       
        DB::table('permission_role')->insert([
            ['permission_id' => 1, 'role_id' => 1],
            ['permission_id' => 2, 'role_id' => 1]
        ]);
        
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 1
        ]);
    }
}
