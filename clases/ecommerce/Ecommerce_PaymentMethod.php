<?php

/**
 * @property Usuario				$usuario
 * @property Usuario				$usuarioBaja
 * @property Usuario				$usuarioUltimaMod
 */
class Ecommerce_PaymentMethod extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
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
		$methods = Factory::getInstance()->getListObject('Ecommerce_PaymentMethod', 'nombre = ' . Datos::objectToDB($idEcommerce));
		if (count($methods)) {
			return $methods[0];
		}
		return Factory::getInstance()->getEcommerce_PaymentMethod();
	}

	//GETS y SETS
}

?>