<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sinister;
use App\Models\SinisterMultimedia;

class SinisterMultimediaSeeder extends Seeder
{
    public function run(): void
    {
        // Disable query log to prevent memory leaks with large binary blobs
        \Illuminate\Support\Facades\DB::disableQueryLog();

        // Obtener todos los siniestros para asignarles multimedia
        $sinisters = Sinister::all();
        
        // Si no hay, crear algunos para probar
        if ($sinisters->isEmpty()) {
            $sinisters = Sinister::factory(10)->create();
        }
        
        foreach($sinisters as $sinister) {
            // Asignar entre 1 y 4 archivos multimedia a cada siniestro
            $count = rand(1, 4); 
            
            SinisterMultimedia::factory($count)->create([
                'sinister_id' => $sinister->id,
            ]);
        }
    }
}
