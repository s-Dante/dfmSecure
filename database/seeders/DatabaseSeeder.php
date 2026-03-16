<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Execution order respects FK dependencies:
     *  1. Roles          (referenced by users)
     *  2. Addresses      (referenced by users)
     *  3. Users          (references roles + addresses)
     *  4. Fiscals        (references users)
     *  5. VehicleModels  (referenced by insured_vehicles)
     *  6. InsuredVehicles (references users + vehicle_models)
     *  7. Plans          (referenced by policies)
     *  8. Policies       (references insured_vehicles + users + plans)
     *  9. Sinisters      (references policies + users)
     * 10. SinisterComments  (references sinisters + users)
     * 11. SinisterMultimedia (references sinisters)
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

