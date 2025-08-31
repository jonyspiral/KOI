<?php

/**
 * @property Localidad			$direccionLocalidad
 * @property Pais				$direccionPais
 * @property Provincia			$direccionProvincia
 * @property FajaHoraria		$fajaHoraria
 * @property SeccionProduccion	$seccionProduccion
 * @property string				$nombreApellido
 * @property array				$fichajes
 */

class Personal extends Base {
	const		_primaryKey = '["idPersonal"]';

	public		$idPersonal;
	public		$anulado;
	public		$apellido;
	public		$celular;
	public		$cuil;
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
	public		$dni;
	public		$email;
	public		$idFajaHoraria;
	protected	$_fajaHoraria;
	public		$fechaAntiguedadGremio;
	public		$fechaEgreso;
	public		$fechaIngreso;
	public		$fechaNacimiento;
	public		$foto;
	public		$legajo;
	public		$modalidadRetribucion;
	public		$nombre;
	protected	$_nombreApellido;
	//public		$objetivo1;
	//public		$objetivo2;
	//public		$objetivo3;
	//public		$obraSocial;
	//public		$premio1;
	//public		$premio2;
	//public		$premio3;
	protected	$_fichajes;
	public		$idSeccionProduccion;
	protected	$_seccionProduccion;
	//public		$situacion;
	public		$telefono;
	public		$valorHora;
	//public		$valorHora1;
	//public		$valorHoraMerienda;
	public		$valorMes;
	//public		$valorMes1;
	//public		$valorPares;
	public		$valorQuincena;
	public		$ficha;

	public function ficha() {
		return $this->ficha == 'S';
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
	protected function getFajaHoraria() {
		if (!isset($this->_fajaHoraria)){
			$this->_fajaHoraria = Factory::getInstance()->getFajaHoraria($this->idFajaHoraria);
		}
		return $this->_fajaHoraria;
	}
	protected function setFajaHoraria($fajaHoraria) {
		$this->_fajaHoraria = $fajaHoraria;
		return $this;
	}
	protected function getFichajes() {
		if (!isset($this->_fichajes)){
			$this->_fichajes = Factory::getInstance()->getListObject('Fichaje', 'legajo_nro = ' . Datos::objectToDB($this->legajo) . ' AND movimiento_tipo = ' . Datos::objectToDB('ENT'));
		}
		return $this->_fichajes;
	}
	protected function setFichajes($fichajes) {
		$this->_fichajes = $fichajes;
		return $this;
	}
	protected function getNombreApellido() {
		if (!isset($this->_nombreApellido)){
			$this->_nombreApellido = $this->nombre . ' ' . $this->apellido;
		}
		return $this->_nombreApellido;
	}
	protected function setNombreApellido($nombreApellido) {
		$this->_nombreApellido = $nombreApellido;
		return $this;
	}
	protected function getSeccionProduccion() {
		if (!isset($this->_seccionProduccion)){
			$this->_seccionProduccion = Factory::getInstance()->getSeccionProduccion($this->idSeccionProduccion);
		}
		return $this->_seccionProduccion;
	}
	protected function setSeccionProduccion($seccionProduccion) {
		$this->_seccionProduccion = $seccionProduccion;
		return $this;
	}
}

?>