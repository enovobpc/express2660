<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name'       => 'Administrador',
            'email'      => 'root@root.com',
            'password'   => bcrypt('root'),
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        ]);
    }
}
