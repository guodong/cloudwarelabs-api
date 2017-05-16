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
        \App\Models\User::create([
            'id' => \Ramsey\Uuid\Uuid::uuid4(),
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'role' => 'admin'
        ]);
    }
}
