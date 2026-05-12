<?php

class ManejadorFtp {
	private static $instancia;

	private $connectionId;
	private $loginOk = false;
	private $messageArray = array();
	private $asciiArray = array('txt', 'csv');
	private $localTmpFolder;
	private $ftpActualFolder;
	private $intentosDeConexion = 5;

	public static function getInstance() {
		if (!self::$instancia instanceof self) {
			self::$instancia = new self;
		}

		return self::$instancia;
	}

	public function __construct() {
		$this->localTmpFolder = Config::pathBase . 'tmp/ftp/';
		$this->ftpActualFolder = '/';
	}

	public function __deconstruct() {
		if ($this->connectionId) {
			ftp_close($this->connectionId);
		}
	}

	private function logMessage($message) {
		$this->messageArray[] = $message;
	}

	public function getMessages() {
		return $this->messageArray;
	}

	public function connect($server, $ftpUser, $ftpPassword, $isPassive = false) {
		$intentosDeConexion = $this->intentosDeConexion;

		while ($intentosDeConexion != 0) {
			// Levanto conexión básica
			$this->connectionId = ftp_connect($server);

			// Login with user and password
			$loginResult = ftp_login($this->connectionId, $ftpUser, $ftpPassword);

			// Setear pasive mode
			ftp_pasv($this->connectionId, $isPassive);

			// Verificar conexión
			if ((!$this->connectionId) || (!$loginResult)) {
				$this->logMessage('La conexión al FTP falló');
				$this->logMessage('Intento de conexión al FTP ' . $server . ' para el usuario ' . $ftpUser);
			} else {
				$this->logMessage('Se conectó al ' . $server . ' con el usuario ' . $ftpUser);
				$this->loginOk = true;
				return true;
			}
		}

		return false;
	}

	public function changeDir($dirString) {
		$dirString = ltrim($dirString, '/');
		$dirs = explode('/', $dirString);

		ftp_chdir($this->connectionId, '/');
		$this->ftpActualFolder = '/';
		foreach ($dirs as $dir) {
			if (ftp_chdir($this->connectionId, $dir)) {
				$this->logMessage('El directorio actual es: ' . ftp_pwd($this->connectionId));
				$this->ftpActualFolder .= $dir . '/';
			} else {
				$this->logMessage('No se pudo cambiar al directorio');
				return false;
			}
		}

		return true;
	}

	public function uploadFile($fileFrom, $fileTo, $deleteFile = false) {
		// Determinar el modo de transferencia
		$extension = end(explode('.', $fileFrom));
		if (in_array($extension, $this->asciiArray)) {
			$mode = FTP_ASCII;
		} else {
			$mode = FTP_BINARY;
		}

		// Subir archivo
		$upload = ftp_put($this->connectionId, $fileTo, $this->localTmpFolder . $fileFrom, $mode);

		// Verificar el estado del upload
		if (!$upload) {
			$this->logMessage('Falló la subida del archivo');
			$flag = false;
		} else {
			$this->logMessage('Se subió el archivo "' . $this->localTmpFolder . $fileFrom . '" a "' . $this->ftpActualFolder . $fileTo . '"');
			$flag = true;
		}

		if ($deleteFile) {
			$this->deleteTemp($this->localTmpFolder . $fileFrom);
		}

		return $flag;
	}

	public function downloadFile($fileFrom, $fileTo) {
		// Setear modo de transferencia
		$asciiArray = array('txt', 'csv');
		$extension = end(explode('.', $fileFrom));
		if (in_array($extension, $asciiArray)) {
			$mode = FTP_ASCII;
		} else {
			$mode = FTP_BINARY;
		}

		// Intentar descargar $remote_file y guardarlo en $handle
		if (ftp_get($this->connectionId, $this->localTmpFolder . $fileTo, $fileFrom, $mode, 0)) {
			$this->logMessage('El archivo "' . $fileTo . '" se descargó correctamente');
			return true;
		} else {
			$this->logMessage('Hubo un error al intentar descargar el archivo "' . $fileFrom . '" a "' . $this->localTmpFolder . $fileTo . '"');
			return false;
		}
	}

	public function delete($fileName) {
		if (ftp_delete($this->connectionId, $fileName)) {
			$this->logMessage('El archivo "' . $fileName . '" se borró correctamente');
			return true;
		} else {
			$this->logMessage('Hubo un error al intentar borrar el archivo "' . $fileName . '"');
			return false;
		}
	}

	public function fileExists($fileName) {
		if (ftp_size($this->connectionId, $fileName) == -1) {
			$this->logMessage('El archivo "' . $fileName . '" no existe en la ruta "' . $this->ftpActualFolder . '"');
			return false;
		} else {
			$this->logMessage('El archivo "' . $fileName . '" existe en la ruta "' . $this->ftpActualFolder . '"');
			return true;
		}
	}

	private function deleteTemp($fileName) {
		if (file_exists($fileName)) {
			unlink($fileName);
		}
	}
}

?>