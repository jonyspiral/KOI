<?php

class Funciones {
	static function esPar($numero) {
		return !($numero % 2);
	}
	static function esImpar($numero) {
		return ($numero % 2);
	}
	static function reemplazar($buscarEsto, $reemplazarPorEsto, $enEsteString) {
		return str_replace($buscarEsto, $reemplazarPorEsto, $enEsteString);
	}
	static function sacarTildes($str) {
		$str = str_replace('Á', 'A', $str);
		$str = str_replace('É', 'E', $str);
		$str = str_replace('Í', 'I', $str);
		$str = str_replace('Ó', 'O', $str);
		$str = str_replace('Ú', 'U', $str);
		$str = str_replace('Ñ', 'N', $str);
		$str = str_replace('á', 'a', $str);
		$str = str_replace('é', 'e', $str);
		$str = str_replace('í', 'i', $str);
		$str = str_replace('ó', 'o', $str);
		$str = str_replace('ú', 'u', $str);
		$str = str_replace('ñ', 'n', $str);
		$str = str_replace('ü', 'u', $str);
		$str = str_replace('Ü', 'U', $str);
		return $str;
	}
	static function limpiarNombreDeArchivo($str) {
		$str = self::sacarTildes($str);
		$str = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $str);
		return $str;
	}
	static function formatearDecimales($numero, $decimales, $simboloDecimales = ',', $simboloMiles = '', $exceptions = true) {
		$numero = str_replace('$', '', $numero);
		$numero = str_replace(' ', '', $numero);
		$comaPos = strpos($numero, ',');
		$puntoPos = strpos($numero, '.');
		$comaPosDos = (strlen($numero) > ($comaPos + 1)) ? strpos($numero, ',', $comaPos + 1) : false;
		$puntoPosDos = (strlen($numero) > ($puntoPos + 1)) ? strpos($numero, ',', $puntoPos + 1) : false;
		if ($comaPosDos && $puntoPosDos) {
			if ($exceptions) {
				throw new FactoryExceptionCustomException('Formato de número no reconocido (tiene más de una coma y un punto)');
			}
			return '';
		}
		if ($comaPos && $puntoPos) {
			$numero = str_replace($comaPos < $puntoPos ? ',' : '.', '', $numero);
		}
		$numero = str_replace(',', '.', $numero);
		return number_format($numero, abs($decimales), $simboloDecimales, $simboloMiles);
		/*
		$numero = str_replace('$', '', $numero);
		$numero = str_replace(' ', '', $numero);
		$numero = str_replace(",", ".", $numero);
		return number_format($numero, abs($decimales), $simboloDecimales, $simboloMiles);
		*/
	}
	static function formatearMoneda($numero, $simbolo = '$', $decimales = 2, $simboloDecimales = ',', $simboloMiles = '') {
		return $simbolo . ' ' . self::formatearDecimales($numero, $decimales, $simboloDecimales, $simboloMiles);
	}
	static function formatearPorcentaje($numero, $simbolo = '%', $decimales = 2, $simboloDecimales = ',') {
		return self::formatearDecimales($numero, $decimales, $simboloDecimales) . ' ' . $simbolo;
	}
	static function strFechas(&$desde, &$hasta, $campoFecha, $desdeObligatorio = false, $hastaObligatorio = false, $maxDias = 0, /*$minDias = 0, MUCHO LIO */$forzarHasta = false, $forzarDesde = false){
		$desde == '__/__/____' && $desde = null;
		$hasta == '__/__/____' && $hasta = null;
        //$minDias < 0 && $minDias = 0;
        $minDias = 0; // Dado que tenemos comentado el param de minDias, comento la linea de arriba y lo seteo en 0
        $maxDias < 0 && $maxDias = 0;
		if (is_null($desde) && $desdeObligatorio) {
			throw new FactoryExceptionCustomException('Debe ingresar una fecha "desde"');
		}
		if (is_null($hasta) && $hastaObligatorio) {
			throw new FactoryExceptionCustomException('Debe ingresar una fecha "hasta"');
		}
		if (is_null($desde) && is_null($hasta) && ($maxDias || $minDias)) {
			throw new FactoryExceptionCustomException('No se puede limitar la cantidad de días sin una fecha "desde" o "hasta"');
		}

		$error = 'El rango de fechas no puede superar los ' . $maxDias . ' días';

		if ($maxDias) {
			$desdeMasMaxDias = Funciones::sumarTiempo($desde, $maxDias, 'days');
			if (is_null($hasta)) {
				if ($forzarHasta) {
					$hasta = $desdeMasMaxDias;
				} else {
					throw new FactoryExceptionCustomException($error);
				}
			} elseif (!is_null($hasta) && Funciones::esFechaMenor($desdeMasMaxDias, $hasta)) {
				if ($forzarHasta) {
					$hasta = $desdeMasMaxDias;
				} else {
					throw new FactoryExceptionCustomException($error);
				}
			}
		}

		if ($maxDias) {
			$hastaMenosMaxDias = Funciones::sumarTiempo($hasta, -1 * $maxDias, 'days');
			if (is_null($desde)) {
				if ($forzarDesde) {
					$desde = $hastaMenosMaxDias;
				} else {
					throw new FactoryExceptionCustomException($error);
				}
			} elseif (!is_null($desde) && Funciones::esFechaMenor($desde, $hastaMenosMaxDias)) {
				if ($forzarDesde) {
					$desde = $hastaMenosMaxDias;
				} else {
					throw new FactoryExceptionCustomException($error);
				}
			}
		}

		if (!is_null($desde) && !is_null($hasta) && Funciones::esFechaMenor($hasta, $desde)) {
			throw new FactoryExceptionCustomException('La fecha "desde" no puede ser posterior a la fecha "hasta"');
		}

		$strFechas = '';
		(!is_null($desde)) && ($strFechas .= ' (' . $campoFecha . ' >= dbo.relativeDate(dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($desde)) . '), ' . Datos::objectToDB('today') . ', 0)' . (is_null($hasta) ? ')' : ''));
		(!is_null($hasta)) && ($strFechas .= ' AND ' . (is_null($desde) ? '(' : '') . '' . $campoFecha . ' < dbo.relativeDate(dbo.toDate(' . Datos::objectToDB(Funciones::formatearFecha($hasta)) . '), ' . Datos::objectToDB('tomorrow') . ', 0)) ');
		return trim($strFechas, ' AND');
	}
	static function esFechaMayor($fecha1, $fecha2) {
		return (strtotime(Funciones::formatearFecha($fecha1, 'm/d/Y')) > strtotime(Funciones::formatearFecha($fecha2, 'm/d/Y')));
	}
	static function esFechaMenor($fecha1, $fecha2) {
		return (strtotime(Funciones::formatearFecha($fecha1, 'm/d/Y')) < strtotime(Funciones::formatearFecha($fecha2, 'm/d/Y')));
	}
	static function esFechaMenorOIgual($fecha1, $fecha2) {
		return (strtotime(Funciones::formatearFecha($fecha1, 'm/d/Y')) <= strtotime(Funciones::formatearFecha($fecha2, 'm/d/Y')));
	}
	static function diferenciaMinutos($hora1, $hora2) {
		$ah1 = explode(':', $hora1);
		$ah2 = explode(':', $hora2);
		$difHora = (Funciones::toInt($ah1[0]) - Funciones::toInt($ah2[0])) * 60;
		$difMinutos = Funciones::toInt($ah1[1]) - Funciones::toInt($ah2[1]);
		$dif = $difHora + $difMinutos;
		return $dif;
	}

	/**
	 * Devuelve la cantidad de días que hay entre la fecha 1 y la fecha 2
	 * Si el tercer parámetro es FALSE, devuelve positivo si la primer fecha es mayor
	 *
	 * @param      $fecha1
	 * @param      $fecha2
	 * @param bool $abs
	 *
	 * @return float
	 */
	static function diferenciaFechas($fecha1, $fecha2, $abs = true) {
		$dif = strtotime(Funciones::formatearFecha($fecha1, 'm/d/Y')) - strtotime(Funciones::formatearFecha($fecha2, 'm/d/Y'));
		$abs && $dif = abs($dif);
		return floor($dif / (60*60*24));
	}
	/**
	 * Ejemplo de uso: Funciones::sumarTiempo('2012-05-03', -8, 'months', 'd/m/Y H:i');
	 *
	 * @param string	$fecha
	 * @param Int		$cantidad	Un valor, positivo o negativo
	 * @param string	$unidad		Puede ser 'seconds', 'minutes', 'hours', 'days', 'weeks', 'months' o 'years'
	 * @param string	$formato
	 * @return null|string
	 */
	static function sumarTiempo($fecha, $cantidad, $unidad = 'days', $formato = 'd/m/Y') {
		return Funciones::formatearFecha(strtotime(($cantidad < 0 ? '-' : '+') . abs($cantidad) . ' ' . $unidad, strtotime(Funciones::formatearFecha($fecha, 'Y-m-d'))), $formato);
	}
	static function formatearHora($hora, $formato = 'H:i') {
		return date($formato, $hora);
	}
	static function formatearFecha($fecha, $formato = 'd/m/Y') {
		/**
		 * Puede formatear los siguientes formatos:
		 * dd/mm/aaaa[ hh:mm]
		 * dd-mm-aaaa[ hh:mm]
		 * dd MES aaaa[ hh:mm]
		 *
		 * $today = date("d/m/Y H:i");						// 10/03/2001 05:16
		 * $today = date("F j, Y, g:i a");					// March 10, 2001, 5:16 pm
		 * $today = date("m.d.y");							// 03.10.01
		 * $today = date("j, n, Y");						// 10, 3, 2001
		 * $today = date("Ymd");							// 20010310
		 * $today = date('h-i-s, j-m-y, it is w Day');		// 05-16-18, 10-03-01, 1631 1618 6 Satpm01
		 * $today = date('\i\t \i\s \t\h\e jS \d\a\y.');	// it is the 10th day.
		 * $today = date("D M j G:i:s T Y");				// Sat Mar 10 17:16:18 MST 2001
		 * $today = date('H:m:s \m \i\s\ \m\o\n\t\h');		// 17:03:18 m is month
		 * $today = date("H:i:s");							// 17:16:18
		 */
		if (!isset($fecha) || $fecha == '0000-00-00') {
			return null;
		}
		if (!is_int($fecha)) {
			$arrayMeses = array( 'ene' => 'jan', 'abr' => 'apr', 'ago' => 'aug', 'dic' => 'dec');
			foreach ($arrayMeses as $es => $en) {
				$fecha = str_replace($es, $en, $fecha);
			}
			$fecha = strtotime(str_replace('/', '-', $fecha));
		}
		if (!$fecha) {
			throw new FactoryExceptionCustomException('La fecha no tiene un formato válido, o es posterior al año 2037');
		}
		return date($formato, $fecha);
	}
	static function formatearNumeroCheque($numero) {
		$trim = trim($numero, '-__');
		return trim($trim, '_');
	}
	static function ahora($formato = 'H:i:s') {
		return date($formato, time());
	}
	static function arraySort($array, $atributo, $funcion = 'cmp') {
		$arrayAux = array();
		foreach($array as $item) {
			if(isset($item->$atributo)) {
				$arrayAux[$item->$atributo] = $item;
			} else {
				if(isset($item[$atributo])) {
					$arrayAux[$item[$atributo]] = $item;
				}
			}
		}

		uksort($arrayAux, $funcion);

		return (count($arrayAux) == 0 ? $array : $arrayAux);
	}
	static function hoy($formato = 'd/m/Y') {
		return date($formato);
	}
	static function getDate($formato = 'd/m/Y H:i:s', $timestamp = null) {
		is_null($timestamp) && $timestamp = time();
		return date($formato, $timestamp);
	}
	static function roundUp($num) {
		return ceil($num);
	}
	static function roundDown($num) {
		return floor($num);
	}
	static function getType($var) {
		if(is_object($var))
			return get_class($var);
		if(is_null($var))
			return 'null';
		if(is_string($var))
			return 'string';
		if(is_array($var))
			return 'array';
		if(is_int($var))
			return 'int';
		if(is_bool($var))
			return 'bool';
		if(is_float($var))
			return 'float';
		if(is_double($var))
			return 'double';
		return '';
	}
	static function toInt($var) {
		$var = self::formatearDecimales($var, 0);
		if (is_int($var))
			return $var;
		else
			return (int)$var;
	}
	static function toFloat($var, $decimales = '4') {
		$var = Funciones::formatearDecimales($var, $decimales, '.');
		return (float)$var;
	}
	static function toDouble($var) {
		$var = str_replace(',', '.', $var);
		if (is_double($var))
			return $var;
		else
			return (double)$var;
	}
	static function toNatural($var) {
		$var = self::toInt($var);
		return ($var < 0) ? 0 : $var;
	}
	static function toString($var) {
		if (is_string($var))
			return trim($var);
		elseif (is_scalar($var))
			return trim($var);
		else
			return '';
	}
	static function toSHA1($var){
		if (!isset($var))
			return $var;
		return sha1($var);
	}
	static function tieneId($arr) {
		//Se usa en Factory
        if (!is_array($arr) || !count($arr)) {
            return false;
        }
		foreach($arr as $id) {
			if (!isset($id) || $id == '' || $id == -1)
				return false;
		}
		return true;
	}
	static function cast($obj, $claseCast) {
		try {
			$clone = new $claseCast();
			foreach ($obj as $key => $val) {
				$clone->$key = $val;
			}
			return $clone;
		} catch (Exception $ex) {
			throw new Exception('La clase ' . Funciones::getType($obj) . ' no es casteable a ' . $claseCast);
		}
	}
	static function get($valor, $asignar = null) {
		(!is_null($asignar)) && $_GET[$valor] = $asignar;
		if ((!isset($_GET[$valor])) || (($obj = $_GET[$valor]) == ''))
			return null;
		return HTML::utfDecode($obj);
	}
	static function ponerGuionesAlCuit($cuit) {
		$noSeQueEs = substr($cuit, 0, 2);
		$dni = substr($cuit, 2, count($cuit) - 2);
		$digitoVerificador = substr($cuit, -1);

		return $noSeQueEs . '-' . $dni . '-' . $digitoVerificador;
	}
	static function post($valor, $asignar = null) {
		(!is_null($asignar)) && $_POST[$valor] = $asignar;
		if ((!isset($_POST[$valor])) || (($obj = $_POST[$valor]) == ''))
			return null;
		return HTML::utfDecode($obj);
	}
	static function session($valor, $asignar = null) {
		(!is_null($asignar)) && $_SESSION[$valor] = $asignar;
		if ((!isset($_SESSION[$valor])) || (($obj = $_SESSION[$valor]) == ''))
			return null;
		return HTML::utfDecode($obj);
	}
	static function iIsSet($var, $returnIfNotSet = '') {
		if (isset($var))
			return $var;
		else
			return $returnIfNotSet;
	}
	static function keyIsSet($array, $key, $returnIfNotSet = '') {
		if (!is_array($array) && !is_object($array)) {
			$array = array();
		}
		return array_key_exists($key, $array) ? $array[$key] : $returnIfNotSet;
	}
	static function sumaArray($array, $soloPositivos = false) {
        if (isset($array) && is_array($array)) {
            if ($soloPositivos) {
                $newArray = array();
                foreach ($array as $i => $num) {
                    $newArray[$i] = ($num < 0) ? 0 : $num;
                }
                $array = $newArray;
            }
            return array_sum($array);
        }
		return 0;
	}
	static function soloPositivos($array) {
		for ($i = 1; $i <= count($array); $i++)
			if ($array[$i] < 1)
				$array[$i] = 0;
		return $array;
	}
	static function reconstruirArray($array){
		if (isset($array))
			return array_values($array); 
		return array();
	}
	static function acortar($str, $cantMax, $charFinal = '...') {
		if (strlen($str) <= $cantMax)
			return $str;
		return substr($str, 0, $cantMax) . $charFinal;
	}
	static function padLeft($obj, $cantidad, $charRelleno = '0') {
		if (!isset($obj) || is_null($obj))
			return null;
		return str_pad($obj, $cantidad, $charRelleno, STR_PAD_LEFT);
	}
	static function padRight($obj, $cantidad, $charRelleno = '0') {
		if (!isset($obj) || is_null($obj))
			return null;
		return str_pad($obj, $cantidad, $charRelleno, STR_PAD_RIGHT);
	}
	static function padBoth($obj, $cantidad, $charRelleno = '0') {
		if (!isset($obj) || is_null($obj))
			return null;
		//Si tiene que rellenar más de un lado que del otro, rellena más del right
		return str_pad($obj, $cantidad, $charRelleno, STR_PAD_BOTH);
	}
	static function toLower($str) {
		if (!isset($str) || is_null($str))
			return '';
		return strtolower($str);
	}
	static function toUpper($str) {
		if (!isset($str) || is_null($str))
			return '';
		return strtoupper($str);
	}
	static function getIpAddress() {
		return $_SERVER['REMOTE_ADDR'];
	}
	static function getMacAddress() {
		$macAddr = false;
		//Comando externo
		$ipAddress = self::getIpAddress();
		$arp = `arp -a $ipAddress`; //LAS COMILLAS VAN ASÍ!!!
		$lines = explode('\n', $arp);
		//Busco la linea que tiene la IP que busco
		foreach($lines as $line) {
			$cols = preg_split('/\s+/', trim($line));
			if ($cols[9] == $ipAddress) {
				$macAddr = $cols[10];
			}
		}
		return self::toUpper($macAddr);
	}
	static function validarCuit($cuit) {
		if ($cuit == '50000009986') {
			return true;
		}

		$cuit = Funciones::toString($cuit);
		$arrayMultiplicacion = array(5, 4, 3, 2, 7, 6, 5, 4, 3, 2);
		$divisor = 11;
		$dividendo = 0;

		if (strlen($cuit) != 11) {
			return false;
		}
		$c12 = $cuit[0] . $cuit[1];
		if (!($c12 == "20" || $c12 == "23" || $c12 == "24" || $c12 == "27" || $c12 == "30" || $c12 == "33" || $c12 == "34")) {
			return false;
		}
		for($i = 0; $i < 10; $i++) {
			$dividendo += $cuit[$i] * $arrayMultiplicacion[$i];
		}
		$resto = $dividendo % $divisor;
		$digito = 11 - $resto;

		switch ($digito) {
			case 11:
				$digitoVerificadorCalculado = 0;
				break;
			case 10:
				$digitoVerificadorCalculado = 9;
				break;
			default:
				$digitoVerificadorCalculado = $digito;
		}

		if($digitoVerificadorCalculado != $cuit[10]){
			return false;
		}

		return true;
	}
	static function validarDni($dni) {
		$dni = Funciones::toString($dni);
    	if (strlen($dni) >= 7 && strlen($dni) <= 9) {
			return true;
		}
		return false;
	}
	static function jsonDecode($var, $assoc = true) {
		return json_decode($var, $assoc);
	}
	static function jsonEncode($var) {
		return json_encode($var);
	}
	static function contieneString($cadenaDeTexto, $cadenaBuscada) {
		//Se fija si $cadenaDeTexto contiene a $cadenaBuscada
		return !!strpos($cadenaDeTexto, $cadenaBuscada);
	}
	static function hash($item) {
		if (!is_string($item)) {
			$item = json_encode($item);
		}
		return md5($item);
	}
	static function lcfirst($input) {
	    return strtolower(substr($input ,0,1)).substr($input,1);
    }
	static function snakeCase($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : Funciones::lcfirst($match);
        }
        return implode('_', $ret);
    }
    static function getFilesFromDir($dir) {
        function filterDirs($file) {
            return !in_array($file, array('.', '..'));
        }
        $files = scandir($dir);
        return array_values(array_filter(
            $files ? $files : array(),
            "filterDirs"
        ));
    }
}


?>