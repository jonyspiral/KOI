<?php

class Datos {
	private static $_linkDeConexion;

	static protected function abrirConexion() {
		try {
			if (!isset(self::$_linkDeConexion)){
				self::$_linkDeConexion = @mssql_connect(Config::conexion_sql_ip, Config::conexion_sql_user, Config::conexion_sql_pass);
				@mssql_select_db(Config::conexion_sql_db);
			}
		} catch (Exception $ex) {
			throw new Exception("Error cuando se intentaba conectar a la base de datos. Message: " . $ex->getMessage());
		}
	}
	static function EjecutarSQLsinQuery($sSql) {
		try {
			if (!empty($sSql)) {
				Datos::abrirConexion();
				Datos::getQuery($sSql);
			}
		} catch (Exception $ex) {
			$msg = "Error al tratar de ejecutar la sentencia: " . $sSql . ". Message: " . $ex->getMessage();
			Logger::addError($msg);
			throw new Exception($msg);
		}
	}
	static function EjecutarSQLItem($sSql, $cacheTag = null) {
		$items = self::EjecutarSQL($sSql, $cacheTag);
		if (count($items) != 1) {
			throw new FactoryExceptionRegistroNoExistente();
		} else {
			return $items[0];
		}
	}
	static function EjecutarSQL($sSql, $cacheTag = null) {
		try {
			if (!is_string($cacheTag)) {
				$cacheTag = null;
			}
			$resultados = array();

			Datos::abrirConexion();

			// Check if cache is required by the class
			$cacheTime = -1;
			if (class_exists($cacheTag) && property_exists($cacheTag, '__CACHE_TIME')) {
				$cacheTagClass = new $cacheTag();
				$cacheTime = $cacheTagClass->__CACHE_TIME;
			}

			if ($cacheTag && $cacheTime !== false) {
				$cacheHash = Funciones::hash($sSql);
				$result = Cache::get($cacheHash, $cacheTag);
				if ($result) {
					return $result;
				}
			}

			$dbResults = Datos::getQuery($sSql);
			for ($i = 0; $i < Datos::getNumRows($dbResults); $i++) {
				$resultados[] = Datos::getRow($dbResults);
			}

			if ($cacheTag && $cacheTime !== false) {
				Cache::set($cacheHash, $resultados, $cacheTag, $cacheTime);
			}

			return $resultados;
		} catch (Exception $ex) {
			$msg = "Error al tratar de ejecutar la sentencia: " . $sSql . ". Message: " . $ex->getMessage();
			Logger::addError($msg);
			throw new Exception($msg);
			
		}
	}
	static function EjecutarSQL2($sSql, $cacheTag = null) {
		try {
			Datos::abrirConexion();
			return Datos::getQuery($sSql);
		} catch (Exception $ex) {
			$msg = "Error al tratar de ejecutar la sentencia: " . $sSql . ". Message: " . $ex->getMessage();
			Logger::addError($msg);
			throw new Exception($msg);
		}
	}
	static function EjecutarTransaccion($sentencia) {
		Datos::abrirConexion();
		try {
			Datos::EjecutarSQL($sentencia);
		} catch (Exception $ex) {
			throw new TransactionException($ex);
		}
	}
	static function objectToDB($obj) {
		try {
			if (is_null($obj)/* || (!(is_scalar($obj) && ($obj != 0)))*/)
				return 'NULL';
			switch (Funciones::getType($obj)){
				case 'bool':
					if ($obj) return 'true';
					elseif(!$obj) return 'false';
					else return 'NULL';
				case 'string':
					if ($obj == null || $obj == '') return "''";
					else return "'" . str_replace("'", "''", $obj) . "'";
				case 'int':
				case 'float':
				case 'double':
					return $obj;
				case 'array':
					return self::objectToDB(Funciones::jsonEncode($obj));
				default:
					if ($obj == null)
						return null;
					else
						return $obj;
			}
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	static private function getQuery($sql){
		try {
			//Cuando hay un error devuelve "Error " al principio para que el getNumRows devuelva 0
			//cuando le llega un error en lugar de resultados correctos (sino crashea el getNumRows).
			$result = @mssql_query($sql);
			if (!$result){
				$error = @mssql_get_last_message();
				if (true/*rango()*/) //Esto es para que no cualquiera pueda ver los errores. CuestiỉƯơn de seguridad.
					throw new FactoryExceptionRegistroNoExistente('Error ' . $error);
					//return "Error " . $error . ' QUERY: ' . $sql;
				else
					throw new FactoryExceptionRegistroNoExistente('Error inesperado. Contacte al administrador.');
					//return "Error inesperado. Contacte al administrador.";
			} else
				if ($result == "true") //Esto es lo que devuelven las consultas como UPDATE o INSERT o DELETE cuando salen bien.
					return true;
				else
					return $result;
		} catch (FactoryExceptionRegistroNoExistente $eex) {
			throw $eex;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	static function getNumRows($resultado){
/*		if ($resultado == '' || substr($resultado . '', 0, 6) == "Error ")
			return 0;
		else
			return @mssql_num_rows($resultado);
*/
		return @mssql_num_rows($resultado);
	}
	static function getRow($resultado){
		try {
			if (Datos::getNumRows($resultado) > 0)
				return @mssql_fetch_assoc($resultado);
			else
				throw new FactoryExceptionRegistroNoExistente();
		} catch (Exception $ex) {
			throw new FactoryExceptionRegistroNoExistente();
		}
	}
}

?>
