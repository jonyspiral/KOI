<?php

class Ecommerce_WS_Error_Handler {
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

/**
 * @method create_coupon
 */
class Ecommerce_WS extends WS {

	public function __construct() {
		parent::__construct(Config::desarrollo() ? 'eshop_prueba_2317/_api/' : 'eshop/_api/');
	}

	public function __call($name, $arguments) {
		$parts = explode('_', $name);
		if (count($parts) > 1 && count($arguments) == 1) {
			$action = $parts[0];
			if (method_exists($this, $action)) {
				$model_name = str_replace($action . '_', '', $name);
				return $this->$action($model_name, $arguments[0]);
			}
			throw new Exception('No existe el método ' . $action . ' en la clase "' . get_class($this) . '"');
		}
		throw new Exception('No se ha logrado encontrar un método válido para ' . $name . ' en la clase "' . get_class($this) . '" que admita ' . count($arguments) . ' parámetros');
	}

	protected function create($model_name, $data) {
		$response = null;
		try {
			$response = parent::post($model_name . '/create/', array('request' => $data));
		} catch (Exception $ex) {
		}
		$this->handle_response($response, $model_name . '/create/');
		return $response['response'];
	}

	private function handle_response($response, $action) {
		try {
			if (!Ecommerce_WS_Error_Handler::isValidResponse($response)) {
				throw new Exception('no se puede comprender el mensaje de respuesta del servidor');
			} elseif (!Ecommerce_WS_Error_Handler::isSuccess($response)) {
				if (Ecommerce_WS_Error_Handler::getErrorCode($response) == Ecommerce_WS_Error_Handler::MODEL_EXISTS_ONCREATE) {
					throw new WS_Exception_ModelExists('el objeto que intentó guardar ya existe en el Ecommerce');
				} elseif (Ecommerce_WS_Error_Handler::getErrorCode($response) == Ecommerce_WS_Error_Handler::MODEL_NOT_EXISTS_ONUPDATE) {
					throw new WS_Exception_ModelNotExists('el objeto que intentó guardar no existe en el Ecommerce');
				} else {
					throw new FactoryExceptionCustomException($response['response']['message']);
				}
			}
			$this->log(self::LOG_TIPO_SUCCESS, $action, 'Se envió correctamente el request');
		} catch (Exception $ex) {
			$this->log(self::LOG_TIPO_ERROR, $action, 'Error al enviar el request: ' . $ex->getMessage());
			throw $ex;
		}
	}
}

?>