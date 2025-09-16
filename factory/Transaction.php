<?php

abstract class Transaction  {
    // Mantengo las constantes por compatibilidad (pueden estar referenciadas en otros lados)
     const BEGIN    = 'START TRANSACTION';
    const COMMIT   = 'COMMIT';
    const ROLLBACK = 'ROLLBACK';

    private static $_transaction = 0;

    /** Detecta driver actual. Conservador: todo lo que NO sea mysql -> T-SQL */
    private static function driverName() {
        $drv = 'sqlsrv';
        if (class_exists('Config') && method_exists('Config', 'get')) {
            $d = strtolower((string) @Config::get('db', 'driver'));
            if ($d) { $drv = $d; }
        }
        return $drv;
    }

    /** Devuelve el SQL correcto según driver */
    private static function sql($op) {
        $drv = self::driverName();
        $isMy = ($drv === 'mysql' || $drv === 'mysqli' || $drv === 'pdo_mysql');

        if ($isMy) {
            switch ($op) {
                case 'BEGIN':    return 'START TRANSACTION;'; // en MySQL no se nombra
                case 'COMMIT':   return 'COMMIT;';
                case 'ROLLBACK': return 'ROLLBACK;';
            }
        } else {
            // SQL Server / ODBC FreeTDS
            switch ($op) {
                case 'BEGIN':    return self::BEGIN;
                case 'COMMIT':   return self::COMMIT;
                case 'ROLLBACK': return self::ROLLBACK;
            }
        }
        // Fallback seguro
        return ($op === 'BEGIN') ? 'START TRANSACTION;' : ($op === 'COMMIT' ? 'COMMIT;' : 'ROLLBACK;');
    }

    public static function exists($silent = false) {
        if (self::$_transaction < 0) {
            if (!$silent) {
                throw new TransactionException('No hay una instancia de transacción iniciada');
            }
        }
        return self::$_transaction > 0;
    }

    private static function addOneLevel() {
        return self::$_transaction++;
    }

    private static function subtractOneLevel() {
        return self::$_transaction--;
    }

    public static function begin() {
        try {
            $beginNew = !self::exists();
            if ($beginNew) {
                // Antes emitía self::BEGIN (T-SQL). Ahora elegimos por driver.
                Datos::EjecutarSQL(self::sql('BEGIN'));
            }
            self::addOneLevel();
            return $beginNew;
        } catch (Exception $ex) {
            throw new TransactionException($ex->getMessage());
        }
    }

    public static function commit() {
        try {
            self::subtractOneLevel();
            $commit = !self::exists();
            if ($commit) {
                Datos::EjecutarSQL(self::sql('COMMIT'));
            }
            return $commit;
        } catch (Exception $ex) {
            throw new TransactionException($ex->getMessage());
        }
    }

    public static function rollback() {
        try {
            $rollback = self::exists(true);
            if ($rollback) {
                Datos::EjecutarSQL(self::sql('ROLLBACK'));
            }
            self::$_transaction = 0;
            return $rollback;
        } catch (Exception $ex) {
            throw new TransactionException($ex->getMessage());
        }
    }
}

?>
