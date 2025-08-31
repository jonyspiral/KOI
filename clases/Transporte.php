<?php

/**
 * @property Localidad	$direccionLocalidad
 * @property Pais		$direccionPais
 * @property Provincia	$direccionProvincia
 */

class Transporte extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$cuit;
	public		$direccionCalle;
	public		$direccionCodigoPostal;
	public		$direccionDepartamento;
	public		$idDireccionLocalidad;
	protected	$_direccionLocalidad;
	public		$direccionNumero;
	public		$idDireccionPais;
	protected	$_direccionPais;
	public		$direccionPartidoDepartamento;
	public		$direccionPiso;
	public		$idDireccionProvincia;
	protected	$_direccionProvincia;
	public		$horario;
	public		$email;
	public		$nombre;
	public		$telefono;

	public function __construct() {
		parent::__construct();
	}

	public function armarDireccion() {
		return $this->direccionCalle . ' ' . $this->direccionNumero . ' ' . $this->direccionPiso . ' ' . $this->direccionDepartamento . ' - ' . $this->direccionLocalidad->nombre . ' - ' . $this->direccionProvincia->nombre;
	}

	//GETS y SETS
	protected function getDireccionPais() {
		if (!isset($this->_direccionPais)){
			$this->_direccionPais = Factory::getInstance()->getPais($this->idDireccionPais);
		}
		return $this->_direccionPais;
	}
	protected function setDireccionPais($pais) {
		$this->_direccionPais = $pais;
		return $this;
	}
	protected function getDireccionLocalidad() {
		if (!isset($this->_direccionLocalidad)){
			$this->_direccionLocalidad = Factory::getInstance()->getLocalidad($this->idDireccionPais, $this->idDireccionProvincia, $this->idDireccionLocalidad);
		}
		return $this->_direccionLocalidad;
	}
	protected function setDireccionLocalidad($localidad) {
		$this->_direccionLocalidad = $localidad;
		return $this;
	}
	protected function getDireccionProvincia() {
		if (!isset($this->_direccionProvincia)){
			$this->_direccionProvincia = Factory::getInstance()->getProvincia($this->idDireccionPais, $this->idDireccionProvincia);
		}
		return $this->_direccionProvincia;
	}
	protected function setDireccionProvincia($provincia) {
		$this->_direccionProvincia = $provincia;
		return $this;
	}
}

?>