<?php

abstract class Transaction  {
	const	BEGIN = 'BEGIN TRANSACTION T1; ';
	const	COMMIT = 'COMMIT TRANSACTION T1; ';
	const	ROLLBACK = 'ROLLBACK TRANSACTION T1; ';

	private static $_transaction = 0;

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
			($beginNew) && Datos::EjecutarSQL(self::BEGIN);
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
			($commit) && Datos::EjecutarSQL(self::COMMIT);
			return $commit;
		} catch (Exception $ex) {
			throw new TransactionException($ex->getMessage());
		}
	}

	public static function rollback() {
		try {
			$rollback = self::exists(true);
			($rollback) && Datos::EjecutarSQL(self::ROLLBACK);
			self::$_transaction = 0;
			return $rollback;
		} catch (Exception $ex) {
			throw new TransactionException($ex->getMessage());
		}
	}
}

?>