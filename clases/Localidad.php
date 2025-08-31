<?php

/**
 * @property Pais		$pais
 * @property Provincia	$provincia
 * @property Zona		$zona
 */

class Localidad extends Base {
	const		_primaryKey = '["idPais", "idProvincia", "id"]';

	public		$id;
	public		$idPais;
	protected	$_pais;
	public		$idProvincia;
	protected	$_provincia;
	public		$anulado;
	public		$codigoPostal;
	public		$nombre;
	public		$idZona;
	protected	$_zona;

	public function __construct() {
		parent::__construct();
	}

	//GETS y SETS
	protected function getPais() {
		if (!isset($this->_pais)){
			$this->_pais = Factory::getInstance()->getPais($this->idPais);
		}
		return $this->_pais;
	}
	protected function setPais($pais) {
		$this->_pais = $pais;
		return $this;
	}
	protected function getProvincia() {
		if (!isset($this->_provincia)){
			$this->_provincia = Factory::getInstance()->getProvincia($this->idPais, $this->idProvincia);
		}
		return $this->_provincia;
	}
	protected function setProvincia($provincia) {
		$this->_provincia = $provincia;
		return $this;
	}
	protected function getZona() {
		if (!isset($this->_zona)){
			$this->_zona = Factory::getInstance()->getZona($this->idZona);
		}
		return $this->_zona;
	}
	protected function setZona($zona) {
		$this->_zona = $zona;
		return $this;
	}
}

?>