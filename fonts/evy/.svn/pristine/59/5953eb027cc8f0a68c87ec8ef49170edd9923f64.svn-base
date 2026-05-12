<?php

/**
 * @property Base			$object
 * @property Base			$_object
 * @property Factory		$_factory
 */
abstract class Model_Base {
	protected $_koi_class_name;
	protected $_always_where = '';
	protected $_always_order = '';
	protected $_properties;

	protected $_object;
	protected $_factory;

	public function __construct($object = null) {
		$this->_factory = Factory::getInstance();

		if (is_null($object) && (($class = get_class($this)) != 'Model_Base')) {
			$method = 'get' . $this->_koi_class_name;
			$object = $this->_factory->$method();
		}
		if ($object instanceof Base) {
			$this->_object = $object;
		}
	}

	public function __get($property) {
		return $this->get($property);
	}
	public function get($property) {
		$method = 'get_' . $property;
		if (method_exists($this, $method)) {
			$var = $this->$method();
			return $var;
		} elseif (array_key_exists($property, $this->_properties)) {
			$objectProp = $this->_properties[$property];
			$var = $this->_object->$objectProp; // Me ahorro un notice
			return $var;
		} else {
			throw new Model_Exception_AppException('No se encontró la propiedad "' . $property . '" en la clase ' . get_called_class());
		}
	}

	public function __set($property, $value) {
		return $this->set($property, $value);
	}
	public function set($property, $value = null) {
		$method = 'set_' . $property;
		if (is_string($property) && method_exists($this, $method)) {
			return $this->$method($value);
		} elseif (is_array($property)) {
			foreach ($property as $p => $v) {
				$this->set($p, $v);
			}
		} else {
			if (func_num_args() < 2) {
				throw new Model_Exception_AppException('Error al llamar al método "set": se esperaba recibir dos parámetros');
			} elseif (array_key_exists($property, $this->_properties)) {
				$objectProp = $this->_properties[$property];
				$this->_object->$objectProp = $value;
			}
		}
		return $this;
	}

	/**
	 * @param string		$class_name
	 * @param null|int		$id
	 * @param string		$where
	 * @param string		$order
	 *
	 * @return array|null
	 */
	public static function forge($class_name, $id = null, $where = '', $order = '') {
		/** @var Model_Base $object **/
		$object = new $class_name();
		return $object->find($id, $where, $order);
	}

	public function find($id = null, $where = '', $order = '') {
		// Busco un objeto
		$class_name = get_class($this);
		$return = null;
		$where_order = trim(trim($where . ' AND ' . $this->_always_where, ' AND ') . (!empty($order) || !empty($this->_always_order) ? ' ORDER BY ' . (!empty($order) ? $order : $this->_always_order) : ''), ' ');
		if ($id === 'all') {
			// Devuelvo todos los registros que cumplen con el WHERE
			$return = array();
			$objects = $this->_factory->getListObject($this->_koi_class_name, $where_order);
			foreach ($objects as $object) {
				$return[] = new $class_name($object);
			}
		} elseif ($id === 'one' || $id === 'first' || $id === 'last') {
			// Devuelvo el primer o último registro que cumple con el WHERE
			$list = $this->_factory->getListObject($this->_koi_class_name, $where_order);
			return (!count($list) ? array() : new $class_name($id === 'last' ? $list[count($list) - 1] : $list[0]));
		} else {
			// Devuelvo un registro específico según el ID
			$return = $this->get_by_id($id);
		}
		return $return;
	}

	protected function get_by_id($id) {
		$class_name = get_class($this);
		$method = 'get' . $this->_koi_class_name;
		$return = new $class_name($this->_factory->$method($id));
		return $return;
	}

	public function save($funcionalidad = false) {
		try {
			$this->before_save();
			$this->_object->guardar()->notificar($funcionalidad);
			$this->after_save();
			return $this;
		} catch (Exception $ex) {
			throw $ex;
		}
	}

	public function delete($funcionalidad = false) {
		try {
			$this->before_delete();
			$this->_object->borrar($funcionalidad);
			$this->after_delete();
			return $this;
		} catch (Exception $ex) {
			throw $ex;
		}
	}

	//Observers
	protected function before_save() {
		return true;
	}
	protected function after_save() {
		return true;
	}
	protected function before_delete() {
		return true;
	}
	protected function after_delete() {
		return true;
	}

	//Getters & Setters
	protected function get_object() {
		return $this->_object;
	}
	protected function set_object($value) {
		$this->_object = $value;
		return $this;
	}
}

?>