<?php

namespace Database\Seeders;

use App\Enums\PlanStatusEnum;
use App\Models\Sinister;
use Illuminate\Database\Seeder;

use Database\Seeders\RoleSeeder;
use Database\Seeders\AddressSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\FiscalSeeder;
use Database\Seeders\VehicleModelSeeder;
use Database\Seeders\InsuredVehicleSeeder;
use Database\Seeders\PlanSeeder;
use Database\Seeders\PolicySeeder;
use Database\Seeders\SinisterSeeder;
use Database\Seeders\SinisterCommentSeeder;
use Database\Seeders\SinisterMultimediaSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Orden de ejecucion
     * 
     * 1-. Roles
     * 2-. Addresses
     * 3-. Users
     * 4-. Fiscals
     * 5-. Vehicle Models
     * 6-. Insured Vehicles
     * 7-. Plans
     * 8-. Policies
     * 9-. Sinisters
     * 10-. Sinister Comments
     * 
     * 11-. Sinister Multimedia
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AddressSeeder::class,
            UserSeeder::class,
            FiscalSeeder::class,
            VehicleModelSeeder::class,
            InsuredVehicleSeeder::class,
            PlanSeeder::class,
            PolicySeeder::class,
            SinisterSeeder::class,
            SinisterCommentSeeder::class,
            SinisterMultimediaSeeder::class,
        ]);
    }
}

