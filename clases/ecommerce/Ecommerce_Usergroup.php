<?php

/**
 * @property Usuario				$usuario
 * @property Usuario				$usuarioBaja
 * @property Usuario				$usuarioUltimaMod
 */
class Ecommerce_Usergroup extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
	public		$empresa;
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

	public static function getFromName($name) {
		$usergroups = Factory::getInstance()->getListObject('Ecommerce_Usergroup', 'nombre = ' . Datos::objectToDB($name));
		if (count($usergroups)) {
			return $usergroups[0];
		}
		return Factory::getInstance()->getEcommerce_Usergroup();
	}

	//GETS y SETS
}

?>