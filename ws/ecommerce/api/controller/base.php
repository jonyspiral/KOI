<?php

abstract class Controller_Base extends Ecommerce_Core_Controller_Rest {
	protected $error_code = 0;
	protected $response_description = 'Successful';
	protected $model_name;
	protected $permissions;
	protected $fields = array();
	protected $fieldsToExlude = array(
		'get'  => array('fecha_alta', 'fecha_ultima_mod', 'fecha_baja'),
		'create'  => array(),
		'update' => array()
	);

	//M?todos de un restful controller (Controller_Rest)
	/**
	 * @param    Model_Base|int|null $id
	 *
	 * @return    object
	 */
	public function get_get($id = null) {
		$this->handle_action('get', $id);
	}

	public function get_autocomplete($modo = null) {
		$this->handle_action('autocomplete', $modo);
	}

	public function post_create() {
		$this->handle_action('create');
	}

	public function post_update($id) {
		$this->handle_action('update', $id);
	}

	public function post_delete($id) {
		$this->handle_action('delete', $id);
	}

	/* DEJO ESTO AS? DESPU?S USO LAS EXCEPTIONS */
	protected function handle_action($action, $id = null) {
		try {
			try {

				UsuarioLogin::login('ecommerce', 'fed2f98583ef9a943adeb8f8ccb9cc63d45a58da');
				/*
				if (is_string($this->permissions[$action])) {
					//Warden::authorize($action, $this->permissions[$action]);
				}
				if (!$this->permissions[$action]) { //Si es false
					//throw new AccessDenied('Permiso denegado a "' . $action . '"');
				}
				*/
				return $this->response(array('response' => array('error' => $this->error_code, 'desc' => $this->response_description, 'object' => Ecommerce_Core_Format::forge()->to_array($this->$action($id)))));
				/*
				} catch (AccessDenied $ex) {
					return $this->response(array('messages' => array(array('code' => self::MESSAGE_ERROR, 'message' => 'Acceso denegado'))), Warden::logged_in() ? 403 : 401);
				} catch (RecordNotFound $ex) {
					return $this->response(array('messages' => array(array('code' => self::MESSAGE_ERROR, 'message' => 'Registro no encontrado'))), 404);
				*/
			} catch (Exception $ex) {
				new Model_Exception_Handler($ex);
			}
		} catch (Model_Exception_AppException $ex) {
			return $this->response(array('response' => array('error' => $ex->getCode(), 'desc' => $ex->getMessage(), 'object' => null)), 500);
		} catch (Exception $ex) {
			return $this->response(array('response' => array('error' => Model_Exception_Handler::ERRORES_VARIOS, 'desc' => 'Ocurrió un error inesperado al intentar realizar la acción','object' => null)), 500);
		}
	}

	//Protecteds and defaults
	/**
	 * @param int|Model_Base $id
	 *
	 * @throws Model_Exception_RecordNotFound
	 *
	 * @return Model_Base|array
	 */
	protected function get($id) {
		/** @var $data        Model_Base */
		/** @var $da        Model_Base */
		/** @var $attrs        array */
		if (is_object($id)) {
			$data = $id;
		} else {
			if (!is_null($id)) {
				$data = Model_Base::forge('Model_Order', $id);
			} else {
				$query = Ecommerce_Core_Input::get('query');
				$query && $query = json_decode(stripcslashes($query), true);
				$data = Model_Base::forge('Model_Order', 'all', $this->build_where($query));
			}
		}
		if (is_null($data)) {
			throw new Model_Exception_RecordNotFound('El registro no existe', Model_Exception_Handler::REGISTRO_NO_EXISTE);
		}
		$this->before_get($data);
		$isArray = is_array($data);
		$isArray || $data = array($data);
		$arr = array();
		$item = array();
		foreach ($data as $da) {
			$attrs = isset($this->fields['get']) ? $this->fields['get'] : $da;
			foreach ($attrs as $attr => $val) {
				if (!is_string($attr)) {
					$attr = $val;
					$val = null;
				}
				if (!in_array($attr, $this->fieldsToExlude['get'])) {
					$uselessVar = $da->$attr;
					if (is_null($uselessVar) && (is_array($attrs))) { //Si est? en null y tengo fijado un valor por defecto ($val) en el array de $fields, seteo el default ($val)
						$item[$attr] = $val;
					} else {
						$item[$attr] = $uselessVar;
					}
				}
			}
			$this->after_get($da);
			$arr[] = $item;
		}
		$data = $isArray ? $arr : $arr[0];
		return $data;
	}

	/**
	 * @return bool|mixed
	 */
	protected function create() {
		/** @var $data Model_Base */
		/** @var $attrs array */
		$data = new $this->model_name;
		//$data = Model_Base::forge($this->model_name);
		$values = Ecommerce_Core_Input::all();
		$values = $values['request']['object'];
		$attrs = $this->get_attrs('create');
		foreach ($attrs as $attr => $val) {
			if (in_array($attr, $this->fieldsToExlude['create'])) {
				unset($attrs[$attr]);
			} else {
				$attrs[$attr] = isset($values[$attr]) && !is_null($values[$attr]) ? $values[$attr] : $attrs[$attr];
			}
		}
		$data->set($attrs);
		$this->before_create($data);
		$data->save();
		$this->after_create($data, $result);
		return $data->object;
	}

	/**
	 * @param $id
	 *
	 * @throws Model_Exception_RecordNotFound
	 * @return bool|mixed
	 */
	protected function update($id) {
		return $result;
	}

	/**
	 * @param $id
	 *
	 * @throws Model_Exception_RecordNotFound
	 * @return bool|mixed
	 */
	protected function delete($id) {
		return $result;
	}

	/**
	 * @param Model_Base &$data
	 *
	 * @return bool
	 */
	protected function before_get(&$data) {
		return true;
	}

	/**
	 * @param Model_Base|array &$data
	 *
	 * @return Model_Base
	 */
	protected function after_get(&$data) {
		return $data;
	}

	/**
	 * @param Model_Base &$data
	 *
	 * @return bool
	 */
	protected function before_create(&$data) {
		return true;
	}

	/**
	 * @param Model_Base &$data
	 * @param mixed      $result
	 *
	 * @return mixed
	 */
	protected function after_create(&$data, $result) {
		return $data;
	}

	/**
	 * @param Model_Base &$data
	 *
	 * @return bool
	 */
	protected function before_update(&$data) {
		return true;
	}

	/**
	 * @param Model_Base &$data
	 * @param mixed      $result
	 *
	 * @return mixed
	 */
	protected function after_update(&$data, $result) {
		return $data;
	}

	/**
	 * @param Model_Base &$data
	 *
	 * @return bool
	 */
	protected function before_delete(&$data) {
		return true;
	}

	/**
	 * @param Model_Base &$data
	 * @param mixed      $result
	 *
	 * @return mixed
	 */
	protected function after_delete(&$data, $result) {
		return $data;
	}

	/**
	 * Método encargado de armar el WHERE según un array
	 *
	 * @param $query
	 *
	 * @return string
	 */
	protected function build_where($query) {
		$where = '';
		if ($query) {
			foreach ($query as $field => $val) {
				$where .= ' AND ' . $field . ' = ' . $this->objectToDB($val);
			}
		}
		return trim($where, ' AND ');
	}

	/**
	 * Dado un objeto/array y una posici?n/attributo, devuelve su valor si es que est? seteado [y no es empty], o el valor por default si no lo est?
	 *
	 * @param               $obj
	 * @param               $attr
	 * @param bool          $not_empty
	 * @param mixed|null    $default
	 *
	 * @return mixed|null
	 */
	protected static function is_set($obj, $attr, $not_empty = false, $default = null) {
		return (isset($obj[$attr]) && ((!$not_empty) || ($not_empty && !empty($obj[$attr]))) ? $obj[$attr] : $default);
	}

	//Privates
	private function get_attrs($mode) {
		$attrs = isset($this->fields[$mode]) ? $this->fields[$mode] : Ecommerce_Core_Input::all();
		foreach ($attrs as $attr => $val) {
			if (!is_string($attr)) {
				$attrs[$val] = null;
				unset($attrs[$attr]);
			}
		}
		return $attrs;
	}

	private function objectToDB($obj) {
		if (is_null($obj)) {
			return 'NULL';
		} elseif (is_object($obj)) {
			return get_class($obj);
		} elseif (is_string($obj)) {
			return "'" . str_replace("'", "''", $obj) . "'";
		} elseif (is_array($obj)) {
			return 'array';
		} elseif (is_bool($obj)) {
			return ($obj ? '1' : '0');
		} elseif (is_int($obj) || is_float($obj) || is_double($obj)) {
			return $obj;
		} else {
			return $obj;
		}
	}
}

?>