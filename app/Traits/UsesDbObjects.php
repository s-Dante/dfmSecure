<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

/**
 * Trait UsesDbObjects
 *
 * Proporciona un helper para determinar si el sistema debe usar
 * objetos de BD (Stored Procedures, Views, Functions) o Eloquent.
 *
 * Controlado por la variable USE_DB_OBJECTS en .env
 */
trait UsesDbObjects
{
    /**
     * Indica si el sistema debe usar objetos de BD en lugar de Eloquent.
     */
    protected function useDbObjects(): bool
    {
        return config('app.use_db_objects', false);
    }

    /**
     * Ejecuta un Stored Procedure y retorna los resultados.
     *
     * @param  string  $procedure  Nombre del SP
     * @param  array   $params     Parámetros del SP
     * @return array
     */
    protected function callProcedure(string $procedure, array $params = []): array
    {
        $placeholders = implode(', ', array_fill(0, count($params), '?'));
        $sql = "CALL {$procedure}({$placeholders})";

        return DB::select($sql, $params);
    }

    /**
     * Ejecuta un Stored Procedure que no retorna resultados (INSERT/UPDATE/DELETE).
     *
     * @param  string  $procedure  Nombre del SP
     * @param  array   $params     Parámetros del SP
     * @return bool
     */
    protected function execProcedure(string $procedure, array $params = []): bool
    {
        $placeholders = implode(', ', array_fill(0, count($params), '?'));
        $sql = "CALL {$procedure}({$placeholders})";

        return DB::statement($sql, $params);
    }
}
