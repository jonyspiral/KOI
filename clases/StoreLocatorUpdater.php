<?php

class StoreLocatorUpdater_ErrorHandler {
	const	MODEL_EXISTS_ONCREATE = 11;
	const	MODEL_NOT_EXISTS_ONUPDATE = 21;
	const	MODEL_NOT_EXISTS_ONDELETE = 31;

	public static function isValidResponse($response) {
		return (isset($response['response']) && is_array($response['response']) && isset($response['response']['error']) && isset($response['response']['message']));
	}

	public static function isSuccess($response) {
		return ($response['response']['error'] == 0);
	}

	public static function getErrorCode($response) {
		return $response['response']['error'];
	}
}

class StoreLocatorUpdater {
	const	LOG_TIPO_SUCCESS = 'SUCCESS';
	const	LOG_TIPO_INFO = 'INFO';
	const	LOG_TIPO_WARNING = 'WARNING';
	const	LOG_TIPO_ERROR = 'ERROR';

	private	static $base_url = 'http://www.spiralshoes.com/ws/storelocator/';
	private static $logs = array();
	private static $sessionkey = 'ACAUNACLAVE';

	public static function create_sucursal(Sucursal $sucursal) {
		self::send_model($sucursal, 'create');
	}

	public static function update_sucursal(Sucursal $sucursal, Sucursal $original) {
		if (
			$original->cliente->nombre != $sucursal->cliente->nombre ||
			$original->cliente->razonSocial != $sucursal->cliente->razonSocial ||
			$original->nombre != $sucursal->nombre ||
			$original->direccionCalle != $sucursal->direccionCalle ||
			$original->direccionNumero != $sucursal->direccionNumero ||
			$original->direccionLocalidad->nombre != $sucursal->direccionLocalidad->nombre ||
			$original->direccionProvincia->nombre != $sucursal->direccionProvincia->nombre ||
			$original->direccionLatitud != $sucursal->direccionLatitud ||
			$original->direccionLongitud != $sucursal->direccionLongitud
		) {
			//En el caso de las comparaciones del cliente (razón social y nombre de fantasía), lo que viene en el $original es en realidad el último (ver lo que hago en el guardar de clientes) y lo que viene
			// en $sucursal es en realidad lo original. Por eso hago la siguiente linea:
			$sucursal->cliente = $original->cliente;
			self::send_model($sucursal, 'update');
		}
	}

	public static function delete_sucursal(Sucursal $sucursal) {
		self::send_model($sucursal, 'delete');
	}

	protected static function send_model($sucursal, $method) {
		try {
			$model = self::forge_model($sucursal);
			$request = self::forge_request($model, $method);
			$response = self::send_request($request);

			if (!StoreLocatorUpdater_ErrorHandler::isValidResponse($response)) {
				throw new Exception('no se puede comprender el mensaje de respuesta del servidor');
			}
			try {
				if (!StoreLocatorUpdater_ErrorHandler::isSuccess($response)) {
					if (StoreLocatorUpdater_ErrorHandler::getErrorCode($response) == StoreLocatorUpdater_ErrorHandler::MODEL_EXISTS_ONCREATE) {
						throw new FactoryExceptionCustomException('Se intentó crear la sucursal ' . $model['NOMSUC'] . ' del cliente ' . $model['RZNSOCIAL'] . ' pero ya existía en el store locator y fue pasado como UPDATE');
					} elseif (StoreLocatorUpdater_ErrorHandler::getErrorCode($response) == StoreLocatorUpdater_ErrorHandler::MODEL_NOT_EXISTS_ONUPDATE) {
						throw new FactoryExceptionCustomException('Se intentó actualizar la sucursal ' . $model['NOMSUC'] . ' del cliente ' . $model['RZNSOCIAL'] . ' pero no existía en el store locator y fue pasado como CREATE');
					} elseif (StoreLocatorUpdater_ErrorHandler::getErrorCode($response) == StoreLocatorUpdater_ErrorHandler::MODEL_NOT_EXISTS_ONDELETE) {
						throw new FactoryExceptionCustomException('Se intentó eliminar la sucursal ' . $model['NOMSUC'] . ' del cliente ' . $model['RZNSOCIAL'] . ' pero no existía en el store locator');
					}
				}
			} catch (FactoryExceptionCustomException $ex) {
				self::log(self::LOG_TIPO_INFO, 'send_model', 'Info al enviar request', 'Ocurrió un evento al intentar enviar un request: ' . $ex->getMessage());
			}
		} catch (Exception $ex) {
			self::log(self::LOG_TIPO_ERROR, 'send_model', 'Error al enviar request', 'Ocurrió un error y el proceso fue finalizado: ' . $ex->getMessage());
		}

		self::log(self::LOG_TIPO_SUCCESS, 'send_model', 'Enviar request', 'Se envió correctamente la sucursal ' . $model['NOMSUC'] . ' del cliente ' . $model['RZNSOCIAL'] . ' con el método ' . $method, $sucursal);

		self::log_results();
	}

	private static function forge_model($sucursal) {
		return array(
			'CODCLI'				=> $sucursal->cliente->id,
			'CODSUC'				=> $sucursal->id,
			'RZNSOCIAL'				=> $sucursal->cliente->razonSocial,
			'NOMFANTA'				=> $sucursal->cliente->nombre,
			'NOMSUC'				=> $sucursal->nombre,
			'CALLE'					=> $sucursal->direccionCalle,
			'CALLENUM'				=> $sucursal->direccionNumero,
			'LOCALIDAD'				=> $sucursal->direccionLocalidad->nombre,
			'PROVINCIA'				=> $sucursal->direccionProvincia->nombre,
			'LAT'					=> $sucursal->direccionLatitud,
			'LNG'					=> $sucursal->direccionLongitud,
			'formatted_address'		=> $sucursal->direccionCalle . ' ' . $sucursal->direccionNumero . ', ' . $sucursal->direccionLocalidad->nombre . ', ' . $sucursal->direccionProvincia->nombre . ', ' . $sucursal->direccionPais->nombre,
			'estado'				=> '1'
		);
	}

	private static function forge_request($model, $method) {
		return array(
			'method' => $method,
			'data' => array(
				'request' => array(
					'sessionkey' => self::$sessionkey,
					'model' => $model
				)
			)
		);
	}

	private static function send_request($request) {
		$result = null;
		$url = self::$base_url . $request['method'] . '.php';
		$options = array(
			'http' => array(
				'header'  => 'Content-type: application/json\r\n',
				'method'  => 'POST',
				'content' => json_encode($request['data']),
			)
		);
		try {
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
			$result = json_decode($result, true);
		} catch (Exception $ex) {
			self::log(self::LOG_TIPO_ERROR, 'send_request', 'Enviando request', 'Ocurrió un error al enviar un request (' . $ex->getMessage() . ')', $request['data']);
		}
		return $result;
	}

	private static function log($tipo, $metodo, $nombre, $descripcion = '', $object = null) {
		self::$logs[] = array(
			'tipo' => $tipo,
			'metodo' => $metodo,
			'nombre' => $nombre,
			'descripcion' => $descripcion,
			'object' => $object
		);
	}

	private function log_results() {
		$echo = '';
		foreach (self::$logs as $log) {
			$echo .= '<span style="font-weight: bold; color: ' . (($log['tipo'] == self::LOG_TIPO_ERROR) ? 'red' : ($log['tipo'] == self::LOG_TIPO_SUCCESS ? 'green' : ($log['tipo'] == self::LOG_TIPO_INFO ? 'blue' : 'black'))) . '">';
			$echo .= Funciones::ahora() . ' || ';
			$echo .= $log['tipo'] . ' || ';
			$echo .= $log['metodo'] . ' || ';
			$echo .= $log['nombre'] . ' ';
			$echo .= '</span>';
			$echo .= '<br>';
			$echo .= '[' . $log['descripcion'] . ']';
			$echo .= '<br><br>';
		}

		$fp = fopen(Config::pathBase . 'tmp/logs/' . Funciones::hoy('Y-m-d') . '.html', 'a+');
		fwrite($fp, $echo);
		fclose($fp);
	}
}

?>