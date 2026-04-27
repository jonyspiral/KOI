<?php

/**
 * @property array	$roles
 */

class Indicador extends Base {
	public 		$__CACHE_TIME = false; // 120 default

	const		maxFields = 4;
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
	public		$descripcion;
	public		$view;
	public		$valor1; //Verde1
	public		$valor2; //Amarillo
	public		$valor3; //Rojo
	public		$where;	//Es una cláusula WHERE para mandarle a la view. Su scope es Usuario::logueado(). Puede ser NULL. (Ej: cod_cli=///contacto->cliente->id///)
	public		$fields; //Es un campo varchar en el que se ponen los campos que van a generar el indicador (pueden ser más de un número) separados por coma (Ej: pares_39, pares_40)
	public		$query;	//Puede ponerse una query en lugar de una view (no haría falta poner ni view, ni where, pero sí fields)
	protected	$_roles;
	public		$anulado;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function __construct() {
		parent::__construct();
	}

	public function addRol(Rol $rol) {
		$ixr = Factory::getInstance()->getIndicadorPorRol();
		$ixr->id = $rol->id;
		$this->getRoles(); //En caso de indicador nuevo, esto me va a traer un array vacío
		$this->_roles[] = $ixr;
	}

	public function getValores() {
		/** @noinspection PhpUnusedLocalVariableInspection */
		$valores = array();
		if (empty($this->query)) {
			$fields = $this->formarFields();
			$where = $this->formarWhere();
			$valores = Factory::getInstance()->getArrayFromView($this->view, $where, 1, $fields);
		} else {
			$valores = Factory::getInstance()->getArrayFromQuery($this->formarWhereQuery($this->query));
		}
		if (!count($valores)) {
			foreach ($fields as $field) {
				$valores[$field] = 0;
			}
		} else {
			$valores = $valores[0];
		}
		return $valores;
	}

	public function getValoresColores() {
		$valores = array('1' => array(), '2' => array(), '3' => array());
		if (isset($this->valor1)) {
			$valores[1] = explode(',', str_replace(' ', '', $this->valor1));
		}
		if (isset($this->valor2)) {
			$valores[2] = explode(',', str_replace(' ', '', $this->valor2));
		}
		if (isset($this->valor3)) {
			$valores[3] = explode(',', str_replace(' ', '', $this->valor3));
		}
		return $valores;
	}

	private function formarFields() {
		if (!empty($this->fields)) {
			$arr = explode(',', str_replace(' ', '', $this->fields));
			if (!empty($arr)) {
				return $arr;
			}
		}
		return array('computed');
	}

	private function formarWhere() {
		try {
			$detalle = '';
			$arr = explode('///', $this->where);
			if (Funciones::esImpar(count($arr))) {
				for ($i = 0; $i < count($arr); $i++) {
					if (Funciones::esPar($i)) {
						$detalle .= $arr[$i];
					} else {
						$arrAttr = explode('->', $arr[$i]);
						$attr = Usuario::logueado();
						foreach($arrAttr as $attrAct)
							$attr = $attr->$attrAct;
						$detalle .= Datos::objectToDB($attr);
					}
				}
			} else {
				$detalle = '1 = 1';
			}
			return $detalle;
		} catch (Exception $ex) {
			return '1 = 1';
		}
	}

	private function formarWhereQuery($query) {
		try {
			$arr = explode('///', $query);
			if (Funciones::esImpar(count($arr))) {
				for ($i = 0; $i < count($arr); $i++) {
					if (!Funciones::esPar($i)) {
						$arrAttr = explode('->', $arr[$i]);
						$attr = Usuario::logueado();
						foreach($arrAttr as $attrAct)
							$attr = $attr->$attrAct;
						$query = str_replace('///' . $arr[$i] . '///', Datos::objectToDB($attr), $query);
					}
				}
			}
			return $query;
		} catch (Exception $ex) {
			return $query;
		}
	}

	//GETS y SETS
	protected function getRoles() {
		if (!isset($this->_roles)){
			$this->_roles = Factory::getInstance()->getListObject('IndicadorPorRol', 'cod_indicador = ' . Datos::objectToDB($this->id));
		}
		return $this->_roles;
	}
	protected function setRoles($roles){
		$this->_roles = $roles;
		return $this;
	}
	
}

?>