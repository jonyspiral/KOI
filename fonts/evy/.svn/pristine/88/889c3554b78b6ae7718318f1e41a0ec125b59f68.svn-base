<?php

class Sendmodels_Error_Handler {
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

class Controller_Sendmodels extends Ecommerce_Core_Controller_Rest {
	protected $error_code = 0;
	//private	$base_url = 'http://www.aster-consulting.com/prestashop/_api/models/';
	private	$base_url = 'http://www.spiralshoes.com/eshop/_api/models/';
	private $session_id = null;
	private $logs = array();
	private	$requests_types = array();

	const	LOG_TIPO_SUCCESS = 'SUCCESS';
	const	LOG_TIPO_INFO = 'INFO';
	const	LOG_TIPO_WARNING = 'WARNING';
	const	LOG_TIPO_ERROR = 'ERROR';

	public function get_index() {
		try {
			try {
				$this->login();

				$this->script();

				$this->log_results();

				return $this->response(array('response' => array('error' => $this->error_code, 'desc' => 'El proceso concluyo. Para mayor informacion ver el archivo de log')));

			} catch (Exception $ex) {
				new Model_Exception_Handler($ex);
			}

		} catch (Model_Exception_AppException $ex) {
			return $this->response(array('response' => array('error' => $ex->getCode(), 'desc' => $ex->getMessage(), 'object' => null)), 500);
		} catch (Exception $ex) {
			return $this->response(array('response' => array('error' => Model_Exception_Handler::ERRORES_VARIOS, 'desc' => 'Ocurrió un error inesperado al intentar realizar la acción','object' => null)), 500);
		}
	}

	private function script() {
		$this->requests_types = array(
			'create' => array(
				'where' => '(ecommerce_existe = ' . Datos::objectToDB('S') . ') AND (ecommerce_fecha_ultima_sinc IS NULL)',
				'handle_response' => 'handle_create_response',
				'method' => 'create',
				'data_id_attr' => false,
				'models' => array()
			),
			'update' => array(
				'where' => '(ecommerce_existe = ' . Datos::objectToDB('S') . ') AND (ecommerce_fecha_ultima_sinc IS NOT NULL) AND (fecha_ultima_mod > ecommerce_fecha_ultima_sinc)',
				'handle_response' => 'handle_update_response',
				'method' => 'update',
				'data_id_attr' => 'reference',
				'models' => array()
			),
			'delete' => array(
				'where' => '(ecommerce_existe = ' . Datos::objectToDB('N') . ') AND (ecommerce_fecha_ultima_sinc IS NOT NULL) AND (fecha_ultima_mod > ecommerce_fecha_ultima_sinc)',
				'handle_response' => 'handle_delete_response',
				'method' => 'delete',
				'data_id_attr' => 'reference',
				'models' => array()
			)
		);

		foreach ($this->requests_types as $rk => $rt) {
			try {
				$colores = Factory::getInstance()->getArrayFromView('ecommerce_colores_por_articulo_v', $rt['where']);
				$this->requests_types[$rk]['models'] = $this->forge_models_array($colores);
			} catch (Exception $ex) {
				$this->log(self::LOG_TIPO_ERROR, 'script', 'Array de models', 'Ocurrió un error al intentar obtener/generar un array de models para el método ' . $rk . ' (' . $ex->getMessage() . ')', $rt['where']);
			}
		}

		foreach ($this->requests_types as $rk => $rt) {
			foreach ($rt['models'] as $model) {
				$this->send_model($model, $rk);
			}

			$this->log(self::LOG_TIPO_INFO, 'script', 'Enviar requests', 'Se enviaron ' . count($rt['models']) . ' registros para el método ' . $rt['method'], $cxa);
		}

		$this->log(self::LOG_TIPO_INFO, 'script', 'Finalizó el proceso', 'El proceso concluyó');
	}

	private function send_model($model, $rk) {
		$rt = $this->requests_types[$rk];
		$request = $this->forge_request($model, $rt['method'], $rt['data_id_attr']);
		$response = $this->send_request($request);
		$handler = $rt['handle_response'];

		//Proceso la respuesta y hago lo que corresponda en cada caso
		$this->$handler($model, $response);
	}

	private function handle_create_response($model, $response) {
		try {
			if (!Sendmodels_Error_Handler::isValidResponse($response)) {
				throw new Exception('no se puede comprender el mensaje de respuesta del servidor');
			}
			if (!Sendmodels_Error_Handler::isSuccess($response)) {
				if (Sendmodels_Error_Handler::getErrorCode($response) == Sendmodels_Error_Handler::MODEL_EXISTS_ONCREATE) {
					$this->send_model($model, 'update');
					throw new Ecommerce_Core_EcommerceException('el artículo ' . $model['reference'] . ' ya existía en el Ecommerce, y fue pasado como update');
				} else {
					throw new Exception($response->error);
				}
			}
		} catch (Ecommerce_Core_EcommerceException $ex) {
			$this->log(self::LOG_TIPO_INFO, 'handle_create_response', 'Info al enviar request', 'Ocurrió un evento al intentar enviar un request: ' . $ex->getMessage());
			return;
		} catch (Exception $ex) {
			$this->log(self::LOG_TIPO_ERROR, 'handle_create_response', 'Error al enviar request', 'Ocurrió un error al intentar enviar un request: ' . $ex->getMessage());
			return;
		}

		try {
			$cxa = Factory::getInstance()->getColorPorArticulo($model['productid'], $model['colorid']);

			$cxa->ecommerceFechaUltimaSinc = Funciones::getDate('d/m/Y H:i:s', time() + 5);

			$cxa->guardar();
			$this->log(self::LOG_TIPO_SUCCESS, 'handle_create_response', 'Actualizar última sincronización', 'Se actualizó correctamente la fecha de última sincronización del artículo ' . $model['reference'], $cxa);
		} catch (Exception $ex) {
			$this->log(self::LOG_TIPO_ERROR, 'handle_create_response', 'Actualizar última sincronización', 'Ocurrió un error al intentar actualizar la fecha de última sincronización del artículo ' . $model['reference'] . ' (' . $ex->getMessage() . ')', $cxa);
		}
	}

	private function handle_update_response($model, $response) {
		try {
			if (!Sendmodels_Error_Handler::isValidResponse($response)) {
				throw new Exception('no se puede comprender el mensaje de respuesta del servidor');
			}
			if (!Sendmodels_Error_Handler::isSuccess($response)) {
				if (Sendmodels_Error_Handler::getErrorCode($response) == Sendmodels_Error_Handler::MODEL_NOT_EXISTS_ONUPDATE) {
					$this->send_model($model, 'create');
					throw new Ecommerce_Core_EcommerceException('el artículo ' . $model['reference'] . ' no existía en el Ecommerce, y fue pasado como create');
				} else {
					throw new Exception($response->error);
				}
			}
		} catch (Ecommerce_Core_EcommerceException $ex) {
			$this->log(self::LOG_TIPO_INFO, 'handle_update_response', 'Info al enviar request', 'Ocurrió un evento al intentar enviar un request: ' . $ex->getMessage());
			return;
		} catch (Exception $ex) {
			$this->log(self::LOG_TIPO_ERROR, 'handle_update_response', 'Error al enviar request', 'Ocurrió un error al intentar enviar un request: ' . $ex->getMessage());
			return;
		}

		try {
			$cxa = Factory::getInstance()->getColorPorArticulo($model['productid'], $model['colorid']);
			$cxa->ecommerceFechaUltimaSinc = Funciones::getDate('d/m/Y H:i:s', time() + 5);

			$cxa->guardar();
            $this->log(self::LOG_TIPO_SUCCESS, 'handle_update_response', 'Actualizar última sincronización', 'Se actualizó correctamente la fecha de última sincronización del artículo ' . $model['reference'], $cxa);
		} catch (Exception $ex) {
			$this->log(self::LOG_TIPO_ERROR, 'handle_update_response', 'Actualizar última sincronización', 'Ocurrió un error al intentar actualizar la fecha de última sincronización del artículo ' . $model['reference'] . ' (' . $ex->getMessage() . ')', $cxa);
		}
	}

	private function handle_delete_response($model, $response) {
		try {
			if (!Sendmodels_Error_Handler::isValidResponse($response)) {
				throw new Exception('no se puede comprender el mensaje de respuesta del servidor');
			}
			if (!Sendmodels_Error_Handler::isSuccess($response) && Sendmodels_Error_Handler::getErrorCode($response) != Sendmodels_Error_Handler::MODEL_NOT_EXISTS_ONDELETE) {
                throw new Exception($response->error);
			}
		} catch (Exception $ex) {
			$this->log(self::LOG_TIPO_ERROR, 'handle_delete_response', 'Error al enviar request', 'Ocurrió un error al intentar enviar un request: ' . $ex->getMessage());
			return;
		}

		try {
			$cxa = Factory::getInstance()->getColorPorArticulo($model['productid'], $model['colorid']);
			$cxa->ecommerceFechaUltimaSinc = NULL;

			$cxa->guardar();
            $this->log(self::LOG_TIPO_SUCCESS, 'handle_delete_response', 'Actualizar última sincronización', 'Se actualizó correctamente la fecha de última sincronización del artículo ' . $model['reference'], $cxa);
		} catch (Exception $ex) {
			$this->log(self::LOG_TIPO_ERROR, 'handle_delete_response', 'Actualizar última sincronización', 'Ocurrió un error al intentar actualizar la fecha de última sincronización del artículo ' . $model['reference'] . ' (' . $ex->getMessage() . ')', $cxa);
		}
	}

	private function forge_models_array($colores) {
		$requests = array();
		foreach ($colores as $cxa) {
			$req = array();
			$req['productid'] = $cxa['cod_articulo'];
			$req['colorid'] = $cxa['cod_color_articulo'];
			$req['id'] = $req['productid'] . $req['colorid']; //Formado por los dos anteriores
			$req['reference'] = $req['id']; //Igual que el anterior
			$req['category'] = $cxa['categoria_usuario'];
			$req['family'] = substr($cxa['nombre'], 0, strpos($cxa['nombre'], ' '));
			$req['name'] = $cxa['nombre'];
			$req['info'] = (empty($cxa['info']) ? '' : $cxa['info'] . '</br>') . '<p><img src="' . ManejadorDeImagenes::getRutaTablaTallesEshop(Factory::getInstance()->getColorPorArticulo($cxa['cod_articulo'], $cxa['cod_color_articulo'])) . '" alt="" width="458" height="127" /></p>';
			$req['forsale'] = $cxa['forsale'] == 'S' ? 'yes' : 'no';
			$req['condition'] = $cxa['condition'];
			$req['category'] = $cxa['cod_category'];
			$req['exclusive'] = $cxa['exclusive'] == 'S' ? 'yes' : 'no';
			$req['featured'] = $cxa['featured'] == 'S' ? 'yes' : 'no';
			$req['price1'] = $cxa['price1'];
			$req['price2'] = $cxa['price2'];
			$req['price3'] = $cxa['price3'];
			$req['images'] = $cxa['image1'];
			/*
			$req['images'] = array();
			for ($i = 1; $i <= 4; $i++) {
				$req['images'][] = array('url' => $cxa['imagen_' . $i]);
			}
			*/
			$req['stock'] = array();
			for ($i = 1; $i <= 10; $i++) {
				$req['stock'][] = array(
					'modelid' => $req['id'],
					'sizeid' => $cxa['size_id_' . $i],
					'minstock' => $cxa['min_stock_' . $i],
					'replacementstock' => $cxa['replacement_stock_' . $i],
					'maxstock' => $cxa['max_stock_' . $i],
					'currentstock' => $cxa['current_stock_' . $i]
				);
			}

			$requests[] = $req;
		}
		return $requests;
	}

	private function forge_request($model, $method, $data_id_attr) {
		return array(
			'method' => $method,
			'data_id' => ($data_id_attr ? $model[$data_id_attr] : false),
			'data' => array(
				'request' => array(
					'sessionid' => $this->session_id,
					'model' => $model
				)
			)
		);
	}

	private function send_request($request) {
		$result = null;
		$url = $this->base_url . $request['method'] . ($request['data_id'] ?  '/' . $request['data_id'] : '');
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/json\r\n",
				'method'  => 'POST',
				'content' => json_encode($request['data']),
			)
		);
		try {
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
			$result = Ecommerce_Core_Format::forge()->to_array(json_decode($result));
		} catch (Exception $ex) {
			$this->log(self::LOG_TIPO_ERROR, 'send_request', 'Enviando request', 'Ocurrió un error al enviar un request (' . $ex->getMessage() . ')', $request['data']);
		}
		return $result;
	}

	private function login() {
		UsuarioLogin::login('ecommerce', 'fed2f98583ef9a943adeb8f8ccb9cc63d45a58da');
	}

	private function log($tipo, $metodo, $nombre, $descripcion = '', $object = null) {
		$this->logs[] = array(
			'tipo' => $tipo,
			'metodo' => $metodo,
			'nombre' => $nombre,
			'descripcion' => $descripcion,
			'object' => $object
		);
	}

	private function log_results() {
		$echo = '';
		foreach ($this->logs as $log) {
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

		$fp = fopen(LOGSPATH . Funciones::hoy('Y-m-d') . '.html', 'a+');
		fwrite($fp, $echo);
		fclose($fp);

	}
}

?>