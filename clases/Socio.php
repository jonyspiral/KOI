<?php

/**
 * @property Direccion		$direccion
 */

class Socio extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$cuil;
	public		$dni;
	protected	$_direccion;
	public		$email;
	public		$nombre;
	public		$telefono;
	public		$celular;
	public		$observaciones;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	//GETS y SETS
	protected function getDireccion() {
		if (!isset($this->_direccion)){
			$this->_direccion = new Direccion();
		}
		return $this->_direccion;
	}
	protected function setDireccion(Direccion $direccion) {
		$this->_direccion = $direccion;
		return $this;
	}
}

?>