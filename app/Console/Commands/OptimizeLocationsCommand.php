<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OptimizeLocationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convierte los diccionarios JSON inmensos en micro-endpoints estáticos ultrarrápidos para la vista.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '512M'); // Requerido para leer los 53MB

        $this->info("Iniciando optimización de catálogos de ubicaciones...");
        
        $sourceDir = database_path('data/country_state_city-data');
        $publicDir = storage_path('app/public/locations');

        if (!is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }

        // 1. Paises
        $this->info("1. Copiando países...");
        copy("$sourceDir/countries.json", "$publicDir/countries.json");

        // 2. Estados
        $this->info("2. Particionando Estados por country_id...");
        $statesDir = "$publicDir/states";
        if (!is_dir($statesDir)) mkdir($statesDir, 0755, true);
        
        $statesRaw = file_get_contents("$sourceDir/states.json");
        $statesData = json_decode($statesRaw, true);
        
        $statesByCountry = [];
        foreach ($statesData as $state) {
            $statesByCountry[$state['country_id']][] = [
                'id' => $state['id'],
                'name' => $state['name']
            ];
        }

        foreach ($statesByCountry as $countryId => $opts) {
            file_put_contents("$statesDir/$countryId.json", json_encode($opts));
        }

        // 3. Ciudades (El más pesado)
        $this->info("3. Particionando Ciudades por state_id... esto puede tardar unos segundos.");
        $citiesDir = "$publicDir/cities";
        if (!is_dir($citiesDir)) mkdir($citiesDir, 0755, true);

        $citiesRaw = file_get_contents("$sourceDir/cities.json");
        $citiesData = json_decode($citiesRaw, true);

        $citiesByState = [];
        foreach ($citiesData as $city) {
            $citiesByState[$city['state_id']][] = [
                'id' => $city['id'],
                'name' => $city['name']
            ];
        }

        foreach ($citiesByState as $stateId => $opts) {
            file_put_contents("$citiesDir/$stateId.json", json_encode($opts));
        }

        $this->info("¡Completado! Ahora el frontend puede acceder a los datos de manera estática y eficiente en O(1) vía /storage/locations/");
    }
}
