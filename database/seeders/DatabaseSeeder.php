<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\RoleSeeder;
use Database\Seeders\AddressSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\FiscalSeeder;
use Database\Seeders\InsuredVehicleSeeder;
use Database\Seeders\VehicleModelSeeder;
use Database\Seeders\PlanSeeder;
use Database\Seeders\PolicySeeder;
use Database\Seeders\SinisterSeeder;
use Database\Seeders\SinisterCommentSeeder;
use Database\Seeders\SinisterMultimediaSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AddressSeeder::class,
            UserSeeder::class,
        ]);
    }
}
