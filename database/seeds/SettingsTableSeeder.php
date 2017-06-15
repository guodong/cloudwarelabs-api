<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Settings::create([
            'id' => \Ramsey\Uuid\Uuid::uuid4(),
            'key' => 'instance',
            'value' => json_encode([
                'memory' => 0
            ])
        ]);
    }
}
