<?php

abstract class Transaction {
    private static $_transaction = 0;

    public static function exists($silent = false) {
        if (self::$_transaction < 0) {
            if (!$silent) {
                throw new TransactionException('No hay una instancia de transaccion iniciada');
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
                Datos::BeginTransaction();
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
                Datos::CommitTransaction();
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
                Datos::RollbackTransaction();
            }
            self::$_transaction = 0;
            return $rollback;
        } catch (Exception $ex) {
            throw new TransactionException($ex->getMessage());
        }
    }
}

?>
