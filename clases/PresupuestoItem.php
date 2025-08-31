<?php

/**
 * @property Presupuesto			$presupuesto
 * @property Material				$material
 * @property ColorMateriaPrima		$colorMateriaPrima
 * @property Array					$cantidades;
 * @property Array					$precios;
 * @property Array					$cantidadesPendientes;
 */

class PresupuestoItem extends Base {
	const		_primaryKey = '["idPresupuesto","nroItem"]';

	public		$idPresupuesto;
	protected	$_presupuesto;
	public		$numeroDeItem;
	public		$idMaterial;
	protected	$_material;
	public		$idColorMaterial;
	protected	$_colorMateriaPrima;
	public		$fechaEntrega;
	public		$cantidad;
	public		$cantidades;
	public		$saciado;
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

	public function __construct(){
		$this->cantidades = array();
		parent::__construct();
	}

	public function saciar(){
		$this->saciado = 'S';
	}

	public function saciado(){
		return $this->saciado == 'S';
	}

	//GETS y SETS
	protected function getColorMateriaPrima() {
		if (!isset($this->_colorMateriaPrima)){
			$this->_colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($this->material->id, $this->idColorMaterial);
		}
		return $this->_colorMateriaPrima;
	}
	protected function setColorMateriaPrima($colorMateriaPrima) {
		$this->_colorMateriaPrima = $colorMateriaPrima;
		return $this;
	}
	protected function getMaterial() {
		if (!isset($this->_material)){
			$this->_material = Factory::getInstance()->getMaterial($this->idMaterial);
		}
		return $this->_material;
	}
	protected function setMaterial($material) {
		$this->_material = $material;
		return $this;
	}
	protected function getPresupuesto() {
		if (!isset($this->_presupuesto)){
			$this->_presupuesto = Factory::getInstance()->getPresupuesto($this->idPresupuesto);
		}
		return $this->_presupuesto;
	}
	protected function setPresupuesto($presupuesto) {
		$this->_presupuesto = $presupuesto;
		return $this;
	}
}

?>