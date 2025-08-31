<?php

/**
 * @property Ecommerce_Order			$order
 * @property Ecommerce_PaymentMethod	$method
 * @property Usuario					$usuario
 * @property Usuario					$usuarioBaja
 * @property Usuario					$usuarioUltimaMod
 */
class Ecommerce_Payment extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idEcommerce;
	public		$idOrder;
	protected	$_order;
	public		$idMethod;
	protected	$_method;
	public		$instrumentId;
	public		$amount;
	public		$authId;
	public		$info;
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

	//GETS y SETS
	protected function getMethod() {
		if (!isset($this->_method)){
			$this->_method = Factory::getInstance()->getEcommerce_PaymentMethod($this->idMethod);
		}
		return $this->_method;
	}
	protected function setMethod($method) {
		$this->_method = $method;
		return $this;
	}
	protected function getOrder() {
		if (!isset($this->_order)){
			$this->_order = Factory::getInstance()->getEcommerce_Order($this->idOrder);
		}
		return $this->_order;
	}
	protected function setOrder($order) {
		$this->_order = $order;
		return $this;
	}
}

?>