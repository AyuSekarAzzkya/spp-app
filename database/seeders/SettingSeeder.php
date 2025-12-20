<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $data = [
            ['key' => 'school_name', 'value' => 'SMP Harapan Bangsa'],
            ['key' => 'school_address', 'value' => 'Jl. Merdeka No. 10'],
            ['key' => 'school_phone', 'value' => '081234567890'],
            ['key' => 'school_account_number', 'value' => '12345678910'],
        ];

        foreach ($data as $item) {
            Setting::updateOrCreate(['key' => $item['key']], $item);
        }
    }
}
