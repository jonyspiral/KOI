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
 // factory/Datos.php

/**
 * Devuelve una sola fila (array<string,mixed>|null)
 * Normaliza "0 filas" => null (comportamiento SQLServer),
 * y además maneja tipos variados de retorno del driver MySQL.
 */
public static function EjecutarSQLItem($sql, $class = '')
{
    $raw = self::db()->queryOne($sql);

    // --- DEBUG opcional
    if (function_exists('__dbg')) {
        $dbg = array(
            'type'     => gettype($raw),
            'is_array' => is_array($raw),
            'count'    => is_array($raw) ? count($raw) : null
        );
        __dbg('DATOS.EjecutarSQLItem.raw', $dbg);
    }

    // ---------------------------
    // Normalización + compatibilidad KOI1:
    // Si NO hay fila, debe lanzar FactoryExceptionRegistroNoExistente
    // para que Base::existeEnDB() funcione como antes.
    // ---------------------------

    // 1) Sin resultados (false/null/[] ⇒ NO existe)  // <--- BLOQUE CLAVE
    if ($raw === false || $raw === null || (is_array($raw) && count($raw) === 0)) {
        // Mantenemos compatibilidad con el flujo legacy:
        if (class_exists('FactoryExceptionRegistroNoExistente')) {
            throw new FactoryExceptionRegistroNoExistente('Registro no existente');
        } else {
            // Fallback: lanzar una Exception genérica para no “mentir” existencia
            throw new Exception('Registro no existente');
        }
    }

    // 2) Algunos drivers devuelven [[fila]] en lugar de [fila]
    if (is_array($raw) && isset($raw[0]) && is_array($raw[0])) {
        // si viniera envuelto, devolvemos la primera fila asociativa
        return $raw[0];
    }

    // 3) Ya tenemos la fila asociativa
    if (is_array($raw)) {
        return $raw;
    }

    // 4) Cualquier otro caso raro: devolver tal cual
    return $raw;
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
    public static function EjecutarSQLsinQuery($sql) {
    // Si tenés un shim T-SQL→MySQL, aplicalo acá
    if (method_exists('Datos', '_shimTSql')) { $sql = self::_shimTSql($sql); }

    if (function_exists('__dbg')) { __dbg('DATOS.EjecutarSQLsinQuery.sql', mb_substr($sql,0,500)); }

    // Ejecuta con el driver actual (DbMysql->exec)
    $db = self::db();
    $ok = $db->exec($sql);

    if ($ok === false && function_exists('__dbg')) {
        $driverErr = null;
        if (is_object($db)) {
            if (method_exists($db, 'lastError'))       $driverErr = $db->lastError();
            elseif (property_exists($db, 'lastError')) $driverErr = $db->lastError;
        }
        __dbg('DATOS.EjecutarSQLsinQuery.err', array('driverErr'=>$driverErr));
    }
    return $ok;
}

}
