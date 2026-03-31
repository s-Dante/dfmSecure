<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

/**
 * Trait UsesDBObjects
 * 
 * helper que determina si el sistema debe utilizar Eloquent
 * o los objetos de la BD (SPs, Triggers, Views, Functions)
 * 
 * Controlado por USE_DB_OBJECTS en .env
 */
trait UsesDBObjects
{
    /**
     * Summary of useDBObjects, indica si se debe de usar Eloquent 
     * u objetos de la BD
     * @return bool
     */
    protected function useDBObjects(): bool
    {
        return config('app.use_db_objects', false);
    }

    /**
     * Ejecuta un SP que retorna resultados
     * 
     * @param string $procedure
     * @param array $params
     * @return array
     */
    protected function callProcedure(string $procedure, array $params = []): array
    {
        $placeholders = implode(', ', array_fill(0, count($params), '?'));
        $sql = "CALL {$procedure}({$placeholders})";

        return DB::select($sql, $params);
    }

    /**
     * Ejecuta un SP que no returna resultados
     * 
     * @param string $procedure
     * @param array $params
     * @return bool
     */
    protected function execProcedure(string $procedure, array $params = []): bool
    {
        $placeholders = implode(', ', array_fill(0, count($params), '?'));
        $sql = "CALL {$procedure}({$placeholders})";

        return DB::statement($sql, $params);
    }
}
