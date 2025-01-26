<?php

namespace Database\Seeders;

use App\Models\StoreAddress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StoreAddress::factory()->count(5)->create();
    }
}
