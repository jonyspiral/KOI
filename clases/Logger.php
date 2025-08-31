<?php

class Logger {
	const DEBUG = 100;
	const INFO = 200;
	const NOTICE = 250;
	const WARNING = 300;
	const ERROR = 400;
	const CRITICAL = 500;
	const ALERT = 550;
	const EMERGENCY = 600;

	protected static $levels = array(
		100 => 'DEBUG',
		200 => 'INFO',
		250 => 'NOTICE',
		300 => 'WARNING',
		400 => 'ERROR',
		500 => 'CRITICAL',
		550 => 'ALERT',
		600 => 'EMERGENCY',
	);

	public static function logPath() {
		return Config::pathBase . '/tmp/logs/';
	}

	public static function addRecord($level, $message, $context = array()) {
		$contextString = '';
        if (!is_array($context)) {
            $context = (array) $context;
        }
        foreach ($context as $variable => $value) {
			$contextString .= $variable . ' => ' . $value . ', ';
		}
		$contextString = trim($contextString, ', ');
		$lock = true;
		$string = Funciones::toString(Funciones::hoy('Y-m-d H:i:s')) . ' - ' . self::getLevelName($level) . ' - Msg: ' . $message . (empty($contextString) ? '' : ' - Ctx: (' . $contextString . ')') . "\r\n";
		$file = fopen(self::logPath() . Funciones::toString(($level >= 400 ? 'exceptions.' : '') . Funciones::hoy('Y-m-d')) . '.txt', 'a');

		while ($lock) {
			if (flock($file, LOCK_EX)) {
				$lock = false;
				fwrite($file, $string);
				flock($file, LOCK_UN);
			}
		}

		return fclose($file);
	}

	public static function addDebug($message, $context = array()) {
		return self::addRecord(self::DEBUG, $message, $context);
	}

	public static function addInfo($message, $context = array()) {
		return self::addRecord(self::INFO, $message, $context);
	}

	public static function addNotice($message, $context = array()) {
		return self::addRecord(self::NOTICE, $message, $context);
	}

	public static function addWarning($message, $context = array()) {
		return self::addRecord(self::WARNING, $message, $context);
	}

	public static function addError($message, $context = array()) {
		return self::addRecord(self::ERROR, $message, $context);
	}

	public static function addCritical($message, $context = array()) {
		return self::addRecord(self::CRITICAL, $message, $context);
	}

	public static function addAlert($message, $context = array()) {
		return self::addRecord(self::ALERT, $message, $context);
	}

	public static function addEmergency($message, $context = array()) {
		return self::addRecord(self::EMERGENCY, $message, $context);
	}

	public static function getLevelName($level) {
		if (!isset(self::$levels[$level])) {
			throw new FactoryExceptionCustomException('El nivel de loggin solicitado no existe');
		}
		return self::$levels[$level];
	}
}

?>