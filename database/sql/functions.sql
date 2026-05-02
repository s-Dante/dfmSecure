-- =============================================================================
-- FUNCTIONS - dfmSecure
-- =============================================================================
-- Descripcion: Funciones escalares de la base de datos del sistema de seguros.
-- Fuente:      database/migrations/2026_03_31_045740_create_db_functions.php
-- Uso:         Ejecutar directamente en MySQL Workbench o cliente compatible.
-- =============================================================================

DELIMITER $$

-- -----------------------------------------------------------------------------
-- FUNCIONES DE POLIZAS
-- -----------------------------------------------------------------------------

/**
 * fn_policy_is_active
 * Determina si una poliza esta vigente y activa.
 * Retorna 1 si esta activa, 0 si no.
 *
 * Uso: SELECT fn_policy_is_active(1);
 */
DROP FUNCTION IF EXISTS fn_policy_is_active $$
CREATE FUNCTION fn_policy_is_active(
    p_policy_id BIGINT UNSIGNED
)
RETURNS TINYINT(1)
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_count INT DEFAULT 0;

    SELECT COUNT(*)
    INTO v_count
    FROM policies
    WHERE id = p_policy_id
        AND status = 'active'
        AND end_validity >= CURDATE()
        AND deleted_at IS NULL;

    RETURN IF(v_count > 0, 1, 0);
END $$

/**
 * fn_policy_deadline
 * Determina los dias restantes para que una poliza expire.
 * Retorna numero positivo si aun vigente, negativo si ya vencio.
 *
 * Uso: SELECT fn_policy_deadline(1);
 */
DROP FUNCTION IF EXISTS fn_policy_deadline $$
CREATE FUNCTION fn_policy_deadline(
    p_policy_id BIGINT UNSIGNED
)
RETURNS INT
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_end_validity DATE;
    DECLARE v_days_left    INT;

    SELECT end_validity
    INTO v_end_validity
    FROM policies
    WHERE id = p_policy_id
        AND deleted_at IS NULL;

    SET v_days_left = DATEDIFF(v_end_validity, CURDATE());

    RETURN v_days_left;
END $$

DELIMITER ;
