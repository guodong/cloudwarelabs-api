<?php

use Illuminate\Database\Seeder;

class CloudwaresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Cloudware::create([
            'id' => \Ramsey\Uuid\Uuid::uuid4(),
            'name' => 'Matlab',
            'logo' => 'http://cloudwarehub.com/apps/matlab/icon.png',
            'description' => 'MathWorks公司出品的商业数学软件',
            'image' => 'cloudwarelabs/xfce4-pulsar-gedit'
        ]);
    }
}
