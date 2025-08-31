<?php

/**
 * @property AsientoContableModeloFila[]	$detalle
 * @property Usuario						$usuario
 * @property Usuario						$usuarioBaja
 * @property Usuario						$usuarioUltimaMod
 */

class AsientoContableModelo extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
	protected	$_detalle;
	protected	$_detalleJson;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function borrar() {
		foreach ($this->detalle as $d) {
			Factory::getInstance()->marcarParaBorrar($d);
		}

		return parent::borrar();
	}

	//GETS y SETS
	protected function getDetalle() {
		if (!isset($this->_detalle) && isset($this->id)){
			$this->_detalle = Factory::getInstance()->getListObject('AsientoContableModeloFila', 'cod_asiento_modelo = ' . Datos::objectToDB($this->id) . ' AND anulado = ' . Datos::objectToDB('N'));
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getDetalleJson() {
		if (!isset($this->_detalleJson)){
			$this->_detalleJson = array();
			foreach ($this->detalle as $f) {
				/** @var FilaAsientoContable $f */
				$fila = array();
				$fila['numeroFila'] = $f->numeroFila;
				$fila['imputacion'] = $f->imputacion;
				$fila['observaciones'] = $f->observaciones;
				$this->_detalleJson[] = $fila;
			}
		}
		return $this->_detalleJson;
	}
	protected function setDetalleJson($detalleJson) {
		$this->_detalleJson = $detalleJson;
		return $this;
	}
}

?>