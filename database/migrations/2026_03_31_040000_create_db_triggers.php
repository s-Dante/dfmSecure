<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Crea los 2 Triggers de negocio del proyecto dfmSecure.
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // TRIGGER 1: trg_validate_policy_on_sinister
        //  → BEFORE INSERT en sinisters
        //  → Valida que la póliza exista y esté activa antes de registrar un siniestro.
        //    Si la póliza está inactiva o vencida, lanza un error y aborta la inserción.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP TRIGGER IF EXISTS trg_validate_policy_on_sinister");

        DB::unprepared("
            CREATE TRIGGER trg_validate_policy_on_sinister
            BEFORE INSERT ON sinisters
            FOR EACH ROW
            BEGIN
                DECLARE v_status    VARCHAR(255);
                DECLARE v_end_date  DATE;

                -- Obtener el status y fecha de fin de la póliza referenciada
                SELECT status, end_validity
                INTO   v_status, v_end_date
                FROM   policies
                WHERE  id = NEW.policy_id
                LIMIT  1;

                -- Validar que la póliza existe y está activa
                IF v_status IS NULL THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Error: La póliza referenciada no existe.';
                END IF;

                IF v_status != 'active' THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Error: No se puede registrar un siniestro. La póliza no está activa.';
                END IF;

                IF v_end_date < CURDATE() THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Error: No se puede registrar un siniestro. La póliza está vencida.';
                END IF;
            END
        ");

        // ─────────────────────────────────────────────────────────────────────
        // TRIGGER 2: trg_auto_set_close_date
        //  → BEFORE UPDATE en sinisters
        //  → Cuando el status cambia a 'closed' o 'resolved' y close_date es NULL,
        //    auto-asigna la fecha actual como fecha de cierre.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP TRIGGER IF EXISTS trg_auto_set_close_date");

        DB::unprepared("
            CREATE TRIGGER trg_auto_set_close_date
            BEFORE UPDATE ON sinisters
            FOR EACH ROW
            BEGIN
                -- Si el status cambió a cerrado/resuelto y aún no tiene fecha de cierre
                IF (NEW.status IN ('closed', 'resolved'))
                    AND (OLD.status NOT IN ('closed', 'resolved'))
                    AND NEW.close_date IS NULL
                THEN
                    SET NEW.close_date = CURDATE();
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_validate_policy_on_sinister");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_auto_set_close_date");
    }
};
