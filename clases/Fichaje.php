<?php

/**
 * @property Personal	$personal
 * @property Fichaje 	$fichajeDiaAnterior
 * @property Fichaje	$fichajeAnterior
 * @property Fichaje	$fichajePosterior
 */

class Fichaje extends Base {
	const		_primaryKey = '["id"]';
	const		MINUTOS_ERROR = 10;		//Son los minutos que tienen que pasar entre fichaje y fichaje

	public		$id;
	public		$legajo;
	protected	$_personal;
	public		$tipo;
	public		$fecha;
	public		$horaEntrada;
	public		$diferenciaEntrada;
	public		$horaSalida;
	public		$diferenciaSalida;
	public		$anomalias;				// S/N
	public		$lugarEntrada;
	public		$lugarSalida;
	public		$tipoCorreccion;
	public		$idMotivoAusentismo;
	protected	$_motivoAusentismo;
	protected	$_fichajeDiaAnterior;	//Es el último fichaje del último día que trabajo (antes de hoy)
	protected	$_fichajeAnterior;		//Si todavía no fichó hoy, o si fichó entrada [y salida], está en NULL
										//Si ya fichó un reingreso [y reegreso], devuelve la ENT original 
										//Si ya fichó más de un reingreso, devuelve el reingreso anterior
										//(para poder borrar los datos de diferencias de horas)
	protected	$_fichajePosterior;		//Si es un fichaje de entrada [y salida] y no tiene REI, está en NULL
										//Si es un ENT y tiene REI, lo devuelve 
										//Si ya fichó más de un reingreso, devuelve el reingreso siguiente (si existe)

	public function ausentismo() {
		return !is_null($this->idMotivoAusentismo);
	}

	public function correccion() {
		return !is_null($this->tipoCorreccion);
	}
 
	//GETS y SETS
	protected function getFichajeAnterior() {
		if (!isset($this->_fichajeAnterior)) {
			$where = 'legajo_nro = ' . Datos::objectToDB($this->legajo);
			$where .= ' AND fecha = dbo.toDate(' . Datos::objectToDB($this->fecha) . ') ';
			$where .= ' AND entrada_horario < dbo.toDate(' . Datos::objectToDB($this->fecha . ' ' . $this->horaEntrada) . ') ';
			$order = ' ORDER BY entrada_horario DESC';
			$arr = Factory::getInstance()->getListObject('Fichaje', $where . $order);
			if (count($arr) > 0)
				$this->_fichajePosterior = $arr[0];
		}
		return $this->_fichajePosterior;
	}
	protected function setFichajeAnterior($fichajeAnterior) {
		$this->_fichajeAnterior = $fichajeAnterior;
		return $this;
	}
	protected function getFichajeDiaAnterior() {
		if (!isset($this->_fichajeDiaAnterior)){
			$order = ' ORDER BY fecha DESC, entrada_horario DESC';
			$arr = Factory::getInstance()->getListObject('Fichaje', 'legajo_nro = ' . Datos::objectToDB($this->legajo) . $order, 1);
			if (count($arr) == 1)
				$this->_fichajeDiaAnterior = $arr[0];
			else
				$this->_fichajeDiaAnterior = Factory::getInstance()->getFichaje();
		}
		return $this->_fichajeDiaAnterior;
	}
	protected function setFichajeDiaAnterior($fichajeDiaAnterior) {
		$this->_fichajeDiaAnterior = $fichajeDiaAnterior;
		return $this;
	}
	protected function getFichajePosterior() {
		if (!isset($this->_fichajePosterior)) {
			$where = 'legajo_nro = ' . Datos::objectToDB($this->legajo);
			$where .= ' AND fecha = dbo.toDate(' . Datos::objectToDB($this->fecha) . ') ';
			$where .= ' AND entrada_horario > dbo.toDate(' . Datos::objectToDB($this->fecha . ' ' . $this->horaEntrada) . ') ';
			$order = ' ORDER BY entrada_horario ASC';
			$arr = Factory::getInstance()->getListObject('Fichaje', $where . $order);
			if (count($arr) > 0)
				$this->_fichajePosterior = $arr[0];
		}
		return $this->_fichajePosterior;
	}
	protected function setFichajePosterior($fichajePosterior) {
		$this->_fichajePosterior = $fichajePosterior;
		return $this;
	}
	protected function getMotivoAusentismo() {
		if (!isset($this->_motivoAusentismo)){
			$this->_motivoAusentismo = Factory::getInstance()->getMotivoAusentismo($this->idMotivoAusentismo);
		}
		return $this->_motivoAusentismo;
	}
	protected function setMotivoAusentismo($motivoAusentismo) {
		$this->_motivoAusentismo = $motivoAusentismo;
		return $this;
	}
	protected function getPersonal() {
		if (!isset($this->_personal)){
			$arr = Factory::getInstance()->getListObject('Personal', 'legajo_nro = ' . Datos::objectToDB($this->legajo));
			if (count($arr) == 1)
				$this->_personal = $arr[0];
			else
				throw new FactoryExceptionRegistroNoExistente();
		}
		return $this->_personal;
	}
	protected function setPersonal($personal) {
		$this->_personal = $personal;
		return $this;
	}
}

?>