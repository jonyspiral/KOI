<?php

abstract class KoiServices {
	const SOCKET_IP = 'localhost';		//IP en la que está escuchando el socket de KoiServices
	const SOCKET_PORT = '8000';			//Puerto en el que está escuchando el socket de KoiServices
	const EOF_MARK = '<EOF>';			//Marca que delimita el final de stream. En KoiServices se espera a la llegada de esta marca
	const READ_BUFFER_SIZE = 512;		//Tamaño del buffer de lectura (se lee cada X bytes)

	protected	$service;				//Es un string con el nombre del servicio a llamar de KoiServices. Por ej: 'HTML2PDF'
	private		$socket = false;
	private		$connected = false;

	protected function connect() {
		if ($this->socket === false) {
			$this->createSocket();
		}
		if (!$this->connected) {
			$this->connectSocket();
		}
	}

	private function createSocket() {
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($this->socket === false) {
			throw new Exception($this->getExceptionMsg('Ocurrió un error al intentar crear el socket'));
		}
		return $this->socket;
	}

	private function connectSocket() {
		$result = socket_connect($this->socket, self::SOCKET_IP, self::SOCKET_PORT);
		if ($result === false) {
			throw new Exception($this->getExceptionMsg('Ocurrió un error al intentar conectar el socket'));
		}
		return $this->connected = true;
	}

	private function closeSocket() {
		if ($this->socket !== false) {
			socket_close($this->socket);
			$this->socket = false;
		}
	}

	private function sendRequest($args) {
		$request = $this->service . ' ' . $args . self::EOF_MARK;
		return socket_write($this->socket, $request, strlen($request));
	}

	private function getResponse() {
		$response = '';
		do {
			$recv = socket_read($this->socket, '512');
			if ($recv === false) {
				return false;
			} elseif ($recv != '') {
				$response .= $recv;
			}
		} while($recv != '');
		return $response;
	}

	protected function getExceptionMsg($msg = '') {
		$errorcode = socket_last_error();
		$errormsg = socket_strerror($errorcode);
		return (!$msg ? 'Ocurrió un error en la conexión con KoiServices: ' : $msg) . '[' . $errorcode . '] ' . $errormsg;
	}

	protected function execute($args = '') {
		$error = false;
		$this->connect();
		$this->sendRequest($args);
		$response = $this->getResponse();
		if ($response === false) {
			$error = $this->getExceptionMsg('Ocurrió un error al recibir una respuesta de KoiServices');
		}
		$this->closeSocket();
		if ($error) {
			throw new Exception($error);
		}
		return $response;
	}
}

?>