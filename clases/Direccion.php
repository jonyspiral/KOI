<?php

/**
 * @property Localidad	$localidad
 * @property Provincia	$provincia
 * @property Pais		$pais
 */

class Direccion extends BasePhp {
	public 		$calle;
	public		$numero;
	public		$departamento;
	public		$piso;
	public		$codigoPostal;
	public		$idLocalidad;
	protected	$_localidad;
	public		$idProvincia;
	protected	$_provincia;
	public		$idPais;
	protected	$_pais;

	public function fill($config = array()) {
		$this->calle = $config['direccion_calle'];
		$this->numero = $config['direccion_numero'];
		$this->piso = $config['direccion_piso'];
		$this->departamento = $config['direccion_departamento'];
		$this->codigoPostal = $config['direccion_codigo_postal'];
		$this->idLocalidad = $config['direccion_cod_localidad'];
		$this->idProvincia = $config['direccion_cod_provincia'];
		$this->idPais = $config['direccion_cod_pais'];
	}

	public function getObjectVars(){
		$array = array();
		foreach($this as $key => $val){
			if (substr($key, 0, 1) == '_') {
				//Esta función se usa para listar las variables y pasarlas en el ECHOJSON que está en HTML.
				//Si el atributo empieza con _ es porque es un valor de LazyLoading, y si
				//es NULL es porque todavía no fue seteado, entonces no lo devuelvo como valor.
				//Para que un valor de LazyLoading pase a JSON hay que pedirlo antes (Ej: $notaDePedido->detalle)
				$key = substr($key, 1);
			}
			$array[] = $key;
		}
		return $array;
	}

	//GETS y SETS
	protected function getLocalidad() {
		if (!isset($this->_localidad)){
			$this->_localidad = Factory::getInstance()->getLocalidad($this->idPais, $this->idProvincia, $this->idLocalidad);
		}
		return $this->_localidad;
	}
	protected function setLocalidad($localidad) {
		$this->_localidad = $localidad;
		return $this;
	}
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
}