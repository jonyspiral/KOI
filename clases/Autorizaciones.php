<?php

/**
 * @property AutorizacionTipo		$autorizacionTipo
 * @property array					$autorizaciones
 * @property array					$autorizacionesPendientes
 * @property array					$personasAutorizacionesPendientes
 */

class Autorizaciones extends Base {
	public		$idEspecifico;
	public		$idAutorizacionTipo;
	protected	$_autorizacionTipo;
	protected	$_autorizaciones;					//Array por definición. El índice es el número de autorización (Ej: $aut[2]->idEspecifico)
	protected	$_autorizacionesPendientes;			//Array con los números de autorizaciones pendientes
	protected	$_personasAutorizacionesPendientes;	//Array (por definición) de AutorizacionesPersonas con las pendientes. El índice es el número de autorización (Ej: $pap[3]->usuario)

	public function __construct($idAutorizacionTipo, $idEspecifico = null) {
		parent::__construct();
		$this->idAutorizacionTipo = $idAutorizacionTipo;
		if (!is_null($idEspecifico))
			$this->idEspecifico = $idEspecifico;
	}

	public function expand() {
		foreach($this->autorizaciones as $key => $val){
			$this->autorizaciones[$key]->usuario->nombre;
			$this->autorizaciones[$key]->usuario->apellido;
		}

		parent::expand();
	}

	//GETS y SETS
	protected function getAutorizaciones() {
        $array = array();
		if (!isset($this->_autorizaciones) && isset($this->getAutorizacionTipo()->id) && isset($this->idEspecifico)){
			$this->_autorizaciones = array();
			$array = Factory::getInstance()->getListObject('Autorizacion', 'cod_tipo_autorizacion = ' . Datos::objectToDB($this->getAutorizacionTipo()->id) . ' AND id_especifico = ' . Datos::objectToDB($this->idEspecifico) . ' ORDER BY numero_autorizacion ASC');
		}
		foreach ($array as $autorizacion){
			$this->_autorizaciones[$autorizacion->numero] = $autorizacion;
		}
		return $this->_autorizaciones;
	}
	protected function setAutorizaciones($autorizaciones) {
		$this->_autorizaciones = $autorizaciones;
		return $this;
	}
	protected function getAutorizacionesPendientes() {
		if (!isset($this->_autorizacionesPendientes)){
			$arrayAux = array();
			for ($i = 1; $i <= $this->getAutorizacionTipo()->cantidad; $i++)
				$arrayAux[$i] = $i;
			foreach ($this->getAutorizaciones() as $autorizacion){
				unset($arrayAux[$autorizacion->numero]);
			}
			$array = array();
			foreach($arrayAux as $item)
				$array[] = $item;
			$this->_autorizacionesPendientes = $array;
		}
		return $this->_autorizacionesPendientes;
	}
	protected function setAutorizacionesPendientes($autorizacionesPendientes) {
		$this->_autorizacionesPendientes = $autorizacionesPendientes;
		return $this;
	}
	protected function getAutorizacionTipo() {
		if (!isset($this->_autorizacionTipo)){
			$this->_autorizacionTipo = Factory::getInstance()->getAutorizacionTipo($this->idAutorizacionTipo);
		}
		return $this->_autorizacionTipo;
	}
	protected function setAutorizacionTipo($autorizacionTipo) {
		$this->_autorizacionTipo = $autorizacionTipo;
		return $this;
	}
	protected function getPersonasAutorizacionesPendientes() {
		if (!isset($this->_personasAutorizacionesPendientes) && isset($this->getAutorizacionTipo()->id)){
			$this->_personasAutorizacionesPendientes = array();
			foreach ($this->getAutorizacionesPendientes() as $pendiente){
				$this->_personasAutorizacionesPendientes[$pendiente] = Factory::getInstance()->getListObject('AutorizacionPersona', 'cod_tipo_autorizacion = ' . Datos::objectToDB($this->getAutorizacionTipo()->id) . ' AND numero_autorizacion = ' . Datos::objectToDB($pendiente));
			}
		}
		return $this->_personasAutorizacionesPendientes;
	}
	protected function setPersonasAutorizacionesPendientes($personasAutorizacionesPendientes) {
		$this->_personasAutorizacionesPendientes = $personasAutorizacionesPendientes;
		return $this;
	}
}

?>