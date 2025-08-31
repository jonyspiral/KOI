<?php
/**
 * Datos
 * Fachada de acceso a datos independiente de motor.
 * Delegamos en el driver configurado (Factory::getInstance()->db()).
 * Mantiene los nombres/firmas usados por el código legacy de KOI1.
 */
class Datos {

    /** @return mixed Driver actual (DbMysql, DbMssql, etc.) */
    private static function db() {
        return Factory::getInstance()->db();
    }

    /* =========================
       SELECTs “crudos”
       ========================= */

    /** Devuelve arreglo de filas (array<array<string,mixed>>) */
    public static function EjecutarSQL($sql, $class = '') {
        return self::db()->query($sql);
    }

    /** Devuelve una sola fila (array<string,mixed>|null) */
    public static function EjecutarSQLItem($sql, $class = '') {
        return self::db()->queryOne($sql);
    }

    /** Devuelve un valor escalar (mixed|null) */
    public static function EjecutarScalar($sql) {
        return self::db()->value($sql);
    }

    /* =========================
       Con parámetros
       ========================= */

    /** SELECT con parámetros (array) */
    public static function EjecutarSQLParams($sql, $params = array()) {
        return self::db()->query($sql, is_array($params) ? $params : array());
    }

    /** No-SELECT (INSERT/UPDATE/DELETE) con o sin parámetros */
    public static function EjecutarCommand($sql, $params = array()) {
        // Algunos drivers exponen exec(); si no existe, usamos query() y devolvemos filas afectadas si está disponible.
        $db = self::db();
        if (method_exists($db, 'exec')) {
            return $db->exec($sql, is_array($params) ? $params : array());
        }
        $res = $db->query($sql, is_array($params) ? $params : array());
        // Compat: si no hay filas afectadas, retornamos 0.
        return is_int($res) ? $res : 0;
    }

	// En factory/Datos.php, dentro de class Datos
public static function objectToDB($obj) {
    try {
        // Mantener contrato legacy: NULL literal
        if (is_null($obj)) {
            return 'NULL';
        }

        switch (Funciones::getType($obj)) {
            case 'bool':
                // Literal SQL, sin comillas (legacy): true/false
                if ($obj) return 'true';
                elseif (!$obj) return 'false';
                else return 'NULL';

            case 'string':
                // Cadena vacía -> '' y escape de comillas simples -> ''
                if ($obj === '') return "''";
                return "'" . str_replace("'", "''", $obj) . "'";

            case 'int':
            case 'float':
            case 'double':
                // Números como vienen (sin comillas)
                return $obj;

            case 'array':
                // Arrays a JSON como hacía el framework; luego quoted via recursión
                return self::objectToDB(Funciones::jsonEncode($obj));

            default:
                // Comportamiento legacy: si llega otro tipo (por ej. un wrapper de expresión),
                // devolverlo crudo para que el Mapper pueda concatenarlo tal cual.
                return ($obj == null) ? null : $obj;
        }
    } catch (Exception $ex) {
        throw $ex;
    }
}



    /* =========================
       Stored Procedures
       ========================= */

    /**
     * Ejecuta un SP y devuelve arreglo de filas.
     * $params puede ser array (se usan placeholders ?) o string ya formateado.
     */
    public static function EjecutarStoredProcedure($name, $params = array()) {
        if (is_array($params)) {
            $placeholders = implode(',', array_fill(0, count($params), '?'));
            $sql = 'CALL ' . $name . '(' . $placeholders . ')';
            return self::db()->query($sql, $params);
        } else {
            $paramStr = trim((string)$params);
            $sql = 'CALL ' . $name . (strlen($paramStr) ? '(' . $paramStr . ')' : '()');
            return self::db()->query($sql);
        }
    }

    /** Ejecuta un SP y devuelve una fila */
    public static function EjecutarStoredProcedureItem($name, $params = array()) {
        $rows = self::EjecutarStoredProcedure($name, $params);
        return (is_array($rows) && count($rows)) ? $rows[0] : null;
    }

    /** Ejecuta un SP y devuelve el primer valor de la primera fila */
    public static function EjecutarStoredProcedureScalar($name, $params = array()) {
        $row = self::EjecutarStoredProcedureItem($name, $params);
        return is_array($row) ? reset($row) : null;
    }

    /* =========================
       Transacciones (alias)
       ========================= */

    public static function BeginTransaction() {
        return method_exists(self::db(), 'beginTransaction')
            ? self::db()->beginTransaction()
            : null;
    }

    public static function CommitTransaction() {
        return method_exists(self::db(), 'commit')
            ? self::db()->commit()
            : null;
    }

    public static function RollbackTransaction() {
        return method_exists(self::db(), 'rollBack')
            ? self::db()->rollBack()
            : (method_exists(self::db(), 'rollback') ? self::db()->rollback() : null);
    }

    /* =========================
       Aliases de compatibilidad
       ========================= */

    public static function EjecutarSQLWithParams($sql, $params = array()) {
        return self::EjecutarSQLParams($sql, $params);
    }

    public static function Exec($sql, $params = array()) {
        return self::EjecutarCommand($sql, $params);
    }
}
