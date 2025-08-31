<?php

/**
 * @property Ecommerce_Order		$order
 * @property Usuario				$usuario
 * @property Usuario				$usuarioBaja
 * @property Usuario				$usuarioUltimaMod
 */
class Ecommerce_Coupon extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idEcommerce;
	public		$code;
	public		$idOrder;
	protected	$_order;
	public		$amount;
	public		$percentage;
	public		$maxAmount;
	public		$appliedAmount;
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

	public static function getFromIdEcommerce($idEcommerce) {
		$coupons = Factory::getInstance()->getListObject('Ecommerce_Coupon', 'cod_coupon_ecommerce = ' . Datos::objectToDB($idEcommerce));
		if (count($coupons)) {
			return $coupons[0];
		}
		return Factory::getInstance()->getEcommerce_Coupon();
	}

	//GETS y SETS
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