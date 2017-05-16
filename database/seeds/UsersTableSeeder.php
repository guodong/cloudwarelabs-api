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
            'id' => \Ramsey\Uuid\Uuid::uuid4(),
            'username' => 'admin',
            'password' => bcrypt('cloudwareadmin'),
            'role' => 'admin'
        ]);
    }
}
